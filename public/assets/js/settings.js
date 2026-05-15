/**
 * ProNetwork - Settings page
 */
'use strict';

const SETTINGS_COPY = {
  account: ['Account preferences', 'Manage profile basics, contact details, and account information.'],
  security: ['Sign in & security', 'Manage your email, password, and current session.'],
  visibility: ['Visibility', 'Control what contact and profile details are visible.'],
  privacy: ['Data privacy', 'Manage data controls and export your account data.'],
  preferences: ['General preferences', 'Tune language, display, and playback behavior.']
};

let settingsUser = {};
let settingsPrefs = {};

document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('settings-tabs')) initSettingsPage();
});

async function initSettingsPage() {
  bindSettingsTabs();
  bindSettingsActions();
  await loadSettingsData();
}

async function loadSettingsData() {
  try {
    const res = await fetch(`${URLROOT}/user/settings_data`);
    const data = await res.json();
    if (!data.success) {
      settingsToast(data.message || 'Could not load settings.', 'error');
      return;
    }
    settingsUser = data.user || {};
    settingsPrefs = data.settings || {};
    if (typeof populateUserData === 'function') populateUserData(settingsUser);
    if (typeof setUserState === 'function') setUserState(settingsUser);
    renderSettings();
  } catch (e) {
    console.error(e);
    settingsToast('Could not load settings.', 'error');
  }
}

function bindSettingsTabs() {
  document.querySelectorAll('.settings-tab').forEach((tab) => {
    tab.addEventListener('click', () => activateSettingsTab(tab.dataset.settingsTab));
  });
}

function activateSettingsTab(tabName) {
  const copy = SETTINGS_COPY[tabName] || SETTINGS_COPY.account;
  document.getElementById('settings-page-title').textContent = copy[0];
  document.getElementById('settings-page-copy').textContent = copy[1];

  document.querySelectorAll('.settings-tab').forEach((tab) => {
    const active = tab.dataset.settingsTab === tabName;
    tab.classList.toggle('is-active', active);
    tab.classList.toggle('bg-blue-50', active);
    tab.classList.toggle('text-[#0A66C2]', active);
    tab.classList.toggle('font-bold', active);
    tab.classList.toggle('border-[#0A66C2]', active);
    tab.classList.toggle('text-gray-600', !active);
    tab.classList.toggle('border-transparent', !active);
  });
  document.querySelectorAll('.settings-panel').forEach((panel) => {
    panel.classList.toggle('hidden', panel.dataset.settingsPanel !== tabName);
  });
}

function bindSettingsActions() {
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-settings-action]');
    if (!btn) return;
    handleSettingsAction(btn.dataset.settingsAction);
  });
  document.getElementById('settings-export-shortcut')?.addEventListener('click', exportUserData);
}

function renderSettings() {
  setText('settings-profile-summary', [
    settingsUser.full_name,
    settingsUser.headline,
    settingsUser.location,
    settingsUser.industry
  ].filter(Boolean).join(' · ') || 'Complete your profile basics');

  setText('settings-contact-summary', [
    settingsUser.email,
    settingsUser.phone,
    settingsUser.website
  ].filter(Boolean).join(' · ') || 'Add contact details');

  setText('settings-demographics-summary', settingsPrefs.demographics || 'Not added');
  setText('settings-verification-summary', settingsPrefs.verification_note || 'Not added');
  setText('settings-email-summary', settingsUser.email || 'No email set');
  setText('settings-session-summary', `${settingsUser.role || 'Professional'} account · Active in this browser`);
  setText('settings-profile-visibility-summary', labelFor('profile_visibility', settingsPrefs.profile_visibility));
  setText('settings-show-email-summary', onOff(settingsPrefs.show_email));
  setText('settings-show-phone-summary', onOff(settingsPrefs.show_phone));
  setText('settings-allow-messages-summary', labelFor('allow_messages', settingsPrefs.allow_messages));
  setText('settings-personalization-summary', onOff(settingsPrefs.data_personalization));
  setText('settings-search-summary', onOff(settingsPrefs.search_visibility));
  setText('settings-theme-summary', labelFor('theme', settingsPrefs.theme));
  setText('settings-language-summary', settingsPrefs.language || 'English (US)');
  setText('settings-content-language-summary', settingsPrefs.content_language || 'English');
  setText('settings-autoplay-summary', onOff(settingsPrefs.autoplay_videos));
  applyThemePreference(settingsPrefs.theme);
}

