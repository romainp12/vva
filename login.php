<?php
include 'includes/db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT USER, TYPEPROFIL FROM COMPTE WHERE USER = :username AND MDP = :password");
    $stmt->execute(['username' => $username, 'password' => $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user'] = $user['USER'];
        $_SESSION['type'] = $user['TYPEPROFIL'];
        header('Location: /index.php');
        exit;
    } else {
        $error = "Identifiants incorrects.";
    }
}
?>

<meta charset="UTF-8">

<div class="container">
    <div class="card mb-4 mx-auto" style="max-width: 500px;">
        <div class="card-header text-center">
            <h1 class="mb-0">Connexion</h1>
        </div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="post" class="mt-3">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> Nom d'utilisateur
                    </label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Mot de passe
                    </label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
