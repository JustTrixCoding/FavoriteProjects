****************************************
# Test Scenario 1: Retrieve current availability.
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Navigate to the quartet.infinityfreeapp.com website. | The front page (index.php) will appear with store information. |
| 2 | Click the login icon on the rightmost part of the menu located at the top of the page. | A page will appear showing the login form. |
| 3 | Input a valid username and password and click the login button. | The site will redirect to the barber-side dashboard page. |

****************************************

****************************************
# Test Scenario 2: Enter long input when logging into Account
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-2 from Test Scenario 1. | See above. |
| 2 | Input an invalid username and password and click the login button. | The site will prompt the user with the error message: "Invalid username or password." |

****************************************

****************************************
# Test Scenario 3:Trying to add SQL Injection
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-2 from Test Scenario 1. | See above. |
| 2 | Input an SQL injection instead of an username and password and click the login button. | The site will not run the SQL injection." |



