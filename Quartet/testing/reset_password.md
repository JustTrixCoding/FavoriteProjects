****************************************
# Test Scenario 1: Reset password when forgotten
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Navigate to the quartet.infinityfreeapp.com website. | The front page (index.php) will appear with store information. |
| 2 | Click the login icon on the rightmost part of the menu located at the top of the page. | A page will appear showing the login form. |
| 3 | Click on the forgot password button. | The site will redirect to the password forget password page. |
| 4 | Enter an email that belongs to a barber on the database | Email will be sent with token to reset the password, you are redirected to reset_pasword |
| 5 | Enter the given token, a new password, and an equal password confirmation and press the button| Password will get updated for the barber
 
****************************************

****************************************
# Test Scenario 2: Wrong token input
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-4 from Test Scenario 1 | See above. |
| 2 | Enter a token different than the one given or wait an hour before entering the token, alongside password and an equal password confirmation and press the button | Error Message appears saying "Invalid or expired reset token" |

****************************************

****************************************
# Test Scenario 3: Mismatching Passwords
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-4 from Test Scenario 1 | See above. |
| 2 | Enter a token alongside password and a different password confirmation and press the button | Error Message appears saying "Passwords must match" |
****************************************

****************************************
# Test Scenario 4:  Passwords Lenght Validation
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-4 from Test Scenario 1 | See above. |
| 2 | Enter a token alongside password and an equal password confirmation longer than 75 character or shorter than 6 and press the button| Error Message appears saying "Password must be at least 6 characters" or "Password must be less than 75 characters" |

****************************************

****************************************
# Test Scenario 5: SQL Injection
Status: Passed for all fields

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-4 from Test Scenario 1 | See above. |
| 2 | Enter a token alongside password and an equal password confirmation| Fields get Filled
| 3 | Replace one field with an SQL Injection command and press the button | The SQL command doesn't get executed |

