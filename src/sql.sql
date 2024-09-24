-- Create database if not exists
CREATE DATABASE IF NOT EXISTS recipe_app;

-- Use the database
USE recipe_app;

-- Create or alter users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role VARCHAR(50),
    status VARCHAR(50)
);

-- Add columns if they don't exist
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS role VARCHAR(50),
    ADD COLUMN IF NOT EXISTS status VARCHAR(50);

-- Create or alter recipes table
CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_name VARCHAR(255) NOT NULL,
    recipe_owner INT NOT NULL,
    recipe_image VARCHAR(255),
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ingredients TEXT,
    instructions TEXT,
    FOREIGN KEY (recipe_owner) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Add columns if they don't exist
ALTER TABLE recipes
    ADD COLUMN IF NOT EXISTS recipe_image VARCHAR(255),
    ADD COLUMN IF NOT EXISTS category_id INT,
    ADD CONSTRAINT IF NOT EXISTS fk_recipe_owner FOREIGN KEY (recipe_owner) REFERENCES users(id),
    ADD CONSTRAINT IF NOT EXISTS fk_category_id FOREIGN KEY (category_id) REFERENCES categories(id);

-- Create or alter categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);




-- Create database if not exists
CREATE DATABASE IF NOT EXISTS recipe_app;

-- Use the database
USE recipe_app;

-- Create or alter users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role VARCHAR(50),
    status VARCHAR(50)
);

-- Create or alter categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Create or alter recipes table
CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_name VARCHAR(255) NOT NULL,
    recipe_owner INT NOT NULL,
    recipe_image VARCHAR(255),
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ingredients TEXT,
    instructions TEXT,
    FOREIGN KEY (recipe_owner) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
