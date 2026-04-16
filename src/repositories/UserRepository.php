<?php

require_once __DIR__ . '/../models/User.php';

class UserRepository
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM utente ORDER BY idUtente ASC");
        $rows = $stmt->fetchAll();

        return array_map([$this, 'mapRowToUser'], $rows);
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM utente WHERE idUtente = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? $this->mapRowToUser($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM utente WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        return $row ? $this->mapRowToUser($row) : null;
    }

    public function create(User $user): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO utente (nomeUtente, password, email, ruolo, creazioneAccount)
            VALUES (?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $user->name,
            $user->passwordHash,
            $user->email,
            $user->role,

        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(User $user): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE utente
            SET nomeUtente = ?, password = ?, email = ?, ruolo = ?, creazioneAccount = ?
            WHERE idUtente = ?
        ");

        return $stmt->execute([
            $user->name,
            $user->passwordHash,
            $user->email,
            $user->role,
            $user->id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM utente WHERE idUtente = ?");
        return $stmt->execute([$id]);
    }

    private function mapRowToUser(array $row): User
    {
        return new User(
            (int)$row['idUtente'],
            $row['nomeUtente'],
            $row['email'],
            $row['password'],
            $row['ruolo'],
            new DateTime($row['creazioneAccount'])
        );
    }
}