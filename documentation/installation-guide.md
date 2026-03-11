# 🚀 Installation Guide

## Prerequisites
- XAMPP (PHP 7.4+ and MySQL 5.7+)
- Web browser (Chrome, Firefox, Safari)
- Text editor (VS Code, Sublime, etc.)

## Installation Steps

### 1. Download and Install XAMPP
- Download from https://www.apachefriends.org/
- Install and start Apache & MySQL services

### 2. Setup Project
- Extract project files to `C:\xampp\htdocs\jennys-cosmetics`
- Open browser: `http://localhost/phpmyadmin`
- Create database `jennys_cosmetics`

### 3. Import Database
- In phpMyAdmin, select `jennys_cosmetics` database
- Import `database/database.sql` file
- Import `database/sample-data.sql` for sample products

### 4. Configure Application
- Verify `config.php` settings
- Create uploads folder: `assets/images/uploads/`
- Set folder permissions if needed

### 5. Test Installation
- Visit: `http://localhost/jennys-cosmetics/`
- Admin login: `http://localhost/jennys-cosmetics/admin/login.php`
- Username: `admin` | Password: `admin123`

## Troubleshooting
- If database connection fails, check XAMPP MySQL service
- For upload issues, check folder permissions
- Clear browser cache if styles don't load