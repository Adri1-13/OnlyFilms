<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\auth;


class User {
    private int $id;
    private string $firstname;
    private string $name;
    private string $mail;
    private string $passwd;
    private int $role;

    public function __construct(int $id, string $firstname, string $name, string $mail, string $passwd, int $role) {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->name = $name;
        $this->mail = $mail;
        $this->passwd = $passwd;
        $this->role = $role;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getMail(): string
    {
        return $this->mail;
    }

    public function setMail(string $mail): void
    {
        $this->mail = $mail;
    }

    public function getPasswd(): string
    {
        return $this->passwd;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }



    public function setPasswd(string $passwd): void
    {
        $this->passwd = $passwd;
    }

    public function getRole(): int
    {
        return $this->role;
    }

    public function setRole(int $role): void
    {
        $this->role = $role;
    }



}