<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html">

    <title>A blog application</title>

</head>
<body>

    <h1>Blog title</h1>
    <p>This paragraph summarises what the blod is about.</p>

    <?php for ($postId = 1; $postId <= 3 ; $postId++): ?>
        <h2>Article <?php $postId ?> title</h2>
        <div>dd Mon YYYY</div>
        <p>A paragraph summarising article <?php $postId ?>.</p>
        <p><a href="">Read more...</a></p>
    <?php endfor ?>

</body>
</html>
