/**
 * ProNetwork — Centralized Client-side Form Validation
 * public/assets/js/forms.js
 */
'use strict';

// ─── UTILITY FUNCTIONS ───────────────────────────────────────

function showFieldError(fieldId, message) {
  const field = document.getElementById(fieldId);
  if (!field) return;
  field.classList.add('invalid-field');
  clearFieldError(fieldId);
  const span = document.createElement('span');
  span.className = 'field-error text-red-600 text-xs mt-1 flex items-center gap-1 font-bold';
  span.dataset.for = fieldId;
  span.setAttribute('role', 'alert');
  span.innerHTML = '<span class="material-symbols-outlined text-[14px]">error</span> ' + message;
  const parent = field.closest('.space-y-1') || field.parentElement;
  parent.appendChild(span);
}

function clearFieldError(fieldId) {
  const field = document.getElementById(fieldId);
  if (!field) return;
  field.classList.remove('invalid-field');
  const parent = field.closest('.space-y-1') || field.parentElement;
  if (parent) {
    parent.querySelectorAll('.field-error[data-for="' + fieldId + '"]').forEach(el => el.remove());
  }
}

function validateEmail(value) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value.trim());
}

function validatePassword(value, isRegister) {
  if (!isRegister) return { valid: value.length >= 6, strength: value.length >= 6 ? 2 : 0, label: '', color: '' };
  let s = 0;
  if (value.length >= 8) s++;
  if (/[A-Z]/.test(value)) s++;
  if (/[0-9]/.test(value)) s++;
  if (/[^A-Za-z0-9]/.test(value)) s++;
  const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
  const colors = ['', '#ef4444', '#f97316', '#eab308', '#22c55e'];
  return { valid: value.length >= 8 && /[A-Z]/.test(value) && /[0-9]/.test(value), strength: s, label: labels[s] || '', color: colors[s] || '' };
}

function validatePhone(value) {
  const digits = value.replace(/\D/g, '');
  return digits.length >= 10 && digits.length <= 15;
}

function validateName(value) {
  const v = value.trim();
  return v.length >= 2 && v.length <= 60 && /^[\p{L}\s\-\.]+$/u.test(v);
}

function validateFile(file, allowedExts, maxMB) {
  if (!file) return { valid: false, error: 'Please select a file.' };
  const ext = file.name.split('.').pop().toLowerCase();
  if (!allowedExts.includes(ext)) return { valid: false, error: 'Allowed: ' + allowedExts.join(', ').toUpperCase() + ' only.' };
  if (file.size > maxMB * 1024 * 1024) return { valid: false, error: 'File must be ' + maxMB + ' MB or smaller.' };
  return { valid: true };
}

function showToast(message, type) {
  const existing = document.getElementById('pn-toast');
  if (existing) existing.remove();
  const t = document.createElement('div');
  t.id = 'pn-toast';
  const bg = type === 'error' ? 'bg-red-600' : 'bg-green-600';
  const icon = type === 'error' ? 'error' : 'check_circle';
  t.className = 'fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg text-sm font-medium ' + bg + ' text-white transition-all duration-300';
  t.innerHTML = '<span class="material-symbols-outlined text-[18px]">' + icon + '</span> ' + message;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 4000);
}

// ─── LOGIN FORM (#form-signin) ───────────────────────────────

