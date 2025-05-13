<!--
Authors: Alexandra Stratton, Jose Leyba, Brinley Hull, Ben Renner, Kyle Moore
Date: 03/30/2025
Revisions:
    4/23/2025 - Brinley, refactoring
Purpose: Main Pagefor Barbers to see client feedback, allows to send an email back to answer
--> 
<?php

session_start();
require 'db_connection.php';
require 'login_check.php';
require 'role_check.php';

// Get the questions from the table
$FeedQuery = "SELECT Email, Name, Comment FROM Questions";
$FeedResult = $conn->query($FeedQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style/style1.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Feedback</title>
</head>
<body>
    <div class="content-wrapper">
    <br><br>
        <h2>Client Feedback</h2>

        <?php
        if ($FeedResult->num_rows > 0) {
            while ($row = $FeedResult->fetch_assoc()) {
                echo "<div style='border:1px solid black; padding:10px; margin-bottom:10px;'>";
                echo "<p><strong>Name:</strong> " . htmlspecialchars($row["Name"]) . "</p>";
                echo "<p><strong>Email:</strong> " . htmlspecialchars($row["Email"]) . "</p>";
                echo "<p><strong>Comment:</strong> " . nl2br(htmlspecialchars($row["Comment"])) . "</p>";
                ?>
                
                <form action="send_mail.php" method="POST">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($row['Email']) ?>">
                    <input type="hidden" name="name" value="<?= htmlspecialchars($row['Name']) ?>">
                    <input type="hidden" name="question" value="<?= htmlspecialchars($row['Comment']) ?>">
                    <label for="answer">Your Response:</label>
                    <textarea name="comment" required rows="4" cols="50"></textarea>
                    <br>
                    <button type="submit">Send Response</button>
                </form>
                
                <?php
                echo "</div>";
            }
        } else {
            echo "<p>No feedback available.</p>";
        }

        $conn->close(); 
        ?>

    </div>
</body>
</html>