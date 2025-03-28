<?php
require_once '../includes/db.php';
include_once '../includes/functions.php';
include '../includes/header.php';

if (!is_admin()) {
    header('Location: /index.php');
    exit;
}

// Récupérer les identifiants depuis la table COMPTE
$sql = "SELECT USER, MDP, TYPEPROFIL, NOMCOMPTE, PRENOMCOMPTE FROM COMPTE";
$stmt = $pdo->query($sql);
$comptes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<meta charset="UTF-8">
<div class="container">
    <div class="card mb-4">
        <div class="card-header">
            <h1 class="mb-0"><i class="fas fa-users-cog"></i> Gestion des identifiants</h1>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user"></i> Utilisateur</th>
                            <th><i class="fas fa-key"></i> Mot de passe</th>
                            <th><i class="fas fa-id-badge"></i> Type</th>
                            <th><i class="fas fa-user-tag"></i> Nom</th>
                            <th><i class="fas fa-user-tag"></i> Prénom</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comptes as $compte): ?>
                            <tr>
                                <td><?= htmlspecialchars($compte['USER']) ?></td>
                                <td><?= htmlspecialchars($compte['MDP']) ?></td>
                                <td>
                                    <?php 
                                    $type = '';
                                    $badgeClass = '';
                                    
                                    switch ($compte['TYPEPROFIL']) {
                                        case 'AD':
                                            $type = 'Administrateur';
                                            $badgeClass = 'badge-danger';
                                            break;
                                        case 'EN':
                                            $type = 'Encadrant';
                                            $badgeClass = 'badge-warning';
                                            break;
                                        case 'VA':
                                            $type = 'Vacancier';
                                            $badgeClass = 'badge-primary';
                                            break;
                                        default:
                                            $type = $compte['TYPEPROFIL'];
                                            $badgeClass = 'badge-secondary';
                                    }
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($type) ?></span>
                                </td>
                                <td><?= htmlspecialchars($compte['NOMCOMPTE'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($compte['PRENOMCOMPTE'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="text-center mt-4">
                <a href="#" class="btn btn-primary" id="printBtn">
                    <i class="fas fa-print"></i> Imprimer la liste
                </a>
                <a href="/index.php" class="btn btn-secondary ml-2">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('printBtn').addEventListener('click', function(e) {
        e.preventDefault();
        window.print();
    });
</script>

<style>
    @media print {
        header, footer, .btn, nav {
            display: none;
        }
        
        body {
            background-color: white;
        }
        
        .card {
            box-shadow: none;
            border: none;
        }
        
        .card-header {
            background-color: white;
            color: black;
            border-bottom: 2px solid #333;
        }
        
        .container {
            width: 100%;
            max-width: 100%;
            padding: 0;
        }
        
        .badge {
            border: 1px solid #333;
            color: #333 !important;
            background-color: white !important;
        }
    }
</style>

<?php include '../includes/footer.php'; ?>