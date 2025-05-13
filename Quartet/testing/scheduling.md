****************************************
# Test Scenario 1: Select an appointment from calendar.
Status:

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Navigate to the quartet.infinityfreeapp.com website. | The front page (index.php) will appear with store information. |
| 2 | Click the "Schedule" button on the menu located at the top of the page. | The schedule.php page will appear showing a monthly calendar. |
| 3 | Click on the "X Appointments Found" button on the desired date. | The monthly calendar will zoom in to a weekly view with the selected day highlighted. |
| 4 | Click on the desired time for the appointment on the desired date. | A popup will appear showing all appointment details for that timeslot. |
| 5 | Click the "Book Appointment" button. | The confirm_appointment.php page will appear with the correct appointment details filled in the form. |

****************************************

****************************************
# Test Scenario 2: Deselect an appointment from calendar.
Status: Failed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Follow steps 1-4 from Test Scenario 1. | See above. |
| 2 | Click the "x" in the top right corner of the popup. | The popup will disappear and the weekly view will show again. |
| 3 | *do something to go to monthly view* | The weekly calendar will zoom out back to the monthly calendar. |

****************************************

****************************************
# Test Scenario 3: Filter appointments by barber.
Status: Failed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Follow steps 1 and 2 from Test Scenario 1. | See above. |
| 2 | Click the barber filter dropdown located above the calendar. | A dropdown will appear showing the names of every barber in the shop. |
| 3 | Select the name of the desired barber. | The appointment count shown on each day's button may go down. |
| 4 | Click on the "X Appointments Found" button on the desired date. | The monthly calendar will zoom in to a weekly view where all the timeslots are the same color. |
| 5 | Click on the desired time for the appointment on the desired date. | A popup will appear showing the name of the specified barber as part of the appointment details. |
| 6 | Click the "x" in the top right corner of the popup. | The popup will disappear and the weekly view will show again. |
| 7 | Repeat step 2-5 with a different barber. | See above. |

****************************************

****************************************
# Test Scenario 4: Filter appointments by service.
Status: Failed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Follow steps 1 and 2 from Test Scenario 1. | See above. |
| 2 | Click the service filter dropdown located above the calendar. | A dropdown will appear showing the names of every service offered in the shop. |
| 3 | Select the name of the desired service. | The appointment count shown on each day's button may go down. |
| 4 | Repeat steps 3-5 from Test Scenario 1. | See above. |
| 5 | Select the services dropdown at the bottom of the page. | The services dropdown will include the specified service. |

****************************************