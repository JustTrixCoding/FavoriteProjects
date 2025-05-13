<!-- 
    schedule.php
    A page to hold the appointment calendar and scheduler.
    Authors: Alexandra Stratton, Ben Renner, Brinley Hull, Jose Leyba, Kyle Moore
    Revisions:
        2/25/2025 -- Brinley, add calendar
        2/27/2025 -- Brinley, add appointment button popups
        2/28/2025 -- Brinley, add timeslots and populate appointment details
        3/1/2025  -- Jose, Stylizing Choices to page
        3/1/2025 -- Brinley, add confirm appointment abilities
        3/2/2025 -- Ben, added 'search' feature
        3/2/2025 -- Allie upcoming and past appointments
        03/02/2025 -- Jose Leyba, Changed Style of Calendar + Cancelation button
        3/14/2025 -- Brinley, Add week view
        3/16/2025 -- Brinley, add search/filtering
        3/27/2025 -- Brinley, gray out days before current date
        3/28/2025 -- Brinley, gray out timeslots before current time
        4/2/2025 - Brinley, refactoring; fix Sunday button bug
        4/5/2025 - Brinley, fix incorrect month display on week view
        4/10/2025 - Brinley, add minute
        4/14/2025 - Brinley, update filtering
    Creation date:
    Other sources: ChatGPT
-->
<?php
// Start the session to remember user info
session_start();

// connect to the database
require 'db_connection.php';

//get the common header
include('header.php');

$sql = "SELECT * FROM Barber_Information";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "Error preparing statement: " . $conn->error;
    exit();
}

$stmt->execute();
$result = $stmt->get_result();
$barbers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $barbers[] = $row;
    }
}

$sql = "SELECT * FROM Services";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "Error preparing statement: " . $conn->error;
    exit();
}

$stmt->execute();
$result = $stmt->get_result();
$services = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}
$barberColors = [];
$query = "SELECT Barber_ID, Color FROM Barber_Information";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $barberColors[$row['Barber_ID']] = $row['Color'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Defines character encoding for proper text rendering -->
    <meta charset="UTF-8">
    <!-- Ensures proper rendering and touch zooming on mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title of the page displayed in the browser tab -->
    <title>Schedule</title>
    <link rel="stylesheet" href="style/style1.css">
    <!-- Internal CSS for styling the page -->
    <style>
        /* Applies styles to the entire body */

        /* Calendar styles */
        .calendar-container {
            background: rgba(223, 218, 218, 0.97);
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
        }

        .month-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .calendar {
            margin: 15px;
            margin-top: 10px;
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            text-align: center;
            background:rgb(210, 207, 207);
        }

        .day {
            position: relative; /* Ensures child elements are positioned relative to this */
            /*aspect-ratio: 1/.75;*/
            background: rgb(252, 250, 250);
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            min-height: 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .day span {
            position: absolute;
            top: 5px;
            left: 5px;
            font-size: 14px;
            font-weight: bold;
            display: grid;
        }


        .dayHead {
            background-color:rgb(143, 48, 55);
            color: white;
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
        }

        .calendar-nav {
            margin-top: 15px;
        }

        /* appointment button styling */
        .day-button {
            background-color: #c4454d;
            color: black;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
        }

        /* Popup styling */
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #333;
            color: black;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgb(255, 255, 255);
            display: none; /* Start hidden */
        }

        .popup-content {
            background: white; /* Dark background */
            color: black;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            text-align: center;
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
            color: black;
        }


        /* Grid container inside the popup */
        .appointment-grid {
            background-color: rgba(248, 248, 248, 0.9);
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); /* Adjust columns dynamically */
            gap: 10px;
            color: black;
            margin-top: 10px;
        }

        /* Individual appointment items */
        .appointment-item {
            background:rgb(246, 246, 246);
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
        }
        .search-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
        }

        .search-container input,
        .search-container select,
        .search-container button {
            padding: 8px;
            font-size: 16px;
            border-radius: 5px;
        }

        .search-container button {
            background-color: #c4454d;;
            color: white;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: rgb(143, 48, 55);
        }
        #timeSlotSelect {
            width: 200px;
            height: 150px;
        }

        #timeSlotSelect option.selected {
            background-color: #c4454d;
            color: white;
        }
        #black-text {
            color: black;
        }
        .filter-section {
            background: rgba(248, 248, 248, 0.95);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 200px;
        }

        .filter-group label {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 16px;
            color: rgb(52, 52, 52);
        }

        .filter-group select {
            width: 100%;
            padding: 8px 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" class="bi bi-caret-down-fill" viewBox="0 0 16 16"> <path d="M7.247 11.14l-4.796-5.481c-.566-.647-.106-1.659.753-1.659h9.592c.86 0 1.32 1.012.753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/> </svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px 16px;
        }

        .filter-group select[multiple] {
            height: auto;
            min-height: 100px;
        }

        .apply-filters-button {
            background-color: #c4454d;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 24px;
            transition: background-color 0.3s ease;
        }

        .apply-filters-button:hover {
            background-color: rgb(143, 48, 55);
        }
        .black-text {
            color: black;
        }

    </style>
