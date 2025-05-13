<!--
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Date: 03/02/2025
    Revision:
        03/09/2025 -- Jose -- Fixed mistake so text inputed is visible (white)
        03/30/2025 -- Jose -- Now Sends Communication to Database and Barber Side

Purpose: Allows user to send comments that will reach the barbers email for feedback/questions purposes
-->
<?php
// Start the session to remember user info
session_start();

?>
<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!--Define character encoding-->
    <meta charset="UTF-8">
    <!--Ensure proper rendering and touch zooming on mobile devices-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Name of Page-->
    <title>Home Page</title>
    <link rel="stylesheet" href="style/style1.css">
    <!--Style choices for page, they include font used, margins, alignation, background color, display types, and some others-->
    <style>
        button {
            background-color: #c4454d;
            color: black;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: rgb(143, 48, 55);
        }
        .reviews {
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;

        }
        .average-rating {
            font-size: 20px;
            font-weight: bold;
            background: #c4454d;
            padding: 10px;
            border-radius: 5px;
        }
        .review-container {
            max-width: 600px;
            margin: 50px auto;
        }
        .review {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .review strong {
            display: block;
            font-size: 18px;
        }
        .rating {
            color: #c4454d;
        }

        textarea {
            margin: 10px 0;
            border-radius: 5px;
        }

        .form-group {
            display: flex;
            align-items: center;
            gap: 10px; 
        }

        .form-group label {
            white-space: nowrap;
        }
        .rating-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stars {
            display: flex;
            cursor: pointer;
        }

        .star {
            font-size: 25px;
            color: black;
            transition: color 0.3s;
        }
        #black-text {
            color: black;
        }
    </style>
    </style>
    <script>
        function sendData() { //Sends input data to a PHP backend using
            let inputData = document.getElementById("dbInput").value;
            fetch("server.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ data: inputData })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("dbOutput").innerText = data.message;
            });
        }
        //Adds a listener that pays attention to every individual star (for rating)
        document.addEventListener("DOMContentLoaded", function() {
            const stars = document.querySelectorAll(".star");
            const ratingInput = document.getElementById("Rating");
            //For each start, when clicking on it it will assing that rating to the data and will fill the stars red
            stars.forEach(star => {
                star.addEventListener("click", function() {
                    let rating = this.getAttribute("data-value");
                    ratingInput.value = rating;

                    // For eaach star before (including the selcted one), change colors from black to red
                    stars.forEach(s => {
                        s.style.color = s.getAttribute("data-value") <= rating ? "red" : "black";
                    });
                });
            });
        });
    </script>
</head>
<body>
    <!--let's user know the current page they are on-->
    <h1 id="black-text">Contact Us!</h1>
    <br><br>
    <div class="container">
        <div class="section">
            <p>Do you have any questions about our service? Please send us a message and we will respond as soon as possible!<p>
        </div>
        <form action="recieve_feedback.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="name">Name:</label>
            <input type="name" id="name" name="name">
            <label for="comment">Comment:</label>
            <textarea id="comment" name="comment" rows="4" required></textarea>
            
            <button type="submit">Send</button>
        </form>
    </div>
    <p>Do you have any feedback for us? We love to hear your opinion in our services!<p>
    
    <h1 id="black-text">Reviews</h1>
    <div class="container">
        <div class="reviews">
        <form action="submit_review.php" method="POST">
            <div class="form-group">
                <label for="Name">Name:</label>
                <input type="text" id="Name" name="Name">
                <div class="rating-container">
                    <label for="Rating">Rating:</label>
                    <div class="stars">
                        <span class="star" data-value="1">&#9733;</span>
                        <span class="star" data-value="2">&#9733;</span>
                        <span class="star" data-value="3">&#9733;</span>
                        <span class="star" data-value="4">&#9733;</span>
                        <span class="star" data-value="5">&#9733;</span>
                    </div>
                    <input type="hidden" id="Rating" name="Rating" required>
                </div>
            </div>
            <br>
            <label for="Review">Add your Review Here!</label><br>
            <textarea type="text" id="Review" name="Review" required></textarea><br><br>
            <button type="submit">Send your Review!</button>
        </form>
        </div>
    </div>
    <br><br><br><br><br><br><br><br><br>
    <br><br><br><br><br><br><br><br><br>



</body>