// navigation.js
'use strict';

document.addEventListener('DOMContentLoaded', () => {
    initProfileMenu();
    initMobileSearch();
});

function initProfileMenu() {
    const toggle = document.getElementById('pn-profile-menu-toggle');
    const menu = document.getElementById('pn-profile-menu');
    if (!toggle || !menu) return;

    const close = () => {
        menu.classList.add('hidden');
        toggle.setAttribute('aria-expanded', 'false');
    };
    const open = () => {
        menu.classList.remove('hidden');
        toggle.setAttribute('aria-expanded', 'true');
    };

    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.classList.contains('hidden') ? open() : close();
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.pn-profile-menu-wrap')) close();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
}

function initMobileSearch() {
    const input = document.getElementById('global-search-input-mobile');
    const results = document.getElementById('global-search-results-mobile');
    const form = document.querySelector('#global-search-mobile form');
    if (!input || !results || !form) return;

    let timer = null;
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        runMobileSearch(input.value.trim());
    });

    input.addEventListener('input', () => {
        const term = input.value.trim();
        clearTimeout(timer);
        if (term.length < 2) {
            results.classList.add('hidden');
            results.innerHTML = '';
            return;
        }
        results.classList.remove('hidden');
        results.innerHTML = '<div class="px-4 py-3 text-sm text-slate-500">Searching...</div>';
        timer = setTimeout(() => runMobileSearch(term), 220);
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#global-search-mobile')) results.classList.add('hidden');
    });

    async function runMobileSearch(term) {
        if (term.length < 2) return;
        try {
            const response = await fetch(`${URLROOT}/search?q=${encodeURIComponent(term)}`);
            const data = await response.json();
            if (!data.success) {
                renderMobileSearchMessage(results, data.message || 'Search failed.');
                return;
            }
            renderMobileSearchResults(results, data.results || {}, term);
        } catch {
            renderMobileSearchMessage(results, 'Unable to search right now.');
        }
    }
}

function renderMobileSearchMessage(results, message) {
    results.classList.remove('hidden');
    results.innerHTML = `<div class="px-4 py-3 text-sm text-slate-500">${escapeNavHtml(message)}</div>`;
}

function renderMobileSearchResults(results, data, term) {
    const people = data.people || [];
    const jobs = data.jobs || [];
    const companies = data.companies || [];
    const posts = data.posts || [];
    const total = people.length + jobs.length + companies.length + posts.length;
    if (!total) {
        renderMobileSearchMessage(results, `No results for "${term}"`);
        return;
    }

    const items = [
        ...people.map(person => ({ icon: 'person', title: person.full_name, subtitle: person.headline || 'Professional', href: `${URLROOT}/user/profile?id=${person.user_id}` })),
        ...jobs.map(job => ({ icon: 'work', title: job.title, subtitle: [job.company_name, job.location].filter(Boolean).join(' · '), href: `${URLROOT}/user/jobs` })),
        ...companies.map(company => ({ icon: 'business', title: company.company_name, subtitle: company.industry || 'Company', href: `${URLROOT}/company/show/${company.company_id}` })),
        ...posts.map(post => ({ icon: 'article', title: post.content.length > 70 ? post.content.slice(0, 70) + '...' : post.content, subtitle: `Post by ${post.full_name}`, href: `${URLROOT}/user/feed` }))
    ].slice(0, 8);

    results.classList.remove('hidden');
    results.innerHTML = items.map(item => `
        <a href="${escapeNavHtml(item.href)}" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition-colors">
            <span class="w-9 h-9 rounded-xl bg-blue-50 text-[#0A66C2] flex items-center justify-center shrink-0 material-symbols-outlined text-[20px]">${escapeNavHtml(item.icon)}</span>
            <span class="min-w-0">
                <span class="block text-sm font-bold text-slate-900 truncate">${escapeNavHtml(item.title || '')}</span>
                <span class="block text-xs text-slate-500 truncate">${escapeNavHtml(item.subtitle || '')}</span>
            </span>
        </a>
    `).join('');
}

function escapeNavHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

