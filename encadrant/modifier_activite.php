<?php
include '../includes/header.php';
include_once '../includes/functions.php';
require_once '../includes/db.php';

if (!is_encadrant() && !is_admin()) {
    header('Location: /index.php');
    exit;
}

if (!isset($_GET['codeAnim'], $_GET['dateAct'])) {
    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Aucune activité spécifiée.</div>';
    include '../includes/footer.php';
    exit();
}

$codeAnim = $_GET['codeAnim'];
$dateAct = $_GET['dateAct'];

// Récupération des informations de l'activité
$sql = "SELECT a.*, an.NOMANIM, an.NBREPLACEANIM 
         FROM ACTIVITE a 
         JOIN ANIMATION an ON a.CODEANIM = an.CODEANIM 
         WHERE a.CODEANIM = ? AND a.DATEACT = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$codeAnim, $dateAct]);
$activite = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$activite) {
    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Activité introuvable.</div>';
    include '../includes/footer.php';
    exit();
}

// Récupérer le nombre d'inscrits pour cette activité
$stmtInscrits = $pdo->prepare("
    SELECT COUNT(*) 
    FROM INSCRIPTION 
    WHERE CODEANIM = ? 
    AND DATEACT = ? 
    AND DATEANNULE IS NULL
");
$stmtInscrits->execute([$codeAnim, $dateAct]);
$nbInscrits = $stmtInscrits->fetchColumn();

// Calculer le nombre de places disponibles
$capaciteTotale = $activite['NBREPLACEANIM'];
$placesDisponibles = $capaciteTotale - $nbInscrits;

$error = '';
$successMessage = '';

// Mise à jour de l'activité
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codeEtatAct = $_POST['codeEtatAct'];
    $prixAct = $_POST['prixAct'];
    $hrRdvAct = $_POST['hrRdvAct'];
    $hrDebutAct = $_POST['hrDebutAct'];
    $hrFinAct = $_POST['hrFinAct'];
    $nomResp = $_POST['nomResp'];
    $prenomResp = $_POST['prenomResp'];
    
    // Vérification si l'activité est mise à l'état "Annulé" (E3)
    if ($codeEtatAct === 'E3' && $activite['CODEETATACT'] !== 'E3') {
        $canCancel = true;
        
        // Vérifier si l'activité est en cours
        $today = date('Y-m-d');
        if ($canCancel && $dateAct == $today) {
            // Récupérer les heures de l'activité
            $currentTime = date('H:i:s');
            if ($currentTime >= $hrDebutAct && $currentTime <= $hrFinAct) {
                $canCancel = false;
                $error = "Impossible d'annuler une activité en cours.";
            }
        }

        // Vérifier s'il y a des inscrits à cette activité
        if ($canCancel && $nbInscrits > 0) {
            // Si des personnes sont inscrites, on ne peut pas annuler l'activité
            $canCancel = false;
            $error = "Impossible d'annuler cette activité car il y a " . $nbInscrits . " personne(s) inscrite(s). Veuillez d'abord vous assurer que toutes les personnes se sont désinscrites.";
        }

        // Si on ne peut pas annuler, on garde l'état d'origine
        if (!$canCancel) {
            $codeEtatAct = $activite['CODEETATACT'];
        } else {
            // On peut annuler, mettre à jour la date d'annulation
            $sql = "UPDATE ACTIVITE 
                    SET CODEETATACT = ?, PRIXACT = ?, HRRDVACT = ?, HRDEBUTACT = ?, HRFINACT = ?, NOMRESP = ?, PRENOMRESP = ?, DATEANNULEACT = CURDATE() 
                    WHERE CODEANIM = ? AND DATEACT = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$codeEtatAct, $prixAct, $hrRdvAct, $hrDebutAct, $hrFinAct, $nomResp, $prenomResp, $codeAnim, $dateAct]);
            
            $successMessage = "L'activité a été mise à jour et annulée avec succès.";
        }
    } else {
        // Mise à jour normale sans changement d'état vers "Annulé"
        $sql = "UPDATE ACTIVITE 
                SET CODEETATACT = ?, PRIXACT = ?, HRRDVACT = ?, HRDEBUTACT = ?, HRFINACT = ?, NOMRESP = ?, PRENOMRESP = ? 
                WHERE CODEANIM = ? AND DATEACT = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$codeEtatAct, $prixAct, $hrRdvAct, $hrDebutAct, $hrFinAct, $nomResp, $prenomResp, $codeAnim, $dateAct]);
        
        // Si on passe de l'état "Annulé" à un autre état, on vide la date d'annulation
        if ($activite['CODEETATACT'] === 'E3' && $codeEtatAct !== 'E3') {
            $sqlUpdateAnnulation = "UPDATE ACTIVITE SET DATEANNULEACT = NULL WHERE CODEANIM = ? AND DATEACT = ?";
            $stmtUpdateAnnulation = $pdo->prepare($sqlUpdateAnnulation);
            $stmtUpdateAnnulation->execute([$codeAnim, $dateAct]);
        }
        
        $successMessage = "L'activité a été mise à jour avec succès.";
    }
    
    // Recharger les données après la mise à jour
    $stmt = $pdo->prepare($sql = "SELECT a.*, an.NOMANIM, an.NBREPLACEANIM FROM ACTIVITE a JOIN ANIMATION an ON a.CODEANIM = an.CODEANIM WHERE a.CODEANIM = ? AND a.DATEACT = ?");
    $stmt->execute([$codeAnim, $dateAct]);
    $activite = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Recharger le nombre d'inscrits
    $stmtInscrits->execute([$codeAnim, $dateAct]);
    $nbInscrits = $stmtInscrits->fetchColumn();
    $placesDisponibles = $activite['NBREPLACEANIM'] - $nbInscrits;
}
?>
<meta charset="UTF-8">

