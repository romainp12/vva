<?php session_start(); include_once 'functions.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VVA</title>
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- CSS principal -->
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<header>
    <nav>
        <a href="/index.php" class="logo">
            <i class="fas fa-mountain"></i> VVA
        </a>
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="nav-links" id="navLinks">
            <li><a href="/index.php"><i class="fas fa-home"></i> Accueil</a></li>
            
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['type'] === 'EN' || $_SESSION['type'] === 'AD'): ?>
                    <li><a href="/encadrant/ajouter_animation.php"><i class="fas fa-plus-circle"></i> Ajouter Animation</a></li>
                    <li><a href="/encadrant/ajouter_activite.php"><i class="fas fa-calendar-plus"></i> Ajouter Activité</a></li>
                <?php endif; ?>
                
                <?php if ($_SESSION['type'] === 'AD'): ?>
                    <li><a href="/admin/identifiants.php"><i class="fas fa-users-cog"></i> Identifiants</a></li>
                <?php endif; ?>
                
                <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            <?php else: ?>
                <li><a href="/login.php"><i class="fas fa-sign-in-alt"></i> Connexion</a></li>
            <?php endif; ?>
            
            <!-- Bouton thème sombre/clair -->
            <li>
                <a href="#" id="themeToggle">
                    <i class="fas fa-moon"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>
<main>