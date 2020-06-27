<?php

namespace App\Dto;

use App\Utils\AbstractConverter;

class CreateUserDto extends AbstractConverter
{
    private $firstName;

    private $lastName;

    private $email;

    private $password;

    private $countOfPage;

    public function getCountOfPage(): int
    {
        return $this->countOfPage;
    }

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