</head>
<body>
    <!-- Page title -->
    <h1 id="black-text">Schedule</h1>
    
    <!-- Search Feature -->
    <!-- Search Feature -->
    <div class="search-container">
        <div class="filter-section">
            <!-- Barber Filter -->
            <div class="filter-group">
                <label for="barberSelect">Barber:</label>
                <select id="barberSelect" name="barberSelect[]" multiple size="5">
                    <option value="None" selected>All</option>
                    <?php foreach ($barbers as $barber): ?>
                        <option value="<?php echo $barber['Barber_ID']?>"><?php echo $barber['First_Name'] . " " . $barber['Last_Name']?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        <!-- Service Filter (already handled single-select) -->
            <div class="filter-group">
                <label for="serviceSelect">Service:</label>
                <select id="serviceSelect" name="serviceSelect">
                    <option value="None" selected>All</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?php echo $service['Service_ID']?>"><?php echo $service['Name']?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        <!-- Time Filter -->
            <div class="filter-group">
                <label for="timeSelect">Time:</label>
                <select id="timeSelect" name="timeSelect[]" multiple size="10">
                    <option value="None" selected>All</option>
                    <?php
                    for ($h = 6; $h <= 20; $h++) {
                        foreach ([0] as $m) {
                            $timeFormatted = sprintf('%02d:%02d', $h, $m);
                            echo "<option value=\"$timeFormatted\">$timeFormatted</option>";
                        }
                    }
                    ?>
                </select>
            </div>
        
        <!-- Apply Filters Button -->
            <div class="filter-group">
                <button type="button" class="apply-filters-button" onclick="search()">Apply Filters</button>
            </div>
        </div>
    </div>

