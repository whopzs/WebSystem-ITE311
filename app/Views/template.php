<?php
    $currentRoute = service('router')->getMatchedRoute()[0] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ITE311</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        body {
            background-color: #ffffff; 
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: #800000 !important; 
        }

        .navbar-brand, 
        .nav-link {
            color: #ffffff !important;
            font-weight: 500;
        }

        .nav-link:hover {
            color: #ffcccc !important; 
        }

        main {
            flex: 1; 
            padding-top: 50px; 
        }

        footer {
            background-color: #800000;
            color: white;
            text-align: center;
            padding: 10px 0;
            border-top: 3px solid #a94442;
        }

        h1 {
            color: #800000;
            font-weight: bold;
        }

        p {
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <?php if (!in_array($currentRoute, ['dashboard'])): ?>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?= base_url('/') ?>">ITE311</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#navbarNav" aria-controls="navbarNav" 
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('/') ?>">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('about') ?>">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('contact') ?>">Contact</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('login') ?>">Login</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <!-- Dynamic Content -->
    <main class="container">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
     <?php if (!in_array($currentRoute, ['login', 'register', 'dashboard'])): ?>
    <footer>
        <p>&copy; 2025 ITE311 - All Rights Reserved</p>
    </footer>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>