function handleSettingsAction(action) {
  const actions = {
    'edit-profile': openProfileEditor,
    'edit-contact': openContactEditor,
    'open-profile': () => { window.location.href = `${URLROOT}/user/profile`; },
    'edit-demographics': () => openTextPreference('demographics', 'Personal demographics', 'Share optional context you want stored with your account.', true),
    'edit-verification': () => openTextPreference('verification_note', 'Verifications note', 'Add a note about workplace, identity, or education verification status.', true),
    'change-email': openEmailEditor,
    'change-password': openPasswordEditor,
    logout: confirmLogout,
    'profile-visibility': () => openChoicePreference('profile_visibility', 'Profile visibility', [
      ['public', 'Public'],
      ['connections', 'Connections only'],
      ['private', 'Private']
    ]),
    'show-email': () => openChoicePreference('show_email', 'Show email on contact card', [['1', 'On'], ['0', 'Off']]),
    'show-phone': () => openChoicePreference('show_phone', 'Show phone on contact card', [['1', 'On'], ['0', 'Off']]),
    'allow-messages': () => openChoicePreference('allow_messages', 'Who can message you', [
      ['connections', 'Connections only'],
      ['everyone', 'Everyone'],
      ['none', 'No one']
    ]),
    'data-personalization': () => openChoicePreference('data_personalization', 'Personalized recommendations', [['1', 'On'], ['0', 'Off']]),
    'search-visibility': () => openChoicePreference('search_visibility', 'Search visibility', [['1', 'On'], ['0', 'Off']]),
    theme: () => openChoicePreference('theme', 'Theme', [['light', 'Light'], ['dark', 'Dark'], ['system', 'Use system setting']]),
    language: () => openChoicePreference('language', 'Language', [['English (US)', 'English (US)'], ['English (UK)', 'English (UK)'], ['Hindi', 'Hindi']]),
    'content-language': () => openChoicePreference('content_language', 'Content language', [['English', 'English'], ['Hindi', 'Hindi'], ['English and Hindi', 'English and Hindi']]),
    'autoplay-videos': () => openChoicePreference('autoplay_videos', 'Autoplay videos', [['1', 'On'], ['0', 'Off']]),
    'export-data': exportUserData
  };
  actions[action]?.();
}

function openProfileEditor() {
  openSettingsForm({
    title: 'Edit profile basics',
    fields: [
      { id: 'fullName', label: 'Full name', value: settingsUser.full_name, required: true },
      { id: 'headline', label: 'Headline', value: settingsUser.headline },
      { id: 'location', label: 'Location', value: settingsUser.location },
      { id: 'industry', label: 'Industry', value: settingsUser.industry },
      { id: 'bio', label: 'About', value: settingsUser.bio, type: 'textarea' }
    ],
    submitText: 'Save profile',
    onSubmit: async (values) => {
      if (!values.fullName) throw new Error('Full name is required.');
      const data = await postJson(`${URLROOT}/user/update`, values);
      settingsUser = data.user;
      if (typeof populateUserData === 'function') populateUserData(settingsUser);
      if (typeof setUserState === 'function') setUserState(settingsUser);
      renderSettings();
      settingsToast('Profile updated', 'success');
    }
  });
}

