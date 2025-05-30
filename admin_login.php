<?php
session_start();
$conn = new mysqli("localhost", "root", "", "coffee_shop");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($db_username, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            if ($role === 'admin') {
                $_SESSION['username'] = $db_username;
                $_SESSION['role'] = $role;
                header("Location: admin_dashboard.php"); // Redirect to Admin Dashboard
                exit;
            } else {
                $error = "Access denied. Not an admin.";
            }
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Admin not found.";
    }

    $stmt->close();
}
$conn->close();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Tridrax Coffee Shop</title>
    <link rel="stylesheet" href="../login/style.css">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', sans-serif;
    }

    body {
        background: url(../img/log.png) no-repeat center center/cover;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        padding: 20px;
    }

    .login-container {
        background-color: rgb(226, 205, 173);
        padding: 40px 30px;
        border-radius: 15px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 400px;
        text-align: center;
    }

    .login-container h2 {
        margin-bottom: 25px;
        color: #333;
    }

    .login-container input {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 8px;
        transition: border-color 0.3s ease;
    }

    .login-container input:focus {
        border-color: #8b4513;
        outline: none;
    }

    .login-container button {
        width: 100%;
        padding: 12px;
        background-color: #8b4513;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .login-container button:hover {
        background-color: rgb(63, 35, 23);
    }

    .login-container p {
        margin-top: 10px;
        color: red;
    }

    .login-container a {
        display: inline-block;
        margin-top: 15px;
        color: rgb(45, 26, 11);
        text-decoration: none;
        font-weight: bold;
    }

    .login-container a:hover {
        text-decoration: underline;
    }

    /* Mobile responsiveness */
    @media (max-width: 480px) {
        .login-container {
            padding: 30px 20px;
        }

        .login-container h2 {
            font-size: 24px;
        }

        .login-container button {
            font-size: 14px;
        }
    }
</style>

</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <p><?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <a href="../index.php">Back to Home</a>
    </div>
</body>
</html>
