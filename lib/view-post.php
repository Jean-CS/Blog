<?php

function handleAddComment(PDO $pdo, $postId, array $commentData) {
    $errors = addCommentToPost(
        $pdo,
        $postId,
        $commentData
    );

    // If there are no errors, redirect back to self and redisplay
    if (!$errors) {
        redirectAndExit('view-post.php?post_id=' . $postId);
    }

    return $errors;
}

function deleteComment(PDO $pdo, $postId, $commentId) {
    // The comment id on its own would suffice, but post_id is a nice extra safety check
    $sql = "
        DELETE FROM
            comment
        WHERE
            post_id = :post_id
            AND id = :comment_id
    ";

    $stmt = $pdo->prepare($sql);
    if ($stmt === false) {
        throw new Exception('There was a problem preparing this query');
    }

    $result = $stmt->execute(
        array(
            'post_id' => $postId,
            'comment_id' => $commentId,
        )
    );

    return $result !== false;
}

/**
 * Retrieves a single post
 * @param  PDO    $pdo
 * @param  integer $postId
 * @throws Exception
 */
function getPostRow(PDO $pdo, $postId) {

    $stmt = $pdo->prepare('
        SELECT
            title, created_at, body
        FROM
            post
        WHERE
            id = :id'
    );

    if ($stmt === false) {
        throw new Exception('There was a problem preparing this query');
    }

    $result = $stmt->execute([
        'id' => $postId,
    ]);

    if ($result === false) {
        throw new Exception('There was a problem preparing this query');
    }

    // Get a row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row;
}


function addcommentToPost(PDO $pdo, $postId, array $commentData) {
    $errors = array();

    // Do some validation
    if (empty($commentData['name'])) {
        $errors['name'] = 'A name is required';
    }
    if (empty($commentData['text'])) {
        $errors['text'] = 'A comment is required';
    }

    // If we are error free, try writing the comment
    if (!$errors) {
        $sql = '
            INSERT INTO
                comment
            (name, website, text, created_at, post_id)
            VALUES(:name, :website, :text, :created_at, :post_id)
        ';

        $stmt = $pdo->prepare($sql);

        if ($stmt === false) {
            throw new Exception('Cannot prepare statement to insert comment');
        }

        $result = $stmt->execute(
            array_merge(
                $commentData,
                array(
                    'post_id' => $postId,
                    'created_at' => getSqlDateForNow(),
                )
            )
        );

        if ($result === false) {
            // @todo This render a database-level message to the user, fix This
            $errorInfo = $pdo->errorInfo();
            if ($errorInfo) {
                $errors[] = $errorInfo[2];
            }
        }

        return $errors;
    }

}

?>
