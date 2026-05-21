/**
 * ProNetwork — Notifications
 */
'use strict';

const NOTIF_POLL_MS = 12000;
const NOTIF_PREFS_KEY = 'pn_notif_prefs';
const NOTIF_EMAIL_KEY = 'pn_notif_email_prefs';
const DISPLAY_LIMIT_STEP = 25;

const NOTIF_SETTING_OPTIONS = [
    { key: 'likes', label: 'Likes on your posts', default: true },
    { key: 'comments', label: 'Comments on your posts', default: true },
    { key: 'connections', label: 'Connection requests & accepts', default: true },
    { key: 'jobs', label: 'Job & application updates', default: true },
    { key: 'messages', label: 'New messages', default: true }
];

const NOTIF_EMAIL_OPTIONS = [
    { key: 'weekly_digest', label: 'Weekly digest', default: true },
    { key: 'connection_emails', label: 'Connection activity', default: true },
    { key: 'job_emails', label: 'Job recommendations', default: false }
];

let allNotifications = [];
let currentFilter = 'all';
let displayLimit = DISPLAY_LIMIT_STEP;
let unreadCount = 0;
let pollTimer = null;
let listFingerprint = '';

document.addEventListener('DOMContentLoaded', () => {
    if (!document.getElementById('notifications-list')) return;
    initNotificationsPage();
});

function initNotificationsPage() {
    bindNotifUi();
    buildPreferenceToggles();
    fetchNotifications(true);
    initNotifSuggestions();

    pollTimer = setInterval(() => fetchNotifications(false), NOTIF_POLL_MS);
}

function bindNotifUi() {
    document.getElementById('mark-all-read')?.addEventListener('click', markAllRead);
    document.getElementById('clear-all-notifs')?.addEventListener('click', clearAllNotifications);
    document.getElementById('load-more-notifs')?.addEventListener('click', () => {
        displayLimit += DISPLAY_LIMIT_STEP;
        renderFilteredNotifications();
    });

    document.querySelectorAll('.notif-filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.notif-filter-btn').forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            currentFilter = btn.dataset.filter || 'all';
            displayLimit = DISPLAY_LIMIT_STEP;
            renderFilteredNotifications();
        });
    });

    document.querySelectorAll('#notif-pref-nav .notif-pref-btn').forEach(btn => {
        btn.addEventListener('click', () => showPrefPanel(btn.dataset.prefPanel));
    });

    document.getElementById('save-notif-settings')?.addEventListener('click', saveNotifSettings);
    document.getElementById('save-notif-email')?.addEventListener('click', saveNotifEmail);
    document.getElementById('enable-push-btn')?.addEventListener('click', enablePushNotifications);
}

function showPrefPanel(panel) {
    document.querySelectorAll('#notif-pref-nav .notif-pref-btn').forEach(btn => {
        const active = btn.dataset.prefPanel === panel;
        btn.classList.toggle('is-active', active);
        btn.classList.toggle('bg-blue-50', active);
        btn.classList.toggle('text-[#0A66C2]', active);
        btn.classList.toggle('font-bold', active);
        btn.classList.toggle('text-slate-600', !active);
        btn.classList.toggle('font-semibold', !active);
    });

    const isFeed = panel === 'feed';
    document.getElementById('notif-main-feed')?.classList.toggle('hidden', !isFeed);
    document.getElementById('notif-pref-panels')?.classList.toggle('hidden', isFeed);

    if (!isFeed) {
        document.getElementById('notif-panel-settings')?.classList.toggle('hidden', panel !== 'settings');
        document.getElementById('notif-panel-email')?.classList.toggle('hidden', panel !== 'email');
        document.getElementById('notif-panel-push')?.classList.toggle('hidden', panel !== 'push');
    }
}

function getPrefs(key, options) {
    try {
        const saved = JSON.parse(localStorage.getItem(key) || '{}');
        const out = {};
        options.forEach(o => { out[o.key] = saved[o.key] !== undefined ? saved[o.key] : o.default; });
        return out;
    } catch {
        const out = {};
        options.forEach(o => { out[o.key] = o.default; });
        return out;
    }
}

