<?php

require_once __DIR__ . '/../repositories/UserRepository.php';

class AuthService
{
    public function __construct(private UserRepository $userRepository) {}

    public function login(string $email, string $plainPassword): User
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            throw new Exception('User not found.');
        }

        if (!password_verify($plainPassword, $user->passwordHash)) {
            throw new Exception('Invalid credentials.');
        }

        return $user;
    }
}