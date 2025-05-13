<!--
store.php
Purpose: Store Page that allows users to see different products up to sale at the barbershop and their price
Authors: Alexandra Stratton, Jose Leyba, Brinley Hull, Ben Renner, Kyle Moore
Date: 02/12/2025
Revisions:
    03/02/2025 -- Jose Leyba -- Changed UI to look better and dark mode implemented
    03/12/2025 -- Alexandra Stratton -- Added Edit Product menu button that allow users to edit the product page
    03/13/2025 -- Jose Leyba -- Started the Revamped UI of the page
    03/14/2025 --  Alexandra Stratton -- Included the header.php and added Shopping Cart to the menu
    03/16/2025 -- Jose Leyba -- Connected to database, UI now reflects when product gets added to the cart
    03/16/2025 -- Alexandra Stratton -- Connect the add to cart button to the shopping cart
    4/2/2025 - Brinley, refactoring
-->
<?php
// Start the session to remember user info
session_start();

//Connects to database to get the Reviews table information
require 'db_connection.php';

// Get the reviews from the table
$productsQuery = "SELECT * FROM Products";
$productsResult = $conn->query($productsQuery);

// Debugging: Check if the query was successful
if (!$productsResult) {
    die("Query Failed: " . $conn->error);
}

//Puts the Products table information in an easy to iterate way
$products = [];

if ($productsResult) {
    while ($row = $productsResult->fetch_assoc()) {
        $products[] = $row;
    }
}

// For our session, collect the id's for the products and see the amount we have of each
$session_id = session_id();
$cartQuery = "SELECT Product_ID, Quantity FROM Cart WHERE Session_ID = ?";
$stmt = $conn->prepare($cartQuery);
$stmt->bind_param("s", $session_id);
$stmt->execute();
$cartResult = $stmt->get_result();

// Store the info of quantities in an array
$cartQuantities = [];
while ($row = $cartResult->fetch_assoc()) {
    $cartQuantities[$row['Product_ID']] = $row['Quantity'];
}

$stmt->close();
$conn->close();

//Remembers the Cart in your session, if you didn't had one set it to empty
if (!isset($_SESSION['Cart'])) {
    $_SESSION['Cart'] = [];
}

//Adds items to your cart when sending the post requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Product_ID'])) {
    $product_id = $_POST['Product_ID'];
    if (!in_array($product_id, $_SESSION['Cart'])) {
        $_SESSION['Cart'][] = $product_id;
    }
}

include('header.php');
?>

<head>
    <!-- Title for Page --> 
    <title>Store</title>
    <link rel="stylesheet" href="style/style1.css">

    <!-- Internal CSS for styling the page -->
    <style>
        .store-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 40px;
            max-width: 1000px;
            margin: auto;
        }
        .product-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }
        .product-container:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(255, 255, 255, 0.3);
        }
        .product-container img {
            width: 100%;
            max-width: 200px;
            border-radius: 10px;
            transition: opacity 0.3s ease;
        }
        .product-container img:hover {
            opacity: 0.85;
        }
        .product-name {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
        } 
        .popup { 
            display: none; 
            position: fixed; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%); 
            background: rgba(220, 217, 217, 0.97); /* white background with 90% opacity */
            padding: 20px; 
            border-radius: 10px; 
            text-align: center; 
            width: 300px; 
            color: black; /* make text black */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); /* soft shadow */
        }
        .popup h2 {
            color: black;
        }
        .popup img { 
            max-width: 100%; 
            border-radius: 10px; 
        }
        .popup button { 
            margin-top: 10px; 
            background: #c4454d; 
            color: white; 
            border: none; 
            padding: 10px; 
            cursor: pointer; 
        }
        .cart-quantity {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: #c4454d; 
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 14px;
            font-weight: bold;
        }
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
        #black-text {
            color: black;
        }
        .floating-cart-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background-color: #c4454d;
            color: white;
            border: none;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1000;
            transition: background-color 0.3s;
        }
        .floating-cart-button:hover {
            background-color: rgb(143, 48, 55);
        }
        #search {
            width: 400px;
            display: block;
            margin: 20px auto;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
    <!-- Functions needed-->
    <script>
        //Shows the details of a product when clicking it's image
        function showDetails(name, price, image, description) {
            document.getElementById('popup-name').innerText = name;
            document.getElementById('popup-price').innerText = "$" + price;
            document.getElementById('popup-image').src = image;
            document.getElementById('popup-description').innerText = description;
            document.getElementById('popup').style.display = 'block';
        }
        //Closes the popup
        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }

        //Filter the products shown when searching for specific ones
        function filterProducts() {
            let search = document.getElementById('search').value.toLowerCase();
            let products = document.getElementsByClassName('product-container');
            for (let product of products) {
                let name = product.getElementsByClassName('product-name')[0].innerText.toLowerCase();
                product.style.display = name.includes(search) ? '' : 'none';
            }
        }
    </script>
</head>
<body>
    <!--let's user know the current page they are on-->
    <h1 id="black-text">Store</h1>
    <!--Styled grid 3x3 That shows in each space a different product available with a picture and it's name-->
    <input type="text" id="search" onkeyup="filterProducts()" placeholder="Search products...">
    <div class="store-grid">
        <?php foreach ($products as $product) { 
            // Makes sure the special characters don't leak into the SQL query
            $name = htmlspecialchars(addslashes($product['Name']));
            $description = htmlspecialchars(addslashes($product['Description']));
            $image = htmlspecialchars($product['Image']);
            $price = htmlspecialchars($product['Price']);
            $product_id = $product['Product_ID'];
            $quantity = isset($cartQuantities[$product_id]) ? $cartQuantities[$product_id] : 0;
        ?>
            <div class='product-container' onclick="showDetails('<?php echo $name; ?>', '<?php echo $price; ?>', '<?php echo $image; ?>', '<?php echo $description; ?>')">
                <img src='<?php echo $image; ?>' alt='<?php echo $name; ?>'>
                <?php if ($quantity > 0) { ?>
                    <div class="cart-quantity"><?php echo $quantity; ?></div>
                <?php } ?>
                <div class='product-name'><?php echo $product['Name']; ?></div>
                <div class='product-price'>$<?php echo $price; ?></div>
                <form action="add_item.php" method="POST" style="display:inline;" onsubmit="event.stopPropagation();">
                    <input type="hidden" name="product_id" value="<?php echo $product['Product_ID']; ?>">
                    <button type="submit" class="btn add-to-cart-btn" onclick="event.stopPropagation();">Add to Cart</button>
                </form>
            </div>

        <?php } ?>
    </div>
    <!--Popup that appears/dissapears when clicking a product-->
    <div id="popup" class="popup">
        <img id="popup-image" src="" alt="Product Image">
        <h2 id="popup-name"></h2>
        <p id="popup-price"></p>
        <p id="popup-description"></p>
        <button onclick="closePopup()">Close</button>
    </div>
    <button class="floating-cart-button" onclick="location.href='cart.php'">
        <i class="fa-solid fa-cart-shopping"></i>
    </button>
</body>
</html>
