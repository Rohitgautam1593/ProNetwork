/**
 * ProNetwork - Global UI Helpers
 * assets/js/main.js
 */
'use strict';

function initAll() {
  console.log("main.js initializing all components...");
  initDeadLinks();
  initPasswordToggles();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAll);
} else {
  initAll();
}

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

// INITIALIZE PASSWORD TOGGLES
function initPasswordToggles() {
  console.log("initPasswordToggles search started...");
  document.querySelectorAll('[data-toggle-password]').forEach(btn => {
    console.log("Found password toggle button for element:", btn.getAttribute('data-toggle-password'));
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const targetId = btn.getAttribute('data-toggle-password');
      togglePasswordVisibility(targetId, btn);
    });
  });
}

// TOGGLE PASSWORD VISIBILITY
function togglePasswordVisibility(inputId, btnEl) {
  console.log("togglePasswordVisibility execution initiated for:", inputId);
  const input = document.getElementById(inputId);
  if (!input) {
    console.warn("Target password input element not found in DOM:", inputId);
    return;
  }
  const icon = btnEl.querySelector('.material-symbols-outlined') || btnEl;
  if (input.type === 'password') {
    input.type = 'text';
    if (icon) icon.textContent = 'visibility_off';
    console.log("Input type toggled to: text");
  } else {
    input.type = 'password';
    if (icon) icon.textContent = 'visibility';
    console.log("Input type toggled to: password");
  }
}
