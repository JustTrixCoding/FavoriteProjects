2. Modifying User Information
Format:
Test Case ID
Test Scenario
Steps
Expected Result
Status


TC-1
Edit user details successfully
1. Select an existing user
2. Modify first name, last name, email, phone, social media
3. Upload a valid image for professional photo and portfolio
4. Click ‘Update Profile’
- User successfully modified and changes are visible in the barbers page
- Should reflect in the database


Passed
TC-2
Remove a field name
1. Select an existing User
2. Delete a field
3. Click ‘Update Profile’
Error message appears: “Please fill out X field”
Passed
TC-3
Rename a field with a long name
1. Select an existing field
2. Enter a string longer than maximum characters
3. Click ‘Update Profile’
Error message appears: “Maximum X characters allowed”
Passed
TC-4
Edit wrong file type
1. Select an Image file
2. Update a file that isn’t a picture
3. Click ‘Update Profile’
Error message appears: “Please enter a valid image file”
Passed
TC-5
Input a non-number for the phone number field


1. Select phone number field
2. Enter a non-number at any point
3. Click ‘Update Profile’
Error message appears: “Phone must be 10 digits”
Passed
TC-6
Input a long/short for the phone number field


1. Select phone number field
2. Enter less or more than 10 numbers
3. Click ‘Update Profile’
Error message appears:“Phone must be 10 digits”
Passed
TC-7
Input a regular string as a email
1. Select the email field
2. Enter a string without including the ‘@’ symbol 
3. Click ‘Update Profile’
Error message appears: “Please include a @ in the address, email given isn't a valid email address”
Passed


TC-8
Try SQL Injection
1. Select an field to modify
2. Enter a “;” followed by an SQL Query into the field
3. Click ‘Update Profile’
Profile will get updated correctly without running the SQL Query. 
Passed



