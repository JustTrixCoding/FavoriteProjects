****************************************
# Test Scenario 1: Access control
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Attempt to access client_list.php without logging in | System should redirect to login page |
| 2 | Log in as barber user and access page | Page should load successfully with client list with barber header |
| 3 | Log in as manager user and access page | Page should load successfully with client list with manager header |

****************************************

****************************************
# Test Scenario 2: Verify Page
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Log in as manager or barber | Page should load successfully |
| 2 | Verify table headers are present | Table should show "Client ID", "Client Name", "Email", and "Phone Number" columns |
| 3 | Check if client data appears | Clients should appear same as in the database |

****************************************

****************************************
# Test Scenario 3: Test search functionality
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Log in as manager or barber | Page should load successfully |
| 2 | Enter text in search box that matches a client name | Only matching clients should appear |
| 3 | Enter text that matches a phone number | Only matching clients should appear |
| 4 | Enter text that doens't match any clients | An empty table should appear |
| 5 | Clear search box | All clients should reappear |

****************************************a
