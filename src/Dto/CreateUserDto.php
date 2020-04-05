<?php

namespace App\Dto;

class CreateUserDto
{
    private $firstName;

    private $lastName;

    private $email;

    private $password;

    public function __construct(string $email, string $plainPassword, string $firstName, string $lastName)
    {
        $this->email = $email;
        $this->password = $plainPassword;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }


}