function initLoginForm() {
  const form = document.getElementById('form-signin');
  if (!form) return;

  const emailEl = document.getElementById('email');
  const pwEl = document.getElementById('password');

  emailEl?.addEventListener('blur', () => {
    const v = emailEl.value.trim();
    if (!v) showFieldError('email', 'Email is required.');
    else if (!validateEmail(v)) showFieldError('email', 'Please enter a valid email.');
    else clearFieldError('email');
  });
  emailEl?.addEventListener('input', () => clearFieldError('email'));

  pwEl?.addEventListener('blur', () => {
    const v = pwEl.value;
    if (!v) showFieldError('password', 'Password is required.');
    else if (v.length < 6) showFieldError('password', 'Minimum 6 characters.');
    else clearFieldError('password');
  });
  pwEl?.addEventListener('input', () => clearFieldError('password'));

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    let ok = true;
    
    clearFieldError('email');
    clearFieldError('password');

    const em = (emailEl?.value || '').trim();
    const pw = pwEl?.value || '';

    if (!em) { 
      showFieldError('email', 'Email is required.'); 
      ok = false; 
    } else if (!validateEmail(em)) { 
      showFieldError('email', 'Please enter a valid email.'); 
      ok = false; 
    }
    
    if (!pw) { 
      showFieldError('password', 'Password is required.'); 
      ok = false; 
    } else if (pw.length < 6) { 
      showFieldError('password', 'Minimum 6 characters.'); 
      ok = false; 
    }

    if (!ok) return;

    const btn = form.querySelector('[type=submit]');
    const originalText = btn?.textContent;
    if (btn) { btn.disabled = true; btn.textContent = 'Signing in…'; }

    try {
      const response = await fetch(`${URLROOT}/auth/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: em, password: pw })
      });
      const result = await response.json();

      if (result.success) {
        showToast('Login successful!', 'success');
        setTimeout(() => { window.location.href = result.redirect || `${URLROOT}/user/feed`; }, 800);
      } else {
        showToast(result.message || 'Login failed.', 'error');
        if (btn) { btn.disabled = false; btn.textContent = originalText; }
      }
    } catch (err) {
      showToast('A server error occurred.', 'error');
      if (btn) { btn.disabled = false; btn.textContent = originalText; }
    }
  });
}

// ─── REGISTER FORM (#form-signup) ────────────────────────────

function initRegisterForm() {
  const form = document.getElementById('form-signup');
  if (!form) return;

  const nameEl = document.getElementById('fullname');
  const emailEl = document.getElementById('signup-email');
  const pwEl = document.getElementById('signup-password');
  const roleEl = document.getElementById('role-input');
  const bar = document.getElementById('pw-strength-bar');
  const barLabel = document.getElementById('pw-strength-label');

  pwEl?.addEventListener('input', () => {
    const r = validatePassword(pwEl.value, true);
    if (bar) { bar.style.width = (r.strength / 4 * 100) + '%'; bar.style.backgroundColor = r.color; }
    if (barLabel) { barLabel.textContent = r.strength > 0 ? 'Strength: ' + r.label : ''; barLabel.style.color = r.color; }
    clearFieldError('signup-password');
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    let ok = true;
    
    clearFieldError('fullname');
    clearFieldError('signup-email');
    clearFieldError('signup-password');
    clearFieldError('role-input');

    const name = (nameEl?.value || '').trim();
    const em = (emailEl?.value || '').trim();
    const pw = pwEl?.value || '';
    const role = roleEl?.value || '';
    const pr = validatePassword(pw, true);

    if (!name || !validateName(name)) { 
      showFieldError('fullname', '2–60 characters, letters & spaces only.'); 
      ok = false; 
    }
    if (!em || !validateEmail(em)) { 
      showFieldError('signup-email', 'Please enter a valid email.'); 
      ok = false; 
    }
    if (!pw || !pr.valid) { 
      showFieldError('signup-password', 'Min 8 chars, 1 uppercase, 1 number.'); 
      ok = false; 
    }
    if (!role) { 
      showFieldError('role-input', 'Please select your professional role.'); 
      ok = false; 
    }

    if (!ok) return;

    const btn = form.querySelector('[type=submit]');
    if (btn) { btn.disabled = true; btn.textContent = 'Creating account…'; }

    try {
      const response = await fetch(`${URLROOT}/auth/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ fullName: name, email: em, password: pw, role: role })
      });
      const result = await response.json();

      if (result.success) {
        showToast(result.message, 'success');
        setTimeout(() => { window.location.href = `${URLROOT}/auth/login`; }, 1500);
      } else {
        showToast(result.message || 'Registration failed.', 'error');
        if (btn) { btn.disabled = false; btn.textContent = 'Join now'; }
      }
    } catch (err) {
      showToast('A server error occurred.', 'error');
      if (btn) { btn.disabled = false; btn.textContent = 'Join now'; }
    }
  });
}

// ─── APPLY JOB FORM (#apply-job-form) ────────────────────────

