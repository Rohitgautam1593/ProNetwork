/**
 * ProNetwork — Network page logic
 */
'use strict';

const PN_SUGGESTIONS_LIMIT = 12;
const PN_INVITATIONS_PREVIEW = 5;
const PN_DISMISSED_KEY = 'pn_dismissed_suggestions';

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('suggestions-grid')) initNetworkPage();
    if (document.getElementById('connections-list')) initConnectionsListPage();
    if (document.getElementById('invitations-list')) initInvitationsListPage();
    if (document.getElementById('pages-list')) initPagesListPage();
    if (document.getElementById('suggestions-list')) initSuggestionsListPage();
});

function initNetworkPage() {
    initSidebarToggle();
    void Promise.all([
        fetchSummary(),
        fetchFollowedPages(),
        fetchSuggestions(PN_SUGGESTIONS_LIMIT, 'suggestions-grid'),
        fetchInvitations(PN_INVITATIONS_PREVIEW),
    ]);
}

function initSidebarToggle() {
    const btn = document.querySelector('aside .pn-network-sidebar-toggle');
    const nav = document.querySelector('aside nav.pn-network-sidebar-nav');
    if (!btn || !nav) return;
    btn.addEventListener('click', () => {
        const collapsed = nav.classList.toggle('hidden');
        btn.textContent = collapsed ? 'Show more' : 'Show less';
    });
}

function pnNetworkError(message) {
    return `<p class="col-span-full text-center text-sm text-red-500 py-6">${escapeHtml(message)}</p>`;
}

function getDismissedSuggestions() {
    try {
        return JSON.parse(sessionStorage.getItem(PN_DISMISSED_KEY) || '[]');
    } catch {
        return [];
    }
}

function dismissSuggestion(userId) {
    const dismissed = getDismissedSuggestions();
    const id = String(userId);
    if (!dismissed.includes(id)) {
        dismissed.push(id);
        sessionStorage.setItem(PN_DISMISSED_KEY, JSON.stringify(dismissed));
    }
}

async function fetchSummary() {
    try {
        const res = await fetch(`${URLROOT}/network/summary`);
        const data = await res.json();
        if (!data.success) return;
        const set = (id, n) => {
            const el = document.getElementById(id);
            if (el) el.textContent = Number(n || 0).toLocaleString();
        };
        set('connections-count', data.connections);
        set('pages-count', data.pages);
    } catch (e) {
        console.error('Failed to fetch network summary:', e);
    }
}

async function fetchFollowedPages() {
    const grid = document.getElementById('followed-pages-grid');
    const badge = document.getElementById('followed-pages-count');
    if (!grid) return;

    try {
        const res = await fetch(`${URLROOT}/network/pages`);
        const data = await res.json();
        if (!data.success) {
            grid.innerHTML = pnNetworkError('Could not load followed pages.');
            return;
        }
        if (badge) badge.textContent = data.pages.length;
        if (!data.pages.length) {
            grid.innerHTML = `<p class="col-span-full text-xs text-slate-500 italic py-4 text-center">You are not following any company pages yet. <a href="${URLROOT}/company" class="text-[#0A66C2] font-semibold hover:underline">Explore companies</a></p>`;
            return;
        }
        grid.innerHTML = data.pages.map((c) => {
            const logo = pnCompanyLogoImg(c, 'w-12 h-12 rounded object-cover border border-slate-200 shrink-0 bg-white');
            return `<div class="border border-slate-100 rounded-lg p-3 flex items-center gap-3 hover:bg-slate-50 transition-colors cursor-pointer" onclick="window.location.href='${URLROOT}/company/show/${c.company_id}'">${logo}<div class="min-w-0 flex-1"><h4 class="text-sm font-bold text-slate-900 truncate hover:underline">${escapeHtml(c.company_name)}</h4><p class="text-xs text-slate-500 truncate">${escapeHtml(c.industry || 'Company')}</p></div></div>`;
        }).join('');
    } catch (e) {
        console.error('Failed to fetch pages:', e);
        grid.innerHTML = pnNetworkError('Could not load followed pages.');
    }
}

