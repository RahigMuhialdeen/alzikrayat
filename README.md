# Alzikrayat — MVC Photo Sharing Web Application

Course: Advanced Web Technologies  
Architecture: MVC + 3-Tier  
Backend: PHP (handwritten MVC and manual regex router)  
Database: MySQL / PDO  
Frontend: HTML5, Bootstrap, JavaScript

## Requirements covered

- Manual regex routing with a custom `Router` class.
- MVC separation into Models, Views and Controllers.
- 3-Tier separation: Presentation, Application/Business and Data.
- MySQL normalized `Users`, `Photos`, and `Comments` tables.
- Foreign keys with cascade deletes.
- Secure password hashing using PHP `password_hash()` with Bcrypt.
- Session-based registration, login and logout.
- Dynamic navigation showing `Hi <first_name>` and Logout, or `Please Login`.
- Seven-day last-login browser cookie displayed on the login page.
- Responsive home page with images, statistics and About Us.
- Gallery with 3-column, 4-column and list layouts.
- Authenticated photo uploads stored physically in `public/images/uploads/`.
- Photo metadata stored in MySQL.
- Ownership validation before deleting a photo and its physical file.
- Photo details and comments.
- HTML5, custom JavaScript and PHP server-side validation.
- XSS-safe output with `htmlspecialchars()` and parameterized SQL for injection protection.
- Bootstrap responsive UI.

## Folder structure

```text
alzikrayat/
├── config/database.php
├── core/Model.php
├── core/Controller.php
├── core/Router.php
├── controllers/AuthController.php
├── controllers/PhotoController.php
├── controllers/CommentController.php
├── models/User.php
├── models/Photo.php
├── models/Comment.php
├── views/auth/
├── views/photos/
├── views/layout/
├── public/index.php
├── public/.htaccess
├── public/assets/css/style.css
├── public/assets/js/app.js
├── public/images/uploads/
├── database/alzikrayat.sql
└── docs/Architecture_Report.md
```

## XAMPP installation

1. Install/start Apache and MySQL in XAMPP.
2. Copy the `alzikrayat` folder into `C:/xampp/htdocs/`.
3. Open phpMyAdmin.
4. Import `database/alzikrayat.sql`.
5. Confirm `config/database.php` matches your local MySQL credentials. The default XAMPP setup used here is `root` with an empty password.
6. Put the 19 supplied project images into `public/images/uploads/`.
7. Open `http://localhost/alzikrayat/public/`.

## Suggested image filenames

```text
golden-sunset.jpg
mountain-escape.jpg
ocean-waves.jpg
forest.jpg
spring-flowers.jpg
desert.jpg
city-lights.jpg
modern-city.jpg
coffee.jpg
food.jpg
beach.jpg
open-road.jpg
architecture.jpg
old-building.jpg
books.jpg
camera.jpg
flower-closeup.jpg
rainy-day.jpg
starry-night.jpg
```

The application does not require these 19 files to be pre-seeded in the database. Register a user and upload the images through the application so that each file and its metadata are correctly associated with a user.

## Test flow

1. Register an account.
2. Log out and log in again to verify the last-login cookie.
3. Upload a photo with title/description.
4. Open Gallery and switch between 3-column, 4-column and list layouts.
5. Open the photo detail page.
6. Add a comment while logged in.
7. Verify the comment appears immediately after redirect.
8. Verify only the owner sees the delete button and can delete the photo.

## Important deployment note

The `.htaccess` assumes the project folder is named `alzikrayat` and is served from `http://localhost/alzikrayat/public/`. If you use a different folder name, update `RewriteBase` in `public/.htaccess`.