function buildPreferenceToggles() {
    renderToggles('notif-settings-toggles', NOTIF_SETTING_OPTIONS, NOTIF_PREFS_KEY);
    renderToggles('notif-email-toggles', NOTIF_EMAIL_OPTIONS, NOTIF_EMAIL_KEY);
}

function renderToggles(containerId, options, storageKey) {
    const el = document.getElementById(containerId);
    if (!el) return;
    const prefs = getPrefs(storageKey, options);
    el.innerHTML = options.map(o => `
        <label class="notif-toggle-row flex items-center justify-between gap-4 p-3 rounded-lg border border-slate-100 hover:bg-slate-50 cursor-pointer">
            <span class="text-sm font-medium text-slate-700">${o.label}</span>
            <input type="checkbox" data-pref-key="${o.key}" class="notif-toggle-input w-5 h-5 accent-[#0A66C2]" ${prefs[o.key] ? 'checked' : ''}>
        </label>
    `).join('');
}

function savePrefsFromDom(containerId, storageKey, options) {
    const prefs = {};
    document.querySelectorAll(`#${containerId} .notif-toggle-input`).forEach(input => {
        prefs[input.dataset.prefKey] = input.checked;
    });
    localStorage.setItem(storageKey, JSON.stringify(prefs));
    return prefs;
}

function saveNotifSettings() {
    savePrefsFromDom('notif-settings-toggles', NOTIF_PREFS_KEY, NOTIF_SETTING_OPTIONS);
    notifToast('Alert settings saved', 'success');
    renderFilteredNotifications();
}

function saveNotifEmail() {
    savePrefsFromDom('notif-email-toggles', NOTIF_EMAIL_KEY, NOTIF_EMAIL_OPTIONS);
    notifToast('Email preferences saved', 'success');
}

async function enablePushNotifications() {
    const status = document.getElementById('push-status-text');
    if (!('Notification' in window)) {
        if (status) status.textContent = 'Push is not supported in this browser.';
        notifToast('Push not supported', 'error');
        return;
    }
    try {
        const perm = await Notification.requestPermission();
        if (status) {
            status.textContent = perm === 'granted'
                ? 'Push enabled. You will receive browser alerts when permitted.'
                : perm === 'denied'
                    ? 'Push blocked. Enable notifications in browser settings.'
                    : 'Permission dismissed.';
        }
        notifToast(perm === 'granted' ? 'Push notifications enabled' : 'Push permission not granted', perm === 'granted' ? 'success' : 'info');
    } catch {
        if (status) status.textContent = 'Could not request permission.';
    }
}

function isNotifTypeEnabled(notif) {
    const prefs = getPrefs(NOTIF_PREFS_KEY, NOTIF_SETTING_OPTIONS);
    const t = notif.type || '';
    if (t === 'Like') return prefs.likes;
    if (t === 'Comment') return prefs.comments;
    if (t.includes('Connection')) return prefs.connections;
    if (t.includes('Job') || t.includes('Application')) return prefs.jobs;
    return true;
}

async function fetchNotifications(showLoading = true) {
    const list = document.getElementById('notifications-list');
    if (!list) return;
    if (showLoading && !allNotifications.length) {
        list.innerHTML = '<div class="flex justify-center py-12"><div class="animate-spin rounded-full h-8 w-8 border-2 border-slate-200 border-t-[#0A66C2]"></div></div>';
    }
    try {
        const res = await fetch(`${URLROOT}/notification/fetch`);
        const data = await res.json();
        if (data.success) {
            allNotifications = data.notifications || [];
            unreadCount = data.unread_count ?? 0;
            updateUnreadUi();
            renderFilteredNotifications();
        }
    } catch (e) {
        console.error(e);
    }
}