function renderSuggestionCard(user) {
    const card = document.createElement('div');
    card.className = 'border border-slate-200 rounded-lg overflow-hidden flex flex-col items-center text-center relative group hover:shadow-md transition-shadow bg-white pn-suggestion-card';
    card.dataset.userId = user.user_id;
    const profileUrl = pnUserProfileUrl(user.user_id);
    card.innerHTML = `
        <div class="h-16 w-full bg-gradient-to-r from-blue-400 to-indigo-600 cursor-pointer pn-suggestion-banner" data-href="${profileUrl}"></div>
        <button type="button" class="dismiss-suggestion absolute top-2 right-2 w-7 h-7 bg-black/20 text-white rounded-full flex items-center justify-center hover:bg-black/40 transition-colors" title="Dismiss" aria-label="Dismiss suggestion">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>
        <div class="cursor-pointer pn-suggestion-avatar" data-href="${profileUrl}">${pnAvatarImg(user, 'w-20 h-20 rounded-full border-4 border-white -mt-10 object-cover relative z-10')}</div>
        <div class="p-4 pt-2 flex flex-col flex-1 w-full">
            <h3 class="font-semibold text-slate-900 line-clamp-1 hover:underline cursor-pointer pn-suggestion-name" data-href="${profileUrl}">${escapeHtml(user.full_name)}</h3>
            <p class="text-xs text-slate-500 line-clamp-2 min-h-[32px] mt-1">${escapeHtml(user.headline || user.industry || 'Professional')}</p>
            <div class="mt-auto pt-4">
                <button type="button" class="connect-btn w-full border-2 border-[#0A66C2] text-[#0A66C2] font-semibold py-1 rounded-full hover:bg-blue-50 transition-colors" data-user-id="${user.user_id}">Connect</button>
            </div>
        </div>`;
    return card;
}

function wireSuggestionCard(card) {
    card.querySelectorAll('[data-href]').forEach((el) => {
        el.addEventListener('click', () => {
            window.location.href = el.dataset.href;
        });
    });
    card.querySelector('.dismiss-suggestion')?.addEventListener('click', (e) => {
        e.stopPropagation();
        dismissSuggestion(card.dataset.userId);
        card.remove();
    });
    const btn = card.querySelector('.connect-btn');
    btn?.addEventListener('click', async (e) => {
        e.stopPropagation();
        await sendConnectionRequest(btn);
    });
}

async function sendConnectionRequest(btn) {
    const userId = btn.getAttribute('data-user-id');
    if (!userId || btn.disabled) return;
    btn.disabled = true;
    btn.textContent = 'Sending…';
    try {
        const res = await fetch(`${URLROOT}/network/send_request`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId }),
        });
        const data = await res.json();
        if (data.success) {
            btn.textContent = 'Pending';
            btn.classList.remove('text-[#0A66C2]', 'border-[#0A66C2]', 'hover:bg-blue-50');
            btn.classList.add('text-slate-400', 'border-slate-300', 'cursor-not-allowed');
        } else {
            btn.disabled = false;
            btn.textContent = 'Connect';
            alert(data.message || 'Could not send request.');
        }
    } catch (err) {
        console.error(err);
        btn.disabled = false;
        btn.textContent = 'Connect';
    }
}

async function fetchSuggestions(limit, containerId) {
    const grid = document.getElementById(containerId);
    if (!grid) return;

    grid.innerHTML = '<p class="col-span-full text-center text-slate-400 py-8 text-sm">Loading suggestions…</p>';

    try {
        const res = await fetch(`${URLROOT}/network/suggestions?limit=${limit}`);
        const data = await res.json();
        if (!data.success) {
            grid.innerHTML = pnNetworkError('Could not load suggestions.');
            return;
        }

        const dismissed = getDismissedSuggestions();
        const list = (data.suggestions || []).filter((u) => !dismissed.includes(String(u.user_id)));

        grid.innerHTML = '';
        if (!list.length) {
            grid.innerHTML = '<p class="col-span-full text-center text-slate-500 py-8">No new suggestions at this time.</p>';
            return;
        }

        list.forEach((user) => {
            const card = renderSuggestionCard(user);
            wireSuggestionCard(card);
            grid.appendChild(card);
        });
    } catch (err) {
        console.error(err);
        grid.innerHTML = pnNetworkError('Could not load suggestions.');
    }
}

