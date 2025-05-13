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
| 1 | Log in as manager or barber user | Page should load successfully |
| 2 | Verify table headers are present | Table should show "Order #", "Client Name", "Date", and "Total" "Status" "View" columns |
| 3 | Check if order data appears | Orders should appear same as in the database |

****************************************

# Test Scenario 3: View Order Details
Status: Passed

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Click "View" button for an order | Should redirect to manage_orders.php|
| 2 | Verify displayed order information | Displays the order details from the selected order | 

****************************************