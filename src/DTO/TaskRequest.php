<?php

namespace App\Dto;

use App\Validator\Validator;
use App\Validator\ValidationException;

class TaskRequest
{
    public string $title;
    public ?string $description;
    public string $status;
    public ?string $deadline;

    private const ALLOWED_STATUSES = ['в работе', 'завершено', 'дедлайн'];

    public function __construct(array $data)
    {
        $this->title = trim($data['title'] ?? '');
        $this->description = trim($data['description'] ?? '');
        $this->status = $data['status'] ?? 'в работе';
        $this->deadline = $data['deadline'] ?? null;

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if (!Validator::notEmpty($this->title)) {
            $errors['title'] = 'Поле "title" обязательно';
        }

        if (!in_array($this->status, self::ALLOWED_STATUSES)) {
            $errors['status'] = 'Недопустимый статус задачи';
        }

        if ($this->deadline && !Validator::date($this->deadline)) {
            $errors['deadline'] = 'Неверный формат даты (ожидается YYYY-MM-DD)';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