<div class="container">
    <div class="card mb-4">
        <div class="card-header">
            <h1 class="mb-0"><i class="fas fa-edit"></i> Modifier une activité</h1>
        </div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger fade-in">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success fade-in">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($successMessage) ?>
                </div>
            <?php endif; ?>
            
            <div class="activity-details mb-4">
                <h2><?= htmlspecialchars($activite['NOMANIM']) ?></h2>
                <div class="d-flex flex-wrap mb-3">
                    <div class="badge badge-primary mr-2">
                        <i class="fas fa-calendar-day"></i> <?= htmlspecialchars(date('d/m/Y', strtotime($dateAct))) ?>
                    </div>
                    <div class="badge badge-<?= $placesDisponibles > 0 ? 'success' : 'danger' ?> mr-2">
                        <i class="fas fa-users"></i> Places : <?= htmlspecialchars($nbInscrits) ?> / <?= htmlspecialchars($capaciteTotale) ?> (<?= htmlspecialchars($placesDisponibles) ?> disponibles)
                    </div>
                </div>
            </div>
            
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codeEtatAct"><i class="fas fa-info-circle"></i> État de l'activité</label>
                            <select name="codeEtatAct" id="codeEtatAct" class="form-control">
                                <?php
                                $sql = "SELECT * FROM ETAT_ACT";
                                $states = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($states as $state):
                                    ?>
                                    <option value="<?= htmlspecialchars($state['CODEETATACT']) ?>" 
                                            <?= $activite['CODEETATACT'] === $state['CODEETATACT'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($state['NOMETATACT']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="prixAct"><i class="fas fa-euro-sign"></i> Prix de l'activité</label>
                            <input type="number" step="0.01" name="prixAct" id="prixAct" class="form-control" value="<?= htmlspecialchars($activite['PRIXACT']) ?>" required min="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="hrRdvAct"><i class="fas fa-user-clock"></i> Heure de rendez-vous</label>
                            <input type="time" name="hrRdvAct" id="hrRdvAct" class="form-control" value="<?= htmlspecialchars($activite['HRRDVACT']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="hrDebutAct"><i class="fas fa-play"></i> Heure de début</label>
                            <input type="time" name="hrDebutAct" id="hrDebutAct" class="form-control" value="<?= htmlspecialchars($activite['HRDEBUTACT']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="hrFinAct"><i class="fas fa-stop"></i> Heure de fin</label>
                            <input type="time" name="hrFinAct" id="hrFinAct" class="form-control" value="<?= htmlspecialchars($activite['HRFINACT']) ?>" required>
                        </div>
                    </div>
                </div>
                
                <h3 class="mt-4"><i class="fas fa-user-tie"></i> Informations du responsable</h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nomResp">Nom du responsable</label>
                            <input type="text" name="nomResp" id="nomResp" class="form-control" value="<?= htmlspecialchars($activite['NOMRESP']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="prenomResp">Prénom du responsable</label>
                            <input type="text" name="prenomResp" id="prenomResp" class="form-control" value="<?= htmlspecialchars($activite['PRENOMRESP']) ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                    <a href="/animation.php?code=<?= htmlspecialchars($codeAnim) ?>" class="btn btn-secondary ml-2">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <?php if ($nbInscrits > 0): ?>
                    <a href="/encadrant/voir_inscrits.php?codeAnim=<?= htmlspecialchars($codeAnim) ?>&dateAct=<?= htmlspecialchars($dateAct) ?>" class="btn btn-info ml-2">
                        <i class="fas fa-users"></i> Voir les <?= htmlspecialchars($nbInscrits) ?> inscrits
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>