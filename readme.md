# Product API – Symfony + Docker

A simple Symfony API application with product and category management, email notifications, and async message queue.

---

## Requirements

- `make` (or `cmake` on Windows)
- Docker + Docker Compose

---

## Getting Started

1. Run setup:

   ```bash
   make setup
   ```

2. Open the API in your browser or API client:
   - http://localhost:8080/api

3. Open MailHog to check emails:
   - http://localhost:8025/

4. Check logs:

   ```bash
   cat var/log/product.log
   ```

5. Run tests:

   ```bash
   make test
   ```

6. Connect to the database:
   - Exposed on port `3306`

---

## Try It Out

### Create a Category

```http
POST /api/categories HTTP/1.1
Host: localhost
Content-Type: application/json

{
  "code": "cat123"
}
```

### Create a Product

```http
POST /api/products HTTP/1.1
Host: localhost
Content-Type: application/json

{
  "name": "My Example Product 1",
  "price": 123.45,
  "categories": ["/api/categories/1", "/api/categories/2"]
}
```

---

## Notes

- Prices are stored in cents (lowest possible currency unit).
- A separate currency column can be added if needed.
- Emails (FROM and TO) are configured in the `.env` file.

---

## Notifications

- Notifications are sent asynchronously via Symfony Messenger.
- Channels can include:
  - Email (default)
  - Slack, SMS, etc. — just implement `NotificationInterface`
- To register custom channels:
  - Define them as services
  - Inject into the `Notifier` via `services.yaml`

