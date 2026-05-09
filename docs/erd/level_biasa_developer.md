# ERD — Level Biasa (Developer Version)

## Entities & Tables

### 1. `categories`

| Column      | Type            | Constraints             |
| -------------| -----------------| -------------------------|
| id          | BIGINT UNSIGNED | PK, Auto Increment      |
| name        | VARCHAR(100)    | NOT NULL, UNIQUE        |
| slug        | VARCHAR(120)    | NOT NULL, UNIQUE, INDEX |
| description | TEXT            | NULLABLE                |
| created_at  | TIMESTAMP       |                         |
| updated_at  | TIMESTAMP       |                         |

### 2. `products`

| Column      | Type            | Constraints               |
| -------------| -----------------| ---------------------------|
| id          | BIGINT UNSIGNED | PK, Auto Increment        |
| category_id | BIGINT UNSIGNED | FK → categories.id, INDEX |
| name        | VARCHAR(200)    | NOT NULL                  |
| slug        | VARCHAR(220)    | NOT NULL, UNIQUE          |
| sku         | VARCHAR(50)     | NOT NULL, UNIQUE, INDEX   |
| price       | DECIMAL(12,2)   | NOT NULL, DEFAULT 0       |
| stock       | INTEGER         | NOT NULL, DEFAULT 0       |
| description | TEXT            | NULLABLE                  |
| is_active   | BOOLEAN         | DEFAULT true, INDEX       |
| created_at  | TIMESTAMP       |                           |
| updated_at  | TIMESTAMP       |                           |

### 3. `transactions`

| Column         | Type            | Constraints              |
| ----------------| -----------------| --------------------------|
| id             | BIGINT UNSIGNED | PK, Auto Increment       |
| user_id        | BIGINT UNSIGNED | FK → users.id, INDEX     |
| invoice_number | VARCHAR(50)     | NOT NULL, UNIQUE         |
| total_amount   | DECIMAL(12,2)   | NOT NULL                 |
| paid_amount    | DECIMAL(12,2)   | NOT NULL                 |
| change_amount  | DECIMAL(12,2)   | NOT NULL DEFAULT 0       |
| payment_method | VARCHAR(20)     | NOT NULL, DEFAULT 'cash' |
| notes          | TEXT            | NULLABLE                 |
| created_at     | TIMESTAMP       | INDEX                    |
| updated_at     | TIMESTAMP       |                          |

### 4. `transaction_items`

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| transaction_id | BIGINT UNSIGNED | FK → transactions.id, INDEX |
| product_id | BIGINT UNSIGNED | FK → products.id |
| quantity | INTEGER | NOT NULL, > 0 |
| unit_price | DECIMAL(12,2) | NOT NULL |
| subtotal | DECIMAL(12,2) | NOT NULL |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

## Relasi

```
categories 1──N products
products   1──N transaction_items
transactions 1──N transaction_items
users      1──N transactions
```

## Indexes Required

- **products**: `category_id`, `sku` (unique), `is_active`
- **transactions**: `user_id`, `created_at`, `invoice_number` (unique)
- **transaction_items**: `transaction_id`, `product_id`
- **categories**: `slug` (unique), `name` (unique)

## Diagram Relasi (Crow's Foot)

```
┌────────────┐       ┌────────────┐
│ categories │1───N──│  products  │
└────────────┘       └─────┬──────┘
                           │1
                           │
                           N
                    ┌──────┴─────────┐
                    │ transaction_   │
                    │    items       │
                    └──────┬─────────┘
                           │N
                           │1
                    ┌──────┴─────────┐
                    │  transactions  │
                    └──────┬─────────┘
                           │N
                           │1
                    ┌──────┴─────────┐
                    │     users      │
                    └────────────────┘
```
