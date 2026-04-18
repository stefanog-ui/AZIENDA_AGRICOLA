<?php

session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../src/repositories/UserRepository.php';
require_once __DIR__ . '/../src/services/AuthService.php';

$email = '';
$emailError = '';
$passwordError = '';
$generalError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Email validation with RegExp
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = 'Please enter a valid email address.';
    }

    if ($password === '') {
        $passwordError = 'Please enter your password.';
    }

    if ($emailError === '' && $passwordError === '') {
        try {
            $pdo = Database::getConnection();
            $userRepository = new UserRepository($pdo);
            $authService = new AuthService($userRepository);

            $user = $authService->login($email, $password);

            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_role'] = $user->role;

            header('Location: home.php');
            exit;
        } catch (Exception $e) {
            $generalError = 'Invalid email or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
<div class="login-container">
    <h1>Login</h1>

    <?php if ($generalError !== ''): ?>
        <div class="general-error"><?php echo htmlspecialchars($generalError); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email</label>
            <input
                id="email"
                name="email"
                type="text"
                value="<?php echo htmlspecialchars($email); ?>"
                class="<?php echo $emailError !== '' ? 'error' : ''; ?>"
            >
            <?php if ($emailError !== ''): ?>
                <div class="error-text"><?php echo htmlspecialchars($emailError); ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                id="password"
                name="password"
                type="password"
                class="<?php echo $passwordError !== '' ? 'error' : ''; ?>"
            >
            <?php if ($passwordError !== ''): ?>
                <div class="error-text"><?php echo htmlspecialchars($passwordError); ?></div>
            <?php endif; ?>
        </div>

        <button type="submit">Log in</button>
    </form>
</div>
</body>
</html>