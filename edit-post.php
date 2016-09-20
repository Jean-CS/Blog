<?php

require_once 'lib/common.php';

session_start();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>A blog application | New post</title>
    <?php require 'templates/head.php' ?>
</head>
<body>
    <?php require 'templates/title.php' ?>

    <form class="post-form user-form" method="post">
        <div>
            <label for="post-title">Title:</label>
            <input id="post-title" type="text" name="post-title">
        </div>
        <div>
            <label for="post-body">Body:</label>
            <textarea id="post-body" name="post-body" rows="12" cols="70"></textarea>
        </div>
        <div>
            <input type="submit" value="Save post">
        </div>
    </form>
</body>
</html>
