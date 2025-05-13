<!--
index.php
Purpose: Main Page to see the barbershops, Barbers, Cuts, and Availabilities
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Date: 02/12/2025
    Revisions:
        3/1/2025  -- Jose, Stylizing Choices to page
        03/02/2025 -- Dark Mode Added
        03/09/2025 -- Jose -- Started Review Work
        3/13/2025 -- Jose -- Added implementation to see the previous reviews and average score of the baarbershop, started to change color scheme to red
        03/14/2025 -- Alexandra Stratton implementedd header.php
        03/14/2025 -- Alexandra Stratton Got rid of unnecessary information
        03/16/2025 -- Jose Leyba -- UI Changes, makes the Name and Rating on the same horizontal level, replaced number of ratings with a star system
        4/2/2025 - Brinley, refactoring
--> 
<?php
// Start the session to remember user info
session_start();

// check to see if the user is logged in and give them a cute little welcome message
if (isset($_SESSION["user"]) && !empty($_SESSION["user"])) {
    echo "<p class='welcome-message'>Welcome back, " . htmlspecialchars($_SESSION["user"], ENT_QUOTES, 'UTF-8') . "!</p>";
}

// Connect to the database
require 'db_connection.php';

// Query to get the store's location from the Store database
$storeQuery = "SELECT Address, City, State, Zip_Code FROM Store LIMIT 1";  
$storeResult = $conn->query($storeQuery);

// Fetch the store location
if ($storeResult->num_rows > 0) {
    $storeRow = $storeResult->fetch_assoc();
    $storeLocation = $storeRow['Address'] . ', ' . $storeRow['City'] . ', ' . $storeRow['State'] . ' ' . $storeRow['Zip_Code'];
} else {
    $storeLocation = "Location not available";  // Default message if no store info is found
}

// Function to convert 24-hour time to 12-hour AM/PM format
function convertTo12HourFormat($time) {
    $dateTime = DateTime::createFromFormat('H:i:s', $time);
    return $dateTime->format('g:i A'); // 'g:i A' gives 12-hour format with AM/PM
}

// Query to get store hours from Store_Hours table, excluding closed days
$hoursQuery = "SELECT Day, Open_Time, Close_Time FROM Store_Hours WHERE Is_Closed = 0 ORDER BY FIELD(Day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
$hoursResult = $conn->query($hoursQuery);

$storeHours = "<div class='store-hours'>";

if ($hoursResult->num_rows > 0) {
    while ($row = $hoursResult->fetch_assoc()) {
        $day = $row['Day'];
        $open = date("g:i A", strtotime($row['Open_Time']));
        $close = date("g:i A", strtotime($row['Close_Time']));

        $storeHours .= "<div class='store-hour-row'><strong>$day:</strong><span>$open - $close</span></div>";
    }
} else {
    $storeHours .= "<div>No hours available.</div>";
}
$storeHours .= "</div>";

// Get the reviews from the table
$reviewsQuery = "SELECT Name, Rating, Review FROM Testimonies ORDER BY Testimony_ID DESC";
$reviewsResult = $conn->query($reviewsQuery);

// Fetch the average rating
$avgRatingQuery = "SELECT AVG(Rating) AS avg_rating FROM Reviews";
$avgRatingResult = $conn->query($avgRatingQuery);
$avgRatingRow = $avgRatingResult->fetch_assoc();
$averageRating = $avgRatingRow['avg_rating'] ? number_format($avgRatingRow['avg_rating'], 2) : "N/A";

// Close the database connection
$conn->close();

include('header.php');
?>


<head>
    <!--Name of Page-->
    <title>Home Page</title>
    <link rel="stylesheet" href="style/style1.css">

    <style>

        /* Store info section */
        .store-info {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border-radius: 10px;
            margin: 20px;
        }
        .store-info img {
            width: 400px;
            height: auto;
            border-radius: 10px;
            margin-right: 20px;
        }
        .store-text {
            text-align: left;
        }

        /* Arrows for scrolling */
        /* Reviews section */
        .reviews {
            padding: 20px;
            margin: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .average-rating {
            font-size: 20px;
            font-weight: bold;
            background: #c4454d;
            padding: 10px;
            border-radius: 5px;
            color: white;
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

        input{
            border: 1px solid #ccc;
            border-radius: 5px;
            background: rgba(18, 11, 11, 0.7);
            color:white;
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
        .store-hours {
            font-size: 16px;
            margin-top: 10px;
        }

        .store-hour-row {
            display: flex;
            justify-content: space-between;
            width: 300px;
            font-family: monospace;
        }

        .store-hour-row strong {
            width: 120px;
            display: inline-block;
        }


    </style>
    <script>
        // Selects image from query and shows you the currently selected image
        function showImage(barberIndex, index) {
            let images = document.querySelectorAll(`.barber-${barberIndex} img`);
            images.forEach(img => img.classList.remove("active"));
            images[index].classList.add("active");
        }
        //Scrolls to the next image by indexing foward through them
        function nextImage(barberIndex) {
            let images = document.querySelectorAll(`.barber-${barberIndex} img`);
            let currentIndex = Array.from(images).findIndex(img => img.classList.contains("active"));
            currentIndex = (currentIndex + 1) % images.length;
            showImage(barberIndex, currentIndex);
        }
        //Scrolls to the previous image by indexing backwards through them
        function prevImage(barberIndex) {
            let images = document.querySelectorAll(`.barber-${barberIndex} img`);
            let currentIndex = Array.from(images).findIndex(img => img.classList.contains("active"));
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            showImage(barberIndex, currentIndex);
        }
        document.addEventListener("DOMContentLoaded", () => {
            for (let i = 1; i <= 3; i++) {
                showImage(i, 0);
            }
        });

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
    <h1 id="black-text">Home</h1>
       <!--Section that holds image of barbershop, followed by the Sotre Information-->
    <div class="store-info">
        <img src="images/store.jpg" alt="Store Image">
        <div class="store-text">
            <p><strong>Location:</strong> <?php echo $storeLocation; ?></p>
            <p><strong>Hours:</strong></p>
            <?php echo $storeHours; ?>
        </div>
    </div>
    

    <!--Added section to work on the future for the reviews of the page-->
    <div class="reviews">
        <h2 id="black-text">Some Client Testimonies!</h2>
            <!--Shows the average rating to the users-->
            <div class="average-rating">
                Average Rating: <?php echo $averageRating; ?>
            </div>
            
            <div class="review-container">
                <h2 id="black-text">Reviews</h2>
                <!--Shows the reviews already stored in the database, if there is not then Print it to the screen-->
                <?php
                if ($reviewsResult->num_rows > 0) {
                    while ($row = $reviewsResult->fetch_assoc()) {
                        echo "<div class='review'>";
                        echo "<strong>" . htmlspecialchars($row['Name']) . "</strong>";
                        echo "<span class='rating'>Rating: " . htmlspecialchars($row['Rating']) . "/5</span>";
                        echo "<p>" . nl2br(htmlspecialchars($row['Review'])) . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No reviews available.</p>";
                }
                ?>
            </div>
    </div>

</body>
</html>
