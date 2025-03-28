<?php
include '../includes/db.php';
include '../includes/header.php';
include_once '../includes/functions.php';

if (!is_encadrant() && !is_admin()) {
    header('Location: /index.php');
    exit;
}

$error = '';
$success = '';

// Récupérer la liste des animations
$stmt = $pdo->prepare("SELECT CODEANIM, NOMANIM FROM ANIMATION");
$stmt->execute();
$animations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les informations de l'utilisateur connecté
$nomResp = $_SESSION['user'] ? $_SESSION['NOMCOMPTE'] : '';
$prenomResp = $_SESSION['user'] ? $_SESSION['PRENOMCOMPTE'] : '';

// Si les données de l'utilisateur ne sont pas dans la session, les récupérer depuis la base de données
if (empty($nomResp) || empty($prenomResp)) {
    $stmt = $pdo->prepare("SELECT NOMCOMPTE, PRENOMCOMPTE FROM COMPTE WHERE USER = :user");
    $stmt->execute(['user' => $_SESSION['user']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userData) {
        $nomResp = $userData['NOMCOMPTE'];
        $prenomResp = $userData['PRENOMCOMPTE'];
        
        // Mettre à jour la session avec ces informations
        $_SESSION['NOMCOMPTE'] = $nomResp;
        $_SESSION['PRENOMCOMPTE'] = $prenomResp;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codeAnim = $_POST['codeAnim'];
    $dateAct = $_POST['dateAct'];
    $heureDebut = $_POST['heureDebut'];
    $heureFin = $_POST['heureFin'];
    $prixAct = $_POST['prixAct']; 

    // Définir HRRDVACT (Rendez-vous) égale à HRDEBUTACT
    $hrRdvAct = $heureDebut;

    // Vérification si une activité pour cette animation et cette date existe déjà
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM ACTIVITE 
        WHERE CODEANIM = :codeAnim AND DATEACT = :dateAct
    ");
    $stmt->execute(['codeAnim' => $codeAnim, 'dateAct' => $dateAct]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        $error = "Une activité pour cette animation à cette date existe déjà.";
    } else {
        // Insertion de la nouvelle activité dans la base de données
        $stmt = $pdo->prepare("
            INSERT INTO ACTIVITE (CODEANIM, DATEACT, HRDEBUTACT, HRFINACT, HRRDVACT, CODEETATACT, NOMRESP, PRENOMRESP, PRIXACT) 
            VALUES (:codeAnim, :dateAct, :heureDebut, :heureFin, :hrRdvAct, 'E1', :nomResp, :prenomResp, :prixAct)
        ");
        $stmt->execute([
            'codeAnim' => $codeAnim,
            'dateAct' => $dateAct,
            'heureDebut' => $heureDebut,
            'heureFin' => $heureFin,
            'hrRdvAct' => $hrRdvAct,
            'nomResp' => $nomResp,
            'prenomResp' => $prenomResp,
            'prixAct' => $prixAct
        ]);
        $success = "Activité ajoutée avec succès.";
    }
}
?>
<meta charset="UTF-8">
<div class="container">
    <div class="card mb-4">
        <div class="card-header">
            <h1 class="mb-0"><i class="fas fa-calendar-plus"></i> Ajouter une activité</h1>
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
                            <label for="codeAnim"><i class="fas fa-list-alt"></i> Animation</label>
                            <select name="codeAnim" id="codeAnim" class="form-control" required>
                                <option value="">Choisir une animation</option>
                                <?php foreach ($animations as $animation): ?>
                                    <option value="<?= htmlspecialchars($animation['CODEANIM']) ?>">
                                        <?= htmlspecialchars($animation['NOMANIM']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="dateAct"><i class="fas fa-calendar-day"></i> Date de l'activité</label>
                            <input type="date" name="dateAct" id="dateAct" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="prixAct"><i class="fas fa-euro-sign"></i> Prix de l'activité</label>
                            <input type="number" name="prixAct" id="prixAct" class="form-control" required step="0.01" min="0" placeholder="Prix de l'activité">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="heureDebut"><i class="fas fa-play"></i> Heure de début</label>
                            <input type="time" name="heureDebut" id="heureDebut" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="heureFin"><i class="fas fa-stop"></i> Heure de fin</label>
                            <input type="time" name="heureFin" id="heureFin" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-info-circle"></i> Note</label>
                            <div class="alert alert-info mb-0">
                                L'heure de rendez-vous sera automatiquement définie à l'heure de début.
                            </div>
                        </div>
                    </div>
                </div>
                
                <h3 class="mt-4"><i class="fas fa-user-tie"></i> Informations du responsable</h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nomRespDisplay">Nom du responsable</label>
                            <input type="text" id="nomRespDisplay" class="form-control" value="<?= htmlspecialchars($nomResp) ?>" readonly>
                            <!-- Champ caché pour le nom -->
                            <input type="hidden" name="nomResp" value="<?= htmlspecialchars($nomResp) ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="prenomRespDisplay">Prénom du responsable</label>
                            <input type="text" id="prenomRespDisplay" class="form-control" value="<?= htmlspecialchars($prenomResp) ?>" readonly>
                            <!-- Champ caché pour le prénom -->
                            <input type="hidden" name="prenomResp" value="<?= htmlspecialchars($prenomResp) ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Ajouter l'activité
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
    // Définir la date par défaut à aujourd'hui
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('dateAct').value = today;
        
        // Heure par défaut (optionnel)
        document.getElementById('heureDebut').value = '09:00';
        document.getElementById('heureFin').value = '11:00';
    });
</script>

<?php include '../includes/footer.php'; ?>