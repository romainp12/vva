<?php
include 'includes/db.php';
include 'includes/header.php';

header('Content-Type: text/html; charset=utf-8');


// Vérifier si le code de l'animation est fourni
if (!isset($_GET['code'])) {
    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Code de l\'animation manquant.</div>';
    include 'includes/footer.php';
    exit;
}

$codeAnim = $_GET['code'];
$success = '';
$error = '';

// Gestion de l'annulation de l'activité
if (isset($_POST['annuler'])) {
    $dateAct = $_POST['dateAct'];
    $codeAct = $_POST['codeAct'];
    $canCancel = true;
    $cancelError = "";

    // Vérifier si l'activité est en cours
    $today = date('Y-m-d');
    if ($canCancel && $dateAct == $today) {
        // Récupérer les heures de l'activité
        $stmtHeures = $pdo->prepare("
            SELECT HRDEBUTACT, HRFINACT 
            FROM ACTIVITE 
            WHERE CODEANIM = :codeAnim AND DATEACT = :dateAct
        ");
        $stmtHeures->execute([
            'codeAnim' => $codeAct,
            'dateAct' => $dateAct,
        ]);
        $activiteHeures = $stmtHeures->fetch(PDO::FETCH_ASSOC);
        
        $currentTime = date('H:i:s');
        if ($currentTime >= $activiteHeures['HRDEBUTACT'] && $currentTime <= $activiteHeures['HRFINACT']) {
            $canCancel = false;
            $cancelError = "Impossible d'annuler une activité en cours.";
        }
    }

    // Vérifier s'il y a des inscrits à cette activité
    if ($canCancel) {
        $stmtInscrits = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INSCRIPTION 
            WHERE CODEANIM = :codeAnim 
            AND DATEACT = :dateAct 
            AND DATEANNULE IS NULL
        ");
        $stmtInscrits->execute([
            'codeAnim' => $codeAct,
            'dateAct' => $dateAct,
        ]);
        $nbInscrits = $stmtInscrits->fetchColumn();
        
        if ($nbInscrits > 0) {
            // Si des personnes sont inscrites, on ne peut pas annuler l'activité
            $canCancel = false;
            $cancelError = "Impossible d'annuler cette activité car il y a " . $nbInscrits . " personne(s) inscrite(s). Veuillez d'abord vous assurer que toutes les personnes se sont désinscrites.";
        }
    }

    // Si on peut annuler l'activité
    if ($canCancel) {
        // Mettre à jour l'état de l'activité
        $stmtAnnuler = $pdo->prepare("
            UPDATE ACTIVITE
            SET DATEANNULEACT = CURDATE(), CODEETATACT = 'E3'
            WHERE CODEANIM = :codeAnim AND DATEACT = :dateAct
        ");
        $stmtAnnuler->execute([
            'codeAnim' => $codeAct,
            'dateAct' => $dateAct,
        ]);

        $success = "L'activité a été annulée avec succès.";
    } else {
        $error = $cancelError;
    }
}

// Si l'utilisateur est connecté et qu'il a soumis le formulaire d'inscription
if (isset($_SESSION['user']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inscription'])) {
    $codeAct = $_POST['codeAct'];
    $userId = $_SESSION['user'];
    $dateAct = $_POST['dateAct'];

    // Vérifier si l'activité existe
    $stmtCheck = $pdo->prepare("
        SELECT 1 FROM ACTIVITE WHERE CODEANIM = :codeAnim AND DATEACT = :dateAct
    ");
    $stmtCheck->execute([
        'codeAnim' => $codeAnim,
        'dateAct' => $dateAct
    ]);
    $activityExists = $stmtCheck->fetchColumn();

    if (!$activityExists) {
        $error = "L'activité sélectionnée n'existe pas.";
    } else {
        // 1. Récupérer les informations de l'utilisateur (dates de séjour et date de naissance)
        $stmtUser = $pdo->prepare("
            SELECT DATEDEBSEJOUR, DATEFINSEJOUR, DATENAISCOMPTE 
            FROM COMPTE 
            WHERE USER = :userId
        ");
        $stmtUser->execute(['userId' => $userId]);
        $userData = $stmtUser->fetch(PDO::FETCH_ASSOC);

        // 2. Récupérer l'âge minimum requis pour l'animation
        $stmtAnimation = $pdo->prepare("
            SELECT LIMITEAGE, NBREPLACEANIM 
            FROM ANIMATION 
            WHERE CODEANIM = :codeAnim
        ");
        $stmtAnimation->execute(['codeAnim' => $codeAct]);
        $animationData = $stmtAnimation->fetch(PDO::FETCH_ASSOC);

        // Vérifier si l'utilisateur a un profil vacancier et si la date de l'activité est dans la période de séjour
        if ($_SESSION['type'] === 'VA' && (!empty($userData['DATEDEBSEJOUR']) || !empty($userData['DATEFINSEJOUR']))) {
            $dateDebut = $userData['DATEDEBSEJOUR'];
            $dateFin = $userData['DATEFINSEJOUR'];
            
            if ($dateAct < $dateDebut || $dateAct > $dateFin) {
                $error = "Inscription impossible : la date de l'activité (" . date('d/m/Y', strtotime($dateAct)) . ") n'est pas comprise dans votre période de séjour (du " . date('d/m/Y', strtotime($dateDebut)) . " au " . date('d/m/Y', strtotime($dateFin)) . ").";
            }
        }

        // Vérifier l'âge minimum requis
        if (empty($error) && !empty($userData['DATENAISCOMPTE']) && !empty($animationData['LIMITEAGE'])) {
            $dateNaissance = new DateTime($userData['DATENAISCOMPTE']);
            $dateActivite = new DateTime($dateAct);
            $age = $dateNaissance->diff($dateActivite)->y;
            
            if ($age < $animationData['LIMITEAGE']) {
                $error = "Inscription impossible : l'âge minimum requis pour cette activité est de " . $animationData['LIMITEAGE'] . " ans. Vous avez actuellement " . $age . " ans.";
            }
        }

        // Si aucune erreur, vérifier le nombre de places disponibles pour cette activité spécifique
        if (empty($error)) {
            // Compter le nombre d'inscrits pour cette activité spécifique
            $stmtCountInscrits = $pdo->prepare("
                SELECT COUNT(*) 
                FROM INSCRIPTION 
                WHERE CODEANIM = :codeAnim 
                AND DATEACT = :dateAct 
                AND DATEANNULE IS NULL
            ");
            $stmtCountInscrits->execute([
                'codeAnim' => $codeAct,
                'dateAct' => $dateAct
            ]);
            $nbInscrits = $stmtCountInscrits->fetchColumn();
            
            // Calculer les places restantes
            $placesRestantes = $animationData['NBREPLACEANIM'] - $nbInscrits;

            if ($placesRestantes <= 0) {
                $error = "Inscription impossible : il n'y a plus de places disponibles pour cette session.";
            } else {
                // Inscrire l'utilisateur à cette activité
                $stmt = $pdo->prepare("
                    INSERT INTO INSCRIPTION (USER, CODEANIM, DATEACT, DATEINSCRIP) 
                    VALUES (:userId, :codeAct, :dateAct, NOW())
                ");
                $stmt->execute([
                    'userId' => $userId,
                    'codeAct' => $codeAct,
                    'dateAct' => $dateAct
                ]);

                $lastInsertId = $pdo->lastInsertId();
                $success = "Inscription réussie ! Votre numéro d'inscription est : " . htmlspecialchars($lastInsertId);
            }
        }
    }
}

// Gestion de la désinscription
if (isset($_POST['desinscription'])) {
    $codeAct = $_POST['codeAct'];
    $dateAct = $_POST['dateAct'];
    $userId = $_SESSION['user'];

    // Mettre à jour la date d'annulation dans la table INSCRIPTION
    $stmtDesinscrire = $pdo->prepare("
        UPDATE INSCRIPTION
        SET DATEANNULE = CURDATE()
        WHERE USER = :userId AND CODEANIM = :codeAnim AND DATEACT = :dateAct AND DATEANNULE IS NULL
    ");
    $stmtDesinscrire->execute([
        'userId' => $userId,
        'codeAnim' => $codeAct,
        'dateAct' => $dateAct,
    ]);

    if ($stmtDesinscrire->rowCount() > 0) {
        $success = "Vous vous êtes désinscrit avec succès.";
    } else {
        $error = "Une erreur s'est produite lors de la désinscription.";
    }
}

// Récupérer les informations de l'animation
$stmtAnim = $pdo->prepare("
    SELECT an.NOMANIM, an.DESCRIPTANIM, ta.NOMTYPEANIM, an.NBREPLACEANIM, an.LIMITEAGE, an.TARIFANIM, an.DIFFICULTEANIM
    FROM ANIMATION an
    JOIN TYPE_ANIM ta ON an.CODETYPEANIM = ta.CODETYPEANIM
    WHERE an.CODEANIM = :codeAnim
");
$stmtAnim->execute(['codeAnim' => $codeAnim]);
$animation = $stmtAnim->fetch(PDO::FETCH_ASSOC);

if (!$animation) {
    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Animation non trouvée.</div>';
    include 'includes/footer.php';
    exit;
}

// Récupérer les activités disponibles
$stmt = $pdo->prepare("
    SELECT a.DATEACT, a.CODEETATACT, a.HRRDVACT, a.PRIXACT, a.HRDEBUTACT, a.HRFINACT, a.CODEANIM, a.PRIXACT, ea.NOMETATACT
    FROM ACTIVITE a
    JOIN ETAT_ACT ea ON a.CODEETATACT = ea.CODEETATACT
    WHERE a.CODEANIM = :codeAnim AND a.CODEETATACT != 'E3' AND a.DATEANNULEACT IS NULL
");
$stmt->execute(['codeAnim' => $codeAnim]);
$activites = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les informations de l'utilisateur pour l'affichage des alertes
$userInfo = null;
if (isset($_SESSION['user']) && $_SESSION['type'] === 'VA') {
    $stmtUserInfo = $pdo->prepare("
        SELECT DATEDEBSEJOUR, DATEFINSEJOUR, DATENAISCOMPTE 
        FROM COMPTE 
        WHERE USER = :userId
    ");
    $stmtUserInfo->execute(['userId' => $_SESSION['user']]);
    $userInfo = $stmtUserInfo->fetch(PDO::FETCH_ASSOC);
}
?>

<meta charset="UTF-8">

<div class="container">
    <!-- Notification messages -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success fade-in">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger fade-in">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Animation details -->
    <meta charset="UTF-8">
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0"><?= htmlspecialchars($animation['NOMANIM']) ?></h1>
                <span class="badge badge-primary"><?= htmlspecialchars($animation['NOMTYPEANIM']) ?></span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h2>Description</h2>
                    <p><?= nl2br(htmlspecialchars($animation['DESCRIPTANIM'])) ?></p>
                    
                    <!-- Afficher alerte si l'utilisateur ne remplit pas les conditions d'âge -->
                    <?php if ($userInfo && isset($animation['LIMITEAGE']) && !empty($userInfo['DATENAISCOMPTE'])): ?>
                        <?php 
                        $dateNaissance = new DateTime($userInfo['DATENAISCOMPTE']);
                        $maintenant = new DateTime();
                        $age = $dateNaissance->diff($maintenant)->y;
                        
                        if ($age < $animation['LIMITEAGE']): 
                        ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Attention : l'âge minimum requis pour cette animation est de <?= htmlspecialchars($animation['LIMITEAGE']) ?> ans. Votre âge actuel est de <?= $age ?> ans.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-item">
                            <i class="fas fa-users"></i>
                            <span>Capacité par session: <strong><?= htmlspecialchars($animation['NBREPLACEANIM']) ?></strong></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-child"></i>
                            <span>Âge minimum: <strong><?= htmlspecialchars($animation['LIMITEAGE']) ?> ans</strong></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-euro-sign"></i>
                            <span>Tarif de base: <strong><?= htmlspecialchars($animation['TARIFANIM']) ?> €</strong></span>
                        </div>
                        <?php if (!empty($animation['DIFFICULTEANIM'])): ?>
                        <div class="info-item">
                            <i class="fas fa-mountain"></i>
                            <span>Difficulté: <strong><?= htmlspecialchars($animation['DIFFICULTEANIM']) ?></strong></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Afficher alerte sur les dates de séjour -->
                    <?php if ($userInfo && !empty($userInfo['DATEDEBSEJOUR']) && !empty($userInfo['DATEFINSEJOUR'])): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i> Votre séjour : du <?= date('d/m/Y', strtotime($userInfo['DATEDEBSEJOUR'])) ?> au <?= date('d/m/Y', strtotime($userInfo['DATEFINSEJOUR'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Activités disponibles -->
    <h2><i class="fas fa-calendar-alt"></i> Sessions disponibles</h2>
    
    <?php if (empty($activites)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Aucune session n'est actuellement programmée pour cette animation.
        </div>
    <?php else: ?>
        <div class="activity-list">
    <?php foreach ($activites as $activite): 
        // Vérifier si l'utilisateur est déjà inscrit
        $isInscribed = false;
        if (isset($_SESSION['user'])) {
            $stmtCheckInscription = $pdo->prepare("
                SELECT 1 FROM INSCRIPTION
                WHERE USER = :userId AND CODEANIM = :codeAnim AND DATEACT = :dateAct AND DATEANNULE IS NULL
            ");
            $stmtCheckInscription->execute([
                'userId' => $_SESSION['user'],
                'codeAnim' => $activite['CODEANIM'],
                'dateAct' => $activite['DATEACT']
            ]);
            $isInscribed = $stmtCheckInscription->fetchColumn();
        }
        
        // Calculer le nombre de places disponibles pour cette session spécifique
        $stmtCountInscrits = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INSCRIPTION 
            WHERE CODEANIM = :codeAnim 
            AND DATEACT = :dateAct 
            AND DATEANNULE IS NULL
        ");
        $stmtCountInscrits->execute([
            'codeAnim' => $activite['CODEANIM'],
            'dateAct' => $activite['DATEACT']
        ]);
        $nbInscrits = $stmtCountInscrits->fetchColumn();
        $placesDisponibles = $animation['NBREPLACEANIM'] - $nbInscrits;
        
        // Vérifier si la date de l'activité est dans la période de séjour
        $isWithinStayPeriod = true;
        $isAgeOk = true;
        if ($userInfo && $_SESSION['type'] === 'VA') {
            // Vérifier la période de séjour
            if (!empty($userInfo['DATEDEBSEJOUR']) && !empty($userInfo['DATEFINSEJOUR'])) {
                $isWithinStayPeriod = ($activite['DATEACT'] >= $userInfo['DATEDEBSEJOUR'] && 
                                      $activite['DATEACT'] <= $userInfo['DATEFINSEJOUR']);
            }
            
            // Vérifier l'âge
            if (!empty($userInfo['DATENAISCOMPTE']) && isset($animation['LIMITEAGE'])) {
                $dateNaissance = new DateTime($userInfo['DATENAISCOMPTE']);
                $dateActivite = new DateTime($activite['DATEACT']);
                $age = $dateNaissance->diff($dateActivite)->y;
                
                $isAgeOk = ($age >= $animation['LIMITEAGE']);
            }
        }
    ?>
        <div class="activity-item fade-in">
            <div class="activity-header d-flex justify-content-between align-items-center">
                <div>Session du <?= htmlspecialchars(date('d/m/Y', strtotime($activite['DATEACT']))) ?></div>
                <span class="badge <?= $activite['CODEETATACT'] === 'E1' ? 'badge-success' : 'badge-warning' ?>">
                    <?= htmlspecialchars($activite['NOMETATACT']) ?>
                </span>
            </div>
            <div class="activity-body">
                <div class="activity-info">
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-clock"></i> Rendez-vous</div>
                        <div class="info-value"><?= htmlspecialchars($activite['HRRDVACT']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-play"></i> Début</div>
                        <div class="info-value"><?= htmlspecialchars($activite['HRDEBUTACT']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-stop"></i> Fin</div>
                        <div class="info-value"><?= htmlspecialchars($activite['HRFINACT']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-euro-sign"></i> Prix</div>
                        <div class="info-value"><?= htmlspecialchars($activite['PRIXACT']) ?> €</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-users"></i> Places disponibles</div>
                        <div class="info-value"><?= htmlspecialchars($placesDisponibles) ?> / <?= htmlspecialchars($animation['NBREPLACEANIM']) ?></div>
                    </div>
                </div>
                
                <div class="activity-actions" style="background:none; box-shadow:none; padding:0; margin:0; display:flex; flex-wrap:wrap; align-items:center; gap:5px;">
                    <?php if (isset($_SESSION['user'])): ?>
                        <?php if ($isInscribed): ?>
                            <form method="POST" style="display:inline; background:none; box-shadow:none; padding:0; margin:0;">
                                <input type="hidden" name="codeAct" value="<?= htmlspecialchars($activite['CODEANIM']) ?>">
                                <input type="hidden" name="dateAct" value="<?= htmlspecialchars($activite['DATEACT']) ?>">
                                <button type="submit" name="desinscription" class="btn btn-danger">
                                    <i class="fas fa-user-minus"></i> Se désinscrire
                                </button>
                            </form>
                        <?php else: ?>
                            <?php if ($_SESSION['type'] === 'VA' && (!$isWithinStayPeriod || !$isAgeOk)): ?>
                                <?php if (!$isWithinStayPeriod): ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> Cette session est en dehors de votre période de séjour.
                                    </div>
                                <?php endif; ?>
                                <?php if (!$isAgeOk): ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> Vous ne remplissez pas les conditions d'âge minimum.
                                    </div>
                                <?php endif; ?>
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-user-plus"></i> Inscription impossible
                                </button>
                            <?php elseif ($placesDisponibles <= 0): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Cette session est complète.
                                </div>
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-user-plus"></i> Session complète
                                </button>
                            <?php else: ?>
                                <form method="POST" style="display:inline; background:none; box-shadow:none; padding:0; margin:0;">
                                    <input type="hidden" name="codeAct" value="<?= htmlspecialchars($activite['CODEANIM']) ?>">
                                    <input type="hidden" name="dateAct" value="<?= htmlspecialchars($activite['DATEACT']) ?>">
                                    <button type="submit" name="inscription" class="btn btn-primary">
                                        <i class="fas fa-user-plus"></i> S'inscrire
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php
                        // Vérifier si l'utilisateur a un profil 'EN' ou 'AD' pour afficher les options d'administration
                        if (isset($_SESSION['type']) && ($_SESSION['type'] === 'EN' || $_SESSION['type'] === 'AD')): ?>
                            <a href="/encadrant/voir_inscrits.php?codeAnim=<?= htmlspecialchars($codeAnim) ?>&dateAct=<?= htmlspecialchars($activite['DATEACT']) ?>" class="btn btn-secondary">
                                <i class="fas fa-users"></i> Voir les inscrits
                            </a>
                            <a href="/encadrant/modifier_activite.php?codeAnim=<?= htmlspecialchars($codeAnim) ?>&dateAct=<?= htmlspecialchars($activite['DATEACT']) ?>" class="btn btn-secondary">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <form method="POST" style="display:inline; background:none; box-shadow:none; padding:0; margin:0;">
                                <input type="hidden" name="codeAct" value="<?= htmlspecialchars($activite['CODEANIM']) ?>">
                                <input type="hidden" name="dateAct" value="<?= htmlspecialchars($activite['DATEACT']) ?>">
                                <button type="submit" name="annuler" class="btn btn-danger">
                                    <i class="fas fa-ban"></i> Annuler la session
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> Veuillez vous <a href="/login.php">connecter</a> pour vous inscrire.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
    <?php endif; ?>
</div>

<style>
    .info-box {
        background-color: var(--gray-light);
        border-radius: var(--border-radius);
        padding: 1.5rem;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .info-item:last-child {
        margin-bottom: 0;
    }
    
    .info-item i {
        font-size: 1.2rem;
        width: 30px;
        color: var(--primary-color);
    }
</style>

<?php include 'includes/footer.php'; ?>
