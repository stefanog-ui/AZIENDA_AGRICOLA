<?php

require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../models/User.php';

class UserService
{
    public function __construct(private UserRepository $userRepository) {}

    public function register(
        string $username,
        string $email,
        string $plainPassword,
        string $role = 'Cliente'
    ): int {
        $existingUser = $this->userRepository->findByEmail($email);

        if ($existingUser !== null) {
            throw new Exception('Email already registered.');
        }

        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        $user = new User(
            null,
            $username,
            $email,
            $hashedPassword,
            $role,
            new DateTime()
        );

        return $this->userRepository->create($user);
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    public function updateUser(User $user): bool
    {
        return $this->userRepository->update($user);
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }
}