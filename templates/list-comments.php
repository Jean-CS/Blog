<?php
/**
 * @var $pdo PDO
 * @var $postId integer
 */
?>

<div class="comment-list">
    <h3><?php echo countCommentsForPost($pdo, $postId) ?> comments</h3>

    <?php foreach (getCommentsForPost($pdo, $postId) as $comment): ?>
        <div class="comment">
            Comment from
            <?php echo htmlEscape($comment['name']) ?>
            on
            <?php echo convertSqlDate($comment['created_at']) ?>
        </div>
        <div class="comment-body">
            <?php // This is already escaped ?>
            <?php echo convertNewLinesToParagraphs($comment['text']) ?>
        </div>
    <?php endforeach ?>
</div>
