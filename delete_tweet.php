<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
$id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];
$sql = "DELETE FROM tweets WHERE id=$id AND user_id=$user_id";
if ($conn->query($sql)) {
    echo "<script>window.location.href='index.php';</script>";
} else {
    echo "Error: " . $conn->error;
}
?>
