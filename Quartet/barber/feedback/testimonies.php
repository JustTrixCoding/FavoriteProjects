<!--
testimonies.php
Purpose: Allows barbers to select the reviews to showcase in the Main Client Page
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Date: 04/08/2025
Revisions:
    4/23/2025 - Brinley, refactoring
--> 

<?php
session_start();
require 'db_connection.php';
require 'login_check.php';
require 'role_check.php';
// Adds or Removes the Review from the Testimonies Table depending on the pressed button
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reviewID = intval($_POST['review_id']);

    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO Testimonies (Testimony_ID, Name, Rating, Review)
                                SELECT Review_ID, Name, Rating, Review FROM Reviews
                                WHERE Review_ID = ? AND Review_ID NOT IN (SELECT Testimony_ID FROM Testimonies)");
        $stmt->bind_param("i", $reviewID);
        $stmt->execute();
    }

    if (isset($_POST['remove'])) {
        $stmt = $conn->prepare("DELETE FROM Testimonies WHERE Testimony_ID = ?");
        $stmt->bind_param("i", $reviewID);
        $stmt->execute();
    }
}

// Gets all the reviews from the Database
$reviewsQuery = "SELECT Review_ID, Name, Rating, Review FROM Reviews ORDER BY Review_ID DESC";
$reviewsResult = $conn->query($reviewsQuery);

// Stores the Testimonies_ID to see which reviews are already on the table
$testimoniesQuery = "SELECT Testimony_ID FROM Testimonies";
$testimoniesResult = $conn->query($testimoniesQuery);

$testimoniesIDs = [];
while ($row = $testimoniesResult->fetch_assoc()) {
    $testimoniesIDs[] = $row['Testimony_ID'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Reviews</title>
    <link rel="stylesheet" href="style/barber_style.css">
    <style>
        .reviews {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.1);
    }
    .reviews h2 {
        margin-bottom: 20px;
        font-size: 28px;
        color: #333;
        text-align: center;
    }
    .review-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .review {
        background: #fafafa;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #ddd;
    }
    .review strong {
        font-size: 20px;
        color: #222;
    }
    .rating {
        display: block;
        margin-top: 8px;
        font-weight: bold;
        color: #888;
    }
    .review p {
        margin-top: 10px;
        line-height: 1.5;
        color: #555;
    }

    </style>
</head>
<body>
    <div class="content-wrapper">
    <br><br>
        <div class="reviews">
            <h2>User Reviews</h2>            
            <div class="review-container">
                <?php
                //When there are any reviews
                if ($reviewsResult->num_rows > 0) {
                    //It will iterate through all the reviews, showing them to the barber
                    while ($row = $reviewsResult->fetch_assoc()) {
                        $reviewID = $row['Review_ID'];
                        $isInTestimonies = in_array($reviewID, $testimoniesIDs);

                        echo "<div class='review'>";
                        echo "<strong>" . htmlspecialchars($row['Name']) . "</strong>";
                        echo "<span class='rating'>Rating: " . htmlspecialchars($row['Rating']) . "/5</span>";
                        echo "<p>" . nl2br(htmlspecialchars($row['Review'])) . "</p>";

                        echo "<form method='post'>";
                        echo "<input type='hidden' name='review_id' value='" . $reviewID . "' />";
                                    
                        
                        //It will activate the curresponding button to add/remove to/from Testimonies table
                        echo "<button type='submit' name='add' " . ($isInTestimonies ? "disabled" : "") . ">Add to Testimonies</button>";
                        echo "<button type='submit' name='remove' " . (!$isInTestimonies ? "disabled" : "") . ">Remove from Testimonies</button>";
                        echo "</form>";

                        echo "</div>";
                    }
                } else {
                    echo "<p>No reviews available.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>