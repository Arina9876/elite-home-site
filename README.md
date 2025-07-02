# EliteHome — Учебный сайт недвижимости

EliteHome — это учебное PHP‑приложение для аренды и продажи премиальной недвижимости с регистрацией, личным кабинетом и админ‑панелью.
## Стек технологий
- HTML5, CSS3 (адаптив, тёмная/светлая тема)  
- JavaScript (слайдер, валидация, localStorage)  
- PHP 8 + MySQL 8 (PDO), Google reCAPTCHA v2  
- Git + GitHub

## Автор: Арина Горшкова 
## Структура проекта
/
├── css/ # стили
├── js/ # скрипты
├── images/ # картинки
├── index.html, index.php
├── personal.php # личный кабинет
├── admin.php, edit.php, delete.php # CRUD
└── config.php # настройки БД (ignorеd)
## Быстрый старт

```bash
# 1. Клонируем репозиторий
git clone https://github.com/Arina9876/elite-home-site.git
cd elite-home-site

# 2. Запускаем локальный PHP‑сервер
php -S localhost:8000

# 3. Открываем http://localhost:8000/index.html
