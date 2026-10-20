
# web-trello — PHP Backend

Учебное веб-приложение для управления задачами по принципу Trello. Проект разработан на PHP с использованием PostgreSQL, Eloquent ORM и Monolog. Реализованы миграции базы данных, логирование HTTP-запросов и статическая конфигурация с шифрованием.

## Используемые технологии

  * **PHP 8.3** — серверная логика приложения.
  * **PostgreSQL** — хранение данных.
  * **Eloquent ORM** — работа с базой данных через PHP-модели.
  * **Monolog** — логирование запросов и ответов.
  * **Composer** — управление зависимостями.
  * **AES-256-CBC** — шифрование конфигурационных значений.

## Установка и запуск

Необходимы PHP, Composer и PostgreSQL.

Установить зависимости:

    composer install

Создать базу данных `trello` в PostgreSQL.

Создать `.env` из шаблона:

    Copy-Item .env.example .env

Указать параметры PostgreSQL в `.env` и задать ключ `APP_ENCRYPTION_KEY` (32 символа):

    php -r "echo bin2hex(random_bytes(16)), PHP_EOL;"

Применить миграции:

    php migrate.php

Запустить приложение:

    php -S localhost:8000 -t public

Приложение доступно по адресу `http://localhost:8000` и возвращает список пользователей в формате JSON.

## Eloquent ORM и миграции

Eloquent используется для взаимодействия PHP с PostgreSQL. Основные модели находятся в `app/Models/`.

Миграции создают структуру базы данных и управляются скриптом `migrate.php`. Скрипт пропускает уже применённые миграции (проверяет наличие таблицы).

## Структура базы данных

Приложение содержит четыре основные таблицы:

  * **users** — пользователи, email, хеши паролей.
  * **boards** — доски задач и их владельцы.
  * **board_columns** — колонки внутри досок.
  * **cards** — карточки задач, их описание, статус и порядок.

## Логирование Monolog

Monolog записывает каждый обработанный HTTP-запрос: метод, URI, тело запроса и тело ответа.

Файл логов:

    logs/app.log

## Конфигурация и шифрование

Настройки приложения находятся в `config/app.php`, а секретный ключ — в `.env`.

Для шифрования применяется алгоритм **AES-256-CBC**.

Зашифровать значение можно через класс `Src\Config`:

```php
$config = new Src\Config(__DIR__ . '/config/app.php', $_ENV['APP_ENCRYPTION_KEY']);
$encrypted = $config->encrypt('my-secret');   // вернёт строку enc:...
```

Полученное значение `enc:...` можно добавить в конфигурацию.

Для чтения и расшифровки используется:

```php
$config->get('secret');
```

Проверка шифрования:

    php test_config.php

## Структура проекта

    app/Models/             — модели Eloquent
    config/                 — настройки
    database/migrations/    — миграции PostgreSQL
    logs/                   — журналы Monolog
    public/index.php        — обработка HTTP-запросов
    src/Config.php          — конфигурация и шифрование
    src/Database.php        — инициализация Eloquent
    src/Logger.php          — настройка Monolog
    migrate.php             — запуск миграций
    test_config.php         — проверка шифрования
    composer.json           — зависимости проекта
    .env.example            — шаблон настроек
