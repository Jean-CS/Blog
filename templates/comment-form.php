<?php
/**
 * @var $errors string
 * @var $commentData array
 */
 ?>

<?php // Report any errors in a bullet-point list ?>
<?php if ($errors): ?>
    <div class="error box comment-margin">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>

<h3>Add your comment</h3>

<form class="comment-form user-form"
      action="view-post.php?action=add-comment&amp;post_id=<?php echo $postId ?>"
      method="post"
>
    <div>
        <label for="comment-name">Name:</label>
        <input
            id="comment-name"
            type="text"
            name="comment-name"
            value="<?php echo htmlEscape($commentData['name']); ?>"
        >
    </div>
    <div>
        <label for="comment-website">Website:</label>
        <input
            id="comment-website"
            type="text"
            name="comment-website"
            value="<?php echo htmlEscape($commentData['website']); ?>"
        >
    </div>
    <div>
        <label for="comment-text">Comment:</label>
        <textarea
            id="comment-text"
            name="comment-text"
            rows="8"
            cols="70"
        ><?php echo htmlEscape($commentData['text']); ?></textarea>
    </div>

    <div>
        <input type="submit" value="Submit comment">
    </div>

</form>
