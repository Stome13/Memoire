function getToastContainer() {
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'position-fixed top-0 end-0 p-3';
    container.style.zIndex = '1080';
    document.body.appendChild(container);
  }
  return container;
}

function showToast(type, message, delay = 5000) {
  const container = getToastContainer();
  const toastId = `toast-${Date.now()}-${Math.floor(Math.random() * 100000)}`;
  const toastEl = document.createElement('div');
  toastEl.id = toastId;
  toastEl.className = `toast align-items-center text-bg-${type} border-0 mb-2`;
  toastEl.setAttribute('role', 'alert');
  toastEl.setAttribute('aria-live', 'assertive');
  toastEl.setAttribute('aria-atomic', 'true');
  toastEl.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">${message}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  `;

  container.appendChild(toastEl);

  const toast = new bootstrap.Toast(toastEl, {
    autohide: true,
    delay
  });
  toast.show();

  toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
  return toast;
}

function showAlert(type, message) {
  showToast(type, message, 5000);
}

function showAdminAlert(message, type = 'success') {
  showToast(type, message, 5000);
}

function convertInlineAlertsToToasts() {
  document.querySelectorAll('.toast-alert').forEach(alertEl => {
    const cloned = alertEl.cloneNode(true);
    const closeButton = cloned.querySelector('.btn-close');
    if (closeButton) {
      closeButton.remove();
    }

    const typeClass = Array.from(cloned.classList).find(c => c.startsWith('alert-') && c !== 'alert');
    const type = typeClass ? typeClass.replace('alert-', '') : 'info';
    const message = cloned.innerHTML.trim();

    showToast(type, message, 5000);
    alertEl.remove();
  });
}

document.addEventListener('DOMContentLoaded', convertInlineAlertsToToasts);
