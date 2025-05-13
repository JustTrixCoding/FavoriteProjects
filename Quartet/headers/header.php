<!--
header.php
Purpose: Allow barbers to see the products seen in the store
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Creation Date: 03/17/2025
Revisions:
    03/17/2025 -- Alexandra Stratton -- Redesgning the hearder.php
    4/2/2025 - Brinley, add commmon style sheet
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <!--Define character encoding-->
    <meta charset="UTF-8">
    <!--Ensure proper rendering and touch zooming on mobile devices-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style1.css">
    <!-- Load Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!--Style choices for page-->
    <style>
        /* Top Bar at Top with Pages and Login */
        .top-bar {
            background-color: #c4454d;
            padding: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            height: 70px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        /* Size of Letters on its header */
        .top-bar h1 {
            margin: 0;
            padding-left: 20px;
            font-size: 28px;
        }

        /* Space for the login button on the right */
        .login-container {
            display: flex;
            align-items: center;
            padding-right: 20px;
        }

        /* Login Button Format */
        .login-button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgb(110, 7, 7);
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
            margin-left: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Style for the Menu that will have the navigation buttons */
        .menu {
            display: flex;
            flex-grow: 1;
            justify-content: space-evenly; /* Equal spacing between buttons */
            height: 100%;
        }

        /* Style of Navigation Buttons */
        .menu button {
            background-color: #c4454d;
            color: white;
            border: none;
            padding: 20px 25px;
            font-size: 18px;
            cursor: pointer;
            text-align: center;
            font-family: 'Georgia', serif;
            flex: 1; /* Equal width for all buttons */
        }

        /* Color gets darker when hovering the buttons */
        .menu button:hover {
            background-color: rgb(143, 48, 55);
        }
        /* Dropdown content (hidden by default) */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #c4454d;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        /* Links inside the dropdown */
        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        /* Change color of dropdown links on hover */
        .dropdown-content a:hover {
            background-color: rgb(143, 48, 55);
        }

        /* Show the dropdown menu on hover */
        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* Cart icon styling */
        .cart-icon {
            font-size: 20px;
            color: white;
            margin-right: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!--The top Bar at the top that has the name and button that takes you to the login page-->
    <div class="top-bar">
        <h1>Quartet's Barbershop</h1>
        <div class="menu">
            <button onclick="location.href='index.php'">Home</button>
            <button onclick="location.href='schedule.php'">Schedule</button>
            <button onclick="location.href='store.php'">Store</button>
            <button onclick="location.href='barbers.php'">About Barbers</button>
            <button onclick="location.href='feedback.php'">Contact us</button>
        </div>
        <!--Stylized Button to be circular, when clicked takes you to login.html-->
        <div class="login-container">
            <button class="login-button" onclick="location.href='login.php'"><i class="fa fa-sign-in"></i></button>
        </div>
    </div>
</body>
</html>