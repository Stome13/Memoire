// ========================================
// GESTION DE L'INSCRIPTION MULTI-ÉTAPES
// ========================================

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

    if (!step1Form.checkValidity() === false) {
      e.stopPropagation();
    }

    step1Form.classList.add('was-validated');

    if (step1Form.checkValidity()) {
      // Vérifier que les mots de passe correspondent
      if (document.getElementById('password').value !== document.getElementById('confirmPassword').value) {
        showAlert('danger', 'Les mots de passe ne correspondent pas.');
        confirmPasswordInput.classList.add('is-invalid');
        return;
      }

      // Vérifier que l'email n'existe pas déjà (côté serveur)
      const email = document.getElementById('email').value;
      try {
        const checkResult = await fetch(`${API_BASE}/auth.php?action=checkEmail`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'include',
          body: JSON.stringify({ email: email })
        });
        
        if (!checkResult.ok) {
          throw new Error(`HTTP error! status: ${checkResult.status}`);
        }
        
        const data = await checkResult.json();
        console.log('Email check response:', data);
        
        if (!data.available) {
          showAlert('danger', data.message || 'Cet email est déjà utilisé.');
          document.getElementById('email').classList.add('is-invalid');
          return;
        }
      } catch (error) {
        console.error('Erreur vérification email:', error);
        showAlert('danger', 'Erreur de vérification de l\'email: ' + error.message);
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

  // Vérifier la longueur
  if (password.length >= 6) {
    reqLength.classList.add('met');
    strength++;
  } else {
    reqLength.classList.remove('met');
  }

  // Vérifier une lettre majuscule
  if (/[A-Z]/.test(password)) {
    reqUpper.classList.add('met');
    strength++;
  } else {
    reqUpper.classList.remove('met');
  }

  // Vérifier un chiffre
  if (/\d/.test(password)) {
    reqNumber.classList.add('met');
    strength++;
  } else {
    reqNumber.classList.remove('met');
  }

  // Mettre à jour la force visuelle
  strengthDiv.classList.remove('weak', 'medium', 'strong');
  if (strength < 2) {
    strengthDiv.classList.add('weak');
  } else if (strength < 3) {
    strengthDiv.classList.add('medium');
  } else {
    strengthDiv.classList.add('strong');
  }
}

// Valider que les mots de passe correspondent
function validatePasswords() {
  const password = document.getElementById('password').value;
  const confirmPassword = document.getElementById('confirmPassword').value;
  const confirmPasswordInput = document.getElementById('confirmPassword');

  if (confirmPassword && password !== confirmPassword) {
    confirmPasswordInput.classList.add('is-invalid');
  } else {
    confirmPasswordInput.classList.remove('is-invalid');
  }
}

// Afficher une alerte
function showAlert(type, message) {
  const alertDiv = document.createElement('div');
  alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
  alertDiv.role = 'alert';
  alertDiv.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  `;
  
  const container = document.querySelector('.auth-card') || document.body;
  container.insertBefore(alertDiv, container.firstChild);
  
  if (type === 'success') {
    setTimeout(() => alertDiv.remove(), 3000);
  }
}

// Finaliser l'inscription (asynchrone avec API)
async function completeInscription() {
  const userData = {
    firstName: document.getElementById('firstName').value,
    lastName: document.getElementById('lastName').value,
    email: document.getElementById('email').value,
    password: document.getElementById('password').value,
    phone: document.getElementById('telephone')?.value || '',
    city: document.getElementById('city')?.value || '',
    address: document.getElementById('adresse')?.value || '',
    dateOfBirth: document.getElementById('dateOfBirth')?.value || '',
    allergies: document.getElementById('allergies')?.value || ''
  };

  console.log('Appel completeInscription avec:', userData);

  try {
    // Vérifier que authManager existe
    if (typeof authManager === 'undefined') {
      showAlert('danger', 'Erreur: authManager non disponible');
      console.error('authManager n\'est pas défini');
      return;
    }

    // Enregistrer l'utilisateur via l'API
    const result = await authManager.register(userData);
    
    console.log('Résultat inscription:', result);

    if (result.success) {
      showAlert('success', 'Inscription réussie! Redirection en cours...');

      setTimeout(() => {
        window.location.href = 'connexion.html';
      }, 2000);
    } else {
      showAlert('danger', result.message || 'Erreur lors de l\'inscription');
    }
  } catch (error) {
    console.error('Erreur lors de l\'inscription:', error);
    showAlert('danger', 'Erreur: ' + (error.message || 'Erreur de connexion au serveur'));
  }
}