function openContactEditor() {
  openSettingsForm({
    title: 'Edit contact details',
    fields: [
      { id: 'phone', label: 'Phone', value: settingsUser.phone, type: 'tel' },
      { id: 'website', label: 'Website', value: settingsUser.website, type: 'url' }
    ],
    submitText: 'Save contact',
    onSubmit: async (values) => {
      const payload = {
        fullName: settingsUser.full_name,
        headline: settingsUser.headline,
        location: settingsUser.location,
        industry: settingsUser.industry,
        bio: settingsUser.bio,
        phone: values.phone,
        website: values.website
      };
      const data = await postJson(`${URLROOT}/user/update`, payload);
      settingsUser = data.user;
      if (typeof populateUserData === 'function') populateUserData(settingsUser);
      renderSettings();
      settingsToast('Contact details updated', 'success');
    }
  });
}

function openEmailEditor() {
  openSettingsForm({
    title: 'Change email address',
    fields: [
      { id: 'email', label: 'New email', value: settingsUser.email, type: 'email', required: true },
      { id: 'password', label: 'Current password', value: '', type: 'password', required: true }
    ],
    submitText: 'Update email',
    onSubmit: async (values) => {
      const data = await postJson(`${URLROOT}/user/update_email`, values);
      settingsUser = data.user;
      if (typeof populateUserData === 'function') populateUserData(settingsUser);
      renderSettings();
      settingsToast('Email updated', 'success');
    }
  });
}

function openPasswordEditor() {
  openSettingsForm({
    title: 'Change password',
    fields: [
      { id: 'current_password', label: 'Current password', value: '', type: 'password', required: true },
      { id: 'new_password', label: 'New password', value: '', type: 'password', required: true },
      { id: 'confirm_password', label: 'Confirm new password', value: '', type: 'password', required: true }
    ],
    submitText: 'Change password',
    onSubmit: async (values) => {
      if (values.new_password !== values.confirm_password) throw new Error('New passwords do not match.');
      await postJson(`${URLROOT}/user/change_password`, values);
      settingsToast('Password changed', 'success');
    }
  });
}

function openChoicePreference(key, title, choices) {
  const fields = [{
    id: key,
    label: title,
    type: 'select',
    value: settingsPrefs[key],
    choices
  }];
  openSettingsForm({
    title,
    fields,
    submitText: 'Save',
    onSubmit: async (values) => savePreference({ [key]: values[key] })
  });
}

function openTextPreference(key, title, label, multiline = false) {
  openSettingsForm({
    title,
    fields: [{ id: key, label, type: multiline ? 'textarea' : 'text', value: settingsPrefs[key] }],
    submitText: 'Save',
    onSubmit: async (values) => savePreference({ [key]: values[key] })
  });
}

async function savePreference(payload) {
  const data = await postJson(`${URLROOT}/user/save_settings`, payload);
  settingsPrefs = data.settings;
  renderSettings();
  settingsToast('Setting saved', 'success');
}

async function confirmLogout() {
  const ok = await confirm('Sign out of this ProNetwork session?');
  if (ok) window.location.href = `${URLROOT}/auth/logout`;
}

function exportUserData() {
  window.location.href = `${URLROOT}/user/export_data`;
}

