<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
$username = mysqli_real_escape_string($conn, $_GET['user'] ?? $_SESSION['username']);
$sql = "SELECT * FROM users WHERE username='$username'";
$user = $conn->query($sql)->fetch_assoc();
if (!$user) {
    echo "User not found";
    exit;
}
$profile_id = $user['id'];
$is_own = $profile_id == $_SESSION['user_id'];
 
$sql = "SELECT COUNT(*) as c FROM follows WHERE followed_id=$profile_id";
$followers = $conn->query($sql)->fetch_assoc()['c'];
$sql = "SELECT COUNT(*) as c FROM follows WHERE follower_id=$profile_id";
$following = $conn->query($sql)->fetch_assoc()['c'];
 
$following_status = false;
if (!$is_own) {
    $sql = "SELECT * FROM follows WHERE follower_id={$_SESSION['user_id']} AND followed_id=$profile_id";
    $following_status = $conn->query($sql)->num_rows > 0;
}
 
$sql = "SELECT t.*, u.username, u.profile_pic FROM tweets t JOIN users u ON t.user_id = u.id WHERE t.user_id = $profile_id ORDER BY t.created_at DESC LIMIT 50";
$tweets = $conn->query($sql);
 
$current_username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@<?php echo htmlspecialchars($username); ?> - Twitter Clone</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #15202b;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .header {
            background: #192734;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #38444d;
        }
        .header a {
            color: #1da1f2;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
            font-size: 16px;
        }
        .header a:hover {
            text-decoration: underline;
        }
        .profile-header {
            padding: 20px;
            border-bottom: 1px solid #38444d;
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }
        .profile-pic-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid #192734;
            object-fit: cover;
        }
        .bio {
            color: #8899a6;
            margin: 15px 0;
            font-size: 15px;
        }
        .stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            color: #8899a6;
            font-size: 15px;
        }
        .follow-btn, .unfollow-btn, .edit-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 9999px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            transition: background 0.2s;
            margin-top: 10px;
            display: inline-block;
        }
        .follow-btn {
            background: #1da1f2;
            color: white;
        }
        .follow-btn:hover {
            background: #0c85d0;
        }
        .unfollow-btn {
            background: #e0245e;
            color: white;
        }
        .unfollow-btn:hover {
            background: #c51b4a;
        }
        .edit-btn {
            background: #8899a6;
            color: white;
            text-decoration: none;
        }
        .edit-btn:hover {
            background: #657786;
        }
        .feed {
            max-width: 600px;
            margin: 0 auto;
        }
        .tweet {
            border-bottom: 1px solid #38444d;
            padding: 15px;
            display: flex;
            transition: background 0.2s;
        }
        .tweet:hover {
            background: #1c2b3a;
        }
        .profile-pic {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }
        .tweet-content {
            flex: 1;
        }
        .username {
            font-weight: bold;
            color: #ffffff;
            text-decoration: none;
        }
        .username:hover {
            text-decoration: underline;
        }
        .timestamp {
            color: #8899a6;
            font-size: 14px;
            margin-left: 5px;
        }
        .interactions {
            display: flex;
            gap: 20px;
            color: #8899a6;
            font-size: 14px;
            margin-top: 10px;
        }
        .like, .comment-count {
            cursor: pointer;
            transition: color 0.2s;
        }
        .like:hover, .comment-count:hover {
            color: #1da1f2;
        }
        .comment-form {
            margin-top: 10px;
        }
        .comment-form input {
            width: 80%;
            padding: 8px;
            border: 1px solid #38444d;
            background: #253341;
            color: #ffffff;
            border-radius: 4px;
            font-size: 14px;
        }
        .comment-form button {
            background: #1da1f2;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 9999px;
            cursor: pointer;
            font-weight: bold;
        }
        .comment-form button:hover {
            background: #0c85d0;
        }
        .comment {
            margin-left: 58px;
            padding: 10px 0;
            border-top: 1px solid #38444d;
            font-size: 14px;
        }
        .comment .username {
            font-size: 14px;
        }
        a.edit-delete {
            color: #8899a6;
            text-decoration: none;
            margin-left: 10px;
            font-size: 14px;
        }
        a.edit-delete:hover {
            color: #1da1f2;
        }
        @media (max-width: 600px) {
            .feed, .profile-header {
                max-width: 100%;
                padding: 10px;
            }
            .profile-pic-large {
                width: 80px;
                height: 80px;
            }
            .stats {
                flex-direction: column;
                gap: 5px;
            }
            .profile-pic {
                width: 40px;
                height: 40px;
            }
            .comment-form input {
                width: 70%;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <span style="font-weight: bold; font-size: 18px;">Twitter Clone</span>
        <div>
            <a href="profile.php?user=<?php echo htmlspecialchars($current_username); ?>">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="profile-header">
        <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" class="profile-pic-large" alt="Profile Pic">
        <h2>@<?php echo htmlspecialchars($username); ?></h2>
        <p class="bio"><?php echo nl2br(htmlspecialchars($user['bio'] ?? '')); ?></p>
        <div class="stats">
            <span><strong><?php echo $followers; ?></strong> Followers</span>
            <span><strong><?php echo $following; ?></strong> Following</span>
        </div>
        <?php if ($is_own) { ?>
            <a href="edit_profile.php" class="edit-btn">Edit Profile</a>
        <?php } else { ?>
            <?php if ($following_status) { ?>
                <button class="unfollow-btn" onclick="unfollowUser(<?php echo $profile_id; ?>)">Unfollow</button>
            <?php } else { ?>
                <button class="follow-btn" onclick="followUser(<?php echo $profile_id; ?>)">Follow</button>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="feed">
        <?php while ($tweet = $tweets->fetch_assoc()) { 
            $sql = "SELECT COUNT(*) as c FROM likes WHERE tweet_id=" . $tweet['id'];
            $like_count = $conn->query($sql)->fetch_assoc()['c'];
            $sql = "SELECT COUNT(*) as c FROM comments WHERE tweet_id=" . $tweet['id'];
            $comment_count = $conn->query($sql)->fetch_assoc()['c'];
            $sql = "SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.tweet_id=" . $tweet['id'] . " ORDER BY c.created_at ASC";
            $comments_result = $conn->query($sql);
        ?>
        <div class="tweet">
            <img src="<?php echo htmlspecialchars($tweet['profile_pic']); ?>" class="profile-pic" alt="Profile Pic">
            <div class="tweet-content">
                <a href="profile.php?user=<?php echo htmlspecialchars($tweet['username']); ?>" class="username">@<?php echo htmlspecialchars($tweet['username']); ?></a>
                <span class="timestamp"><?php echo $tweet['created_at']; ?></span>
                <p><?php echo nl2br(htmlspecialchars($tweet['content'])); ?></p>
                <div class="interactions">
                    <span class="like" onclick="likeTweet(<?php echo $tweet['id']; ?>)">Like (<?php echo $like_count; ?>)</span>
                    <span class="comment-count">Comment (<?php echo $comment_count; ?>)</span>
                </div>
                <?php if ($tweet['user_id'] == $_SESSION['user_id']) { ?>
                    <a href="edit_tweet.php?id=<?php echo $tweet['id']; ?>" class="edit-delete">Edit</a>
                    <a href="delete_tweet.php?id=<?php echo $tweet['id']; ?>" class="edit-delete" onclick="return confirm('Delete this tweet?');">Delete</a>
                <?php } ?>
                <?php while ($comment = $comments_result->fetch_assoc()) { ?>
                    <div class="comment">
                        <a href="profile.php?user=<?php echo htmlspecialchars($comment['username']); ?>" class="username">@<?php echo htmlspecialchars($comment['username']); ?></a>
                        <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                    </div>
                <?php } ?>
                <form class="comment-form" method="post" action="comment.php">
                    <input type="hidden" name="tweet_id" value="<?php echo $tweet['id']; ?>">
                    <input type="text" name="content" placeholder="Reply to @<?php echo htmlspecialchars($tweet['username']); ?>" required>
                    <button type="submit">Reply</button>
                </form>
            </div>
        </div>
        <?php } ?>
    </div>
    <script>
        function followUser(user_id) {
            fetch('follow.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'user=' + user_id
            }).then(response => response.text())
              .then(() => location.reload())
              .catch(error => console.error('Error:', error));
        }
        function unfollowUser(user_id) {
            fetch('unfollow.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'user=' + user_id
            }).then(response => response.text())
              .then(() => location.reload())
              .catch(error => console.error('Error:', error));
        }
        function likeTweet(tweet_id) {
            fetch('like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'tweet_id=' + tweet_id
            }).then(response => response.text())
              .then(() => location.reload())
              .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
