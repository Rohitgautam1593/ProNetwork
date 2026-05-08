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
    // Update all elements with dynamic data attributes
    document.querySelectorAll('[data-user-name="full"]').forEach(el => el.textContent = u.full_name);
    document.querySelectorAll('[data-user-headline]').forEach(el => el.textContent = u.headline || 'Professional');
    document.querySelectorAll('[data-user-location]').forEach(el => el.textContent = u.location || 'Location not set');
    document.querySelectorAll('[data-user-bio]').forEach(el => {
        if (el.tagName === 'TEXTAREA') el.value = u.bio || '';
        else el.textContent = u.bio || 'No bio yet.';
    });
    document.querySelectorAll('[data-user-email]').forEach(el => el.textContent = u.email);
    
    if (u.profile_pic) {
        const picUrl = u.profile_pic.startsWith('http') ? u.profile_pic : `${URLROOT}/uploads/profiles/` + u.profile_pic;
        document.querySelectorAll('img[data-user-pic="true"]').forEach(img => img.src = picUrl);
        document.querySelectorAll('[data-user-pic]').forEach(el => { if(el.tagName === 'IMG') el.src = picUrl; });
    }
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
