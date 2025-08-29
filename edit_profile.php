<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id=$user_id";
$user = $conn->query($sql)->fetch_assoc();
if (!$user) {
    session_destroy();
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
 
if (isset($_POST['submit'])) {
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $profile_pic = $user['profile_pic'];
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target = $target_dir . time() . '_' . basename($_FILES['profile_pic']['name']);
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {
            $profile_pic = $target;
        }
    }
    $sql = "UPDATE users SET bio='$bio', profile_pic='$profile_pic' WHERE id=$user_id";
    if ($conn->query($sql)) {
        echo "<script>window.location.href='profile.php?user=" . htmlspecialchars($user['username']) . "';</script>";
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Twitter Clone</title>
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
        input[type="file"] {
            margin: 15px 0;
            color: #ffffff;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #38444d;
            background: #253341;
            color: #ffffff;
            border-radius: 4px;
            font-size: 16px;
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
        <h2>Edit Profile</h2>
        <form method="post" enctype="multipart/form-data">
            <label for="profile_pic" style="color: #8899a6;">Profile Picture:</label>
            <input type="file" name="profile_pic" id="profile_pic">
            <label for="bio" style="color: #8899a6;">Bio:</label>
            <textarea name="bio" rows="5"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
            <button type="submit" name="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>
