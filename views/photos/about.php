<?php require __DIR__ . '/../layout/header.php'; ?>
<section class="about-panel rounded-4 p-4 p-md-5">
    <h1 class="fw-bold">About Us</h1>
    <p>Alzikrayat is a photo-sharing web application created for the Advanced Web Technologies course. It demonstrates how a dynamic web application can be built from foundational PHP and MySQL components without a backend framework.</p>
    <h2 class="h4 mt-4">Project Architecture</h2>
    <ul><li><strong>Presentation Tier:</strong> HTML5, Bootstrap, views and client-side JavaScript validation.</li><li><strong>Application / Business Tier:</strong> Controllers, custom regex Router, request handling and server-side validation.</li><li><strong>Data Tier:</strong> Models, PDO database connection and handwritten SQL queries.</li></ul>
    <h2 class="h4 mt-4">Main Features</h2>
    <p class="mb-0">Registration, secure login sessions, seven-day last-login cookie, responsive home page, multiple gallery layouts, physical image uploads, ownership-protected deletion, photo details and comments.</p>
</section>
<?php require __DIR__ . '/../layout/footer.php'; ?>
