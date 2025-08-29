<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    exit;
}
if (isset($_POST['user'])) {
    $followed_id = (int)$_POST['user'];
    $follower_id = $_SESSION['user_id'];
    $sql = "DELETE FROM follows WHERE follower_id=$follower_id AND followed_id=$followed_id";
    if ($conn->query($sql)) {
        echo "Unfollowed";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
