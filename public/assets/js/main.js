/**
 * ProNetwork - Global UI Helpers
 * assets/js/main.js
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
  initDeadLinks();
});

// GLOBAL NOTIFY
function notify(message) {
  let toast = document.getElementById('global-action-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'global-action-toast';
    toast.style.cssText = 'position:fixed;right:16px;bottom:16px;z-index:9999;background:#0a66c2;color:#fff;padding:10px 14px;border-radius:8px;font-size:12px;box-shadow:0 4px 16px rgba(0,0,0,.2);transition:opacity .3s';
    document.body.appendChild(toast);
  }
  toast.textContent = message;
  toast.style.opacity = '1';
  clearTimeout(window.__globalToastTimer);
  window.__globalToastTimer = setTimeout(() => { if (toast) toast.style.opacity = '0'; }, 2500);
}

// DEAD LINKS
function initDeadLinks() {
  document.querySelectorAll('a[href="#"]').forEach(link => {
    link.addEventListener('click', (e) => {
      if (link.dataset.action) return;
      e.preventDefault();
      notify((link.textContent || 'This link').trim() + ' - coming soon.');
    });
  });
}
