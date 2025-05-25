<?php
session_start();
require 'config.php';
include 'includes/header.php';

if (isset($_SESSION['user_id'])) {
    session_regenerate_id();
}

function getPosts($conn) {
    $stmt = $conn->prepare("SELECT posts.*, users.username,
        (SELECT COUNT(*) FROM votes WHERE votes.post_id = posts.id AND vote_type = 'up') - 
        (SELECT COUNT(*) FROM votes WHERE votes.post_id = posts.id AND vote_type = 'down') AS score
        FROM posts JOIN users ON posts.user_id = users.id
        ORDER BY score DESC");
    $stmt->execute();
    return $stmt->get_result();
}

function getComments($conn, $postId) {
    $stmt = $conn->prepare("SELECT comments.*, users.username FROM comments 
                            JOIN users ON comments.user_id = users.id
                            WHERE post_id = ? ORDER BY created_at ASC");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    return $stmt->get_result();
}

function getReplies($conn, $commentId) {
    $stmt = $conn->prepare("SELECT replies.*, users.username FROM replies
                            JOIN users ON replies.user_id = users.id
                            WHERE replies.comment_id = ? ORDER BY replies.created_at ASC");
    $stmt->bind_param("i", $commentId);
    $stmt->execute();
    return $stmt->get_result();
}

function renderReplies($replies, $depth = 0) {
    while ($reply = $replies->fetch_assoc()): ?>
        <div class="reply" style="margin-left: <?= min($depth * 20, 60) ?>px;">
            <div class="meta">By <?= htmlspecialchars($reply['username']) ?> on <?= $reply['created_at'] ?></div>
            <div class="body"><?= nl2br(htmlspecialchars($reply['reply_text'])) ?></div>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $reply['user_id']): ?>
                <div align = right class="reply-controls">
                    <a href="edit_reply.php?id=<?= $reply['id'] ?>&comment_id=<?= $reply['comment_id'] ?>" class="btn btn-edit">Edit Reply</a>
                    <a href="delete_reply.php?id=<?= $reply['id'] ?>&comment_id=<?= $reply['comment_id'] ?>" class="btn btn-delete-reply" onclick="return confirm('Are you sure you want to delete this reply?');">Delete Reply</a>
                </div>
            <?php endif; ?>
        </div>
        <?php
        $nestedReplies = getReplies($GLOBALS['conn'], $reply['id']);
        renderReplies($nestedReplies, $depth + 1);
    endwhile;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sweesh</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="auth-buttons">
<?php if (!isset($_SESSION['user_id'])): ?>
    <a class="btn" href="login.php">Login</a>
    <a class="btn" href="register.php">Register</a>
<?php else: ?>
    <a class="btn" href="logout.php">Logout</a>
<?php endif; ?>
</div>

<?php if (isset($_SESSION['user_id'])): ?>
    <div class="create-post" >
        <a href="create_post.php" class="btn-create-post">Create Post</a>
    </div>
<?php endif; ?>

<?php
$posts = getPosts($conn);
while ($post = $posts->fetch_assoc()):
    $postId = $post['id'];
?>
<div class="post" data-id="<?= $postId ?>">
    <div class="post-controls">
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
            <div align = right>
            <a class="btn btn-edit" href="edit_post.php?id=<?= $postId ?>">Edit</a>
            <a class="btn btn-delete" href="delete_post.php?id=<?= $postId ?>" onclick="return confirm('Delete this post and all its comments?');">Delete</a>
            </div>
        <?php endif; ?>
    </div>

    <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
    <p class="post-meta">By <?= htmlspecialchars($post['username']) ?></p>
    <div id="content-<?= $postId ?>" class="post-content"><?= nl2br(htmlspecialchars($post['content'])) ?></div>

    <?php if (strlen($post['content']) > 300): ?>
        <button id="toggle-<?= $postId ?>" class="toggle-btn" onclick="toggleContent(<?= $postId ?>)">Show More</button>
    <?php endif; ?>

    <p>Score: <span class="score"><?= $post['score'] ?></span></p>

    <div class="post-actions">
        <button class="btn btn-up" onclick="vote(<?= $postId ?>)">Like</button>
    </div>

    <div id="comments-<?= $postId ?>" class="comments" style="display: none;">
        <h4>Comments</h4>
        <?php
        $comments = getComments($conn, $postId);
        while ($comment = $comments->fetch_assoc()):
            $commentId = $comment['id'];
        ?>
            <div class="comment" data-comment-id="<?= $commentId ?>">
                <div class="meta">By <?= htmlspecialchars($comment['username']) ?> on <?= $comment['created_at'] ?></div>
                <div class="body"><?= nl2br(htmlspecialchars($comment['comment_text'])) ?></div>
                <button class="toggle-btn" onclick="toggleReplyForm(<?= $commentId ?>)">Reply</button>

                <div id="reply-form-<?= $commentId ?>" class="comment-reply-form" style="display: none;">
                    <form method="POST" action="add_reply.php">
                        <input type="hidden" name="comment_id" value="<?= $commentId ?>">
                        <textarea name="reply_text" placeholder="Write a reply..." required></textarea>
                        <input type="submit" value="Post Reply">
                    </form>
                </div>

                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                    <div align = right class="comment-controls">
                        <a class="btn btn-edit" href="edit_comment.php?id=<?= $commentId ?>">Edit Comment</a>
                        <a class="btn btn-delete" href="delete_comment.php?id=<?= $commentId ?>" onclick="return confirm('Delete this comment?');">Delete Comment</a>
                    </div>
                <?php endif; ?>

                <div class="replies">
                    <?php
                    $replies = getReplies($conn, $commentId);
                    renderReplies($replies);
                    ?>
                </div>
            </div>
        <?php endwhile; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" action="add_comment.php">
                <input type="hidden" name="post_id" value="<?= $postId ?>">
                <textarea name="comment_text" placeholder="Write a comment..." required></textarea>
                <input type="submit" value="Post Comment">
            </form>
        <?php endif; ?>
    </div>

    <button class="toggle-btn" id="toggle-comments-<?= $postId ?>" onclick="toggleComments(<?= $postId ?>)">Show Comments</button>
</div>
<?php endwhile; ?>

<script>
function toggleContent(id) {
    const content = document.getElementById('content-' + id);
    const btn = document.getElementById('toggle-' + id);
    content.classList.toggle('expanded');
    btn.textContent = content.classList.contains('expanded') ? 'Show Less' : 'Show More';
}

function toggleComments(postId) {
    const comments = document.getElementById('comments-' + postId);
    const btn = document.getElementById('toggle-comments-' + postId);
    if (comments.style.display === 'none' || comments.style.display === '') {
        comments.style.display = 'block';
        btn.textContent = 'Hide Comments';
    } else {
        comments.style.display = 'none';
        btn.textContent = 'Show Comments';
    }
}

function toggleReplyForm(commentId) {
    const form = document.getElementById('reply-form-' + commentId);
    form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
}

function vote(postId) {
    const button = document.querySelector(`.post[data-id="${postId}"] .btn-up`);
    button.disabled = true;

    fetch('vote.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${postId}&type=up`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const scoreSpan = document.querySelector(`.post[data-id="${postId}"] .score`);
            if (scoreSpan) {
                scoreSpan.textContent = data.score;
            }
        } else {
            alert(data.message || 'Failed to vote.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred.');
    })
    .finally(() => {
        setTimeout(() => button.disabled = false, 800);
    });
}
</script>

<footer>
  <div align = center class="footer-content">
    <p>&copy; 2025 Sweesh. All rights reserved.</p>
  </div>
</footer>


</body>
</html>
