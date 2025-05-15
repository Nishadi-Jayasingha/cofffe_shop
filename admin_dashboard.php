<?php
session_start();

// Only admin users can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Tridrax Coffee Shop</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (Admin)</p>
        <a href="../index.php">← Back to Site</a> |
        <a href="../login/logout.php">Logout</a>
    </header>

    <main>
        <section class="dashboard-options">
            <a href="add_product.php" class="card">➕ Add New Product</a>
            <a href="view_orders.php" class="card">📦 View Orders</a>
            <a href="view_users.php" class="card">👥 Manage Users</a>
        </section>
    </main>
</body>
</html>
