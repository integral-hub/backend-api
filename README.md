
# 🧩 String Analyzer API (Laravel)

A RESTful Laravel API that analyzes strings and computes properties such as palindrome check, unique characters, and more.

---

## ⚙️ **Installation & Setup**

### 🧰 Requirements

* PHP ≥ 8.1
* Composer ≥ 2.0
* Laravel 12+
* Wamp | Xampp | Laragon

---

### 🪄 Installation Steps

```bash
# Clone the repository
git clone https://github.com/integral-hub/backend-api.git
cd backend-api

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run database migrations (optional)
php artisan migrate
```

---

## 🧠 **Running the App Locally**

Start the local development server:

```bash
php artisan serve
```

Your API will be available at:
👉 **[http://127.0.0.1:8000/api](http://127.0.0.1:8000/api)**

You can test endpoints using Postman, Thunder Client, or any REST client.

---

## 🌍 **Base URL**

| Environment | Base URL                               |
| ----------- | -------------------------------------- |
| Local       | `http://127.0.0.1:8000/api`            |
| Production  | `https://api.techtrovelab.com/api` |

---

## 📘 **API Endpoints Summary**

| Method   | Endpoint                              | Description                                 |
| -------- | ------------------------------------- | ------------------------------------------- |
| `POST`   | `/strings`                            | Analyze and store a new string              |
| `GET`    | `/strings/{string_value}`             | Retrieve details of a specific string       |
| `GET`    | `/strings`                            | Get all stored strings (supports filtering) |
| `GET`    | `/strings/filter-by-natural-language` | Filter using natural language queries       |
| `DELETE` | `/strings/{string_value}`             | Delete a specific string                    |

> 📄 Full endpoint details, parameters, and example responses are documented in **Scribe API Docs**.

---

## 📚 **API Documentation**

This project uses **Scribe** for auto-generated API documentation.

To generate or update documentation:

```bash
php artisan scribe:generate
```

Then view locally at:

```
http://127.0.0.1:8000/docs
```

---

## ☁️ **Live Deployment Link**

🟢 **Base URL:**
`https://api.techtrovelab.com/api`

🟢 **API Docs (Scribe):**
`https://api.techtrovelab.com/docs`

---

## 🚀 **Deployment**

You can deploy this project on any platform that supports PHP

Make sure to:

1. Set up your `.env` file with correct database credentials.
2. Run `php artisan migrate --force`. (optional)
3. Generate documentation if needed (`php artisan scribe:generate`).

---
