<?php
class User
{
    public ?int $id;
    public string $name;
    public string $email;
    public string $passwordHash;
    public string $role;
    public DateTime $createdAt;

    public function __construct(
        ?int $id,
        string $name,
        string $email,
        string $passwordHash,
        string $role,
        DateTime $createdAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->role = $role;
        $this->createdAt = $createdAt;
    }
}