async function fetchInvitations(previewLimit) {
    const container = document.getElementById('invitations-container');
    const seeAllBtn = document.getElementById('see-all-invitations');
    if (!container) return;

    try {
        const res = await fetch(`${URLROOT}/network/pending`);
        const data = await res.json();
        if (!data.success) {
            container.innerHTML = pnNetworkError('Could not load invitations.');
            return;
        }

        const requests = data.requests || [];
        if (seeAllBtn) {
            seeAllBtn.textContent = requests.length ? `See all ${requests.length}` : 'See all';
        }

        container.innerHTML = '';
        if (!requests.length) {
            container.innerHTML = '<p class="text-center text-slate-500 py-4">No pending invitations.</p>';
            return;
        }

        const shown = previewLimit ? requests.slice(0, previewLimit) : requests;
        shown.forEach((req) => container.appendChild(renderInvitationRow(req)));

        if (previewLimit && requests.length > previewLimit) {
            const more = document.createElement('p');
            more.className = 'text-center text-xs text-slate-500 py-3 border-t border-slate-100';
            more.innerHTML = `<a href="${URLROOT}/network/invitations_list" class="font-semibold text-[#0A66C2] hover:underline">${requests.length - previewLimit} more invitation(s)</a>`;
            container.appendChild(more);
        }
    } catch (err) {
        console.error(err);
        container.innerHTML = pnNetworkError('Could not load invitations.');
    }
}

function renderInvitationRow(req) {
    const item = document.createElement('div');
    item.className = 'p-4 flex items-start gap-4 border-b border-slate-100 last:border-0';
    const profileUrl = pnUserProfileUrl(req.user_id);
    item.innerHTML = `
        ${pnAvatarImg(req, 'w-14 h-14 rounded-full object-cover shrink-0')}
        <div class="flex-1 min-w-0">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="font-semibold text-slate-900 hover:underline cursor-pointer pn-invite-name" data-href="${profileUrl}">${escapeHtml(req.full_name)}</h3>
                    <p class="text-sm text-slate-500 leading-tight line-clamp-2">${escapeHtml(req.headline || 'Professional')}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" class="accept-btn bg-[#0A66C2] text-white font-semibold px-4 py-1.5 rounded-full hover:bg-[#004182] transition-colors" data-user-id="${req.user_id}">Accept</button>
                    <button type="button" class="ignore-btn text-slate-500 font-semibold px-4 py-1.5 rounded-full hover:bg-slate-100 transition-colors" data-user-id="${req.user_id}">Ignore</button>
                </div>
            </div>
        </div>`;

    item.querySelector('.pn-invite-name')?.addEventListener('click', () => {
        window.location.href = profileUrl;
    });
    item.querySelector('.accept-btn')?.addEventListener('click', async () => {
        const ok = await handleInvitation(req.user_id, 'accept');
        if (ok) {
            item.remove();
            refreshAfterInvitationChange();
        }
    });
    item.querySelector('.ignore-btn')?.addEventListener('click', async () => {
        const ok = await handleInvitation(req.user_id, 'reject');
        if (ok) {
            item.remove();
            refreshAfterInvitationChange();
        }
    });
    return item;
}

