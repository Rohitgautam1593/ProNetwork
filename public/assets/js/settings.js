/**
 * ProNetwork — Settings page (account summary + profile edit)
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
  initSettingsPage();
});

async function initSettingsPage() {
  let user = null;
  try {
    const res = await fetch(`${URLROOT}/user/me`);
    const data = await res.json();
    if (data.success && data.user) {
      user = data.user;
      if (typeof populateUserData === 'function') populateUserData(user);
    }
  } catch (e) {
    console.error(e);
  }

  document.querySelectorAll('.settings-edit-trigger').forEach(btn => {
    btn.addEventListener('click', () => {
      const section = btn.getAttribute('data-section') || '';
      if (section === 'account' && user) {
        openAccountEditor(user, updated => {
          user = updated;
          if (typeof setUserState === 'function') setUserState(updated);
          if (typeof populateUserData === 'function') populateUserData(updated);
        });
      } else if (section === 'account' && !user) {
        if (typeof notify === 'function') notify('Could not load your profile. Try refreshing the page.');
      } else if (section === 'profile') {
        window.location.href = `${URLROOT}/user/profile`;
      } else {
        if (typeof notify === 'function') notify('Coming soon');
        else if (typeof showToast === 'function') showToast('Coming soon.', 'error');
      }
    });
  });
}

function openAccountEditor(user, onSaved) {
  const backdrop = document.createElement('div');
  backdrop.className = 'fixed inset-0 z-[200] flex items-center justify-center bg-slate-900/50 p-4';
  backdrop.setAttribute('role', 'dialog');
  backdrop.setAttribute('aria-modal', 'true');
  backdrop.setAttribute('aria-labelledby', 'pn-settings-modal-title');

  const esc = s => String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');

  backdrop.innerHTML = `
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-slate-200 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h2 id="pn-settings-modal-title" class="text-lg font-bold text-slate-900">Edit profile basics</h2>
        <button type="button" class="pn-settings-close w-9 h-9 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-600" aria-label="Close">
          <span class="material-symbols-outlined text-[22px]">close</span>
        </button>
      </div>
      <form id="pn-settings-account-form" class="p-5 space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1" for="pn-set-full">Full name</label>
          <input id="pn-set-full" name="fullName" type="text" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[#0A66C2] focus:border-[#0A66C2]" value="${esc(user.full_name)}" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1" for="pn-set-headline">Headline</label>
          <input id="pn-set-headline" name="headline" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[#0A66C2]" value="${esc(user.headline)}" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1" for="pn-set-location">Location</label>
          <input id="pn-set-location" name="location" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[#0A66C2]" value="${esc(user.location)}" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1" for="pn-set-industry">Industry</label>
          <input id="pn-set-industry" name="industry" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[#0A66C2]" value="${esc(user.industry)}" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1" for="pn-set-bio">About</label>
          <textarea id="pn-set-bio" name="bio" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[#0A66C2]">${esc(user.bio)}</textarea>
        </div>
        <p id="pn-settings-form-error" class="hidden text-xs text-red-600 font-medium"></p>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" class="pn-settings-close px-4 py-2 rounded-full text-sm font-semibold text-slate-600 hover:bg-slate-100">Cancel</button>
          <button type="submit" id="pn-settings-save" class="px-5 py-2 rounded-full text-sm font-bold bg-[#0A66C2] text-white hover:bg-[#004182] disabled:opacity-50">Save</button>
        </div>
      </form>
    </div>
  `;

  document.body.appendChild(backdrop);

  const close = () => {
    backdrop.classList.add('opacity-0');
    setTimeout(() => backdrop.remove(), 180);
  };

  backdrop.querySelectorAll('.pn-settings-close').forEach(el => el.addEventListener('click', close));
  backdrop.addEventListener('click', e => {
    if (e.target === backdrop) close();
  });

  const form = backdrop.querySelector('#pn-settings-account-form');
  const errEl = backdrop.querySelector('#pn-settings-form-error');
  const saveBtn = backdrop.querySelector('#pn-settings-save');

  form.addEventListener('submit', async e => {
    e.preventDefault();
    errEl.classList.add('hidden');
    const payload = {
      fullName: form.querySelector('#pn-set-full').value.trim(),
      headline: form.querySelector('#pn-set-headline').value.trim(),
      location: form.querySelector('#pn-set-location').value.trim(),
      industry: form.querySelector('#pn-set-industry').value.trim(),
      bio: form.querySelector('#pn-set-bio').value.trim()
    };
    if (!payload.fullName) {
      errEl.textContent = 'Full name is required.';
      errEl.classList.remove('hidden');
      return;
    }
    saveBtn.disabled = true;
    try {
      const res = await fetch(`${URLROOT}/user/update`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.success && data.user) {
        if (typeof showToast === 'function') showToast(data.message || 'Saved.', 'success');
        onSaved(data.user);
        close();
      } else {
        errEl.textContent = data.message || 'Could not save changes.';
        errEl.classList.remove('hidden');
      }
    } catch (ex) {
      errEl.textContent = 'Network error. Try again.';
      errEl.classList.remove('hidden');
    } finally {
      saveBtn.disabled = false;
    }
  });

  setTimeout(() => form.querySelector('#pn-set-full').focus(), 50);
}
