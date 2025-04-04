# 📋 To-Do List RESTful API на чистом PHP

Проект — это REST API для управления задачами (To-Do), реализованный на **чистом PHP 8.1+** с использованием **JWT-аутентификации**, архитектуры по слоям (Controller, Repository, Middleware, DTO) и безопасной работы с БД через PDO.

---

## 🚀 Бысткий старт

### 🔧 Требования

- PHP 8.1+
- MySQL 8.0+
- Apache (с поддержкой `.htaccess`)
- Composer
- OpenServer / XAMPP / Laragon (или любой Apache + MySQL стек)

### 📥 Установка

1. Клонируйте репозиторий или распакуйте `.zip`:
   ```bash
   git clone https://github.com/your-username/todo-api.git
   cd todo-api
Установите зависимости:

bash
Копировать
composer install
Настройте базу данных:

Создайте БД todo

Выполните SQL-дамп init.sql вручную (из папки /db)

Настройте src/config.php:

php
Копировать
return [
    'db' => [
        'host' => 'localhost',
        'dbname' => 'todo',
        'user' => 'root',
        'pass' => '',
    ],
    'jwt_secret' => 'your-secret-key',
];
Убедитесь, что в public/.htaccess включён mod_rewrite.

Настройте Apache на директорию /public как корневую.

📁 Структура проекта
csharp
Копировать
todo-api/
├── public/         # index.php — точка входа
├── src/            # Контроллеры, репозитории, мидлвары, валидаторы
├── logs/           # Логи действий и ошибок
├── db/             # SQL-дамп базы данных
├── composer.json   # Зависимости
└── README.md       # Документация
🔐 Аутентификация
Используется JWT (библиотека firebase/php-jwt).

🔹 POST /register
json
Копировать
{
  "email": "test@example.com",
  "password": "secret123"
}
🔹 POST /login
Возвращает JWT:

json
Копировать
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOi..."
}
📌 Эндпоинты задач
🛑 Все ниже защищены JWT и требуют заголовка:

makefile
Копировать
Authorization: Bearer <токен>
🔹 GET /tasks
Получить все задачи текущего пользователя.

Параметры:
status — фильтр по статусу (в работе, завершено, дедлайн)

deadline — дата (YYYY-MM-DD)

sort — created_at / deadline

order — asc / desc

🔹 POST /tasks
json
Копировать
{
  "title": "Купить хлеб",
  "description": "Не забыть батон",
  "status": "в работе",
  "deadline": "2025-04-06"
}
🔹 GET /tasks/{id}
Получить одну задачу по ID.

🔹 PUT /tasks/{id}
json
Копировать
{
  "title": "Обновлённый заголовок",
  "status": "завершено"
}
🔹 DELETE /tasks/{id}
Удалить задачу.

📎 Примеры cURL
bash
Копировать
# Регистрация
curl -X POST http://localhost/register -H "Content-Type: application/json" -d '{"email":"test@example.com", "password":"secret"}'

# Вход
curl -X POST http://localhost/login -H "Content-Type: application/json" -d '{"email":"test@example.com", "password":"secret"}'

# Получить задачи
curl -X GET http://localhost/tasks -H "Authorization: Bearer <your-token>"
🧪 Валидация
Email и пароль валидируются при регистрации и логине.

Задачи валидируются через TaskRequest DTO.

Ответы в JSON с кодами 400 / 401 / 404 / 500.

📒 Примечания
Код разделён по слоям (MVC + Middleware).

Проект не использует фреймворки (Laravel, Symfony).

Аутентификация полностью реализована вручную на JWT.

Безопасность: PDO, password_hash, проверки владельца задачи.

