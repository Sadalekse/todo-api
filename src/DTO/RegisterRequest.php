<?php

namespace App\Dto;

use App\Validator\Validator;
use App\Validator\ValidationException;

class RegisterRequest
{
    public string $email;
    public string $password;

    public function __construct(array $data)
    {
        $this->email = trim($data['email'] ?? '');
        $this->password = trim($data['password'] ?? '');

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if (!Validator::email($this->email)) {
            $errors['email'] = 'Некорректный email';
        }

        if (!Validator::password($this->password)) {
            $errors['password'] = 'Пароль должен быть не менее 6 символов';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