function updateUnreadUi() {
    const badge = document.getElementById('notif-unread-badge');
    if (badge) {
        if (unreadCount > 0) {
            badge.textContent = `${unreadCount} new`;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
    updateNavNotifBadge(unreadCount);
}

function updateNavNotifBadge(count) {
    const el = document.getElementById('nav-notif-badge');
    const elMobile = document.getElementById('nav-notif-badge-mobile');
    if (el) {
        if (count > 0) {
            el.textContent = count > 99 ? '99+' : String(count);
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    }
    if (elMobile) {
        if (count > 0) {
            elMobile.textContent = count > 99 ? '99+' : String(count);
            elMobile.classList.remove('hidden');
        } else {
            elMobile.classList.add('hidden');
        }
    }
}

function getFilteredNotifications() {
    let list = allNotifications.filter(isNotifTypeEnabled);
    if (currentFilter === 'posts') {
        list = list.filter(n => n.source_type === 'post' || n.type === 'Like' || n.type === 'Comment');
    } else if (currentFilter === 'network') {
        list = list.filter(n => (n.type || '').includes('Connection') || n.source_type === 'user');
    } else if (currentFilter === 'jobs') {
        list = list.filter(n => (n.type || '').includes('Job') || (n.type || '').includes('Application') || n.source_type === 'job');
    }
    return list;
}

function renderFilteredNotifications() {
    const list = document.getElementById('notifications-list');
    const loadMore = document.getElementById('load-more-notifs');
    if (!list) return;

    const filtered = getFilteredNotifications();
    const visible = filtered.slice(0, displayLimit);
    const fp = visible.map(n => `${n.notification_id}:${n.is_read}`).join(',');

    if (fp === listFingerprint && list.children.length > 0) return;
    listFingerprint = fp;

    if (loadMore) loadMore.classList.toggle('hidden', filtered.length <= displayLimit);

    if (!filtered.length) {
        list.innerHTML = `
            <div class="notif-empty bg-white p-12 flex flex-col items-center text-center rounded-xl border border-slate-200 shadow-sm">
                <span class="material-symbols-outlined text-5xl text-slate-300 mb-3">notifications_paused</span>
                <h3 class="font-bold text-slate-800 text-lg">You're all caught up</h3>
                <p class="text-slate-500 text-sm mt-1 max-w-xs">No notifications match this filter right now.</p>
                <button type="button" class="mt-4 text-sm font-bold text-[#0A66C2] hover:underline" data-show-all-filters>View all</button>
            </div>`;
        list.querySelector('[data-show-all-filters]')?.addEventListener('click', () => {
            document.querySelector('.notif-filter-btn[data-filter="all"]')?.click();
        });
        return;
    }

    list.innerHTML = visible.map(n => notifCardHtml(n)).join('');
    bindNotifCardEvents(list);
}

function resolveActorId(notif) {
    const id = notif.actor_user_id || notif.actor_id;
    if (id) return Number(id);
    if (notif.source_type === 'user' && notif.source_id) return Number(notif.source_id);
    return null;
}

function actorProfileUrl(actorId) {
    return actorId ? `${URLROOT}/user/profile?id=${encodeURIComponent(actorId)}` : null;
}

function bindNotifCardEvents(root) {
    root.querySelectorAll('[data-notif-open]').forEach(card => {
        card.addEventListener('click', e => {
            if (
                e.target.closest('[data-notif-dismiss]') ||
                e.target.closest('[data-notif-accept]') ||
                e.target.closest('[data-notif-ignore]') ||
                e.target.closest('.notif-actor-link')
            ) return;
            const id = card.dataset.notifId;
            const url = card.dataset.notifUrl;
            markOneRead(id, false);
            if (url) window.location.href = url;
        });
    });
    root.querySelectorAll('[data-notif-dismiss]').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            dismissNotification(btn.dataset.notifDismiss);
        });
    });
    root.querySelectorAll('[data-notif-accept]').forEach(btn => {
        btn.addEventListener('click', async e => {
            e.stopPropagation();
            await acceptConnectionFromNotif(btn.dataset.notifAccept, btn);
        });
    });
    root.querySelectorAll('[data-notif-ignore]').forEach(btn => {
        btn.addEventListener('click', async e => {
            e.stopPropagation();
            await rejectConnectionFromNotif(btn.dataset.notifIgnore, btn);
        });
    });
    root.querySelectorAll('.notif-actor-link').forEach(link => {
        link.addEventListener('click', e => e.stopPropagation());
    });
}

