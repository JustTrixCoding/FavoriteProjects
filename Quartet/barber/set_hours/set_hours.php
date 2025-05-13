<!-- 
    set_hours.php
    A page for the barber to set their hours.
    Author: Alexandra Stratton, Ben Renner, Brinley Hull, Jose Leyba, Kyle Moore
    Revisions:
        2/27/2025 -- Alexandra Stratton, add weekly calendar
        3/10/2025 -- Brinley, start revamp to be the barber set hours page. Change top "week of" format
        3/11/2025 -- Brinley, add searchable week
        3/29/2025 - Brinley, retrieve current availability
        4/2/2025 - Brinley, refactoring, fix Sunday start of week bug
        4/5/2025 - Brinley, fix weeks with mixed months
        4/6/2025 - Brinley, automatically set barber id based on who is logged in
        4/7/2025 - Brinley, allow minute intervals
        4/12/2025 - Brinley, hide barber name retrieval box
        4/23/2025 - Brinley, refactoring
    Creation date: 2/27/2025
    Preconditions
        Acceptable inputs: all
        Unacceptable inputs: none
    Postconditions:
        None
    Error conditions:
        Database issues
    Side effects
        Session variables for year, week, month, and startDate are set.
    Invariants
        None
    Known faults:
        None
-->
<?php
session_start();
include 'login_check.php';
include 'role_check.php';

// Get current date
$dt = new DateTime;
if (isset($_GET['year']) && isset($_GET['week'])) {
    $dt->setISODate($_GET['year'], $_GET['week']);
} else {
    $dt->setISODate($dt->format('o'), $dt->format('W'));
}
$week = $dt->format('W');
$dt->modify('-1 day');
$year = $dt->format('o');

$_SESSION["year"] = $year;
$_SESSION["week"] = $week;
$_SESSION["month"] = $dt->format("m");
$_SESSION["startDate"] = $dt->format("d");

