<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
$id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM tweets WHERE id=$id AND user_id=$user_id";
$tweet = $conn->query($sql)->fetch_assoc();
if (!$tweet) {
    echo "Tweet not found or not yours";
    exit;
}
if (isset($_POST['submit'])) {
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $sql = "UPDATE tweets SET content='$content' WHERE id=$id";
    if ($conn->query($sql)) {
        echo "<script>window.location.href='index.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tweet - Twitter Clone</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #15202b;
            color: #ffffff;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #192734;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }
        h2 {
            text-align: center;
            color: #1da1f2;
            margin-bottom: 20px;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #38444d;
            background: #253341;
            color: #ffffff;
            border-radius: 4px;
            font-size: 18px;
            resize: vertical;
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
            transition: background 0.2s;
        }
        button:hover {
            background-color: #0c85d0;
        }
        @media (max-width: 600px) {
            .container {
                padding: 15px;
                margin: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Tweet</h2>
        <form method="post">
            <textarea name="content" rows="5" maxlength="280" required><?php echo htmlspecialchars($tweet['content']); ?></textarea>
            <button type="submit" name="submit">Update Tweet</button>
        </form>
    </div>
</body>
</html>
