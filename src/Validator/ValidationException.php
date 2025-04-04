<?php

namespace App\Validator;

use Exception;

class ValidationException extends Exception
{
    public array $errors;

    public function __construct(array $errors)
    {
        parent::__construct("Ошибка валидации");
        $this->errors = $errors;
    }
}
