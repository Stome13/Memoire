// ========================================
// PHARMACIEN SIDEBAR - BOOTSTRAP OFFCANVAS
// ========================================

document.addEventListener('DOMContentLoaded', function() {
  // Fermer l'offcanvas quand on clique sur un lien de navigation
  const offcanvasElement = document.getElementById('pharmacienSidebar');
  
  if (offcanvasElement) {
    const offcanvasInstance = new bootstrap.Offcanvas(offcanvasElement);
    
    // Fermer l'offcanvas quand on clique sur les liens de navigation
    const navLinks = document.querySelectorAll('.pharmacien-nav-link');
    navLinks.forEach(link => {
      link.addEventListener('click', function() {
        offcanvasInstance.hide();
      });
    });
  }
  
  // Déterminer la page actuelle à partir de l'URL
  const pathname = window.location.pathname;
  const currentPageFile = pathname.split('/').pop().replace('.php', '');
  
  // Ajouter la classe 'active' à tous les liens de la page actuelle
  if (currentPageFile) {
    // Chercher dans les offcanvas et sidebar
    const activeLinks = document.querySelectorAll(`a[data-page="${currentPageFile}"]`);
    activeLinks.forEach(link => {
      link.classList.add('active');
    });
    
    // Enlever la classe 'active' des autres liens
    const allLinks = document.querySelectorAll('a[data-page]');
    allLinks.forEach(link => {
      if (link.getAttribute('data-page') !== currentPageFile) {
        link.classList.remove('active');
      }
    });
  }
});
