# PromoData — тестовое задание

## Требования

Docker

## Запуск

### 1. Конфигурация

```bash
cp .env.example .env
```

### 2. Поднять контейнеры

```bash
docker compose up -d --build
```

### 3. Установить зависимости и создать схему

```bash
docker compose exec php composer install
docker compose exec php php artisan key:generate
docker compose exec php php artisan migrate
```

Миграции создают таблицы `process_status`, `manufacturer`, `product`, `price`,
`report_process`.

## Проверка

### Формирование отчёта

```bash
# по категории
docker compose exec php php artisan report:products 1

# по категории и конкретному производителю
docker compose exec php php artisan report:products 1 --manufacturer=2
```

Готовый файл кладётся в `storage/app/private/reports/` под именем
`report_<manufacturer_id>_<category_id>_<дата_время>.csv`
(`manufacturer_id` = `0`, если производитель не задан).

### Страница контроля

http://localhost:8080

## Данные для проверки

База после `migrate` пустая, поэтому все отчеты завершится статусом «Ошибка», т.к. данных за период нет. 
Чтобы увидеть полноценный отчёт, нужно залить свои данные в `manufacturer`, `product` и `price`.
