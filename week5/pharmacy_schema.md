```sql
CREATE TABLE categories (
    category_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    PRIMARY KEY (category_id),
    UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE medications (
    medication_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    description VARCHAR(500) NULL,
    price DECIMAL(10,2) UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (medication_id),
    UNIQUE KEY uq_medications_name_brand (name, brand),
    CONSTRAINT fk_medications_category
        FOREIGN KEY (category_id)
        REFERENCES categories(category_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE stocks (
    stock_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    medication_id INT UNSIGNED NOT NULL,
    batch_number VARCHAR(50) NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 0,
    expiry_date DATE NOT NULL,
    PRIMARY KEY (stock_id),
    UNIQUE KEY uq_stocks_medication_batch (medication_id, batch_number),
    CONSTRAINT fk_stocks_medication
        FOREIGN KEY (medication_id)
        REFERENCES medications(medication_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```