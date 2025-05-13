****************************************
# Test Scenario 1: Access Control
Status: Passed

| # | Steps | Expected Result |
| --- | --- | --- |
| 1 | Access page without login | Redirect to login page |
| 3 | Access as manager user | Page loads successfully |
| 4 | Check initial data load | Store information displays correctly |
| 5 | Check with no existing data | Form shows empty fields |

****************************************

****************************************
# Test Scenario 2: Store Information Validation
Status: Passed

| # | Steps | Expected Result |
| --- | --- | --- |
| 1 | Submit empty store name | Show validation error |
| 2 | Enter invalid store name | Show validation error |
| 3 | Enter valid store name | Accept input |
| 4 | Submit invalid phone | Show validation error |
| 5 | Enter no phone number | Show validation error |
| 6 | Enter valid phone number | Accept and format input |
| 7 | Submit invalid email format | Show validation error |
| 8 | Enter valid email | Accept input |
| 9 | Submit empty address | Show validation error |
| 10 | Enter valid address | Accept input |
| 11 | Submit empty city | Show validation error |
| 12 | Enter invalid city | Show validation error |
| 13 | Enter valid city | Accept input |
| 14 | Submit invalid zip format | Show validation error |
| 15 | Enter valid zip | Accept input |

****************************************

****************************************
# Test Scenario 3: Store Information Save
Status: Passed 

| # | Steps | Expected Result |
| --- | --- | --- |
| 1 | Submit valid store info | New data appears in form |
| 2 | Check database after submit | Data matches form input |
| 3 | Submit with new store  | Creates new record |
| 4 | Submit with existing store | Updates existing record |

****************************************

****************************************
# Test Scenario 4: Store Hours 
Status: Passed

| # | Steps | Expected Result |
| --- | --- | --- |
| 1 | Mark day as closed | Disables time inputs |
| 2 | Unmark closed day | Enables time inputs |
| 3 | Submit with missing open/close times | Shows validation errors |
| 4 | Submit with close < open time | Shows validation error |
| 5 | Submit valid hours | Updates database |
| 6 | Check database after submit | Hours match form input |
| 7 | Test all days of week | All days save correctly |

****************************************

