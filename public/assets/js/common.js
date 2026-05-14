/**
 * ProNetwork — Common Logic
 * assets/js/common.js
 * Handles populating user data on all pages.
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
    initApp();
    initGlobalSearch();
});

async function initApp() {
    hydrateUserState();
    try {
        const res = await fetch(`${URLROOT}/user/me`);
        const data = await res.json();
        if (data.success) {
            const u = data.user;
            setUserState(u);
            populateUserData(u);
        }
    } catch(e) {
        console.error('Failed to init app:', e);
    }
}

function setUserState(user) {
    localStorage.setItem('pn_user', JSON.stringify(user));
}

function getUserState() {
    const user = localStorage.getItem('pn_user');
    return user ? JSON.parse(user) : null;
}

function hydrateUserState() {
    const u = getUserState();
    if (u) populateUserData(u);
}

function populateUserData(u) {
    if (!u) return;
    const placeholderAvatar =
        'data:image/svg+xml,' +
        encodeURIComponent(
            '<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 128 128"><rect fill="#e2e8f0" width="128" height="128"/><text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" fill="#64748b" font-family="system-ui,sans-serif" font-size="40" font-weight="600">' +
                String((u.full_name || '?').trim().charAt(0) || '?').toUpperCase() +
                '</text></svg>'
        );

    document.querySelectorAll('[data-user-name="full"]').forEach(el => { el.textContent = u.full_name || ''; });
    document.querySelectorAll('[data-user-headline]').forEach(el => { el.textContent = u.headline || 'Add a headline'; });
    document.querySelectorAll('[data-user-location]').forEach(el => { el.textContent = u.location || 'Location not set'; });
    document.querySelectorAll('[data-user-industry]').forEach(el => { el.textContent = u.industry || 'Industry not set'; });
    document.querySelectorAll('[data-user-bio]').forEach(el => {
        if (el.tagName === 'TEXTAREA') el.value = u.bio || '';
        else el.textContent = u.bio || 'No bio yet.';
    });
    document.querySelectorAll('[data-user-email]').forEach(el => { el.textContent = u.email || ''; });

    const parts = [u.full_name, u.location, u.industry].filter(Boolean);
    document.querySelectorAll('[data-user-summary]').forEach(el => {
        el.textContent = parts.length ? parts.join(' · ') : 'Complete your profile to stand out.';
    });

    const picUrl = u.profile_pic
        ? (u.profile_pic.startsWith('http') ? u.profile_pic : `${URLROOT}/uploads/profiles/${u.profile_pic}`)
        : placeholderAvatar;
    document.querySelectorAll('img[data-user-pic="true"]').forEach(img => {
        img.src = picUrl;
        img.alt = u.full_name ? `${u.full_name} profile photo` : 'Profile photo';
    });
    document.querySelectorAll('[data-user-pic]').forEach(el => {
        if (el.tagName === 'IMG') {
            el.src = picUrl;
            el.alt = u.full_name ? `${u.full_name} profile photo` : 'Profile photo';
        }
    });

    document.querySelectorAll('img[data-user-cover="true"]').forEach(img => {
        if (u.cover_image) {
            const coverUrl = u.cover_image.startsWith('http') ? u.cover_image : `${URLROOT}/uploads/covers/${u.cover_image}`;
            img.src = coverUrl;
            img.classList.remove('hidden');
        } else {
            img.removeAttribute('src');
            img.classList.add('hidden');
        }
    });
}

function escapeHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

function initGlobalSearch() {
    const input = document.getElementById('global-search-input');
    const results = document.getElementById('global-search-results');
    const form = document.getElementById('global-search-form');
    if (!input || !results || !form) return;

    let timer = null;
    let lastQuery = '';

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const term = input.value.trim();
        if (term.length >= 2) runSearch(term);
    });

    input.addEventListener('input', () => {
        const term = input.value.trim();
        clearTimeout(timer);

        if (term.length < 2) {
            hideSearchResults();
            return;
        }

        results.classList.remove('hidden');
        results.innerHTML = '<div class="px-4 py-3 text-sm text-slate-500">Searching...</div>';
        timer = setTimeout(() => runSearch(term), 220);
    });

    input.addEventListener('focus', () => {
        if (results.innerHTML.trim()) results.classList.remove('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#global-search')) hideSearchResults();
    });

    async function runSearch(term) {
        if (term === lastQuery && !results.classList.contains('hidden')) return;
        lastQuery = term;

        try {
            const response = await fetch(`${URLROOT}/search?q=${encodeURIComponent(term)}`);
            const data = await response.json();
            if (!data.success) {
                renderSearchMessage(data.message || 'Search failed.');
                return;
            }
            renderSearchResults(data.results, term);
        } catch (error) {
            renderSearchMessage('Unable to search right now.');
        }
    }

    function hideSearchResults() {
        results.classList.add('hidden');
    }

    function renderSearchMessage(message) {
        results.classList.remove('hidden');
        results.innerHTML = `<div class="px-4 py-3 text-sm text-slate-500">${escapeHtml(message)}</div>`;
    }

    function renderSearchResults(data, term) {
        const people = data.people || [];
        const jobs = data.jobs || [];
        const companies = data.companies || [];
        const posts = data.posts || [];
        const total = people.length + jobs.length + companies.length + posts.length;

        results.classList.remove('hidden');
        if (!total) {
            renderSearchMessage(`No results for "${term}"`);
            return;
        }

        results.innerHTML = [
            renderSection('People', people.map(person => ({
                icon: person.profile_pic ? imageUrl('profiles', person.profile_pic) : '',
                fallback: 'person',
                title: person.full_name,
                subtitle: [person.headline || 'Professional', person.location || ''].filter(Boolean).join(' · '),
                href: `${URLROOT}/user/messaging?chat=${person.user_id}`
            }))),
            renderSection('Jobs', jobs.map(job => ({
                icon: job.logo ? imageUrl('companies', job.logo) : '',
                fallback: 'work',
                title: job.title,
                subtitle: [job.company_name, job.location || job.job_type || ''].filter(Boolean).join(' · '),
                href: `${URLROOT}/user/jobs`
            }))),
            renderSection('Companies', companies.map(company => ({
                icon: company.logo ? imageUrl('companies', company.logo) : '',
                fallback: 'business',
                title: company.company_name,
                subtitle: company.industry || 'Company',
                href: `${URLROOT}/company/dashboard`
            }))),
            renderSection('Posts', posts.map(post => ({
                icon: '',
                fallback: 'article',
                title: post.content.length > 70 ? post.content.slice(0, 70) + '...' : post.content,
                subtitle: `Post by ${post.full_name}`,
                href: `${URLROOT}/user/feed`
            })))
        ].join('');
    }

    function renderSection(label, items) {
        if (!items.length) return '';
        return `
            <div class="py-1">
                <div class="px-4 py-2 text-[11px] uppercase tracking-wide font-bold text-slate-500">${escapeHtml(label)}</div>
                ${items.map(renderItem).join('')}
            </div>
        `;
    }

    function renderItem(item) {
        const avatar = item.icon
            ? `<img src="${escapeHtml(item.icon)}" alt="" class="w-9 h-9 rounded object-cover bg-slate-100">`
            : `<div class="w-9 h-9 rounded bg-[#eef3f8] flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-slate-500 text-[20px]">${escapeHtml(item.fallback)}</span></div>`;

        return `
            <a href="${escapeHtml(item.href)}" class="flex gap-3 px-4 py-2 hover:bg-slate-50 transition-colors">
                ${avatar}
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-slate-900 truncate">${escapeHtml(item.title || '')}</span>
                    <span class="block text-xs text-slate-500 truncate">${escapeHtml(item.subtitle || '')}</span>
                </span>
            </a>
        `;
    }

    function imageUrl(folder, value) {
        return value.startsWith('http') ? value : `${URLROOT}/uploads/${folder}/${value}`;
    }
}

/**
 * Custom Professional Modal
 * Replaces standard alert/confirm/prompt
 * @param {Object} options { title, message, type, isPrompt, placeholder, confirmText, cancelText, isDanger }
 * @returns {Promise} Resolves with value (true/false or input string)
 */
