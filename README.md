# User Balance API

API для управления балансом пользователей: просмотр, депозит, вывод средств и переводы между пользователями.

---

## Docker

Проект полностью готов к запуску через Docker.

### Структура Docker Compose

Используется `docker-compose.yml` с сервисами:

- **nginx** — веб-сервер, слушает порт `8000`.
- **php** — PHP-FPM для запуска Laravel.
- **postgres** — база данных СУБД PostgreSQL.
- **composer** — для установки зависимостей.
- **artisan** — для запуска команд Laravel внутри контейнера.

### Запуск проекта через Docker

1. Убедитесь, что установлен Docker и Docker Compose.
2. Соберите контейнеры:
```bash
docker-compose build
```

или сразу поднимите их в фоне:
```bash
docker-compose up -d
```

Установите зависимости Laravel через контейнер composer:
```bash
docker-compose run --rm composer install
```

Примените миграции:
```bash
docker-compose run --rm artisan migrate
```

Доступ к приложению:
```
http://127.0.0.1:8000
```
## Настройка запросов

### 1. Получение баланса пользователя (GET)

**Пример:** просмотр баланса пользователя.

- **URL:** `http://127.0.0.1:8000/api/balance/1`
- **Метод:** `GET`
- **Headers:** не требуются
- **Body:** не используется

**Пример ответа:**
```json
{
  "success": true,
  "data": {
    "user_id": 1,
    "balance": 100
  },
  "message": "Balance retrieved successfully"
}
```

### 2. Депозит на баланс пользователя (POST)

- **URL:** `http://127.0.0.1:8000/api/deposit`
- **Метод:** `POST`
- **Headers:** требуются
- **Body:** используется

**HEADERS**
```
Accept: application/json
Content-Type: application/json
```
**BODY RAW->JSON**
```json
{
  "user_id": 1,
  "amount": 100,
  "comment": "Пополнение баланса"
}
```

**Пример ответа:**
```json
{
  "success": true,
  "data": {
    "user_id": 1,
    "balance": 200
  },
  "message": "Deposit successful"
}
```

### 3. Вывод средств с баланса пользователя (POST)

- **URL:** `http://127.0.0.1:8000/api/deposit`
- **Метод:** `POST`
- **Headers:** требуются
- **Body:** используется

**HEADERS**
```
Accept: application/json
Content-Type: application/json
```
**BODY RAW->JSON**
```json
{
  "user_id": 1,
  "amount": 50,
  "comment": "Снятие средств"
}
```

**Пример ответа:**
```json
{
  "success": true,
  "data": {
    "user_id": 1,
    "balance": 150
  },
  "message": "Withdrawal successful"
}
```

### 4. Перевод средств между пользователями (POST)

- **URL:** `http://127.0.0.1:8000/api/deposit`
- **Метод:** `POST`
- **Headers:** требуются
- **Body:** используется

**HEADERS**
```
Accept: application/json
Content-Type: application/json
```
**BODY RAW->JSON**
```json
{
  "from_user_id": 1,
  "to_user_id": 2,
  "amount": 30,
  "comment": "Перевод другу"
}
```

**Пример ответа:**
```json
{
  "success": true,
  "data": {
    "from_user_id": 1,
    "to_user_id": 2,
    "amount": 30,
    "from_user_balance": 120,
    "to_user_balance": 130
  },
  "message": "Transfer successful"
}
```


**ВАЖНО**
Все POST-запросы должны содержать JSON с обязательными полями (user_id, amount и т.д.).

Laravel автоматически проверяет валидность данных через FormRequest. Если что-то не заполнено - вернёт ошибки.

GET-запросы можно открыть в браузере, POST-запросы лучше тестировать через Postman или cURL, используя raw → JSON в теле запроса.

