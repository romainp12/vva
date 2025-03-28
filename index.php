<?php
include 'includes/db.php';
include 'includes/header.php';

// Récupérer les animations
$stmt = $pdo->query("SELECT CODEANIM, NOMANIM, DESCRIPTANIM, NBREPLACEANIM, CODETYPEANIM FROM ANIMATION");
$animations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les types d'animations pour les filtres
$stmtTypes = $pdo->query("SELECT CODETYPEANIM, NOMTYPEANIM FROM TYPE_ANIM");
$types = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);
?>

<meta charset="UTF-8">

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Découvrez nos animations</h1>
        
        <!-- Filtre des animations -->
        <div class="filter-container">
            <select id="animationFilter" class="form-control">
                <option value="all">Tous les types</option>
                <?php foreach ($types as $type): ?>
                    <option value="<?= htmlspecialchars($type['CODETYPEANIM']) ?>">
                        <?= htmlspecialchars($type['NOMTYPEANIM']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Liste des animations -->
    <ul class="animations-list" id="animationsList">
        <?php foreach ($animations as $animation): ?>
            <li data-type="<?= htmlspecialchars($animation['CODETYPEANIM']) ?>">
                <div class="animation-card">
                    <div class="animation-title">
                        <?= htmlspecialchars($animation['NOMANIM']) ?>
                    </div>
                    <div class="animation-content">
                        <p class="mb-2">
                            <?= htmlspecialchars(substr($animation['DESCRIPTANIM'], 0, 100)) . (strlen($animation['DESCRIPTANIM']) > 100 ? '...' : '') ?>
                        </p>
                        <div class="mt-2">
                            <span class="badge badge-primary">
                                Places: <?= htmlspecialchars($animation['NBREPLACEANIM']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="animation-footer">
                        <a href="/animation.php?code=<?= htmlspecialchars($animation['CODEANIM']) ?>" class="animation-link">
                            Voir les détails
                        </a>
                        
                        <?php if (isset($_SESSION['type']) && in_array($_SESSION['type'], ['AD', 'EN'])): ?>
                            <a href="encadrant/modifier_animation.php?codeAnim=<?= urlencode($animation['CODEANIM']) ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script>
    // Filtrage des animations par type
    document.getElementById('animationFilter').addEventListener('change', function() {
        const selectedType = this.value;
        const animationItems = document.querySelectorAll('#animationsList li');
        
        animationItems.forEach(item => {
            if (selectedType === 'all' || item.dataset.type === selectedType) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>