$monthYear = $dt->format('m/d/y'); // Get the numerical date
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/barber_style.css">
    <title>Barber Availability</title>
    <style>
    .calendar-navigation {
        display: flex;
        flex-direction: column;
        align-items: center; 
        gap: 10px; 
        }
        #calendarForm {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px; 
        }


        .calendar-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .bottom-controls {
            display: flex;
            gap: 10px;
        }
        .time-slot {
            width: 100px;
            height: 50px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .time-slot:hover {
            background-color:rgb(197, 117, 123); 
        }

        .time-slot.selected {
            background-color:rgb(183, 61, 69); 
        }
        .calendar-table td.selected {
            background-color: rgb(183, 61, 69);
        }
        .calendar-table {
            width: 100%;
            table-layout: fixed;
        }
        .calendar-table th, .calendar-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            vertical-align: middle;
        }
        .time-label {
            width: 80px; 
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .quarter-hour {
            background-color: #fafafa;
        }
        input[type="checkbox"] {
            width: 25px;
            height: 25px;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            background-color: #fff;
            border: 2px solid #ccc;
            border-radius: 4px;
            transition: all 0.2s;
            display: inline-block;
            position: relative;
        }

        input[type="checkbox"]:checked {
            background-color: rgb(183, 61, 69);
            border-color: rgb(183, 61, 69);
        }

        input[type="checkbox"]::after {
            content: '';
            position: absolute;
            top: 4px;
            left: 8px;
            width: 6px;
            height: 12px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            opacity: 0;
            transition: opacity 0.2s;
        }

        input[type="checkbox"]:checked::after {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <!-- calendar navigation with back and forward arrows, week of calendar date select. on edit/click, they reload the page with the new set date/week -->
        <div class="calendar-navigation">
            <a href="<?php echo $_SERVER['PHP_SELF'].'?week='.($week-1).'&year='.$year; ?>">&#9664;</a>
            
            <!-- Week of --- clickable/editable date showing current week we're looking at on the calendar-->
            <h2>
                Week of <input type="text" id="dateInput" placeholder="<?php echo $monthYear?>" onkeypress="if(event.key==='Enter') jumpToDate();">
            </h2>
            <a href="<?php echo $_SERVER['PHP_SELF'].'?week='.($week+1).'&year='.$year; ?>">&#9654;</a>
        </div>

        <form method="POST" id="calendarForm">
            <div class="calendar-table-wrapper">
                <input type="text" value="<?php echo $_SESSION['username']?>" name="barber" id="barber_username" hidden>
                <table class="calendar-table">
                    <tr>
                        <th></th>
                        <?php
                        $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']; // initialize days of the week list
                        $startDayOfWeek = $dt->format('N'); 
                        $startDate = clone $dt; 

                        foreach ($daysOfWeek as $day) { //show the date on the head of each calendar week
                            echo '<th>' . $day . ', ' . $startDate->format('n/j') . '</th>';
                            $startDate->modify('+1 day');
                        }
                        ?>
                    </tr>
                    <?php
                    /* Checkbox time grid */
                    $times = range(8, 17); // make range of valid times
                    $minutes = ['00', '15', '30', '45']; // make range of valid 15-minute intervals
                    foreach ($times as $hour) { // create each row of times and checkboxees
                        $timeLabel = ($hour < 12) ? $hour . ' AM' : (($hour === 12) ? '12 PM' : ($hour - 12) . ' PM'); 
                        echo '<tr class="main-hour"><td class="time-label"><strong>' . $timeLabel . '</strong></td>'; // show the time
                        foreach (range(0, 6) as $day) { //create 7 checkboxes in line with the name of the day and hour for ease of database manipulation
                            $id = $day . '-' . $hour;
                            echo '<td><input type="checkbox" class="hour-checkbox" hour="'. $hour .'" day="'.$day.'" name="' . $id . '" id="' . $id . '"></td>';
                        }
                        echo '</tr>';

                        // Sub-rows for each 15-minute increment
                        foreach ($minutes as $minute) {
                            $timeLabel = ($hour < 12) ? $hour . ':' . $minute . ' AM' : (($hour === 12) ? '12' . ':' . $minute . ' PM' : ($hour - 12) . ':' . $minute . ' PM'); 
                            echo '<tr class="quarter-hour"><td class="time-label">' . $timeLabel . '</td>';
                            foreach (range(0, 6) as $day) {
                                $id = $day . '-' . $hour . '-' . $minute;
                                echo '<td><input type="checkbox" name="' . $id . '" hour="'. $hour .'" day="'.$day.'" id="' . $id . '" class="minute-checkbox"></td>';
                            }
                            echo '</tr>';
                        }
                    }
                    ?>
                </table>
            </div>
            <div class="bottom-controls">
                <button type="submit" name="update" onclick="setFormAction('set_hours_db.php')">Update</button>
                <button type="submit" name="updateall" onclick="setFormAction('set_hours_db_all.php')">Update Reccurring</button>
            </div>
        </form>
    </div>
</body>
<script>
        function retrieveAvailability() {
            // get the barber's current availability

            // get barber
            let barber = document.getElementById("barber_username").value;

            // get the current available appointments for the barber and week
            fetch('retrieve_appointments.php?barber=' + encodeURIComponent(barber) + "&year=" + encodeURIComponent(<?php echo json_encode($year); ?>) + "&week=" + encodeURIComponent(<?php echo json_encode($week); ?>))
            .then(response => response.json())
            .then(data => {
                console.log("Appointments data:", data);
                document.querySelectorAll("input[type='checkbox']").forEach(checkbox => {
                    checkbox.checked = false; // Uncheck all first
                });

                // check the checkboxes for corresponding found appointments
                data.forEach(appointment => {
                    //find checkbox who has the same name as the appointment weekday
                    let checkbox = document.getElementById(`${appointment.Weekday}-${appointment.Time}-${appointment.Minute}`);

                    // if weekday not set, find the weekday by using the month day and year
                    if (appointment.Weekday == -1) {
                        let date = new Date(appointment.Year, appointment.Month, appointment.Day); 
                        checkbox = document.getElementById(`${date.getDay()}-${appointment.Time}-${appointment.Minute}`);
                    }

                    // if checkbox with said name exists, check it
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            })
            .catch(error => console.error("Error fetching appointments:", error));
        }
        function jumpToDate() {
            const dateInput = document.getElementById("dateInput").value; // get the value the user input
            const split = dateInput.split("/");// split the elements by the slash
            let month, day, year;

            // set the month, day, and year
            if (split.length === 3) {
                // If input contains month, day, and year (m/d/yyyy)
                [month, day, year] = split.map(Number);
            } else if (split.length === 2) {
                // If input contains only month and day (m/d), use current year
                [month, day] = split.map(Number);
                year = new Date().getFullYear();
            } else {
                alert("Invalid date format. Please use m/d or m/d/yyyy.");
                return;
            }
            
            const date = new Date(year, month-1, day+1); //create a new date object
            const weekNumber = getISOWeekNumber(date); // calculate the week
            window.location.href = `?week=${weekNumber}&year=${year}`; // reset the calender
        }
        function setFormAction(action) {
            const form = document.getElementById("calendarForm");
            form.action = action;
            form.submit(); // Manually submit the form after setting the action
        }
        // calculate week accurately
        function getISOWeekNumber(date) {
            const tempDate = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
            const dayNumber = (tempDate.getUTCDay() + 6) % 7; // Monday = 0, Sunday = 6
            tempDate.setUTCDate(tempDate.getUTCDate() - dayNumber + 3);
            const firstThursday = new Date(Date.UTC(tempDate.getUTCFullYear(), 0, 4));
            const weekNumber = 1 + Math.round(((tempDate - firstThursday) / 86400000 - 3 + ((firstThursday.getUTCDay() + 6) % 7)) / 7);
            return weekNumber;
        }
        
        retrieveAvailability();

        // function to fill all 15-minute intervals if a general hour checkbox is clicked
        document.addEventListener('DOMContentLoaded', function () {
            // When an hour-checkbox is changed...
            document.querySelectorAll('.hour-checkbox').forEach(function (hourCheckbox) {
                hourCheckbox.addEventListener('change', function () {
                    // get general hour information
                    const hour = this.getAttribute('hour');
                    const day = this.getAttribute('day');
                    const isChecked = this.checked;

                    // Find and update all matching minute checkboxes
                    document.querySelectorAll('.minute-checkbox').forEach(function (minuteCheckbox) {
                        if (
                            minuteCheckbox.getAttribute('hour') === hour &&
                            minuteCheckbox.getAttribute('day') === day && isChecked
                        ) {
                            minuteCheckbox.checked = true;
                        }
                    });
                });
            });
        });
                //Updates all of the Color Boxes everytime we select a new one
                function updateCheckboxColors() {
            document.querySelectorAll('input[type="checkbox"]').forEach(function (checkbox) {
                const td = checkbox.closest('td');
                if (checkbox.checked) {
                    td.classList.add('selected');
                } else {
                    td.classList.remove('selected');
                }
            });
        }
        //When loading the page update the colors
        document.addEventListener('DOMContentLoaded', function() {
            updateCheckboxColors();
            //When selecting an hour block be sure to also color all the 15 minute intervals
            document.querySelectorAll('input[type="checkbox"]').forEach(function (checkbox) {
                checkbox.addEventListener('change', function() {
                    if (checkbox.classList.contains('hour')) {
                        // If you click an "hour" checkbox, select/unselect all checkboxes in the same row
                        const row = checkbox.closest('tr');
                        const checkboxesInRow = row.querySelectorAll('input[type="checkbox"]');

                        checkboxesInRow.forEach(function(cb) {
                            cb.checked = checkbox.checked;
                        });
                    }
                    updateCheckboxColors();
                });
            });
        });

    </script>
</html>