async function handleInvitation(userId, action) {
    try {
        const res = await fetch(`${URLROOT}/network/${action}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId }),
        });
        const data = await res.json();
        if (!data.success) alert(data.message || 'Action failed.');
        return data.success;
    } catch (e) {
        console.error(e);
        return false;
    }
}

function refreshAfterInvitationChange() {
    const container = document.getElementById('invitations-container');
    if (!container) return;
    if (!container.querySelector('.accept-btn')) {
        container.innerHTML = '<p class="text-center text-slate-500 py-4">No pending invitations.</p>';
    }
    fetchSummary();
}

function initConnectionsListPage() {
    const list = document.getElementById('connections-list');
    const totalEl = document.getElementById('total-connections');
    if (!list) return;

    fetch(`${URLROOT}/network/connections`)
        .then((r) => r.json())
        .then((data) => {
            if (!data.success) {
                list.innerHTML = pnNetworkError('Failed to load connections.');
                return;
            }
            const connections = data.connections || [];
            if (totalEl) totalEl.textContent = `${connections.length} connection${connections.length === 1 ? '' : 's'}`;
            if (!connections.length) {
                list.innerHTML = '<div class="text-center py-12 text-slate-500">You don\'t have any connections yet.</div>';
                return;
            }
            list.innerHTML = connections.map((u) => {
                const profileUrl = pnUserProfileUrl(u.user_id);
                return `<div class="flex items-center justify-between p-4 border border-slate-100 rounded-lg hover:bg-slate-50 transition-colors gap-4">
                    <div class="flex items-center gap-4 min-w-0">
                        ${pnAvatarImg(u, 'w-16 h-16 rounded-full object-cover border border-slate-200 shrink-0')}
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-900 hover:underline cursor-pointer" onclick="window.location.href='${profileUrl}'">${escapeHtml(u.full_name)}</h3>
                            <p class="text-sm text-slate-500 line-clamp-1">${escapeHtml(u.headline || 'Professional')}</p>
                        </div>
                    </div>
                    <button type="button" class="shrink-0 px-4 py-1.5 border border-[#0A66C2] text-[#0A66C2] font-semibold rounded-full hover:bg-blue-50 transition-colors" onclick="window.location.href='${URLROOT}/user/messaging?chat=${u.user_id}'">Message</button>
                </div>`;
            }).join('');
        })
        .catch((e) => {
            console.error(e);
            list.innerHTML = pnNetworkError('Failed to load connections.');
        });
}

function initInvitationsListPage() {
    const list = document.getElementById('invitations-list');
    const totalEl = document.getElementById('total-invitations');
    if (!list) return;

    const load = async () => {
        try {
            const res = await fetch(`${URLROOT}/network/pending`);
            const data = await res.json();
            if (!data.success) {
                list.innerHTML = pnNetworkError('Failed to load invitations.');
                return;
            }
            const requests = data.requests || [];
            if (totalEl) totalEl.textContent = `${requests.length} pending invitation${requests.length === 1 ? '' : 's'}`;
            list.innerHTML = '';
            if (!requests.length) {
                list.innerHTML = '<p class="text-center py-12 text-slate-500">You don\'t have any pending invitations.</p>';
                return;
            }
            requests.forEach((req) => list.appendChild(renderInvitationRow(req)));
        } catch (e) {
            console.error(e);
            list.innerHTML = pnNetworkError('Failed to load invitations.');
        }
    };
    load();
}

function initPagesListPage() {
    const list = document.getElementById('pages-list');
    const totalEl = document.getElementById('total-pages');
    if (!list) return;

    fetch(`${URLROOT}/network/pages`)
        .then((r) => r.json())
        .then((data) => {
            if (!data.success) {
                list.innerHTML = pnNetworkError('Failed to load pages.');
                return;
            }
            const pages = data.pages || [];
            if (totalEl) totalEl.textContent = `${pages.length} page${pages.length === 1 ? '' : 's'}`;
            if (!pages.length) {
                list.innerHTML = '<p class="text-center py-12 col-span-full text-slate-500">You are not following any pages yet.</p>';
                return;
            }
            list.innerHTML = pages.map((p) => `
                <div class="flex items-center gap-4 p-4 border border-slate-100 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer" onclick="window.location.href='${URLROOT}/company/show/${p.company_id}'">
                    ${pnCompanyLogoImg(p, 'w-16 h-16 rounded object-cover border border-slate-200 bg-white shrink-0')}
                    <div class="min-w-0">
                        <h3 class="font-bold text-slate-900 truncate hover:underline">${escapeHtml(p.company_name)}</h3>
                        <p class="text-sm text-slate-500 truncate">${escapeHtml(p.industry || 'Company')}</p>
                        <p class="text-xs text-[#0A66C2] font-semibold mt-1">Following</p>
                    </div>
                </div>`).join('');
        })
        .catch((e) => {
            console.error(e);
            list.innerHTML = pnNetworkError('Failed to load pages.');
        });
}

function initSuggestionsListPage() {
    fetchSuggestions(50, 'suggestions-list');
}
