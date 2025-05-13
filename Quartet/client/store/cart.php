<!--
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Date: 03/12/2025
Revisions:
    03/16/2025 -- Alexandra Stratton -- Created the cart.php
    03/30/2025 -- Alexandra Stratton -- Place order for non-empty carts
    04/06/2025 -- Alexandra Stratton -- Refactoring
Purpose: Customers shopping carter
Other Sources: ChatGPT
-->
<?php
session_start();
//Connects to the database
require 'db_connection.php'; 
//Gets everything in session_id's cart
$session_id = session_id();
$sql = "SELECT Cart.*, Products.Name, Products.Price, Products.Image 
        FROM Cart 
        JOIN Products ON Cart.Product_ID = Products.Product_ID
        WHERE Cart.Session_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $session_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['Price'] * $item['Quantity'];
}
?>
<?php include('header.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style/style1.css">
    <title>Shopping Cart</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .cart-container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .empty-btn-container {
            text-align: right;
            margin-bottom: 20px;
        }
        .empty-btn, .checkout-btn, .continue-btn {
            background: #c4454d;
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .empty-btn:hover, .checkout-btn:hover, .continue-btn:hover {
            background: rgb(143, 48, 55);
        }
        .total-price {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            margin-bottom: 20px;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        .quantity-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            padding: 0;
            font-size: 18px;
            color: white;
            background-color: #c4454d;
            border: none;
            cursor: pointer;
            border-radius: 50%; /* Make them circular */
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }

        .quantity-btn:hover {
            background-color: rgb(143, 48, 55);
        }

        .quantity-number {
            font-size: 18px;
            min-width: 20px;
            text-align: center;
            color: black;
        }
        .remove-btn {
            background-color: #FF6A13;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
        }
        .remove-btn:hover {
            background-color: #FF8A3D;
        }
        img {
            max-width: 50px;
            height: auto;
            border-radius: 5px;
        }
        #black-text {
            color: black;
        }
    </style>
</head>
<body>
    <h1 id="black-text">Shopping Cart</h1>
    <div class="cart-container">
        <div class="empty-btn-container">
            <a href="empty_cart.php" class="empty-btn">Empty Cart</a>
        </div>
        <!-- List everything in cart -->
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td>
                            <img src="<?php echo $item['Image']; ?>" alt="<?php echo $item['Name']; ?>">
                            <?php echo $item['Name']; ?>
                        </td>
                        <td>$<?php echo number_format($item['Price'], 2); ?></td>
                        <td>
                            <div class="quantity-wrapper">
                                <form action="update_quantity.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['Cart_ID']; ?>">
                                    <input type="hidden" name="action" value="decrease">
                                    <button type="submit" class="quantity-btn">-</button>
                                </form>

                                <div class="quantity-number">
                                    <?php echo $item['Quantity']; ?>
                                </div>

                                <form action="update_quantity.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['Cart_ID']; ?>">
                                    <input type="hidden" name="action" value="increase">
                                    <button type="submit" class="quantity-btn">+</button>
                                </form>
                            </div>
                        </td>
                        <td>$<?php echo number_format($item['Price'] * $item['Quantity'], 2); ?></td>
                        <td>
                            <form action="remove_item.php" method="POST" style="display:inline;">
                                <input type="hidden" name="cart_id" value="<?php echo $item['Cart_ID']; ?>">
                                <button type="submit" class="remove-btn">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Gives total price -->
        <div class="total-price">
            Total: $<?php echo number_format($total_price, 2); ?>
        </div>
        <!-- Place order button -->
        <div class="btn-container">
            <a href="store.php" class="continue-btn">Continue Shopping</a>
            <?php if (count($cart_items) > 0): ?>
                <a href="place_orders.php" class="checkout-btn">Check Out</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>