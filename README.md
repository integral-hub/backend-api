
# 🌍 Country Currency & Exchange API

A Laravel-based RESTful API that fetches country data and exchange rates from external APIs, computes estimated GDP, caches results in MySQL, and provides CRUD endpoints with filtering, sorting, and image generation.

---

## 🚀 Features

- Fetches countries from [RestCountries API](https://restcountries.com/v2/all)
- Fetches exchange rates from [Open ER API](https://open.er-api.com/v6/latest/USD)
- Computes **estimated GDP** = population × random(1000–2000) ÷ exchange_rate
- Caches data in MySQL
- Provides CRUD endpoints:
  - Refresh (fetch + update DB)
  - Get all countries (with filters/sorting)
  - Get one country
  - Delete a country
  - Status summary
  - Image summary
- Handles external API failures gracefully
- Generates summary image (`cache/summary.png`) after refresh

---

## 🧩 REQUIREMENT

- **Backend:** Laravel 12+ / PHP 8+
- **Database:** WAMP SERVER

---

## ⚙️ Installation Guide

### 1️⃣ Clone Repository

```bash
git clone https://github.com/integral-hub/backend-api.git
cd backend-api
````

### 2️⃣ Install Dependencies

```bash
composer install
```

### 3️⃣ Environment Setup

Copy `.env.example` to `.env`

```bash
cp .env.example .env
```

Then update `.env` with your local DB credentials:


### 4️⃣ Generate Key & Run Migration

```bash
php artisan key:generate
php artisan migrate
```

### 6️⃣ Run Server

```bash
php artisan serve
```

API will be available at:
👉 `http://127.0.0.1:8000/api`

---

## 🌍 API Documentation

### 🔄 POST `/countries/refresh`

Fetches all countries + exchange rates, stores or updates them in DB, and generates summary image.

```

```

### 📋 GET `/countries`

Get all countries with optional filters/sorting.

**Query Params:**

* `region` — e.g. `?region=Africa`
* `currency` — e.g. `?currency=NGN`
* `sort` — `gdp_desc` or `gdp_asc`

**Example:**
`GET /countries?region=Africa&sort=gdp_desc`

```

```

### 🔍 GET `/countries/{name}`

Fetch details of a specific country.

**Example:** `/countries/Nigeria`

```

```

### 🗑️ DELETE `/countries/{name}`

Delete a country record.

---

### 📊 GET `/status`

Returns total countries and last refresh timestamp.

---

### 🖼️ GET `/countries/image`

Returns generated summary image after last refresh.

---

## 💾 Database Schema

| Column            | Type      | Description              |
| ----------------- | --------- | ------------------------ |
| id                | bigint    | Auto ID                  |
| name              | string    | Country name             |
| capital           | string    | Capital city             |
| region            | string    | Region name              |
| population        | bigint    | Population count         |
| currency_code     | string    | Currency code (e.g. NGN) |
| exchange_rate     | decimal   | Exchange rate to USD     |
| estimated_gdp     | decimal   | Computed GDP             |
| flag_url          | string    | Flag image URL           |
| last_refreshed_at | timestamp | Last update timestamp    |

---

## ⚠️ Error Response Format

| HTTP Code | Example                                           |
| --------- | ------------------------------------------------- |
| 400       | `{ "error": "Validation failed" }`                |
| 404       | `{ "error": "Country not found" }`                |
| 503       | `{ "error": "External data source unavailable" }` |
| 500       | `{ "error": "Internal server error" }`            |

---

## 🧠 Logic Notes

* Each `/refresh` call generates **new random multipliers (1000–2000)** per country.
* Updates or inserts countries (case-insensitive name match).
* If currency not found in exchange API → GDP = 0.
* External API errors will **not modify existing DB**.

---

## 🖼️ Summary Image Example

When `/countries/refresh` runs, a `summary.png` is generated at
`cache/summary.png`

Image contains:

* Total countries
* Top 5 countries by estimated GDP
* Last refresh timestamp

---


---

### 🏁 Example Live URL

`[Live API](http://api.techtrovelab.com/api)`

---

```