function notifMeta(type) {
    const map = {
        Like: { icon: 'thumb_up', bg: 'bg-[#0A66C2]', ring: 'ring-blue-100' },
        Comment: { icon: 'comment', bg: 'bg-amber-500', ring: 'ring-amber-100' },
        Connection_Accepted: { icon: 'how_to_reg', bg: 'bg-purple-600', ring: 'ring-purple-100' },
        Connection_Request: { icon: 'person_add', bg: 'bg-indigo-600', ring: 'ring-indigo-100' },
        Job_Alert: { icon: 'work', bg: 'bg-emerald-600', ring: 'ring-emerald-100' },
        Application_Update: { icon: 'task_alt', bg: 'bg-teal-600', ring: 'ring-teal-100' }
    };
    return map[type] || { icon: 'notifications', bg: 'bg-slate-500', ring: 'ring-slate-100' };
}

function resolveNotifUrl(notif) {
    const t = notif.type || '';
    const sid = notif.source_id;
    if (t === 'Like' || t === 'Comment' || notif.source_type === 'post') {
        return sid ? `${URLROOT}/post/show/${sid}` : `${URLROOT}/user/feed`;
    }
    if (t.includes('Connection')) {
        const actorId = resolveActorId(notif);
        return t === 'Connection_Request'
            ? `${URLROOT}/user/network`
            : (actorId ? `${URLROOT}/user/profile?id=${actorId}` : `${URLROOT}/user/network`);
    }
    if (t.includes('Job') || t.includes('Application')) {
        return sid ? `${URLROOT}/user/jobs?id=${sid}` : `${URLROOT}/user/jobs`;
    }
    if (notif.actor_user_id) return `${URLROOT}/user/profile?id=${notif.actor_user_id}`;
    return `${URLROOT}/user/feed`;
}

function formatNotifMessage(notif) {
    const msg = notif.message || '';
    const actor = notif.actor_name;
    const actorId = resolveActorId(notif);
    const profileUrl = actorProfileUrl(actorId);

    if (actor && msg.startsWith(actor)) {
        const rest = msg.slice(actor.length).trim();
        const actorHtml = profileUrl
            ? `<a href="${profileUrl}" class="notif-actor-link font-bold text-slate-900 hover:text-[#0A66C2] hover:underline transition-colors">${escapeHtml(actor)}</a>`
            : `<span class="font-bold text-slate-900">${escapeHtml(actor)}</span>`;
        return `${actorHtml} ${escapeHtml(rest)}`;
    }
    return escapeHtml(msg);
}

function notifCardHtml(notif) {
    const meta = notifMeta(notif.type);
    const isUnread = Number(notif.is_read) === 0;
    const url = resolveNotifUrl(notif);
    const actorId = resolveActorId(notif);
    const profileUrl = actorProfileUrl(actorId);
    const pic = pnProfilePicUrl({ profile_pic: notif.actor_pic, full_name: notif.actor_name });
    const timeAgo = formatTimeAgoNotif(new Date(notif.created_at));
    const isConnReq = notif.type === 'Connection_Request' && actorId;

    const avatarHtml = profileUrl
        ? `<a href="${profileUrl}" class="notif-actor-link relative shrink-0 block rounded-full focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#0A66C2] focus-visible:outline-offset-2" title="View ${escapeHtml(notif.actor_name || 'profile')}">
                <img src="${pic}" alt="${escapeHtml(notif.actor_name || 'User')}" class="w-12 h-12 rounded-full object-cover border border-slate-100 shadow-sm group-hover:ring-2 group-hover:ring-[#0A66C2]/30 transition-all">
                <span class="notif-card__type-badge ${meta.bg} ${meta.ring}">
                    <span class="material-symbols-outlined text-white text-[12px]" style="font-variation-settings:'FILL' 1">${meta.icon}</span>
                </span>
           </a>`
        : `<div class="relative shrink-0">
                <img src="${pic}" alt="" class="w-12 h-12 rounded-full object-cover border border-slate-100 shadow-sm">
                <span class="notif-card__type-badge ${meta.bg} ${meta.ring}">
                    <span class="material-symbols-outlined text-white text-[12px]" style="font-variation-settings:'FILL' 1">${meta.icon}</span>
                </span>
           </div>`;

    const actions = isConnReq ? `
        <div class="flex gap-2 mt-3" data-notif-actions>
            <button type="button" data-notif-accept="${actorId}" class="flex-1 bg-[#0A66C2] text-white text-xs font-bold py-2 rounded-full hover:bg-[#004182]">Accept</button>
            <button type="button" data-notif-ignore="${actorId}" class="flex-1 border border-slate-300 text-slate-700 text-xs font-bold py-2 rounded-full hover:bg-slate-50">Ignore</button>
        </div>` : '';

    return `
        <article class="notif-card group ${isUnread ? 'notif-card--unread' : ''}" data-notif-open data-notif-id="${notif.notification_id}" data-notif-url="${url}" role="button" tabindex="0">
            ${isUnread ? '<span class="notif-card__dot" aria-hidden="true"></span>' : ''}
            <div class="flex gap-3 items-start">
                ${avatarHtml}
                <div class="flex-1 min-w-0 pr-8">
                    <p class="text-sm text-slate-700 leading-snug">${formatNotifMessage(notif)}</p>
                    <span class="text-[11px] font-bold text-slate-400 mt-1 block">${timeAgo}</span>
                    ${actions}
                </div>
                <button type="button" class="notif-dismiss-btn" data-notif-dismiss="${notif.notification_id}" title="Dismiss" aria-label="Dismiss">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        </article>`;
}

