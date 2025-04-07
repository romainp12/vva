<?php
include '../includes/header.php';
include_once '../includes/functions.php';
require_once '../includes/db.php';

if (!is_encadrant() && !is_admin()) {
    header('Location: /index.php');
    exit;
}

if (!isset($_GET['codeAnim'])) {
    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Aucune animation spécifiée.</div>';
    include '../includes/footer.php';
    exit();
}

$codeAnim = $_GET['codeAnim'];

// Récupérer les informations actuelles de l'animation
$sql = "SELECT CODEANIM, NOMANIM, DESCRIPTANIM, CODETYPEANIM, DATEVALIDITEANIM, DUREEANIM, LIMITEAGE, TARIFANIM, 
                NBREPLACEANIM, COMMENTANIM, DIFFICULTEANIM 
        FROM ANIMATION WHERE CODEANIM = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$codeAnim]);
$animation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$animation) {
    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Animation introuvable.</div>';
    include '../includes/footer.php';
    exit();
}

// Récupérer les types d'animations pour la liste déroulante
$sqlTypes = "SELECT CODETYPEANIM, NOMTYPEANIM FROM TYPE_ANIM";
$stmtTypes = $pdo->query($sqlTypes);
$typesAnim = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomAnim = $_POST['nomAnim'];
    $descAnim = $_POST['descAnim'];
    $typeAnim = $_POST['typeAnimation'];
    $dateValiditeAnim = $_POST['dateValiditeAnim'];
    $dureeAnim = $_POST['dureeAnim'];
    $limiteAge = $_POST['limiteAge'];
    $tarifAnim = $_POST['tarifAnim'];
    $nbrePlaceAnim = $_POST['nbrePlaceAnim'];
    $commentAnim = $_POST['commentAnim'];
    $difficulteAnim = $_POST['difficulteAnim'];

    $sql = "
        UPDATE ANIMATION
        SET 
            NOMANIM = ?, 
            DESCRIPTANIM = ?, 
            CODETYPEANIM = ?, 
            DATEVALIDITEANIM = ?, 
            DUREEANIM = ?, 
            LIMITEAGE = ?, 
            TARIFANIM = ?, 
            NBREPLACEANIM = ?, 
            COMMENTANIM = ?, 
            DIFFICULTEANIM = ?
        WHERE CODEANIM = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $nomAnim, $descAnim, $typeAnim, $dateValiditeAnim, $dureeAnim, $limiteAge,
        $tarifAnim, $nbrePlaceAnim, $commentAnim, $difficulteAnim, $codeAnim
    ]);

    $successMessage = "L'animation a été mise à jour avec succès.";
    
    // Recharger les données après la mise à jour
    $stmt = $pdo->prepare($sql = "SELECT CODEANIM, NOMANIM, DESCRIPTANIM, CODETYPEANIM, DATEVALIDITEANIM, DUREEANIM, LIMITEAGE, TARIFANIM, 
                NBREPLACEANIM, COMMENTANIM, DIFFICULTEANIM 
        FROM ANIMATION WHERE CODEANIM = ?");
    $stmt->execute([$codeAnim]);
    $animation = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<meta charset="UTF-8">

<div class="container">
    <div class="card mb-4">
        <div class="card-header">
            <h1 class="mb-0"><i class="fas fa-edit"></i> Modifier une animation</h1>
        </div>
        <div class="card-body">
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success fade-in">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($successMessage) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nomAnim"><i class="fas fa-tag"></i> Nom de l'animation</label>
                            <input type="text" name="nomAnim" id="nomAnim" class="form-control" value="<?= htmlspecialchars($animation['NOMANIM']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="descAnim"><i class="fas fa-align-left"></i> Description</label>
                            <textarea name="descAnim" id="descAnim" class="form-control" rows="5" required><?= htmlspecialchars($animation['DESCRIPTANIM']) ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="typeAnimation"><i class="fas fa-list"></i> Type d'animation</label>
                            <select name="typeAnimation" id="typeAnimation" class="form-control" required>
                                <?php foreach ($typesAnim as $type): ?>
                                    <option value="<?= htmlspecialchars($type['CODETYPEANIM']) ?>"
                                        <?= $type['CODETYPEANIM'] === $animation['CODETYPEANIM'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['NOMTYPEANIM']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="dateValiditeAnim"><i class="fas fa-calendar-check"></i> Date de validité</label>
                            <input type="date" name="dateValiditeAnim" id="dateValiditeAnim" class="form-control" value="<?= htmlspecialchars($animation['DATEVALIDITEANIM']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="dureeAnim"><i class="fas fa-clock"></i> Durée (en heures)</label>
                            <input type="number" name="dureeAnim" id="dureeAnim" class="form-control" value="<?= htmlspecialchars($animation['DUREEANIM']) ?>" step="0.1" min="0.1">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="limiteAge"><i class="fas fa-child"></i> Limite d'âge</label>
                            <input type="number" name="limiteAge" id="limiteAge" class="form-control" value="<?= htmlspecialchars($animation['LIMITEAGE']) ?>" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="tarifAnim"><i class="fas fa-euro-sign"></i> Tarif</label>
                            <input type="number" step="0.01" name="tarifAnim" id="tarifAnim" class="form-control" value="<?= htmlspecialchars($animation['TARIFANIM']) ?>" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="nbrePlaceAnim"><i class="fas fa-users"></i> Nombre de places</label>
                            <input type="number" name="nbrePlaceAnim" id="nbrePlaceAnim" class="form-control" value="<?= htmlspecialchars($animation['NBREPLACEANIM']) ?>" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="difficulteAnim"><i class="fas fa-mountain"></i> Difficulté</label>
                            <input type="text" name="difficulteAnim" id="difficulteAnim" class="form-control" value="<?= htmlspecialchars($animation['DIFFICULTEANIM']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="commentAnim"><i class="fas fa-comment"></i> Commentaire</label>
                            <textarea name="commentAnim" id="commentAnim" class="form-control" rows="4"><?= htmlspecialchars($animation['COMMENTANIM']) ?></textarea>
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
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
