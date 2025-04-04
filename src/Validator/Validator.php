<?php

namespace App\Validator;

class Validator
{
    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function password(string $password): bool
    {
        return strlen($password) >= 6;
    }

    public static function notEmpty(string $value): bool
    {
        return trim($value) !== '';
    }
    public static function date(string $date): bool
    {
    $d = \DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
    }
}
