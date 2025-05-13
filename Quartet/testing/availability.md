****************************************
# Test Scenario 1: Retrieve current availability.
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Navigate to the quartet.infinityfreeapp.com website. | The front page (index.php) will appear with store information. |
| 2 | Click the login icon on the rightmost part of the menu located at the top of the page. | A page will appear showing the login form. |
| 3 | Input a valid username and password and click the login button. | The site will redirect to the barber-side dashboard page. |
| 4 | Select the button on the top left corner of the page. | A menu will appear. |
| 5 | Click the "Hours" button on the menu. | A weekly calendar will appear showing the current week's availability for the logged-in barber. |

****************************************

****************************************
# Test Scenario 2: Navigate to a specified week.
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-5 from Test Scenario 1. | See above. |
| 2 | Input a date into the "Week of" textbox at the top of the page in integers in the format m/d and hit enter. | The weekly calendar will show the week that includes the specified date. |
| 3 | Input a date into the "Week of" textbox in integers in the format m/d/y and hit enter. | The weekly calendar will show the week that includes the specified date. |

****************************************

****************************************
# Test Scenario 3: Update a specific week's availability.
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1 and 2 from Test Scenario 2. | See above. |
| 2 | Check various desired timeslots on desired days. | Checkboxes for selected times will appear checked. |
| 3 | Click the "Update" button. | The page will continue to show the specified times as checked. |
| 4 | Navigate to the quartet.infinityfreeapp.com website. | The front page (index.php) will appear with store information. |
| 5 | Click the "Schedule" button on the menu located at the top of the page. | The schedule.php page will appear showing a monthly calendar. |
| 6 | Navigate to the corresponding updated week by clicking the next or previous month button. | The calendar will show the specified month at the top of the page. | 
| 7 | Click on the "X Appointments Found" button on one of the days in the specified week. | The monthly calendar will zoom in to a weekly view showing the correct specified availability. |

****************************************

****************************************
# Test Scenario 4: Update a recurring week's availability.
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-5 from Test Scenario 1. | See above. |
| 2 | Repeat step 2 from Test Scenario 3. | See above. |
| 3 | Click the "Update Recurring" button. | The page will continue to show the specified times as checked. |
| 4 | Navigate to the quartet.infinityfreeapp.com website. | The front page (index.php) will appear with store information. |
| 5 | Click the "Schedule" button on the menu located at the top of the page. | The schedule.php page will appear showing a monthly calendar. |
| 6 | Click on the "X Appointments Found" button on one of the days in any week. | The monthly calendar will zoom in to a weekly view showing the correct specified availability. |

****************************************

****************************************
# Test Scenario 5: Retrieve current availability for a specified barber.
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Navigate to the quartet.infinityfreeapp.com website. | The front page (index.php) will appear with store information. |
| 2 | Click the login icon on the rightmost part of the menu located at the top of the page. | A page will appear showing the login form. |
| 3 | Input a valid username and password for a manager account and click the login button. | The site will redirect to the barber-side dashboard page. |
| 4 | Select the button on the top left corner of the page. | A menu will appear. |
| 5 | Click the "Employees" button on the menu. | The menu will show a dropdown with more buttons including "Employee Hours". |
| 6 | Click the "Employee Hours" button on the menu. | A weekly calendar will appear showing the current week's availability for the manager. |
| 7 | Enter the username for a specific barber and select "Retrieve availability". | The weekly calendar will update to show the weekly availability for the specified barber. |

****************************************

****************************************
# Test Scenario 6: Update availability for a specified barber.
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-7 from Test Scenario 5. | See above. |
| 2 | Repeat steps 2-7 from Test Scenario 3. | See above. |
| 3 | Repeat step 1. | See above. |
| 4 | Repeat steps 2-6 from Test Scenario 4. | See above. |

****************************************

****************************************
# Test Scenario 7: Input an invalid date
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-5 from Test Scenario 1. | See above. |
| 2 | Input a invalid character (symbols, letters, etc.) or single number into the "Week of" textbox at the top of the page and hit enter. | An alert box will popup indicating an invalid input. |

****************************************