<?php
include 'config.php';
 
$error = '';
if (isset($_POST['submit'])) {
    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $password = trim($_POST['password']);
    // Validate username: alphanumeric and underscores only, 3-50 chars
    if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
        $error = "Username must be 3-50 characters and contain only letters, numbers, or underscores.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if username already exists (case-insensitive)
        $sql = "SELECT id FROM users WHERE LOWER(username)=LOWER('$username')";
        $result = $conn->query($sql);
        if ($result === false) {
            $error = "Database error: " . $conn->error;
            file_put_contents('debug.log', "Register query error: " . $conn->error . "\n", FILE_APPEND);
        } elseif ($result->num_rows > 0) {
            $error = "Username '$username' is already taken. Please choose another.";
        } else {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password) VALUES ('$username', '$pass_hash')";
            if ($conn->query($sql)) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['username'] = $username;
                file_put_contents('debug.log', "Registration successful - Username: $username\n", FILE_APPEND);
                echo "<script>window.location.href='index.php';</script>";
                exit;
            } else {
                $error = "Registration failed: " . $conn->error;
                file_put_contents('debug.log', "Registration failed - Username: $username, Error: " . $conn->error . "\n", FILE_APPEND);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Twitter Clone</title>
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
        <h2>Register</h2>
        <?php if ($error) { ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="submit">Register</button>
        </form>
        <p style="text-align: center; margin-top: 10px;">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </div>
</body>
</html>
