****************************************
# Test Scenario 1: Creating an Account
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Navigate to the quartet.infinityfreeapp.com website. | The front page (index.php) will appear with store information. |
| 2 | Click the login icon on the rightmost part of the menu located at the top of the page. | A page will appear showing the login form. |
| 3 | Input a valid username and password and click the login button. | The site will redirect to the barber-side dashboard page. |
| 4 | Select the button on the top left corner of the page. | A menu will appear. |
| 5 | Click the "Create New Account" button on the menu. | A register page will appear to fill the information of the new barber. |
| 6 | Fill the fields with a valid first name, last name, username, and password | The page should have filled fields
| 7 | Click on the 'Sign Up' button on the bottom of the page | Barber should be succesfully added to the database and should be able to log into their account. |

****************************************

****************************************
# Test Scenario 2:Add an User with missing field
Status: Passed for all fields

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-5 from Test Scenario 1. | See above. |
| 2 | Fill the fields with a valid first name, last name, username, and password. | The page should have filled fields
| 3 | Delete one of the fields | Field should appear emptry |
| 4 | Click on the 'Sign Up' button on the bottom of the page | Barber should be prompted with error message saying: “Please fill out this field”. |


****************************************

****************************************
# Test Scenario 3:Add an User with a long field
Status: Passed for all fields

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-5 from Test Scenario 1. | See above. |
| 2 | Fill the fields with a valid first name, last name, username, and password. | The page should have filled fields
| 3 | Write on one of the fields over 500 characters | Field should appear overfilled |
| 4 | Click on the 'Sign Up' button on the bottom of the page | Barber should be prompted with error message saying: “FieldName must not Exceed X amount of characters”. |

****************************************

****************************************
# Test Scenario 4: Add an username with mismatching passwords
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-5 from Test Scenario 1. | See above. |
| 2 | Fill the fields with a valid first name, last name, username, and password. | The page should have filled fields | 
| 3 | Delete one of the password fields and write mismatching passwords | Passwords should be mismatched |
| 4 | Click on the 'Sign Up' button on the bottom of the page | Barber should be prompted with error message saying: “Passwords do not match”. |
****************************************

****************************************
# Test Scenario 5:Trying to add SQL Injection
Status: Passed for all fields

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Repeat steps 1-5 from Test Scenario 1. | See above. |
| 2 | Fill the fields with a valid first name, last name, username, and password. | The page should have filled fields |
| 3 | Replace one of the fields with an SQL Injection line | SQL Injection should appear |
| 4 | Click on the 'Sign Up' button on the bottom of the page | Barber should be succefully added to the database but the SQL query is NOT exectuted. |