window.pnModal = function(options = {}) {
    return new Promise((resolve) => {
        const {
            title = 'Notification',
            message = '',
            type = 'info', // info, warning, success, flag
            isPrompt = false,
            placeholder = 'Enter details...',
            confirmText = 'Confirm',
            cancelText = 'Cancel',
            isDanger = false,
            defaultValue = ''
        } = options;

        // Create elements
        const backdrop = document.createElement('div');
        backdrop.className = 'pn-modal-backdrop';
        
        const iconMap = {
            info: 'info',
            warning: 'warning',
            success: 'check_circle',
            flag: 'flag'
        };

        const safeTitle = escapeHtml(title);
        const safeMessage = escapeHtml(message);
        const safePlaceholder = escapeHtml(placeholder);
        const safeDefaultValue = escapeHtml(defaultValue);
        const safeConfirmText = escapeHtml(confirmText);
        const safeCancelText = escapeHtml(cancelText);

        backdrop.innerHTML = `
            <div class="pn-modal-container">
                <div class="pn-modal-header">
                    <div class="pn-modal-icon ${type}">
                        <span class="material-symbols-outlined">${iconMap[type] || 'info'}</span>
                    </div>
                    <h3 class="pn-modal-title">${safeTitle}</h3>
                </div>
                <div class="pn-modal-body">
                    <p class="pn-modal-message">${safeMessage}</p>
                    ${isPrompt ? `<textarea class="pn-modal-input" placeholder="${safePlaceholder}" rows="3">${safeDefaultValue}</textarea>` : ''}
                </div>
                <div class="pn-modal-footer">
                    <button class="pn-modal-btn pn-modal-btn-secondary" id="pn-modal-cancel">${safeCancelText}</button>
                    <button class="pn-modal-btn ${isDanger ? 'pn-modal-btn-danger' : 'pn-modal-btn-primary'}" id="pn-modal-confirm">${safeConfirmText}</button>
                </div>
            </div>
        `;

        document.body.appendChild(backdrop);

        // Animation
        setTimeout(() => backdrop.classList.add('active'), 10);
        if (isPrompt) {
            setTimeout(() => {
                const input = backdrop.querySelector('.pn-modal-input');
                if (input) {
                    input.focus();
                    input.setSelectionRange(input.value.length, input.value.length);
                }
            }, 100);
        }

        const close = (val) => {
            backdrop.classList.remove('active');
            setTimeout(() => {
                backdrop.remove();
                resolve(val);
            }, 300);
        };

        backdrop.querySelector('#pn-modal-confirm').onclick = () => {
            if (isPrompt) {
                const input = backdrop.querySelector('.pn-modal-input').value;
                close(input);
            } else {
                close(true);
            }
        };

        backdrop.querySelector('#pn-modal-cancel').onclick = () => close(isPrompt ? null : false);
        
        // Close on backdrop click
        backdrop.onclick = (e) => {
            if (e.target === backdrop) close(isPrompt ? null : false);
        };
    });
};
