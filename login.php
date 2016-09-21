<?php

require_once 'lib/common.php';

// We need to test for a minimum version of PHP, because earlier versions have bugs that affect security
if (version_compare(PHP_VERSION, '5.3.7') < 0) {
    throw new Exception("This system needs PHP 5.5 or later");
}

session_start();

// If we're already logged in, go back home
if (isLoggedIn()) {
    redirectAndExit('index.php');
}

// Handle the form posting
$email = '';
if ($_POST) {
    // Init database
    $pdo = getPDO();

    // We redirect only if the password is correct
    $email = $_POST['email'];
    $ok = tryLogin($pdo, $email, $_POST['password']);

    if ($ok) {
        login($email);
        redirectAndExit('index.php');
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>A blog application | Login</title>
    <?php require 'templates/head.php' ?>
</head>
<body>
    <?php require 'templates/title.php'; ?>

    <?php // If we have a email, then the user got something wrong, so lets have an error ?>
    <?php if ($email): ?>
        <div class="error box">
            The email or password is incorrect, try again
        </div>
    <?php endif ?>

    <p>Login here:</p>

    <form class="user-form" method="post">
        <div>
            <label for="email">Email:</label>
            <input id="email" type="text" name="email" value="<?php echo htmlEscape($email); ?>">
        </div>
        <div>
            <label for="password">Password:</label>
            <input id="password" type="password" name="password">
        </div>
        <input type="submit" name="submit" value="Login">
    </form>

</body>
</html>
