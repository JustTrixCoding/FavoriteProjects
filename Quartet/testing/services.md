****************************************
# Test Scenario 1: Add a service.
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Navigate to the quartet.infinityfreeapp.com website. | The front page (index.php) will appear with store information. |
| 2 | Click the login icon on the rightmost part of the menu located at the top of the page. | A page will appear showing the login form. |
| 3 | Input a valid username and password and click the login button. | The site will redirect to the barber-side dashboard page. |
| 4 | Select the button on the top left corner of the page. | A menu will appear. |
| 5 | Click the "Services" button on the menu. | A page will show with a two tables showing services information: Your services and All services. |
| 6 | Click the "Add Service" button on the top right. | The page will redirect to the Add Service page with a form with inputs for name, price, and duration. |
| 7 | Input invalid data into the form (i.e., numbers for name, symbols, leave form empty) and click Add Service. | The page will indicate that invalid input was put into the form. |
| 8 | Input valid data into the form and click Add Service. | The page will redirect to the services page, which will show the new service under the All Services table. |

****************************************

****************************************
# Test Scenario 2: Edit a service.
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-5 from Test Scenario 1. | See above. |
| 2 | Find the desired service to edit under the All Services table and click the "Edit" button. | The page will redirect to the Edit Service page with a form with inputs prefilled with the existing name, price, and duration. |
| 3 | Input invalid data into the form (i.e., numbers for name, symbols, leave form empty) and click Add Service. | The page will indicate that invalid input was put into the form. |
| 4 | Input valid data into the form and click Update Service. | The page will redirect to the services page, which will show the updated service information under the All Services table. |

****************************************

****************************************
# Test Scenario 3: Delete a service.
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-5 from Test Scenario 1. | See above. |
| 2 | Find the desired service to delete under the All Services table and click the "Delete" button. | A popup will show with an "Are you sure?" message. |
| 3 | Click the "No" button. | The popup will disappear and the services page will remain unchanged. |
| 4 | Repeat step 2. | See above. |
| 5 | Click the "Yes" button. | The popup will disappear and the services page will update and will not show the deleted service anymore. |

****************************************

****************************************
# Test Scenario 4: Add a service to your offered services.
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-5 from Test Scenario 1. | See above. |
| 2 | Find the desired service to delete under the All Services table and click the "Add to your services" button. | The service will now appear under the Your services table. |
| 3 | Repeat step 2 with the same service. | The Your services table will remain unchanged with no duplicates. |

****************************************

****************************************
# Test Scenario 5: Delete a service from your offered services.
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1 and 2 from Test Scenario 4. | See above. |
| 2 | Click the corresponding "Delete" button from the Your Services table for the desired service. | The service will disappear from the Your Services table. |

****************************************

****************************************
# Test Scenario 6: Add a service to a specified barber's services.
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Navigate to the quartet.infinityfreeapp.com website. | The front page (index.php) will appear with store information. |
| 2 | Click the login icon on the rightmost part of the menu located at the top of the page. | A page will appear showing the login form. |
| 3 | Input a valid username and password for a manager and click the login button. | The site will redirect to the barber-side dashboard page. |
| 4 | Select the button on the top left corner of the page. | A menu will appear. |
| 5 | Click the "Employees" button on the menu. | The menu will show a dropdown with more buttons including "Employee Services". |
| 6 | Click the "Employee Services" button on the menu. | A page will show with a two tables showing services information: Your services and All services. |
| 7 | Enter the username for a specific barber and select "Retrieve". | The Your services table will update to show the specified barber's offered services. |
| 8 | Repeat step 2 from Test Scenario 4. | See above. |

****************************************

****************************************
# Test Scenario 7: Delete a service from a specified barber's services.
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-8 from Test Scenario 6. | See above. |
| 2 | Repeat step 2 from Test Scenario 5. | See above. |

****************************************