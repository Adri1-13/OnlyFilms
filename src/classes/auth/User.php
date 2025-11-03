<?php

declare(strict_types=1);

namespace iutnc\netvod\auth;

use iutnc\netvod\exception\InvalidPropertyNameException;

class User {
    private int $id;
    private string $email;
    private string $passwd;
    private int $role;

    public function __construct(int $id, string $email, string $passwd, int $role) {
        $this->id = $id;
        $this->email = $email;
        $this->passwd = $passwd;
        $this->role = $role;
    }

    public function __get(string $name) : string|int {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new InvalidPropertyNameException($name);
    }

}