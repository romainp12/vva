# 🏕️ VVA - Village Vacances Alpes

Ce projet est une application web pour la gestion des animations et activités d'un village vacances. Elle permet aux utilisateurs (administrateurs, encadrants, vacanciers) de consulter, s'inscrire et gérer les différentes animations proposées.

## 📋 Table des matières

- [Présentation du projet](#-présentation-du-projet)
- [Fonctionnalités](#-fonctionnalités)
- [Structure des fichiers](#-structure-des-fichiers)
- [Installation](#-installation)
- [Profils utilisateurs](#-profils-utilisateurs)
- [Technologies utilisées](#️-technologies-utilisées)

## 🎯 Présentation du projet

Ce projet a été développé dans le cadre de l'épreuve E6 du BTS SIO SLAM. Il s'agit d'une application de gestion des animations et activités pour un village vacances qui permet aux vacanciers de consulter et de s'inscrire aux activités proposées, et aux encadrants de gérer ces activités.

L'application respecte les cas d'utilisation spécifiés dans le cahier des charges.

## ✨ Fonctionnalités

### Utilisateurs non connectés
- Consulter les animations et activités disponibles

### Vacanciers
- Se connecter/déconnecter
- Consulter les animations
- S'inscrire aux activités
- Se désinscrire des activités

### Encadrants
- Ajouter/modifier des animations
- Ajouter/modifier des activités
- Consulter la liste des inscrits à une activité
- Annuler une activité

### Administrateurs
- Accès à toutes les fonctionnalités des encadrants
- Gestion des identifiants des utilisateurs

## 📂 Structure des fichiers

```
VVA/
│
├── index.php                  # Page d'accueil avec la liste des animations
├── login.php                  # Page de connexion
├── logout.php                 # Script de déconnexion
├── animation.php              # Page de détail d'une animation avec ses activités
│
├── includes/                  # Fichiers partagés
│   ├── db.php                 # Configuration de la connexion à la base de données
│   ├── header.php             # En-tête partagé sur toutes les pages
│   ├── footer.php             # Pied de page partagé
│   └── functions.php          # Fonctions utilitaires
│
├── encadrant/                 # Pages accessibles aux encadrants et administrateurs
│   ├── ajouter_animation.php  # Formulaire d'ajout d'une animation
│   ├── ajouter_activite.php   # Formulaire d'ajout d'une activité
│   ├── modifier_animation.php # Formulaire de modification d'une animation
│   ├── modifier_activite.php  # Formulaire de modification d'une activité
│   └── voir_inscrits.php      # Liste des inscrits à une activité
│
├── admin/                     # Pages accessibles uniquement aux administrateurs
│   └── identifiants.php       # Gestion des identifiants utilisateurs
│
└── style.css                  # Feuille de style principale
```

## 💻 Installation

### Version en ligne

Une version de démonstration du projet est disponible en ligne à l'adresse suivante :
http://romainp12-vva.fwh.is/

Vous pouvez utiliser cette version pour tester l'application sans avoir à l'installer localement.

#### Comptes de test disponibles

Voici les identifiants de connexion pour tester les différents profils d'utilisateurs :

| Type de compte | Identifiant | Mot de passe |
|----------------|-------------|--------------|
| Administrateur | admin       | admin        |
| Encadrant      | lucas       | lucas        |
| Vacancier      | romain      | romain       |

### Installation locale

#### Prérequis
- Serveur web local (MAMP, WAMP, EasyPHP, XAMPP, etc.)
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur

### Étapes d'installation

1. **Clonez le repository sur votre machine locale**
   ```bash
   git clone https://github.com/romainp12/vva.git
   ```

2. **Placez les fichiers dans le répertoire web de votre serveur local**
   - Pour MAMP : `/Applications/MAMP/htdocs/vva` (Mac) ou `C:\MAMP\htdocs\vva` (Windows)
   - Pour WAMP : `C:\wamp\www\vva`
   - Pour XAMPP : `C:\xampp\htdocs\vva`
   - Pour EasyPHP : `C:\Program Files (x86)\EasyPHP\www\vva`

3. **Créez une base de données MySQL**
   - Nom de la base de données : `vva` (ou le nom de votre choix)
   - Charset : `utf8mb4_general_ci`

4. **Importez le fichier SQL dans votre base de données**
   - Utilisez phpMyAdmin ou un autre outil de gestion de base de données
   - Importez le fichier `vva.sql` fourni dans le repository

5. **Configurez la connexion à la base de données**
   - Ouvrez le fichier `includes/db.php`
   - Modifiez les informations de connexion selon votre configuration locale :
   ```php
   $host = 'localhost'; // Votre hôte MySQL (généralement localhost)
   $dbname = 'vva';     // Le nom de la base de données que vous avez créée
   $username = 'root';  // Votre nom d'utilisateur MySQL
   $password = '';      // Votre mot de passe MySQL (souvent vide en local)
   ```

6. **Accédez à l'application via votre navigateur**
   - Ouvrez votre navigateur et accédez à `http://localhost/vva` (ou l'URL correspondant à votre configuration)

## 👥 Profils utilisateurs

L'application comprend trois types de profils utilisateurs :

### Vacancier (VA)
- Peut consulter les animations et activités
- Peut s'inscrire/se désinscrire aux activités pendant sa période de séjour
- Ne peut s'inscrire qu'aux activités compatibles avec son âge

### Encadrant (EN)
- Peut gérer les animations (ajouter, modifier)
- Peut gérer les activités (ajouter, modifier, annuler)
- Peut consulter la liste des inscrits aux activités

### Administrateur (AD)
- Possède toutes les fonctionnalités des encadrants
- Peut accéder à la gestion des identifiants utilisateurs

## 🛠️ Technologies utilisées

- **Frontend** : HTML, CSS, JavaScript, Font Awesome
- **Backend** : PHP
- **Base de données** : MySQL

## 📝 Notes supplémentaires

- L'application a été conçue pour être responsive et s'adapter à différentes tailles d'écran.
- Un thème sombre est disponible et peut être activé via le bouton dédié dans la barre de navigation.