function openSettingsForm({ title, fields, submitText, onSubmit }) {
  const backdrop = document.createElement('div');
  backdrop.className = 'fixed inset-0 z-[200] flex items-center justify-center bg-slate-900/50 p-4';
  backdrop.setAttribute('role', 'dialog');
  backdrop.setAttribute('aria-modal', 'true');

  backdrop.innerHTML = `
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-slate-200 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h2 class="text-lg font-bold text-slate-900">${escapeHtml(title)}</h2>
        <button type="button" class="settings-modal-close w-9 h-9 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-600" aria-label="Close">
          <span class="material-symbols-outlined text-[22px]">close</span>
        </button>
      </div>
      <form class="settings-modal-form p-5 space-y-4">
        ${fields.map(renderField).join('')}
        <p class="settings-form-error hidden text-xs text-red-600 font-medium"></p>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" class="settings-modal-close px-4 py-2 rounded-full text-sm font-semibold text-slate-600 hover:bg-slate-100">Cancel</button>
          <button type="submit" class="settings-save px-5 py-2 rounded-full text-sm font-bold bg-[#0A66C2] text-white hover:bg-[#004182] disabled:opacity-50">${escapeHtml(submitText)}</button>
        </div>
      </form>
    </div>`;

  document.body.appendChild(backdrop);
  const close = () => {
    backdrop.classList.add('opacity-0');
    setTimeout(() => backdrop.remove(), 180);
  };
  backdrop.querySelectorAll('.settings-modal-close').forEach(el => el.addEventListener('click', close));
  backdrop.addEventListener('click', e => { if (e.target === backdrop) close(); });

  const form = backdrop.querySelector('.settings-modal-form');
  const error = backdrop.querySelector('.settings-form-error');
  const save = backdrop.querySelector('.settings-save');
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    error.classList.add('hidden');
    const values = {};
    fields.forEach(field => {
      values[field.id] = form.querySelector(`[name="${field.id}"]`)?.value.trim() || '';
    });
    const missing = fields.find(field => field.required && !values[field.id]);
    if (missing) {
      error.textContent = `${missing.label} is required.`;
      error.classList.remove('hidden');
      return;
    }
    save.disabled = true;
    try {
      await onSubmit(values);
      close();
    } catch (ex) {
      error.textContent = ex.message || 'Could not save changes.';
      error.classList.remove('hidden');
    } finally {
      save.disabled = false;
    }
  });
  setTimeout(() => form.querySelector('input, textarea, select')?.focus(), 50);
}

function renderField(field) {
  const value = escapeHtml(field.value || '');
  if (field.type === 'textarea') {
    return `<div><label class="block text-xs font-semibold text-slate-600 mb-1" for="settings-${field.id}">${escapeHtml(field.label)}</label><textarea id="settings-${field.id}" name="${field.id}" rows="4" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[#0A66C2]">${value}</textarea></div>`;
  }
  if (field.type === 'select') {
    return `<div><label class="block text-xs font-semibold text-slate-600 mb-1" for="settings-${field.id}">${escapeHtml(field.label)}</label><select id="settings-${field.id}" name="${field.id}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[#0A66C2]">${field.choices.map(([val, label]) => `<option value="${escapeHtml(val)}" ${String(field.value) === String(val) ? 'selected' : ''}>${escapeHtml(label)}</option>`).join('')}</select></div>`;
  }
  return `<div><label class="block text-xs font-semibold text-slate-600 mb-1" for="settings-${field.id}">${escapeHtml(field.label)}</label><input id="settings-${field.id}" name="${field.id}" type="${escapeHtml(field.type || 'text')}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[#0A66C2]" value="${value}" /></div>`;
}

async function postJson(url, payload) {
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });
  const data = await res.json();
  if (!data.success) throw new Error(data.message || 'Request failed.');
  return data;
}

function labelFor(key, value) {
  const labels = {
    theme: { light: 'Light', dark: 'Dark', system: 'Use system setting' },
    profile_visibility: { public: 'Public', connections: 'Connections only', private: 'Private' },
    allow_messages: { connections: 'Connections only', everyone: 'Everyone', none: 'No one' }
  };
  return labels[key]?.[value] || value || 'Not set';
}

function onOff(value) {
  return String(value) === '1' ? 'On' : 'Off';
}

function setText(id, value) {
  const el = document.getElementById(id);
  if (el) el.textContent = value;
}

function applyThemePreference(theme) {
  document.body.classList.toggle('pn-theme-dark', theme === 'dark');
}

function settingsToast(message, type = 'info') {
  if (typeof showToast === 'function') showToast(message, type);
  else if (typeof jobsToast === 'function') jobsToast(message, type);
}

function escapeHtml(s) {
  return String(s == null ? '' : s)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}
