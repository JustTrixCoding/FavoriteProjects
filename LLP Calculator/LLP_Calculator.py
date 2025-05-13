from flask import Flask, request, render_template_string
import openpyxl
import requests
import io
import locale

# Set the locale to format currency correctly
locale.setlocale(locale.LC_ALL, '')

# Flask app setup
app = Flask(__name__)

# URL to Google Sheets
GOOGLE_SHEET_URL = "https://docs.google.com/spreadsheets/d/e/2PACX-1vTupzgT6YaVUf7u-BCLCvccePkhC0YRBfsZPzG-Uoml-Ks8eZu8Lt4UhjloD6nR5NuG7re90v1kVf8b/pub?output=xlsx"

# HTML template
html_template = """
<!doctype html>
<html>
    <head>
        <title>LLP Pricing Calculator</title>
        <style>
            /* Container with side padding to constrain table + buttons */
            .table-container {
                padding: 0 50px; /* 50px padding on left and right */
                width: 100%; /* Ensures full width available for the table */
                box-sizing: border-box;
            }
            
            /* Make table behave responsively */
            table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
            }

            /* Style table header cells */
            th {
                background-color: #0a3d62;
                color: white;
                text-align: left;
                padding: 8px;
                border: 1px solid black;
            }

            /* Style table body cells */
            td {
                background-color: #f1f1f1;
                padding: 8px;
                border: 1px solid black
            }

            /* Ensure inputs fit perfectly in cells */
            td input {
                width: 100%;
                padding: 6px;
                box-sizing: border-box;
                font-size: 1em;
                border: 1px solid #999;
            }

            /* Container for buttons aligned to table */
            .button-row {
                display: flex;
                justify-content: space-between;
                margin-top: 5px;
                margin-bottom: 5px;
                width: 100%;
            }

            /* Left and right button groupings */
            .left-buttons, .right-buttons {
                display: flex;
                gap: 10px;
            }

            /* Button styles */
            button {
                padding: 10px 16px;
                background-color: #0a3d62;
                color: white;
                border: none;
                cursor: pointer;
                min-width: 100px;
                text-align: center;
            }
            .bold { 
                font-weight: bold; 
            }
            body {
                height: 100vh; 
                margin: 0;
                background-image: linear-gradient(to bottom right, #4ec9ff, #214f8d );
                background-repeat: no-repeat; 
                background-size: cover;
                background-attachment: fixed;
            }
            button:hover {
                background-color: #074175;
            }
        </style>
        <script>
            function addRow() { // JavaScript function to dynamically add rows to the table
                let table = document.getElementById("inputTable");
                let rowCount = table.rows.length;  // Get current row count (includes header row)
                let row = table.insertRow(-1);
                let cell0 = row.insertCell(0);
                let cell1 = row.insertCell(1);
                let cell2 = row.insertCell(2);
                cell0.innerHTML = rowCount;  // Correct row numbering
                cell1.innerHTML = `<input type="text" name="part_nb_${rowCount - 1}">`;
                cell2.innerHTML = `<input type="text" name="cycles_rm_${rowCount - 1}">`;
            }
            function addFiveRows() {
                for (let i = 0; i < 5; i++) {
                    addRow();
                }
            }
        </script>
    </head>
    <body>
        <h1 style="text-align: center; background-color: #124b79;color: #fcfcfc; padding-top: 20px; padding-bottom: 20px;">Welcome to the LLP Pricing Calculator</h1>
        <form method="post">
            <div class="table-container">
                <table id="inputTable">
                    <tr>
                        <th>Row</th>
                        <th>Part Number</th>
                        <th>Cycles Remaining</th>
                    </tr>
                    {% for i in range(3) %} <!-- Create three default input rows -->
                    <tr>
                        <td>{{ i + 1 }}</td>
                        <td><input type="text" name="part_nb_{{ i }}"></td>
                        <td><input type="text" name="cycles_rm_{{ i }}"></td>
                    </tr>
                    {% endfor %}
                </table>
                <div class="button-row">
                    <div class="left-buttons">
                        <button type="submit" class="uniform-button">Calculate</button>
                    </div>
                    <div class="right-buttons">
                        <button type="button" class="uniform-button" onclick="addRow()">Add Row</button>
                        <button type="button" class="uniform-button" onclick="addFiveRows()">Add 5 Rows</button>
                    </div>
                </div>
            </div>
        </form>

        {% if results %} <!-- If results are available, display them -->
            <ul style="padding-left: 5px; padding-right: 5px; margin: 0; list-style-type: none;">
                {% for result in results %}
                <li>
                    {% if result.error %} <!-- Display errors if any -->
                        <h2 style = "background-color: #fffb92; margin: 0; padding-left: 5px; border: 5px solid #ffce2d; margin-bottom: 10px;"><strong>P#: {{ result.part_nb }}</strong> {{ result.error }}</h2>
                    {% else %} <!-- Display calculated results -->
                        <h2 style="background-color: #124b79;color: #fcfcfc; margin: 0;padding-left: 5px;"><span class="bold"> PN: {{ result.part_nb }}</span></h2>
                        <p style="background-color: #eaeaea; margin: 0; margin-top: 1px; border-top: 2px solid black; border-left: 2px solid black; border-right: 2px solid black; padding-left: 5px;"><strong> Description: </strong> {{ result.part_nm }}</p>
                        <p style="background-color: #eaeaea; margin: 0; border-left: 2px solid black; border-right: 2px solid black;padding-left: 5px;"><strong> CLP: </strong> {{ result.clp }}</p>
                        <p style="background-color: #eaeaea; margin: 0; border-bottom: 2px solid black; border-left: 2px solid black; border-right: 2px solid black;padding-left: 5px;"><strong> Model: </strong> {{ result.model }}</p>

                        <!-- Container for horizontal scroll -->
                        <div style="display: flex; flex-wrap: nowrap; overflow-x: auto; padding-bottom: 10px;">
                            {% for prorate_dict in result.prorates %}
                            <div style="margin: 1px; background-color: #eaeaea;flex: 1; min-width: 200px; max-width: 300px;border: 2px solid black; padding-left: 3px;">
                                <p><strong>Thrust: </strong> {{ prorate_dict.thrust }}</p>
                                <p><strong>Cycle Limit: </strong> {{ prorate_dict.cycle_lm }} cycles</p>
                                
                                <p><strong>Pro Rates:</strong></p>
                                <div style="display: flex; flex-direction: column; justify-content: flex-start; align-items: flex-start;">
                                    {% for percent, rate in prorate_dict.prorates.items() %}
                                        <div style="min-width: 150px; margin-bottom: 1px;padding-left: 20px;">
                                            <span>{{ percent }}% Pro Rate:</span>
                                            <span>{{ rate }}</span>
                                        </div>
                                    {% endfor %}
                                </div>
                            </div>
                            {% endfor %}
                        </div>
                    {% endif %}
                </li>
                {% endfor %}
            </ul>
        {% endif %}




        {% if error %}
        <h2 style = "background-color: #ff4e4e; margin: 0; padding-left: 5px; border: 5px solid #e10040; margin-bottom: 10px;"><strong>{{ error }}</strong></h2>
        {% endif %}
    </body>
</html>
"""
def fetch_worksheet():
    """Download and load the Google Sheet."""
    try:
        response = requests.get(GOOGLE_SHEET_URL)
        response.raise_for_status()
        workbook = openpyxl.load_workbook(io.BytesIO(response.content), data_only=True)
        return workbook.active
    except Exception as e:
        return str(e)

