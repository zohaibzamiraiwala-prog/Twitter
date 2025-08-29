<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
$user_id = $_SESSION['user_id'];
$sql = "SELECT username FROM users WHERE id=$user_id";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
    session_destroy();
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
$current_username = $result->fetch_assoc()['username'];
 
$followed = [$user_id];
$sql = "SELECT followed_id FROM follows WHERE follower_id=$user_id";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $followed[] = $row['followed_id'];
}
$followed_str = implode(',', $followed) ?: '0';
 
$sql = "SELECT t.*, u.username, u.profile_pic FROM tweets t JOIN users u ON t.user_id = u.id WHERE t.user_id IN ($followed_str) ORDER BY t.created_at DESC LIMIT 50";
$tweets = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Twitter Clone</title>
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
        .tweet-box {
            background: #192734;
            padding: 15px;
            border-bottom: 1px solid #38444d;
            max-width: 600px;
            margin: 0 auto;
        }
        .tweet-input {
            width: 100%;
            border: none;
            background: transparent;
            color: #ffffff;
            font-size: 18px;
            resize: none;
            padding: 10px;
        }
        .tweet-input:focus {
            outline: none;
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
        button[type="submit"] {
            background: #1da1f2;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 9999px;
            font-weight: bold;
            transition: background 0.2s;
        }
        button[type="submit"]:hover {
            background: #0c85d0;
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
            .feed, .tweet-box {
                max-width: 100%;
                padding: 10px;
            }
            .header {
                padding: 10px;
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
    <div class="tweet-box">
        <form method="post" action="post_tweet.php">
            <textarea name="content" class="tweet-input" placeholder="What's happening?" rows="3" maxlength="280" required></textarea>
            <button type="submit">Tweet</button>
        </form>
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
                <?php if ($tweet['user_id'] == $user_id) { ?>
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
        function likeTweet(tweet_id) {
            fetch('like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'tweet_id=' + tweet_id
            }).then(response => response.text())
              .then(() => location.reload())
              .catch(error => console.error('Error:', error));
        }
        setInterval(() => location.reload(), 10000);
    </script>
</body>
</html>