<script>
    // Custom multi-select behavior for barber filter
    const barberSelect = document.getElementById('barberSelect');
    barberSelect.addEventListener('mousedown', function(e) {
        e.preventDefault(); // Prevent default select behavior

        const option = e.target;
        if (option.tagName === 'OPTION') {
            if (option.value === 'None') {
                // If 'All' is clicked, deselect all others and select 'All'
                Array.from(barberSelect.options).forEach(opt => opt.selected = false);
                option.selected = true;
            } else {
                // Toggle the selected state
                option.selected = !option.selected;

                // If anything else is selected, unselect 'All'
                const allOption = barberSelect.querySelector('option[value=\"None\"]');
                if (allOption) allOption.selected = false;
            }
        }
    });

    // Custom multi-select behavior for time filter
    const timeSelect = document.getElementById('timeSelect');
    timeSelect.addEventListener('mousedown', function(e) {
        e.preventDefault(); // Prevent default select behavior

        const option = e.target;
        if (option.tagName === 'OPTION') {
            if (option.value === 'None') {
                // If 'All' is clicked, deselect all others and select 'All'
                Array.from(timeSelect.options).forEach(opt => opt.selected = false);
                option.selected = true;
            } else {
                // Toggle the selected state
                option.selected = !option.selected;

                // If anything else is selected, unselect 'All'
                const allOption = timeSelect.querySelector('option[value=\"None\"]');
                if (allOption) allOption.selected = false;
            }
        }
    });
    // Ensure "All" is selected on page load if nothing else is
    window.addEventListener('load', function() {
        // Barber Select
        const barberOptions = Array.from(barberSelect.options);
        if (!barberOptions.some(opt => opt.selected && opt.value !== 'None')) {
            // No other barber selected, ensure "All" is selected
            const allOption = barberSelect.querySelector('option[value="None"]');
            if (allOption) allOption.selected = true;
        }

        // Time Select
        const timeOptions = Array.from(timeSelect.options);
        if (!timeOptions.some(opt => opt.selected && opt.value !== 'None')) {
            // No other time selected, ensure "All" is selected
            const allOption = timeSelect.querySelector('option[value="None"]');
            if (allOption) allOption.selected = true;
        }
    });

    function search() {
        // Barber Filter
        let barberSelect = document.getElementById("barberSelect");
        let selectedBarbers = Array.from(barberSelect.selectedOptions).map(option => option.value);

        // Time Filter
        let timeSelect = document.getElementById("timeSelect");
        let selectedTimes = Array.from(timeSelect.selectedOptions).map(option => option.value);

        // Service Filter
        let service = document.getElementById("serviceSelect").value || null;

        // Create query for the selected filters
        let query = '?';

        if (selectedBarbers.length > 0 && !selectedBarbers.includes('None')) {
            query += 'barberSelect=' + encodeURIComponent(selectedBarbers.join(',')) + '&';
        }

        if (service && service !== 'None') {
            query += 'serviceSelect=' + encodeURIComponent(service) + '&';
        }

        if (selectedTimes.length > 0 && !selectedTimes.includes('None')) {
            query += 'timeSelect=' + encodeURIComponent(selectedTimes.join(',')) + '&';
        }

        // Remove trailing '&' or '?' if no filters were added
        query = query.endsWith('&') || query.endsWith('?') ? query.slice(0, -1) : query;

        // Send the filters to the server via fetch (or use your existing POST method)
        window.location.href = window.location.pathname + query; // Example to apply the filters
    }
</script>
    <!-- End Search Feature -->

    <div class="calendar-container">
        <div class="month-name" id="monthName">
            <!-- Month Name will be displayed here -->
        </div>

        <div class="calendar">
            <!-- Days of the week -->
            <div class="dayHead">Sunday</div>
            <div class="dayHead">Monday</div>
            <div class="dayHead">Tuesday</div>
            <div class="dayHead">Wednesday</div>
            <div class="dayHead">Thursday</div>
            <div class="dayHead">Friday</div>
            <div class="dayHead">Saturday</div>

            <!-- Calendar Days will be dynamically generated here -->
        </div>

        <div class="calendar-nav">
            <button id="prevButton" onclick="changeMonth(-1)" style="margin-bottom: 5px;">Previous Month</button>
            <button id="nextButton" onclick="changeMonth(1)" style="margin-bottom: 5px;">Next Month</button>
            <!-- Week navigation buttons -->
            <button id="prevWeekButton" onclick="changeWeek(-1)" style="display: none; margin-bottom: 5px;">Previous Week</button>
            <button id="nextWeekButton" onclick="changeWeek(1)" style="display: none; margin-bottom: 5px;">Next Week</button>
            <!-- back to month view from week view -->
            <button id="backToMonthButton" onclick="goToCurrentMonth()" style="display: none;">Back to Current Month</button>
        </div>

                <!-- Popup Modal -->
        <div id="appointmentPopup" class="popup">
            <div class="popup-content">
                <span class="close-btn">&times;</span>
                <h2><span id="appointmentDay"></span></h2>
                <div id="appointmentGrid" class="appointment-grid"></div>
            </div>
        </div>
    </div>
    <br>

