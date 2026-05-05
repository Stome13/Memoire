<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Si déjà connecté, rediriger selon le rôle
if (isLoggedIn()) {
    redirectByRole();
}

// Récupérer le message de login si présent
$loginMessage = getLoginMessage();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Connexion - PharmaGarde</title>
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

    .form-divider {
      position: relative;
      text-align: center;
      margin: 30px 0;
    }

    .form-divider::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 1px;
      background: var(--border-color);
    }

    .form-divider span {
      background: var(--bg-white);
      padding: 0 15px;
      position: relative;
      color: var(--text-light);
      font-size: 0.9rem;
    }

    .social-auth {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }

    .social-auth button {
      flex: 1;
      padding: 10px;
      border: 1px solid var(--border-color);
      background: var(--bg-white);
      border-radius: 8px;
      cursor: pointer;
      transition: var(--transition);
      font-size: 0.9rem;
    }

    .social-auth button:hover {
      border-color: var(--primary);
      background: var(--bg-light);
    }

    .nav-auth {
      text-align: center;
      margin-top: 20px;
    }

    .nav-auth a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
    }

    .nav-auth a:hover {
      text-decoration: underline;
    }

    .tab-content {
      padding: 0;
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
              <div id="alertContainer"></div>
              
              <?php if ($loginMessage): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                  <i class="fas fa-info-circle me-2"></i><?php echo escape($loginMessage); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              <?php endif; ?>

              <!-- Login Tab -->
              <div class="tab-content" id="authTabsContent">
                <div class="tab-pane fade show active" id="login" role="tabpanel">
                  <div class="auth-header mb-4">
                    <h2>Bienvenue</h2>
                    <p class="text-muted">Connectez-vous pour accéder à votre compte</p>
                  </div>
                  <form id="loginForm" class="needs-validation" novalidate>
                    <div class="mb-3">
                      <label for="login-email" class="form-label fw-bold">Email</label>
                      <input type="email" class="form-control form-control-lg" id="login-email" placeholder="votre@email.com" required />
                      <div class="invalid-feedback">Veuillez entrer un email valide.</div>
                    </div>

                    <div class="mb-3">
                      <label for="login-password" class="form-label fw-bold">Mot de passe</label>
                      <input type="password" class="form-control form-control-lg" id="login-password" placeholder="••••••••" required />
                      <div class="invalid-feedback">Veuillez entrer votre mot de passe.</div>
                    </div>

                    <div class="form-check mb-3">
                      <input class="form-check-input" type="checkbox" id="remember-me" />
                      <label class="form-check-label" for="remember-me">
                        Se souvenir de moi
                      </label>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 my-3">
                      <i class="fas fa-sign-in-alt me-2"></i>Connexion
                    </button>

                    <p class="text-center nav-auth">
                      Vous n'avez de compte? <a href="inscription.php">S'inscrire</a>
                    </p>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../../includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/auth.js"></script>
  <script>
    // Gestion du formulaire de connexion côté client avec API
    document.addEventListener('DOMContentLoaded', function () {
      const loginForm = document.getElementById('loginForm');
      
      if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
      }
    });

    function handleLogin(e) {
      e.preventDefault();

      const email = document.getElementById('login-email').value.trim();
      const password = document.getElementById('login-password').value;
      const rememberMe = document.getElementById('remember-me').checked;

      if (!email || !password) {
        showAlert('danger', 'Veuillez remplir tous les champs.');
        return;
      }

      // Appel API à la connexion
      fetch('/PharmaLocal/backend/api/auth.php', {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
          action: 'login',
          email: email,
          password: password
        })
      })
      .then(response => response.json())
      .then(result => {
        if (result.success) {
          if (rememberMe) {
            localStorage.setItem('pharmaLocal_rememberMe', 'true');
          }

          localStorage.setItem('pharmaLocal_currentUser', JSON.stringify(result.user));
          showAlert('success', 'Connexion réussie! Redirection en cours...');

          setTimeout(() => {
            // Redirection PHP côté serveur
            redirectByRole(result.user.role);
          }, 1500);
        } else {
          showAlert('danger', result.error || 'Erreur lors de la connexion');
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur lors de la connexion');
      });
    }

    function redirectByRole(role) {
      if (role === 'admin') {
        window.location.href = '/PharmaLocal/frontend/users/Admin/dashboard.php';
      } else if (role === 'pharmacie') {
        window.location.href = '/PharmaLocal/frontend/users/pharmacien/dashboard.php';
      } else {
        window.location.href = '/PharmaLocal/frontend/users/clients/profil.php';
      }
    }

    function showAlert(type, message) {
      const container = document.getElementById('alertContainer');
      const alertDiv = document.createElement('div');
      alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
      alertDiv.setAttribute('role', 'alert');
      alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      `;
      container.appendChild(alertDiv);
    }
  </script>
</body>
</html>
