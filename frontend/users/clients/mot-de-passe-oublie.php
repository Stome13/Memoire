<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../../backend/includes/db.php';

// Si déjà connecté, rediriger
if (isLoggedIn()) {
    redirectByRole();
}

$message = '';
$messageType = '';
$email = '';
$successMessage = false;

// Traiter le changement de mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $newPassword = trim($_POST['new-password'] ?? '');
    $confirmPassword = trim($_POST['confirm-password'] ?? '');
    
    if (empty($email) || empty($newPassword) || empty($confirmPassword)) {
        $message = "Veuillez remplir tous les champs.";
        $messageType = 'danger';
    } elseif ($newPassword !== $confirmPassword) {
        $message = "Les mots de passe ne correspondent pas.";
        $messageType = 'danger';
    } elseif (strlen($newPassword) < 6) {
        $message = "Le mot de passe doit contenir au moins 6 caractères.";
        $messageType = 'danger';
    } else {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Vérifier que l'email existe
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND role = 'client'");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $message = "Aucun compte trouvé avec cet email.";
                $messageType = 'danger';
            } else {
                // Mettre à jour le mot de passe
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashedPassword, $user['id']]);
                
                $message = "Mot de passe changé avec succès! Vous pouvez maintenant vous connecter.";
                $messageType = 'success';
                $successMessage = true;
            }
        } catch (Exception $e) {
            $message = "Erreur serveur. Veuillez réessayer.";
            $messageType = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Changer Mot de Passe - PharmaGarde</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="css/variables.css" />
  <link rel="stylesheet" href="css/styles.css" />
  <style>
    .auth-section {
      min-height: calc(100vh - 200px);
      display: flex;
      align-items: center;
    }

    .auth-card {
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-lg);
    }

    .auth-header {
      text-align: center;
      margin-bottom: 30px;
    }

    .auth-header h2 {
      font-weight: 700;
      color: var(--text-dark);
    }

    .nav-auth a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
    }

    .nav-auth a:hover {
      text-decoration: underline;
    }

    .reset-code {
      background-color: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      font-family: monospace;
      text-align: center;
      margin: 20px 0;
      border: 1px solid var(--border-color);
    }

    .success-icon {
      font-size: 3rem;
      color: #28a745;
      margin: 20px 0;
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <?php include __DIR__ . '/../../includes/nav-client.php'; ?>

  <main>
    <section class="auth-section">
      <div class="container-lg">
        <div class="row justify-content-center">
          <div class="col-lg-5">
            <div class="auth-card bg-white p-5 my-5">
              <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                  <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                  <?php echo escape($message); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              <?php endif; ?>

              <?php if (!$successMessage): ?>
                <!-- Formulaire de changement de mot de passe -->
                <div class="auth-header mb-4">
                  <h2><i class="fas fa-lock me-2"></i>Changer votre mot de passe</h2>
                  <p class="text-muted">Entrez votre email et votre nouveau mot de passe</p>
                </div>

                <form method="POST" class="needs-validation" novalidate>
                  <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="votre@email.com" value="<?php echo escape($email); ?>" required />
                    <div class="invalid-feedback">Veuillez entrer un email valide.</div>
                  </div>

                  <div class="mb-3">
                    <label for="new-password" class="form-label fw-bold">Nouveau mot de passe</label>
                    <input type="password" class="form-control form-control-lg" id="new-password" name="new-password" placeholder="••••••••" required />
                    <small class="text-muted">Minimum 6 caractères</small>
                    <div class="invalid-feedback">Veuillez entrer un mot de passe.</div>
                  </div>

                  <div class="mb-3">
                    <label for="confirm-password" class="form-label fw-bold">Confirmer le mot de passe</label>
                    <input type="password" class="form-control form-control-lg" id="confirm-password" name="confirm-password" placeholder="••••••••" required />
                    <div class="invalid-feedback">Veuillez confirmer le mot de passe.</div>
                  </div>

                  <button type="submit" class="btn btn-success btn-lg w-100 my-3">
                    <i class="fas fa-check me-2"></i>Changer le mot de passe
                  </button>

                  <p class="text-center nav-auth">
                    <a href="connexion.php"><i class="fas fa-arrow-left me-1"></i>Retour à la connexion</a>
                  </p>
                </form>

              <?php else: ?>
                <!-- Message de succès -->
                <div class="text-center">
                  <i class="fas fa-check-circle success-icon"></i>
                  <h2>Succès!</h2>
                  <p class="text-muted mb-4">Votre mot de passe a été changé avec succès.</p>

                  <a href="connexion.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../../includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Validation Bootstrap
    (function () {
      'use strict';
      window.addEventListener('load', function () {
        const forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function (form) {
          form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
    })();
  </script>
</body>
</html>
