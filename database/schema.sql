-- EOQ APSI Handover
-- Creates empty tables only. No sample/demo rows are inserted.

CREATE DATABASE IF NOT EXISTS handover_eoq_apsi
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE handover_eoq_apsi;

CREATE TABLE IF NOT EXISTS categories (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id BIGINT UNSIGNED NULL,
  item_code VARCHAR(50) NOT NULL,
  name VARCHAR(150) NOT NULL,
  unit VARCHAR(30) NOT NULL DEFAULT 'unit',
  description TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_items_item_code (item_code),
  KEY idx_items_category_id (category_id),
  KEY idx_items_is_active (is_active),
  CONSTRAINT fk_items_category FOREIGN KEY (category_id)
    REFERENCES categories(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS eoq_analyses (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  item_id BIGINT UNSIGNED NOT NULL,
  analysis_name VARCHAR(150) NULL,
  annual_demand DECIMAL(18,4) NOT NULL,
  ordering_cost DECIMAL(18,4) NOT NULL,
  holding_cost DECIMAL(18,4) NOT NULL,
  eoq_quantity DECIMAL(18,4) NOT NULL,
  orders_per_year DECIMAL(18,6) NOT NULL,
  order_interval_days DECIMAL(18,4) NULL,
  ordering_cost_total DECIMAL(18,4) NOT NULL,
  holding_cost_total DECIMAL(18,4) NOT NULL,
  relevant_total_cost DECIMAL(18,4) NOT NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_analyses_item_id (item_id),
  KEY idx_analyses_created_at (created_at),
  CONSTRAINT fk_analyses_item FOREIGN KEY (item_id)
    REFERENCES items(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT chk_annual_demand_positive CHECK (annual_demand > 0),
  CONSTRAINT chk_ordering_cost_positive CHECK (ordering_cost > 0),
  CONSTRAINT chk_holding_cost_positive CHECK (holding_cost > 0)
) ENGINE=InnoDB;
