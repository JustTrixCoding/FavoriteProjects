****************************************
# Test Scenario 1: Access control
Status: 

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Access without Order_ID parameter | Error message shown |
| 2 | Access with invalid Order_ID | Error message shown |
| 3 | Access as unauthorized user | Redirect to login |
| 4 | Access as barber with valid Order_ID | Page loads with barber header |
| 5 | Access as manager with valid Order_ID | Page loads with manager header |

****************************************

****************************************
# Test Scenario 2: Order status changes
Status: 

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Change status to "ready" | Email sent, success message shown |
| 2 | Change status to "cancelled" | Email sent, success message shown |
| 3 | Change status with notes | Status updates successfully |
| 4 | Change status without notes | Status updates successfully |

****************************************

****************************************
# Test Scenario 3: Email notifications
Status: 

| # | Steps | Expected result |
| --- | --- | --- |
| 1 | Change to ready status | Verify ready email content |
| 2 | Change to cancelled status | Verify cancellation email content |

****************************************