function initApplyForm() {
  const form = document.getElementById('apply-job-form');
  if (!form) return;

  const firstName = document.getElementById('apply-first-name');
  const lastName = document.getElementById('apply-last-name');
  const phone = document.getElementById('apply-phone');
  const resume = document.getElementById('apply-resume');
  const coverLetter = document.getElementById('apply-cover-letter');
  const errorEl = document.getElementById('apply-form-error');

  const showErr = (msg) => { if (errorEl) { errorEl.textContent = msg; errorEl.classList.remove('hidden'); } };
  const clearErr = () => { if (errorEl) { errorEl.textContent = ''; errorEl.classList.add('hidden'); } };

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    let ok = true;
    
    clearFieldError('apply-first-name');
    clearFieldError('apply-last-name');
    clearFieldError('apply-phone');
    clearFieldError('apply-resume');
    clearErr();

    const fn = (firstName?.value || '').trim();
    const ln = (lastName?.value || '').trim();
    const ph = (phone?.value || '').trim();
    const file = resume?.files?.[0];

    if (!fn || !validateName(fn)) { showFieldError('apply-first-name', 'First name required.'); ok = false; }
    if (!ln || !validateName(ln)) { showFieldError('apply-last-name', 'Last name required.'); ok = false; }
    if (ph.replace(/\D/g, '').length < 10) { showFieldError('apply-phone', 'Valid phone required.'); ok = false; }
    if (!file) { showFieldError('apply-resume', 'Resume is required.'); ok = false; }

    if (!ok) return;

    showToast('Application submitted successfully!', 'success');
    setTimeout(() => { window.location.href = `${URLROOT}/user/jobs`; }, 700);
  });
}


// ─── SEARCH FORMS ───────────────────────────────────────────

function initSearchForms() {
  document.querySelectorAll('form[action*="search"]').forEach(form => {
    form.addEventListener('submit', (e) => {
      const q = form.querySelector('input[name="q"]');
      if (q && !q.value.trim()) {
        e.preventDefault();
        showToast('Please enter a search term.', 'error');
      }
    });
  });
}

// ─── AUTH ACTIONS ───────────────────────────────────────────

function initAuthActions() {
  document.querySelectorAll('[data-action="forgot-password"]').forEach(link => {
    link.addEventListener('click', async (e) => {
      e.preventDefault();
      const entered = await pnModal({
        title: 'Forgot Password',
        message: 'Enter your registered email address to receive a password reset link.',
        type: 'info',
        isPrompt: true,
        placeholder: 'e.g. name@example.com',
        confirmText: 'Send Reset Link',
        cancelText: 'Cancel'
      });
      
      if (entered && validateEmail(entered)) {
        showToast('Password reset link sent to ' + entered.trim() + '.', 'success');
      } else if (entered !== null) {
        showToast('Please enter a valid email address.', 'error');
      }
    });
  });

  document.querySelectorAll('button[data-provider]').forEach(btn => {
    btn.addEventListener('click', () => {
      const label = btn.dataset.provider === 'google' ? 'Google' : 'LinkedIn';
      showToast(label + ' sign in is not configured yet.', 'error');
    });
  });
}

// ─── PROFILE & SETTINGS EDITORS ──────────────────────────────

