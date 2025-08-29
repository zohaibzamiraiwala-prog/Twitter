<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
if (isset($_POST['content'])) {
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $tweet_id = (int)$_POST['tweet_id'];
    $user_id = $_SESSION['user_id'];
    $sql = "INSERT INTO comments (user_id, tweet_id, content) VALUES ($user_id, $tweet_id, '$content')";
    if ($conn->query($sql)) {
        echo "<script>window.location.href='index.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