def build_part_lookup(sheet):
    """Build a fast lookup dictionary for part numbers."""
    lookup = {}
    for row in sheet.iter_rows(min_row=2, values_only=True):
        part_nb = (row[0] or "").strip().upper()
        if part_nb:
            if part_nb not in lookup:
                lookup[part_nb] = []
            lookup[part_nb].append(row)
    return lookup

def calculate_prorates(clp, cycle_lm, cycles_rm):
    """Calculate prorates from CLP and cycles."""
    prorates = {}
    if not all((clp, cycle_lm, cycles_rm)):
        return prorates
    rate = (clp / cycle_lm) * cycles_rm
    for pct in range(100, 0, -5):
        prorates[pct] = locale.currency(rate * (pct / 100), grouping=True).split('.')[0]
    return prorates

@app.route('/', methods=['GET', 'POST'])
def index():
    if request.method == 'POST':
        # Parse form
        inputs = [
            (request.form.get(f'part_nb_{i}', '').strip().upper(),
             request.form.get(f'cycles_rm_{i}', '').strip())
            for i in range(len(request.form)//2)
        ]
        inputs = [(p, c) for p, c in inputs if p or c]

        if not inputs:
            return render_template_string(html_template, error="At least one part number and cycles entry required.")

        # Validate cycles input
        parsed_inputs = []
        for idx, (p, c) in enumerate(inputs, start=1):
            if not p:
                parsed_inputs.append({"row_number": idx, "error": f"Missing part number in row {idx}"})
                continue
            try:
                cycles_rm = int(c) if c else None
            except ValueError:
                parsed_inputs.append({"row_number": idx, "error": f"Invalid cycles remaining at row {idx}"})
                continue
            parsed_inputs.append({"part_nb": p, "cycles_rm": cycles_rm})

        # Load Sheet
        worksheet = fetch_worksheet()
        if isinstance(worksheet, str):
            return render_template_string(html_template, error=f"Error loading sheet: {worksheet}")

        part_lookup = build_part_lookup(worksheet)

        # Build results
        results = []
        for entry in parsed_inputs:
            if "error" in entry:
                results.append(entry)
                continue

            part_nb = entry['part_nb']
            cycles_rm = entry['cycles_rm']

            if part_nb not in part_lookup:
                results.append({"part_nb": part_nb, "error": "Part number not found in database."})
                continue

            rows = part_lookup[part_nb]
            first_row = rows[0]
            clp = first_row[2] if isinstance(first_row[2], (int, float)) else None
            cycle_lm = first_row[3] if isinstance(first_row[3], (int, float)) else None
            part_nm = first_row[1] or "Add to Sheet"
            model = first_row[4] or "Add to Sheet"

            prorate_blocks = []
            for r in rows:
                thrust = r[8] or "Add to Sheet"
                prorates = calculate_prorates(clp, cycle_lm, cycles_rm)
                prorate_blocks.append({
                    "thrust": thrust,
                    "cycle_lm": cycle_lm or "Add to Sheet",
                    "prorates": prorates
                })

            results.append({
                "part_nb": part_nb,
                "part_nm": part_nm,
                "clp": locale.currency(clp, grouping=True).split('.')[0] if clp else "Add to Sheet",
                "model": model,
                "prorates": prorate_blocks
            })

        return render_template_string(html_template, results=results)

    return render_template_string(html_template)

if __name__ == '__main__':
    app.run(debug=True)
