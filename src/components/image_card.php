<?php

require_once __DIR__ . '/../models/Like.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/User.php';

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$likesCount = Like::getLikesCount((int)$img['id']);
$comments = Comment::getComments((int)$img['id']);
$postedBy = User::findById((int)$img['user_id']);
?>
<div class="card mb-4">
    <img src="<?php echo htmlspecialchars($img['image_path'], ENT_QUOTES, 'UTF-8'); ?>" 
         alt="Image" class="card-img-top">
    <div class="card-body">
        <p class="mb-2 text-muted small">
            Posted by: <?php echo htmlspecialchars($postedBy['username'], ENT_QUOTES, 'UTF-8'); ?>
        </p>
        <div class="d-flex justify-content-between align-items-center">
            <?php if(isset($loggedIn) && $loggedIn): ?>
                <form action="../controllers/like_image.php" method="POST" class="me-2">
                    <input type="hidden" name="csrf_token" 
                           value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="image_id" 
                           value="<?php echo htmlspecialchars($img['id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="btn btn-primary btn-sm">Like</button>
                </form>
                <button class="btn btn-link btn-sm text-primary comment-toggle" 
                        data-image-id="<?php echo htmlspecialchars($img['id'], ENT_QUOTES, 'UTF-8'); ?>">
                    Comment
                </button>
            <?php else: ?>
                <span class="text-muted small">Log in to like or comment</span>
            <?php endif; ?>
        </div>
        <p class="mt-2 small">
            <?php echo htmlspecialchars($likesCount, ENT_QUOTES, 'UTF-8'); ?> 
            <?php echo ($likesCount == 1) ? 'like' : 'likes'; ?>
        </p>
        <?php if(isset($loggedIn) && $loggedIn): ?>
            <div id="comments-<?php echo htmlspecialchars($img['id'], ENT_QUOTES, 'UTF-8'); ?>" 
                 class="mt-3 d-none">
                <form action="../controllers/comment_image.php" method="POST" class="comment-form">
                    <input type="hidden" name="csrf_token" 
                           value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="image_id" 
                           value="<?php echo htmlspecialchars($img['id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="mb-2">
                        <textarea name="comment" class="form-control" 
                                maxlength="500" required 
                                placeholder="Write your comment here..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm">Submit</button>
                </form>
            </div>
        <?php endif; ?>
        <div class="mt-3">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="border rounded p-2 mb-2">
                        <strong><?php echo htmlspecialchars($comment['username'], ENT_QUOTES, 'UTF-8'); ?>:</strong>
                        <?php echo htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <span class="text-muted small">No comments yet.</span>
            <?php endif; ?>
        </div>
    </div>
</div>
