<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    exit;
}
if (isset($_POST['tweet_id'])) {
    $tweet_id = (int)$_POST['tweet_id'];
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM likes WHERE user_id=$user_id AND tweet_id=$tweet_id";
    if ($conn->query($sql)->num_rows == 0) {
        $sql = "INSERT INTO likes (user_id, tweet_id) VALUES ($user_id, $tweet_id)";
        if ($conn->query($sql)) {
            echo "Liked";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Already liked";
    }
}
?>
