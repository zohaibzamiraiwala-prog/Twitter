<?php
include 'config.php';
 
$error = '';
if (isset($_POST['submit'])) {
    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $password = trim($_POST['password']);
    $sql = "SELECT * FROM users WHERE LOWER(username)=LOWER('$username')";
    $result = $conn->query($sql);
    if ($result === false) {
        $error = "Database error: " . $conn->error;
        file_put_contents('debug.log', "Query error: " . $conn->error . "\n", FILE_APPEND);
    } elseif ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        file_put_contents('debug.log', "Login attempt - Username: $username, Password verify: " . (password_verify($password, $user['password']) ? 'Success' : 'Failed') . "\n", FILE_APPEND);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo "<script>window.location.href='index.php';</script>";
            exit;
        } else {
            $error = "Incorrect password. Please try again.";
        }
    } else {
        $error = "Username not found. Please register.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Twitter Clone</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #15202b;
            color: #ffffff;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: #192734;
            padding: 30px;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }
        h2 {
            text-align: center;
            color: #1da1f2;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #38444d;
            border-radius: 4px;
            background: #253341;
            color: #ffffff;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            background-color: #1da1f2;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 9999px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            font-size: 16px;
            transition: background-color 0.2s;
        }
        button:hover {
            background-color: #0c85d0;
        }
        .error {
            color: #e0245e;
            text-align: center;
            margin-bottom: 10px;
        }
        a {
            color: #1da1f2;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            .container {
                padding: 20px;
                margin: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if ($error) { ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="submit">Login</button>
        </form>
        <p style="text-align: center; margin-top: 10px;">
            Don't have an account? <a href="register.php">Register</a>
        </p>
    </div>
</body>
</html>
