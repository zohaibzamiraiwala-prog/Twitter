<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    exit;
}
if (isset($_POST['user'])) {
    $followed_id = (int)$_POST['user'];
    $follower_id = $_SESSION['user_id'];
    if ($followed_id != $follower_id) {
        $sql = "INSERT IGNORE INTO follows (follower_id, followed_id) VALUES ($follower_id, $followed_id)";
        if ($conn->query($sql)) {
            echo "Followed";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Cannot follow yourself";
    }
}
?>
