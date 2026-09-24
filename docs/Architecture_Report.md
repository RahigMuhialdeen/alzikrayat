# Alzikrayat Architecture Report

## 1. Overview

Alzikrayat is an MVC-based photo sharing web application developed for the Advanced Web Technologies course. The implementation uses PHP and MySQL without a backend web framework. The application is organized using MVC and a physical 3-Tier boundary.

## 2. Three-Tier Architecture

### Presentation Tier

The presentation tier contains HTML5 views, Bootstrap layout components and JavaScript validation. The common navigation dynamically displays the current authentication state.

### Application / Business Tier

Controllers coordinate requests and business rules. `Router.php` is a handwritten routing engine that converts route placeholders into regular expressions and dispatches matching controller actions. Authentication, upload checks, ownership checks and validation are handled before data operations.

### Data Tier

The data tier contains the singleton PDO database connection and model classes. SQL statements are handwritten and parameterized. The database is normalized into Users, Photos and Comments with foreign-key referential integrity and cascade deletion.

## 3. MVC Pattern

Models map application data and execute SQL. Views render the presentation. Controllers receive route requests, validate input, call models and select views or redirects. This separation reduces coupling between the user interface and persistence layer.

## 4. Security and Validation

Passwords are stored using PHP's Bcrypt-backed `password_hash()` function and checked with `password_verify()`. SQL input uses PDO prepared statements. Output is escaped with `htmlspecialchars()` to reduce XSS risk. Forms use HTML5 validation, custom JavaScript validation and server-side PHP validation. Photo deletion verifies both the photo ID and the authenticated user's ID before deleting the database record and physical file.

## 5. Authentication State

PHP sessions preserve authenticated state. The navigation changes between a logged-in greeting and a login prompt. A secure seven-day browser cookie stores the timestamp of the last successful login and the login page displays that timestamp as context.

## 6. Photo Lifecycle

An authenticated user selects an image and submits title/description. The server checks extension, MIME type, image validity and file size, creates a randomized safe filename, moves the physical file into `public/images/uploads/`, and stores its metadata in the Photos table. Photo details load the image metadata and comments from MySQL.

## 7. Conclusion

The implementation demonstrates the foundational web mechanisms requested by the project: manual routing, MVC separation, 3-Tier architecture, raw SQL, session authentication, cookies, validation, file handling, gallery layouts and comments.