<script>
    const barberColors = <?php echo json_encode($barberColors); ?>;
    let monthView = true;

    function search() {
        let barberSelect = document.getElementById("barberSelect");
        let selectedBarbers = Array.from(barberSelect.selectedOptions).map(option => option.value);

        let timeSelect = document.getElementById("timeSelect");
        let selectedTimes = Array.from(timeSelect.selectedOptions).map(option => option.value);

        let service = document.getElementById("serviceSelect").value || null;

        fetch('set_filter.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                filter: true,
                barber: selectedBarbers,
                service: service,
                time: selectedTimes
            })
        }).then(response => response.text())
        .then(data => {
            renderCalendar(selectedDate.getDate(), selectedDate.getDay());
        }).catch(error => {
            console.error('Error:', error);
        });
    }

        // ChatGPT help start
        let currentMonth = new Date().getMonth(); // Current month (0-11)
        let currentYear = new Date().getFullYear(); // Current year
        let currentDay = new Date().getDate();
        let currentWeekday = new Date().getDate();
        let currentTime = new Date().getHours();
        let selectedDate = new Date(); // Defaults to today
        let savedMonth = currentMonth;
        let savedYear = currentYear;
        let monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June', 
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        let dayNames = ['Sunday', 'Monday', "Tuesday", 'Wednesday', 'Thursday', 'Friday', "Saturday"];
        let appointmentsData = [];
    // Function to render the calendar
    function renderCalendar(day=0, weekday=0) {
        currentTime = new Date().getHours();

        let prevButton = document.getElementById('prevButton'); //identifies button for switching months
        let nextButton = document.getElementById('nextButton'); //identifies button for switching months
        let prevWeekButton = document.getElementById('prevWeekButton'); //identifies button for switching weeks
        let nextWeekButton = document.getElementById('nextWeekButton'); //identifies button for switching weeks
        document.getElementById('backToMonthButton').style.display = 'none'; // hide the back button

        // Get the first day of the month and the total number of days in the month
        let firstDay = new Date(currentYear, currentMonth, 1).getDay(); // First day of the month
        let daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate(); // Number of days in the month
        let daysInPrevMonth = new Date(currentYear, currentMonth, 0).getDate(); // Number of days in the previous month

        // Update month name
        document.getElementById('monthName').innerHTML = `${monthNames[currentMonth]} ${currentYear}`;

        // Clear the previous calendar month
        let calendar = document.querySelector('.calendar');
        calendar.querySelectorAll('.day').forEach(day => day.remove());

        if (monthView) {
            //show month switch buttons, remove week switch buttons
            prevButton.style.display = 'inline-block';
            nextButton.style.display = 'inline-block';
            prevWeekButton.style.display = 'none';
            nextWeekButton.style.display = 'none';
            // Add empty divs for days before the 1st day of the month
            for (let i = 0; i < firstDay; i++) {
                let emptyDay = document.createElement('div');
                emptyDay.classList.add('day');
                emptyDay.style.background = 'rgb(155, 155, 155)';
                calendar.appendChild(emptyDay);
            }

            // Add actual days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                let dayDiv = document.createElement('div');
                dayDiv.classList.add('day');

                // Create a span for the day number
                let dayNumber = document.createElement('span');
                dayNumber.textContent = day;

                // Gray out days before current date
                if (currentYear < new Date().getFullYear() || 
                (currentYear === new Date().getFullYear() && currentMonth < new Date().getMonth()) || 
                (currentYear === new Date().getFullYear() && currentMonth === new Date().getMonth() && day < new Date().getDate())) {
                    dayDiv.style.background = 'rgb(205, 202, 202)';
                    dayNumber.style.color = 'darkgray';
                } else {
                    // Show appointment buttons

                    // Create a button
                    let button = document.createElement('button');

                    // Get the weekday
                    let weekday = new Date(currentYear, currentMonth, day).getDay();

                    // Fetch appointment count from backend
                    fetch(`get_appointments.php?year=${currentYear}&month=${currentMonth}&day=${day}&weekday=${weekday}`)
                        .then(response => response.json())
                        .then(data => {
                            appointmentsData = data;
                            button.textContent = `${appointmentsData.length} Appointment(s) Found`;
                        })
                        .catch(error => {
                            console.error("Error fetching appointment count:", error);
                            button.textContent = "Error fetching appointment count";
                        });

                        button.addEventListener('click', () => {
                            // Save the month/year before switching to week view
                            savedMonth = currentMonth;
                            savedYear = currentYear;

                            selectedDate = new Date(currentYear, currentMonth, day);
                            monthView = false;
                            document.getElementById('backToMonthButton').style.display = 'inline-block';
                            renderCalendar(day, weekday);
                        });
                    button.classList.add('day-button');

                    // Append elements
                    dayDiv.appendChild(button);
                }
                
                dayDiv.appendChild(dayNumber);
                calendar.appendChild(dayDiv);
            }

        } else {
            let firstWeekDay = new Date(currentYear, currentMonth, day - weekday); // First day of the current week
            let lastWeekDay = new Date(currentYear, currentMonth, day - weekday + 6); // Last day of the current week
            //show week switch buttons, remove month switch buttons
            prevButton.style.display = 'none';
            nextButton.style.display = 'none';
            prevWeekButton.style.display = 'inline-block';
            nextWeekButton.style.display = 'inline-block';
            document.getElementById('backToMonthButton').style.display = 'inline-block'; // show the back button
            

            // Add actual days of the WEEK
            for (let offset = 0; offset < 7; offset++) {
                let tempMonth = currentMonth;
                let tempYear = currentYear;
                let wday = day - weekday + offset; // Correct calculation for the week day
                let dayDiv = document.createElement('div');
                if (wday < 1){
                    wday += daysInPrevMonth;
                    tempMonth--;
                    if (tempMonth < 0) {
                        tempMonth = 11;
                        tempYear--;
                    }
                } else if (wday > daysInMonth) {
                    wday -= daysInMonth;
                    tempMonth++;
                    if (tempMonth > 11) {
                        tempMonth = 0;
                        tempYear++;
                    }
                }
                if (offset == weekday) { // if we're on the selected day
                    dayDiv.style.backgroundColor = "#c4454d"; // change background color to show it's selected
                }
                dayDiv.classList.add('day');

                // Create a span for the day number
                let dayNumber = document.createElement('span');
                dayNumber.textContent = wday;

                if ((wday < currentDay && tempMonth == new Date().getMonth()) || tempMonth < new Date().getMonth()) { //gray out days before current day
                    dayDiv.style.background = 'rgb(70, 70, 70)';
                    dayNumber.style.color = 'darkgray';
                } else {
                    //otherwise show appointments

                    // Fetch appointment count from backend
                    fetch(`get_appointments.php?year=${tempYear}&month=${tempMonth}&day=${wday}&weekday=${new Date(tempYear, tempMonth, wday).getDay()}`)
                        .then(response => response.json())
                        .then(data => {
                            // Reset appointments for this day
                            let appointments = data.length ? data : ["No appointments"];

                            if (appointments !== "No appointments") {
                                // Sort appointments by time before creating buttons
                                appointments.sort((a, b) => (a.Time * 60 + a.Minute) - (b.Time * 60 + b.Minute));
                            }

                            // Create timeslots dynamically
                            appointments.forEach(appointment => {
                                let item = document.createElement('button');
                                if (appointment === "No appointments") {
                                    item.textContent = "No appointments";
                                } else {
                                    let time = (appointment.Time <= 12 ? appointment.Time : appointment.Time - 12);
                                    let period = (appointment.Time < 12 ? "AM" : "PM");
                                    item.textContent = time + ":" + appointment.Minute + period;
                                    
                                    // Change button color based on barber
                                    const barberColor = barberColors[appointment.Barber_ID];
                                    if (barberColor) {
                                        item.style.backgroundColor = barberColor;
                                    } else {
                                        item.style.backgroundColor = "IndianRed"; // fallback
                                    }
                                    let now = new Date();
                                        let currentHours = now.getHours();
                                        let currentMinutes = now.getMinutes();

                                        //only make appointment clickable if time is after current time
                                        let isToday = (tempYear === now.getFullYear() && tempMonth === now.getMonth() && wday === now.getDate());
                                        if (isToday) {
                                            if (
                                                appointment.Time < currentHours || 
                                                (appointment.Time == currentHours && appointment.Minute <= currentMinutes)
                                            ) {
                                                item.disabled = true;
                                                item.style.backgroundColor = 'rgb(113, 121, 126)';
                                            }
                                        }
                                    // Add click event to show appointment details
                                    item.addEventListener('click', () => {
                                        openAppointmentInfo(appointment, tempYear, tempMonth, wday);
                                    });
                                }
                                item.classList.add('day-button');
                                dayDiv.appendChild(item);
                                
                                
                            });
                        })
                        .catch(error => {
                            console.error("Error fetching appointment count:", error);
                        });
                }

                    
                dayDiv.appendChild(dayNumber);
                // Append the dayDiv to the calendar before fetching data
                calendar.appendChild(dayDiv);
                if (offset == 0){
                    firstWeekDay = `${monthNames[tempMonth]} ${wday} `;
                } else if (offset == 6 ){
                    lastWeekDay = `${monthNames[tempMonth]} ${wday}`;
                }
            }
            document.getElementById('monthName').innerHTML = `${firstWeekDay} - ${lastWeekDay}`;
        }
    }

        // Function to change the month (either forward or backward)
        function changeMonth(direction) {
            currentMonth += direction;

            // If the month goes out of bounds, adjust the year and month
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            } else if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }

            renderCalendar(); // Re-render the calendar for the new month
        }
        function changeWeek(direction) { //need to fix
            selectedDate.setDate(selectedDate.getDate() + (7 * direction));

            // Update currentMonth and currentYear based on the new selectedDate
            currentMonth = selectedDate.getMonth();
            currentYear = selectedDate.getFullYear();

            // Re-render the calendar with the new selectedDate
            renderCalendar(selectedDate.getDate(), selectedDate.getDay());
        }


        function openAppointmentInfo(appointment, year, month, day) {
            const popup = document.getElementById('appointmentPopup');
            const appointmentGrid = document.getElementById('appointmentGrid');
            const appointmentDay = document.getElementById('appointmentDay');
            const time = appointment.Time;
            const minute = appointment.Minute;

            // Always make sure popup is visible
            popup.style.display = 'block';

            // Clear the popup
            appointmentGrid.innerHTML = "";

            // Update appointment day/title
            appointmentDay.textContent = `Selected Appointment`; // ✅ Just update the text!
            appointmentDay.className = 'black-text';
            let appointmentInfoPara = document.createElement('p');
            appointmentInfoPara.innerHTML = `Date: ${dayNames[new Date(year, month, day).getDay()]}, ${monthNames[month]} ${day}, ${year}
                                        \nTime: ${(time <= 12 ? time : time-12) + ":" + minute + (time < 12 ? "AM" : "PM")}
                                        \nBarber: ${appointment.Barber_ID}`;
            appointmentGrid.appendChild(appointmentInfoPara);

            let bookButton = document.createElement('button');
            bookButton.textContent = "Book Appointment";
            bookButton.addEventListener('click', () => {
                bookAppointment(appointment, day, month, year, time, minute);
            });

            appointmentGrid.appendChild(bookButton);
        }


        function bookAppointment(appointment, day, month, year, time, minute) {
            // Function to show client a space to add their information and confirm their appointment.
            fetch('set_appointment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ appointment: appointment,
                                        day: day,
                                        month: month,
                                        year: year,
                                        time: time,
                                        minute: minute
                 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    window.location.href = "confirm_appointment.php"; // Redirect after session is set
                } else {
                    console.error("Error:", data.message);
                }
            })
            .catch(error => console.error("Fetch error:", error));
        
        }

        // Close popup when clicking X button
        document.querySelector('.close-btn').addEventListener('click', () => {
            document.getElementById('appointmentPopup').style.display = 'none';
        });

        // Initial render
        search();
        renderCalendar();
        //ChatGPT end

        function goToCurrentMonth() {
            currentMonth = savedMonth;
            currentYear = savedYear;
            monthView = true;
            renderCalendar();
        }
    </script>
</body>
</html>
