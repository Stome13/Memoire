<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Si déjà connecté, rediriger selon le rôle
if (isLoggedIn()) {
    redirectByRole();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inscription - PharmaGarde</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="css/variables.css" />
  <link rel="stylesheet" href="css/styles.css" />
  <style>
    .auth-section {
      min-height: calc(100vh - 200px);
      display: flex;
      align-items: center;
      background: linear-gradient(135deg, #f5f7fa 0%, #f9fafc 100%);
    }

    .auth-card {
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-lg);
      border: 1px solid var(--border-color);
    }

    .auth-header {
      text-align: center;
      margin-bottom: 30px;
    }

    .auth-header h2 {
      font-weight: 700;
      color: var(--text-dark);
    }

    .form-section {
      display: none;
    }

    .form-section.active {
      display: block;
      animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .progress-steps {
      display: flex;
      gap: 10px;
      margin-bottom: 30px;
    }

    .progress-item {
      flex: 1;
      height: 8px;
      background: var(--border-color);
      border-radius: 4px;
      transition: all 0.3s ease;
    }

    .progress-item.completed {
      background: var(--success);
    }

    .progress-item.active {
      background: var(--primary);
    }

    .password-strength {
      height: 6px;
      background: #e0e0e0;
      border-radius: 3px;
      margin-top: 8px;
      overflow: hidden;
    }

    .password-strength div {
      height: 100%;
      width: 0;
      transition: all 0.3s ease;
      border-radius: 3px;
    }

    .password-strength.weak div {
      width: 33%;
      background: #ff6b6b;
    }

    .password-strength.medium div {
      width: 66%;
      background: #ffa500;
    }

    .password-strength.strong div {
      width: 100%;
      background: #51cf66;
    }

    .form-divider {
      position: relative;
      text-align: center;
      margin: 20px 0;
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
      background: white;
      padding: 0 10px;
      position: relative;
      color: var(--text-light);
      font-size: 0.85rem;
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

    .form-group {
      margin-bottom: 15px;
    }

    .requirements-list {
      font-size: 0.85rem;
      margin-top: 10px;
    }

    .requirements-list li {
      color: var(--text-light);
      margin-bottom: 5px;
    }

    .requirements-list li.met {
      color: var(--success);
    }

    .requirements-list i {
      margin-right: 5px;
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
            <div class="auth-card bg-white p-5">
              <!-- Progress Bar -->
              <div class="progress-steps">
                <div class="progress-item active" id="step1Progress"></div>
              </div>

              <!-- Step 1: Informations de compte -->
              <div class="form-section active" id="step1">
                <div class="auth-header mb-4">
                  <h2>Créer un compte</h2>
                  <p class="text-muted">Inscrivez-vous pour accéder à votre espace personnel</p>
                </div>

                <div id="alertContainer"></div>

                <form id="step1Form" class="needs-validation" novalidate>
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label for="firstName" class="form-label fw-bold">Prénom</label>
                      <input type="text" class="form-control form-control-lg" id="firstName" placeholder="Jean" required />
                      <div class="invalid-feedback">Le prénom est requis.</div>
                    </div>
                    <div class="col-md-6 form-group">
                      <label for="lastName" class="form-label fw-bold">Nom</label>
                      <input type="text" class="form-control form-control-lg" id="lastName" placeholder="Dupont" required />
                      <div class="invalid-feedback">Le nom est requis.</div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <input type="email" class="form-control form-control-lg" id="email" placeholder="votre@email.com" required />
                    <div class="invalid-feedback">Veuillez entrer un email valide.</div>
                  </div>

                  <div class="form-group">
                    <label for="telephone" class="form-label fw-bold">Téléphone</label>
                    <input type="tel" class="form-control form-control-lg" id="telephone" placeholder="+229 XX XX XX XX" />
                  </div>

                  <div class="form-group">
                    <label for="password" class="form-label fw-bold">Mot de passe</label>
                    <input type="password" class="form-control form-control-lg" id="password" placeholder="••••••••" required />
                    <div class="password-strength" id="passwordStrength">
                      <div></div>
                    </div>
                    <ul class="requirements-list">
                      <li id="req-length"><i class="fas fa-times"></i>Au moins 8 caractères</li>
                      <li id="req-upper"><i class="fas fa-times"></i>Au moins une lettre majuscule</li>
                      <li id="req-number"><i class="fas fa-times"></i>Au moins un chiffre</li>
                    </ul>
                  </div>

                  <div class="form-group">
                    <label for="confirmPassword" class="form-label fw-bold">Confirmer le mot de passe</label>
                    <input type="password" class="form-control form-control-lg" id="confirmPassword" placeholder="••••••••" required />
                    <div class="invalid-feedback">Les mots de passe ne correspondent pas.</div>
                  </div>

                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="terms" required />
                    <label class="form-check-label" for="terms">
                      J'accepte les <a href="#" class="text-decoration-none">conditions d'utilisation</a>
                    </label>
                    <div class="invalid-feedback">Vous devez accepter les conditions.</div>
                  </div>

                  <button type="submit" class="btn btn-primary btn-lg w-100">S'inscrire</button>
                </form>

                <p class="text-center nav-auth">
                  Vous avez déjà un compte? <a href="connexion.php">Se connecter</a>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../../includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const API_BASE = '/PharmaLocal/backend/api';

    document.addEventListener('DOMContentLoaded', function () {
      setupInscriptionForm();
    });

    function setupInscriptionForm() {
      const step1Form = document.getElementById('step1Form');
      const passwordInput = document.getElementById('password');
      const confirmPasswordInput = document.getElementById('confirmPassword');

      // Vérification dynamique du mot de passe
      passwordInput.addEventListener('input', function () {
        checkPasswordStrength(this.value);
        validatePasswords();
      });

      confirmPasswordInput.addEventListener('input', validatePasswords);

      // Step 1: Validation et inscription
      step1Form.addEventListener('submit', async function (e) {
        e.preventDefault();

        step1Form.classList.add('was-validated');

        if (step1Form.checkValidity()) {
          // Vérifier que les mots de passe correspondent
          if (document.getElementById('password').value !== document.getElementById('confirmPassword').value) {
            showAlert('danger', 'Les mots de passe ne correspondent pas.');
            confirmPasswordInput.classList.add('is-invalid');
            return;
          }

          // Vérifier que l'email n'existe pas déjà
          const email = document.getElementById('email').value;
          try {
            const checkResult = await fetch(`${API_BASE}/auth.php?action=checkEmail`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              credentials: 'include',
              body: JSON.stringify({ email: email })
            });
            
            const data = await checkResult.json();
            
            if (!data.available) {
              showAlert('danger', data.message || 'Cet email est déjà utilisé.');
              document.getElementById('email').classList.add('is-invalid');
              return;
            }
          } catch (error) {
            console.error('Erreur vérification email:', error);
            showAlert('danger', 'Erreur de vérification de l\'email');
            return;
          }

          // Procéder à l'inscription
          completeInscription();
        }
      });
    }

    // Vérifier la force du mot de passe
    function checkPasswordStrength(password) {
      const reqLength = document.getElementById('req-length');
      const reqUpper = document.getElementById('req-upper');
      const reqNumber = document.getElementById('req-number');
      const strengthDiv = document.getElementById('passwordStrength');

      let strength = 0;

      if (password.length >= 8) {
        reqLength.classList.add('met');
        strength++;
      } else {
        reqLength.classList.remove('met');
      }

      if (/[A-Z]/.test(password)) {
        reqUpper.classList.add('met');
        strength++;
      } else {
        reqUpper.classList.remove('met');
      }

      if (/[0-9]/.test(password)) {
        reqNumber.classList.add('met');
        strength++;
      } else {
        reqNumber.classList.remove('met');
      }

      strengthDiv.classList.remove('weak', 'medium', 'strong');
      if (strength === 1) strengthDiv.classList.add('weak');
      else if (strength === 2) strengthDiv.classList.add('medium');
      else if (strength === 3) strengthDiv.classList.add('strong');
    }

    function validatePasswords() {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      const confirmPasswordInput = document.getElementById('confirmPassword');

      if (password && confirmPassword && password !== confirmPassword) {
        confirmPasswordInput.classList.add('is-invalid');
      } else {
        confirmPasswordInput.classList.remove('is-invalid');
      }
    }

    async function completeInscription() {
      const firstName = document.getElementById('firstName').value;
      const lastName = document.getElementById('lastName').value;
      const email = document.getElementById('email').value;
      const telephone = document.getElementById('telephone').value || '';
      const password = document.getElementById('password').value;
      const passwordConfirm = document.getElementById('confirmPassword').value;

      try {
        const response = await fetch(`${API_BASE}/auth.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({
            action: 'register',
            prenom: firstName,
            nom: lastName,
            email: email,
            telephone: telephone,
            password: password,
            passwordConfirm: passwordConfirm
          })
        });

        const data = await response.json();

        if (data.success) {
          showAlert('success', 'Inscription réussie! Redirection en cours...');
          localStorage.setItem('pharmaLocal_currentUser', JSON.stringify(data.user));
          
          setTimeout(() => {
            window.location.href = '/PharmaLocal/frontend/users/clients/connexion.php';
          }, 1500);
        } else {
          showAlert('danger', data.error || 'Erreur lors de l\'inscription');
        }
      } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur lors de l\'inscription');
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