function initProfileEditor() {
  const saveBtn = document.getElementById('save-profile-edit');
  const modal = document.getElementById('profile-edit-modal');
  if (!saveBtn || !modal) return;

  saveBtn.addEventListener('click', async () => {
    const data = {
      fullName: document.getElementById('edit-full-name')?.value.trim(),
      headline: document.getElementById('edit-headline')?.value.trim(),
      location: document.getElementById('edit-location')?.value.trim(),
      bio: document.getElementById('edit-bio')?.value.trim()
    };
    try {
      const response = await fetch(`${URLROOT}/user/update`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      const result = await response.json();
      if (result.success) {
        showToast('Profile updated!', 'success');
        location.reload();
      }
    } catch (err) { showToast('Server error.', 'error'); }
  });
}

function initSettingsEditor() {
  const saveBtn = document.getElementById('save-settings-edit');
  if (!saveBtn) return;
  saveBtn.addEventListener('click', async () => {
    const data = {
      fullName: document.getElementById('set-full-name')?.value.trim(),
      location: document.getElementById('set-location')?.value.trim(),
      industry: document.getElementById('set-industry')?.value.trim()
    };
    try {
      const response = await fetch(`${URLROOT}/user/update`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      const result = await response.json();
      if (result.success) {
        showToast('Settings updated!', 'success');
        location.reload();
      }
    } catch (err) { showToast('Server error.', 'error'); }
  });
}

// ─── FEED COMPOSER ───────────────────────────────────────────

function initFeedComposer() {
  const openBtn = document.getElementById('open-post-composer');
  const openMediaBtn = document.getElementById('open-post-media');
  const openEventBtn = document.getElementById('open-post-event');
  const openArticleBtn = document.getElementById('open-post-article');
  const modal = document.getElementById('post-composer-modal');
  const closeBtn = document.getElementById('close-post-composer');
  const form = document.getElementById('feed-post-form');
  const content = document.getElementById('feed-post-content');
  const mediaInput = document.getElementById('feed-post-media');
  
  if (!openBtn || !modal || !closeBtn || !form || !content) return;

  function setType(type) {
    document.getElementById('post-media-section')?.classList.toggle('hidden', type !== 'media');
    document.getElementById('post-event-section')?.classList.toggle('hidden', type !== 'event');
    document.getElementById('post-article-section')?.classList.toggle('hidden', type !== 'article');
  }

  function openModal(type) {
    setType(type);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    content.focus();
  }

  openBtn.addEventListener('click', () => { 
    openModal('');
  });

  openMediaBtn?.addEventListener('click', () => { 
    openModal('media');
  });

  openEventBtn?.addEventListener('click', () => {
    openModal('event');
  });

  openArticleBtn?.addEventListener('click', () => {
    openModal('article');
  });

  closeBtn.addEventListener('click', () => { 
    modal.classList.add('hidden'); 
    modal.classList.remove('flex'); 
  });

  modal.addEventListener('click', (e) => { 
    if (e.target === modal) { 
      modal.classList.add('hidden'); 
      modal.classList.remove('flex'); 
    } 
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const text = content.value.trim();
    const file = mediaInput?.files?.[0];

    if (!text && !file) {
      showToast('Post content or media is required.', 'error');
      return;
    }

    const btn = form.querySelector('[type=submit]');
    if (btn) { btn.disabled = true; btn.textContent = 'Posting…'; }

    try {
      const formData = new FormData();
      formData.append('content', text);
      if (file) formData.append('media', file);

      const response = await fetch(`${URLROOT}/post`, {
        method: 'POST',
        body: formData
      });
      const result = await response.json();
      if (result.success) {
        showToast('Post created!', 'success');
        if (typeof window.pnPrependFeedPost === 'function' && result.post) {
          window.pnPrependFeedPost(result.post);
        }
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        if (mediaInput) mediaInput.value = '';
        document.getElementById('post-event-section')?.classList.add('hidden');
        document.getElementById('post-article-section')?.classList.add('hidden');
        if (!result.post || typeof window.pnPrependFeedPost !== 'function') {
          location.reload();
        }
      } else {
        showToast(result.message || 'Failed to create post.', 'error');
      }
    } catch (err) {
      showToast('A server error occurred.', 'error');
    } finally {
      if (btn) { btn.disabled = false; btn.textContent = 'Post'; }
    }
  });
}

function initProfilePicUpload() {
    const trigger = document.getElementById('trigger-pic-upload');
    const input = document.getElementById('profile-pic-input');
    if (!trigger || !input) return;

    trigger.addEventListener('click', () => input.click());
    input.addEventListener('change', async () => {
        if (!input.files?.[0]) return;
        const formData = new FormData();
        formData.append('profile_pic', input.files[0]);

        try {
            const res = await fetch(`${URLROOT}/user/upload_pic`, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                showToast('Profile picture updated!', 'success');
                location.reload();
            }
        } catch(e) { showToast('Server error.', 'error'); }
    });
}

function initBannerUpload() {
    const trigger = document.getElementById('trigger-banner-upload');
    const input = document.getElementById('banner-upload-input');
    if (!trigger || !input) return;

    trigger.addEventListener('click', () => input.click());
    input.addEventListener('change', async () => {
        if (!input.files?.[0]) return;
        const file = input.files[0];
        const formData = new FormData();
        formData.append('cover_image', file);

        showToast('Uploading cover image...', 'info');

        try {
            const res = await fetch(`${URLROOT}/user/upload_cover`, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                showToast('Cover image updated!', 'success');
                location.reload();
            } else {
                showToast(data.message || 'Upload failed', 'error');
            }
        } catch(e) { showToast('Server error.', 'error'); }
    });
}

// ─── HELPERS ─────────────────────────────────────────────────

function escapeHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

// ─── BOOTSTRAP ───────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {
  initLoginForm();
  initRegisterForm();
  initApplyForm();
  initSearchForms();
  initAuthActions();
  initProfileEditor();
  initSettingsEditor();
  initFeedComposer();
  initProfilePicUpload();
  initBannerUpload();
});
