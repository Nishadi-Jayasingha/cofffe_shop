<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

$conn = new mysqli("localhost", "root", "", "coffee_shop");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <style>
        body {
            background: url(../img/log.png) no-repeat center/cover;
            font-family: Arial, sans-serif;
            color: white;
        }

        .container {
            background-color: rgba(55, 46, 40, 0.7);
            width: 90%;
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 10px;
        }

        input[type="text"], input[type="submit"], select, input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            background-color: white;
            color: black;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #f3c481;
        }

        input[type="number"] {
            width: 80px;
        }
        a.shop {
            display: inline-block;
            margin: 20px auto;
            background: #6b3e3e;
            padding: 10px 20px;
            border-radius: 6px;
            color: white;
            text-decoration: none;
            transition: background 0.3s ease;
            text-align: center;
        }

        a.shop:hover {
            background: rgb(65, 7, 7);
        }

    </style>
</head>
<body>
<div class="container">
    <h2>Checkout</h2>

    <?php if (empty($cart)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <form action="process_order.php" method="post" id="checkoutForm">
            <label>Customer Name:</label>
            <input type="text" name="customer_name" required>

            <label>Payment Method:</label>
            <select name="payment_method" required>
                <option value="">-- Select Payment Method --</option>
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="online">Online</option>
            </select>

            <table id="cartTable">
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
                <?php foreach ($cart as $index => $product_id): ?>
                    <?php
                    $product = $conn->query("SELECT * FROM product WHERE id = $index")->fetch_assoc();
                    $price = $product['price'];
                    $qty = $cart[$index];
                    $subtotal = $price * $qty;
                    $total += $subtotal;
                    ?>
                    <tr data-index="<?= $index ?>">
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td>
                            <input type="number" name="quantities[]" class="qty" value="<?= $qty ?>" min="1" required>
                            <input type="hidden" name="product_ids[]" value="<?= $index ?>">
                        </td>
                        <td class="price" data-price="<?= $price ?>">Rs. <?= number_format($price, 2) ?></td>
                        <td class="subtotal">Rs. <?= number_format($subtotal, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td id="totalCell"><strong>Rs. <?= number_format($total, 2) ?></strong></td>
                </tr>
            </table>
                </br>
                </br>
            <div style="text-align: center;">
    <button type="submit" name="place_order" style="padding: 10px 20px; background-color:rgb(172, 135, 82); border: none; font-size: 16px; cursor: pointer; border-radius: 5px;">
        Place Order
    </button>
</div>

            <div style="text-align:center;">
        <a href="view_cart.php" class="shop">← Back to cart</a>
    </div>
        </form>
    <?php endif; ?>
</div>

<script>
    const qtyInputs = document.querySelectorAll('.qty');
    const totalCell = document.getElementById('totalCell');

    function updateTotals() {
        let total = 0;
        document.querySelectorAll('#cartTable tr[data-index]').forEach(row => {
            const qtyInput = row.querySelector('.qty');
            const price = parseFloat(row.querySelector('.price').dataset.price);
            const subtotal = qtyInput.value * price;
            row.querySelector('.subtotal').innerText = 'Rs. ' + subtotal.toFixed(2);
            total += subtotal;
        });
        totalCell.innerHTML = '<strong>Rs. ' + total.toFixed(2) + '</strong>';
    }

    qtyInputs.forEach(input => {
        input.addEventListener('input', updateTotals);
    });
</script>
</body>
</html>