async function markOneRead(id, refresh = true) {
    try {
        const res = await fetch(`${URLROOT}/notification/mark_one/${id}`, { method: 'POST' });
        const data = await res.json();
        if (data.success) {
            const n = allNotifications.find(x => Number(x.notification_id) === Number(id));
            if (n) n.is_read = 1;
            unreadCount = data.unread_count ?? unreadCount;
            updateUnreadUi();
            if (refresh) {
                listFingerprint = '';
                renderFilteredNotifications();
            }
        }
    } catch (e) {
        console.error(e);
    }
}

async function markAllRead() {
    const btn = document.getElementById('mark-all-read');
    if (btn) btn.disabled = true;
    try {
        const res = await fetch(`${URLROOT}/notification/mark_read`, { method: 'POST' });
        const data = await res.json();
        if (data.success) {
            allNotifications.forEach(n => { n.is_read = 1; });
            unreadCount = 0;
            updateUnreadUi();
            listFingerprint = '';
            renderFilteredNotifications();
            notifToast('All notifications marked as read', 'success');
        }
    } catch {
        notifToast('Could not mark as read', 'error');
    }
    if (btn) btn.disabled = false;
}

async function dismissNotification(id) {
    try {
        const res = await fetch(`${URLROOT}/notification/dismiss/${id}`, { method: 'POST' });
        const data = await res.json();
        if (data.success) {
            allNotifications = allNotifications.filter(n => Number(n.notification_id) !== Number(id));
            unreadCount = data.unread_count ?? unreadCount;
            updateUnreadUi();
            listFingerprint = '';
            renderFilteredNotifications();
            notifToast('Notification dismissed', 'info');
        }
    } catch {
        notifToast('Could not dismiss', 'error');
    }
}

async function clearAllNotifications() {
    const ok = await confirm('Delete all notifications? This cannot be undone.');
    if (!ok) return;
    try {
        const res = await fetch(`${URLROOT}/notification/clear_all`, { method: 'POST' });
        const data = await res.json();
        if (data.success) {
            allNotifications = [];
            unreadCount = 0;
            updateUnreadUi();
            listFingerprint = '';
            renderFilteredNotifications();
            notifToast('All notifications cleared', 'success');
        }
    } catch {
        notifToast('Could not clear notifications', 'error');
    }
}

async function acceptConnectionFromNotif(userId, btn) {
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Accepting…';
    }
    try {
        const res = await fetch(`${URLROOT}/network/accept`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId })
        });
        const data = await res.json();
        if (data.success) {
            notifToast('Connection accepted', 'success');
            await fetchNotifications(false);
        } else {
            notifToast(data.message || 'Could not accept', 'error');
            if (btn) { btn.disabled = false; btn.textContent = 'Accept'; }
        }
    } catch {
        notifToast('Network error', 'error');
    }
}

async function rejectConnectionFromNotif(userId, btn) {
    if (btn) btn.disabled = true;
    try {
        const res = await fetch(`${URLROOT}/network/reject`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId })
        });
        const data = await res.json();
        if (data.success) {
            notifToast('Invitation ignored', 'info');
            await fetchNotifications(false);
        } else {
            notifToast(data.message || 'Could not ignore', 'error');
            if (btn) btn.disabled = false;
        }
    } catch {
        notifToast('Network error', 'error');
    }
}

