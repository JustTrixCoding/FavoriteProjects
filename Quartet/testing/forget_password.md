****************************************
# Test Scenario 1: Recieve Email with Token
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Navigate to the quartet.infinityfreeapp.com website. | The front page (index.php) will appear with store information. |
| 2 | Click the login icon on the rightmost part of the menu located at the top of the page. | A page will appear showing the login form. |
| 3 | Click on the forgot password button. | The site will redirect to the password forget password page. |
| 4 | Enter an email that belongs to a barber on the database | Email will be sent with token to reset the password |
 
****************************************

****************************************
# Test Scenario 2: Recieve Email with Token with wrong email
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-3 from Test Scenario 1 | See above. |
| 2 | Enter an email that doesn't belongs to a barber on the database | Email will not be sent with recovery token |

****************************************

****************************************
# Test Scenario 3:Trying to add SQL Injection
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-3 from Test Scenario 1 | See above. |
| 2 | Enter an SQL Injection instead of an email on the field | Email will not be sent with recovery token, SQL injection will not work |


