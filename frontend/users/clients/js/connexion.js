// ========================================
// GESTION DE LA PAGE CONNEXION
// ========================================

document.addEventListener('DOMContentLoaded', function () {
  // Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord approprié
  if (authManager.isLoggedIn()) {
    const currentUser = authManager.getCurrentUser();
    console.log('User already logged in:', currentUser); // Debug
    
    if (currentUser && currentUser.role) {
      console.log('Redirecting based on role:', currentUser.role); // Debug
      redirectByRole(currentUser.role);
    } else {
      console.log('No role found, redirecting to profil.html'); // Debug
      window.location.href = 'profil.html';
    }
    return;
  }

  setupLoginForm();
});

function redirectByRole(role) {
  console.log('[redirectByRole] Role:', role);
  
  // Redirection vers les pages PHP
  let redirectUrl;
  if (role === 'admin') {
    redirectUrl = '../Admin/dashboard.php';
  } else if (role === 'pharmacie') {
    redirectUrl = '../pharmacien/dashboard.php';
  } else {
    // client
    redirectUrl = 'profil.php';
  }
  
  console.log('[redirectByRole] Redirecting to:', redirectUrl);
  window.location.href = redirectUrl;
}

function setupLoginForm() {
  const loginForm = document.getElementById('loginForm');
  const emailInput = document.getElementById('login-email');
  const passwordInput = document.getElementById('login-password');
  const loginBtn = document.querySelector('a[href="#"].btn.btn-success');

  if (loginForm) {
    loginForm.addEventListener('submit', handleLogin);

    // Cliquer sur le bouton Connexion (qui est actuellement un lien)
    if (loginBtn) {
      loginBtn.addEventListener('click', function (e) {
        e.preventDefault();
        loginForm.dispatchEvent(new Event('submit'));
      });
    }
  }
}

function handleLogin(e) {
  e.preventDefault();

  const email = document.getElementById('login-email').value.trim();
  const password = document.getElementById('login-password').value;
  const rememberMe = document.getElementById('remember-me').checked;

  if (!email || !password) {
    showAlert('danger', 'Veuillez remplir tous les champs.');
    return;
  }

  // Appel asynchrone à l'API
  authManager.login(email, password).then(result => {
    console.log('Login result:', result); // Debug
    
    if (result.success) {
      console.log('Login successful, user:', result.user); // Debug
      
      if (rememberMe) {
        localStorage.setItem('pharmaLocal_rememberMe', 'true');
      }

      // Stocker explicitement l'utilisateur avant la redirection
      localStorage.setItem('pharmaLocal_currentUser', JSON.stringify(result.user));
      console.log('Stored currentUser in localStorage:', localStorage.getItem('pharmaLocal_currentUser')); // Debug

      showAlert('success', 'Connexion réussie! Redirection en cours...');

      setTimeout(() => {
        console.log('Calling redirectByRole with role:', result.user.role); // Debug
        if (typeof redirectByRole === 'function') {
          redirectByRole(result.user.role);
        } else {
          console.error('redirectByRole non défini au moment du login');
          window.location.href = 'profil.html';
        }
      }, 1500);
    } else {
      showAlert('danger', result.message);
      document.getElementById('login-password').value = '';
    }
  }).catch(error => {
    console.error('Erreur lors de la connexion:', error);
    showAlert('danger', 'Erreur de connexion au serveur');
  });
}

// Fonction utilitaire pour afficher les alertes (si non définie dans auth.js)
if (typeof showAlert === 'undefined') {
  function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    const container = document.querySelector('main') || document.body;
    container.insertBefore(alertDiv, container.firstChild);

    setTimeout(() => {
      alertDiv.remove();
    }, 5000);
  }
}