async function initNotifSuggestions() {
    const sugCont = document.getElementById('notif-suggestions');
    if (!sugCont) return;
    try {
        const res = await fetch(`${URLROOT}/network/suggestions`);
        const data = await res.json();
        if (!data.success || !data.suggestions?.length) {
            sugCont.innerHTML = '<p class="text-sm text-slate-500">No suggestions right now.</p>';
            return;
        }
        sugCont.innerHTML = data.suggestions.slice(0, 3).map(u => {
            const pic = pnProfilePicUrl(u);
            const profileUrl = `${URLROOT}/user/profile?id=${encodeURIComponent(u.user_id)}`;
            return `
                <div class="flex gap-3 items-start">
                    <a href="${profileUrl}" class="notif-actor-link shrink-0 rounded-full focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#0A66C2]">
                        <img src="${pic}" alt="${escapeHtml(u.full_name)}" class="w-10 h-10 rounded-full object-cover border border-slate-100 hover:ring-2 hover:ring-[#0A66C2]/30 transition-all">
                    </a>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-sm font-bold truncate">
                            <a href="${profileUrl}" class="notif-actor-link text-slate-900 hover:text-[#0A66C2] hover:underline">${escapeHtml(u.full_name)}</a>
                        </h4>
                        <p class="text-[11px] text-slate-500 truncate">${escapeHtml(u.headline || 'Professional')}</p>
                        <button type="button" class="notif-suggest-connect mt-2 inline-flex items-center gap-1 border border-slate-300 rounded-full px-3 py-1 text-[11px] font-bold text-slate-700 hover:border-[#0A66C2] hover:text-[#0A66C2]" data-user-id="${u.user_id}">
                            <span class="material-symbols-outlined text-[14px]">person_add</span> Connect
                        </button>
                    </div>
                </div>`;
        }).join('');
        sugCont.querySelectorAll('.notif-suggest-connect').forEach(btn => {
            btn.addEventListener('click', async () => {
                btn.disabled = true;
                btn.textContent = 'Sending…';
                try {
                    const r = await fetch(`${URLROOT}/network/send_request`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ user_id: btn.dataset.userId })
                    });
                    const d = await r.json();
                    if (d.success) {
                        btn.textContent = 'Pending';
                        notifToast('Request sent', 'success');
                    } else {
                        btn.textContent = 'Connect';
                        btn.disabled = false;
                        notifToast(d.message || 'Failed', 'error');
                    }
                } catch {
                    btn.disabled = false;
                    btn.textContent = 'Connect';
                }
            });
        });
    } catch {
        sugCont.innerHTML = '<p class="text-sm text-slate-500">Could not load suggestions.</p>';
    }
}

function notifToast(msg, type = 'info') {
    document.getElementById('notif-toast')?.remove();
    const t = document.createElement('div');
    t.id = 'notif-toast';
    const bg = type === 'error' ? 'bg-red-600' : type === 'success' ? 'bg-green-600' : 'bg-slate-800';
    const icon = type === 'error' ? 'error' : type === 'success' ? 'check_circle' : 'info';
    t.className = `fixed bottom-6 left-1/2 -translate-x-1/2 z-[10001] flex items-center gap-2 px-5 py-3 rounded-full shadow-2xl text-sm font-bold ${bg} text-white transition-all opacity-0 translate-y-4`;
    t.innerHTML = `<span class="material-symbols-outlined text-[20px]">${icon}</span>${escapeHtml(msg)}`;
    document.body.appendChild(t);
    requestAnimationFrame(() => t.classList.remove('opacity-0', 'translate-y-4'));
    setTimeout(() => {
        t.classList.add('opacity-0', 'translate-y-4');
        setTimeout(() => t.remove(), 300);
    }, 3500);
}

function formatTimeAgoNotif(date) {
    const s = Math.floor((Date.now() - date) / 1000);
    if (s < 60) return 'Just now';
    const m = Math.floor(s / 60);
    if (m < 60) return `${m}m ago`;
    const h = Math.floor(m / 60);
    if (h < 24) return `${h}h ago`;
    return `${Math.floor(h / 24)}d ago`;
}

function escapeHtml(s) {
    if (!s) return '';
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
