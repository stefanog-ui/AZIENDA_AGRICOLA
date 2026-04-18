<?php

session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../src/repositories/UserRepository.php';
require_once __DIR__ . '/../src/services/UserService.php';
require_once __DIR__ . '/../src/models/User.php';

$name = '';
$email = '';

$nameError = '';
$emailError = '';
$passwordError = '';
$confirmPasswordError = '';
$generalError = '';
$role = 'Cliente';
$roleError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'Cliente';

    if ($name === '') {
        $nameError = 'Please enter your name.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = 'Please enter a valid email address.';
    }

    if (strlen($password) < 6) {
        $passwordError = 'Password must be at least 6 characters.';
    }

    if ($confirmPassword === '') {
        $confirmPasswordError = 'Please confirm your password.';
    } elseif ($password !== $confirmPassword) {
        $confirmPasswordError = 'Passwords do not match.';
    }

    $allowedRoles = ['Cliente', 'Produttore'];

    if (!in_array($role, $allowedRoles, true)) {
        $roleError = 'Please select a valid role.';
    }

    if (
        $nameError === '' &&
        $emailError === '' &&
        $passwordError === '' &&
        $confirmPasswordError === '' &&
        $roleError === ''
    ) {
        try {
            $pdo = Database::getConnection();
            $userRepository = new UserRepository($pdo);
            $userService = new UserService($userRepository);

            $userId = $userService->register(
                $name,
                $email,
                $password,
                $role,
            );

            $_SESSION['registration_success'] = 'Account created successfully. You can now log in.';
            header('Location: login.php');
            exit;
        } catch (Exception $e) {
            $generalError = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
<div class="register-container">
    <h1>Create account</h1>

    <?php if ($generalError !== ''): ?>
        <div class="general-error"><?php echo htmlspecialchars($generalError); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="name">Username</label>
            <input
                id="name"
                name="name"
                type="text"
                value="<?php echo htmlspecialchars($name); ?>"
                class="<?php echo $nameError !== '' ? 'error' : ''; ?>"
            >
            <?php if ($nameError !== ''): ?>
                <div class="error-text"><?php echo htmlspecialchars($nameError); ?></div>
            <?php endif; ?>
        </div>

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

        <div class="form-group">
            <label for="confirm_password">Confirm password</label>
            <input
                id="confirm_password"
                name="confirm_password"
                type="password"
                class="<?php echo $confirmPasswordError !== '' ? 'error' : ''; ?>"
            >
            <?php if ($confirmPasswordError !== ''): ?>
                <div class="error-text"><?php echo htmlspecialchars($confirmPasswordError); ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Role</label>

            <div class="radio-group <?php echo $roleError !== '' ? 'error-group' : ''; ?>">
                <label class="radio-option">
                    <input
                        type="radio"
                        name="role"
                        value="Cliente"
                        <?php echo $role === 'Cliente' ? 'checked' : ''; ?>
                    >
                    Cliente
                </label>

                <label class="radio-option">
                    <input
                        type="radio"
                        name="role"
                        value="Produttore"
                        <?php echo $role === 'Produttore' ? 'checked' : ''; ?>
                    >
                    Produttore
                </label>
            </div>

            <?php if ($roleError !== ''): ?>
                <div class="error-text"><?php echo htmlspecialchars($roleError); ?></div>
            <?php endif; ?>
        </div>

        <button type="submit">Sign up</button>
    </form>

    <p class="bottom-text">
        Already have an account?
        <a href="login.php">Log in</a>
    </p>
</div>
</body>
</html>