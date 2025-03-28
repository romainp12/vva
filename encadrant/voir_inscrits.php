<?php
include '../includes/db.php';
include_once '../includes/functions.php';
include '../includes/header.php';

if (!is_encadrant() && !is_admin()) {
    header('Location: /index.php');
    exit;
}

// Vérifier si les paramètres 'codeAnim' et 'dateAct' sont présents dans l'URL
if (!isset($_GET['codeAnim']) || !isset($_GET['dateAct'])) {
    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Aucune activité spécifiée.</div>';
    include '../includes/footer.php';
    exit();
}

// Récupérer les valeurs des paramètres
$codeAnim = $_GET['codeAnim'];
$dateAct = $_GET['dateAct'];

// Récupérer le nom de l'animation et le nombre total de places
$stmtAnim = $pdo->prepare("SELECT NOMANIM, NBREPLACEANIM FROM ANIMATION WHERE CODEANIM = ?");
$stmtAnim->execute([$codeAnim]);
$animData = $stmtAnim->fetch(PDO::FETCH_ASSOC);
$animName = $animData['NOMANIM'];
$capaciteTotale = $animData['NBREPLACEANIM'];

// Récupérer les inscrits pour l'activité et la date spécifiées
$sql = "SELECT v.NOMCOMPTE AS NOM, v.PRENOMCOMPTE AS PRENOM, i.DATEINSCRIP, i.USER 
        FROM INSCRIPTION i
        INNER JOIN COMPTE v ON i.USER = v.USER
        WHERE i.CODEANIM = ? AND i.DATEACT = ? AND i.DATEANNULE IS NULL
        ORDER BY i.DATEINSCRIP";

$stmt = $pdo->prepare($sql);
$stmt->execute([$codeAnim, $dateAct]);
$inscrits = $stmt->fetchAll();

// Calculer le nombre de places disponibles
$nombreInscrits = count($inscrits);
$placesDisponibles = $capaciteTotale - $nombreInscrits;

// Récupérer les informations sur l'activité
$sqlActivity = "SELECT HRDEBUTACT, HRFINACT, PRIXACT FROM ACTIVITE WHERE CODEANIM = ? AND DATEACT = ?";
$stmtActivity = $pdo->prepare($sqlActivity);
$stmtActivity->execute([$codeAnim, $dateAct]);
$activity = $stmtActivity->fetch();
?>
<meta charset="UTF-8">

<div class="container">
    <div class="card mb-4">
        <div class="card-header">
            <h1 class="mb-0"><i class="fas fa-users"></i> Liste des inscrits</h1>
        </div>
        <div class="card-body">
            <div class="activity-details mb-4">
                <h2><?= htmlspecialchars($animName) ?></h2>
                <div class="d-flex flex-wrap mb-3">
                    <div class="badge badge-primary mr-2">
                        <i class="fas fa-calendar"></i> <?= htmlspecialchars(date('d/m/Y', strtotime($dateAct))) ?>
                    </div>
                    
                    <?php if ($activity): ?>
                    <div class="badge badge-secondary mr-2">
                        <i class="fas fa-clock"></i> 
                        <?= htmlspecialchars($activity['HRDEBUTACT']) ?> - <?= htmlspecialchars($activity['HRFINACT']) ?>
                    </div>
                    
                    <div class="badge badge-info mr-2">
                        <i class="fas fa-euro-sign"></i> <?= htmlspecialchars($activity['PRIXACT']) ?> €
                    </div>
                    
                    <div class="badge badge-<?= $placesDisponibles > 0 ? 'success' : 'danger' ?>">
                        <i class="fas fa-users"></i> Places : <?= htmlspecialchars($nombreInscrits) ?> / <?= htmlspecialchars($capaciteTotale) ?> (<?= htmlspecialchars($placesDisponibles) ?> disponibles)
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($inscrits): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><i class="fas fa-user"></i> Nom</th>
                                <th><i class="fas fa-user"></i> Prénom</th>
                                <th><i class="fas fa-calendar-alt"></i> Date d'inscription</th>
                                <th><i class="fas fa-id-badge"></i> Identifiant</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inscrits as $index => $inscrit): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($inscrit['NOM']) ?></td>
                                    <td><?= htmlspecialchars($inscrit['PRENOM']) ?></td>
                                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($inscrit['DATEINSCRIP']))) ?></td>
                                    <td><?= htmlspecialchars($inscrit['USER']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="text-right mt-3">
                    <div class="badge badge-success p-2">
                        <i class="fas fa-users"></i> Nombre total d'inscrits: <?= count($inscrits) ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aucun vacancier inscrit pour cette activité à cette date.
                </div>
            <?php endif; ?>
            
            <div class="text-center mt-4">
                <a href="/animation.php?code=<?= htmlspecialchars($codeAnim) ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à l'animation
                </a>
                
                <?php if ($inscrits): ?>
                <button class="btn btn-primary ml-2" id="printBtn">
                    <i class="fas fa-print"></i> Imprimer la liste
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('printBtn')?.addEventListener('click', function() {
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
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        thead {
            background-color: #f2f2f2;
        }
    }
</style>

<?php include '../includes/footer.php'; ?>