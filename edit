# Учебный стенд: Nginx + PHP-FPM + PostgreSQL

Песочница для практики Docker Compose. Стек: Nginx (reverse-proxy), PHP-FPM 8.5 (чистый PHP) и PostgreSQL 18. Образы собираются локально из `docker/`.

## Быстрый старт

    make init && make up && make composer

* `make init` — создает `.env` с вашими UID/GID.
* `make up` — собирает и запускает контейнеры.
* `make composer` — ставит PHP-зависимости в `app/vendor/`.

**Доступы:**
* Сайт: http://127.0.0.1:8080/
* Health-check: http://127.0.0.1:8080/health.php
* PostgreSQL: `127.0.0.1:5432` (пароли в `.env`)

## Когда нужен рестарт или пересборка

* **Код в `app/` (PHP):** изменения видны сразу (F5).
* **Конфиги** (`docker/nginx/`, `php.ini` и т.д.): нужен `docker compose restart <сервис>`.
* **`Dockerfile` или `composer.json`:** нужен `make build && make up` (и `make composer`, если менялись зависимости).

## Ловушка: initdb срабатывает только один раз

SQL-скрипты из `docker/postgres/initdb/` применяются только при создании нового тома `pgdata`. Если база уже была создана, правки в SQL не применятся.

Решение — снести том и поднять заново (удалит все данные):

    make clean && make up

Если база есть, но нужно донастроить мониторинг, используйте `make pg-monitoring` (безопасно, данные не удаляет).

## Мониторинг и логи

Стенд собирается по уровням:

| Уровень | Команда | Что добавляется | RAM (простой) |
|---|---|---|---|
| База | `make up` | Nginx, PHP, PostgreSQL | ~60 МБ |
| + Мониторинг | `make mon-up` | Prometheus, Grafana, Alertmanager, Mailpit | +~500 МБ |
| + Логи | `make logs-up` | Graylog, OpenSearch, MongoDB | +~2.2 ГБ |

*Для уровня с логами выделите Docker минимум 6 ГБ RAM.*

    make mon-up      # Запуск мониторинга
    make mon-down    # Отключение мониторинга
    make logs-up     # Запуск логов
    make logs-down   # Отключение логов

### Адреса сервисов

| Сервис | Адрес | Логин / Пароль |
|---|---|---|
| Приложение | http://127.0.0.1:8080/ | — |
| Grafana | http://127.0.0.1:3000/ | admin / admin |
| Prometheus | http://127.0.0.1:9090/ | — |
| Mailpit (письма) | http://127.0.0.1:8025/ | — |
| Graylog | http://127.0.0.1:9000/ | admin / admin |

*Все порты доступны только на `127.0.0.1`.*

## Мониторинг Trello-сервиса (задание)

### 1. Пробелы в базовом мониторинге
Базовый стенд видел только инфраструктуру (5xx ошибки, железо). Он не замечал:
* Бизнес-ошибки (4xx/422), например, массовый отказ при создании карточек.
* Заторы в колонках канбана.
* Медленные SQL-запросы на уровне Eloquent.

### 2. Что добавлено
В код приложения добавлены кастомные метрики Prometheus:
* `trello_cards_created_total` — созданные карточки.
* `trello_cards_by_column` — карточки по колонкам.
* `trello_business_errors_total` — ошибки бизнес-логики.
* `trello_db_query_duration_seconds` — время SQL-запросов.

Также добавлены 4 панели в Grafana и 2 алерта (`TrelloHighBusinessErrorRate`, `TrelloSlowDbQueries`).

### 3. Как проверить
1. Запустить стек:

    docker compose -f compose.yaml -f compose.monitoring.yaml up -d

2. Открыть сайт (`http://127.0.0.1:8080`), создать карточку или вызвать ошибку валидации (пустая форма).
3. Проверить метрики:

    docker compose exec nginx curl -s http://localhost:8081/metrics.php | grep trello_

4. Смотреть графики в Grafana (дашборд "Обзор сервиса").

## Структура

    app/              — код приложения (PHP)
    docker/           — Dockerfile и конфиги (nginx, php, postgres, prometheus, grafana)
    docs/runbooks/    — инструкции к алертам
    compose.yaml      — базовая оркестрация
    compose.monitoring.yaml — стек мониторинга
    compose.logging.yaml    — стек логов

## Особенности ОС
* **Windows/WSL2:** Запускайте проект строго внутри файловой системы Linux (например, `~/projects/`), а не на диске `C:`. Иначе будут проблемы с правами (UID/GID).
* **Переносы строк:** `.gitattributes` форсирует LF, чтобы скрипты не ломались на Windows.
