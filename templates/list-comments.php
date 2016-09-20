<?php
/**
 * @var $pdo PDO
 * @var $postId integer
 */
?>

<form class="comment-list"
      action="view-post.php?action=delete-comment&amp;post_id=<?php echo $postId ?>&amp;"
      method="post"
>
    <h3><?php echo countCommentsForPost($pdo, $postId) ?> comments</h3>

    <?php foreach (getCommentsForPost($pdo, $postId) as $comment): ?>
        <div class="comment">
            Comment from
            <?php echo htmlEscape($comment['name']) ?>
            on
            <?php echo convertSqlDate($comment['created_at']) ?>
            <?php if (isLoggedIn()): ?>
                <input type="submit" name="delete-comment[<?php echo $comment['id'] ?>]" value="Delete">
            <?php endif ?>
        </div>
        <div class="comment-body">
            <?php // This is already escaped ?>
            <?php echo convertNewLinesToParagraphs($comment['text']) ?>
        </div>
    <?php endforeach ?>
</form>
