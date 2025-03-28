<?php
include '../includes/db.php';
include '../includes/header.php';
include_once '../includes/functions.php';

if (!is_encadrant() && !is_admin()) {
    header('Location: /index.php');
    exit;
}

// Récupérer les types d'animations
$stmt = $pdo->query("SELECT CODETYPEANIM, NOMTYPEANIM FROM TYPE_ANIM");
$typesAnim = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $codeAnim = $_POST['codeAnim'];
    $nomAnim = $_POST['nomAnim'];
    $description = $_POST['description'];
    $nbPlaces = $_POST['nbPlaces'];
    $typeAnim = $_POST['typeAnim'];
    $limiteAge = $_POST['limiteAge'];
    $tarifAnim = $_POST['tarifAnim'];
    $dateCreation = $_POST['dateCreation'];
    $dateValidite = $_POST['dateValidite'];
    $dureeAnim = $_POST['dureeAnim'];
    $commentAnim = $_POST['commentAnim'];
    $difficulteAnim = $_POST['difficulteAnim'];

    // Vérifier si l'animation existe déjà (basée sur NOMANIM uniquement)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ANIMATION WHERE NOMANIM = :nomAnim");
    $stmt->execute(['nomAnim' => $nomAnim]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        $error = "Une animation avec ce nom existe déjà.";
    } else {
        // Insertion de l'animation
        $stmt = $pdo->prepare("INSERT INTO ANIMATION (CODEANIM, CODETYPEANIM, NOMANIM, DATECREATIONANIM, DATEVALIDITEANIM, DUREEANIM, LIMITEAGE, TARIFANIM, NBREPLACEANIM, DESCRIPTANIM, COMMENTANIM, DIFFICULTEANIM) 
            VALUES (:codeAnim, :typeAnim, :nomAnim, :dateCreation, :dateValidite, :dureeAnim, :limiteAge, :tarifAnim, :nbPlaces, :description, :commentAnim, :difficulteAnim)");
        $stmt->execute([
            'codeAnim' => $codeAnim,
            'typeAnim' => $typeAnim,
            'nomAnim' => $nomAnim,
            'dateCreation' => $dateCreation,
            'dateValidite' => $dateValidite,
            'dureeAnim' => $dureeAnim,
            'limiteAge' => $limiteAge,
            'tarifAnim' => $tarifAnim,
            'nbPlaces' => $nbPlaces,
            'description' => $description,
            'commentAnim' => $commentAnim,
            'difficulteAnim' => $difficulteAnim
        ]);
        $success = "Animation ajoutée avec succès.";
    }
}
?>
<meta charset="UTF-8">

<div class="container">
    <div class="card mb-4">
        <div class="card-header">
            <h1 class="mb-0"><i class="fas fa-plus-circle"></i> Ajouter une animation</h1>
        </div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger fade-in">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success fade-in">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codeAnim"><i class="fas fa-code"></i> Code de l'animation</label>
                            <input type="text" name="codeAnim" id="codeAnim" class="form-control" maxlength="8" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nomAnim"><i class="fas fa-tag"></i> Nom de l'animation</label>
                            <input type="text" name="nomAnim" id="nomAnim" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="typeAnim"><i class="fas fa-list"></i> Type d'animation</label>
                            <select name="typeAnim" id="typeAnim" class="form-control" required>
                                <?php foreach ($typesAnim as $type): ?>
                                    <option value="<?= htmlspecialchars($type['CODETYPEANIM']) ?>"><?= htmlspecialchars($type['NOMTYPEANIM']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="description"><i class="fas fa-align-left"></i> Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nbPlaces"><i class="fas fa-users"></i> Nombre de places</label>
                            <input type="number" name="nbPlaces" id="nbPlaces" class="form-control" min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="limiteAge"><i class="fas fa-child"></i> Limite d'âge</label>
                            <input type="number" name="limiteAge" id="limiteAge" class="form-control" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="tarifAnim"><i class="fas fa-euro-sign"></i> Tarif de l'animation</label>
                            <input type="number" name="tarifAnim" id="tarifAnim" class="form-control" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="dureeAnim"><i class="fas fa-clock"></i> Durée (en heures)</label>
                            <input type="number" name="dureeAnim" id="dureeAnim" class="form-control" step="0.1" min="0.1" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dateCreation"><i class="fas fa-calendar-plus"></i> Date de création</label>
                            <input type="date" name="dateCreation" id="dateCreation" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dateValidite"><i class="fas fa-calendar-check"></i> Date de validité</label>
                            <input type="date" name="dateValidite" id="dateValidite" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="difficulteAnim"><i class="fas fa-mountain"></i> Difficulté</label>
                            <input type="text" name="difficulteAnim" id="difficulteAnim" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="commentAnim"><i class="fas fa-comment"></i> Commentaire</label>
                            <textarea name="commentAnim" id="commentAnim" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Ajouter l'animation
                    </button>
                    <a href="/index.php" class="btn btn-secondary ml-2">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto-generation de la date de création comme aujourd'hui
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('dateCreation').value = today;
        
        // Définir une date de validité par défaut (par exemple, un an à partir d'aujourd'hui)
        const nextYear = new Date();
        nextYear.setFullYear(nextYear.getFullYear() + 1);
        document.getElementById('dateValidite').value = nextYear.toISOString().split('T')[0];
    });
</script>

<?php include '../includes/footer.php'; ?>