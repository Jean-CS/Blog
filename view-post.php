<?php

// Work out the path to the database, so SQLite/PDO can connect
$root = __DIR__;
$db = $root . '/data/data.sqlite';
$dsn = 'sqlite:' . $db;

// Get the post ID
if (isset($_GET['post_id'])) {
    $postId = $_GET['post_id'];
} else {
    // So we always have a post Id var defined
    $postId = 0;
}

// Connect to the database, run a query, handle errors
$pdo = new PDO($dsn);
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

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html">

        <title>
            A blog application |
            <?php echo htmlspecialchars($row['title'], ENT_HTML5, 'UTF-8'); ?>
        </title>

    </head>
    <body>

        <h1>Blog title</h1>
        <p>This paragraph summarises what the blog is about.</p>

        <h2>
            <?php echo htmlspecialchars($row['title'], ENT_HTML5, 'UTF-8'); ?>
        </h2>

        <div>
            <?php echo $row['created_at']; ?>
        </div>

        <p>
            <?php echo htmlspecialchars($row['body'], ENT_HTML5, 'UTF-8'); ?>

        </p>
    </body>
</html>
