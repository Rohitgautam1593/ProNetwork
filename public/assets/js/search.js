/**
 * ProNetwork — Global Search Logic
 * assets/js/search.js
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('global-search-input');
    const resultsDiv = document.getElementById('global-search-results');
    const form = document.getElementById('global-search-form');

    if (input && resultsDiv) {
        let timeout = null;

        input.addEventListener('input', () => {
            clearTimeout(timeout);
            const query = input.value.trim();

            if (query.length < 2) {
                resultsDiv.classList.add('hidden');
                return;
            }

            timeout = setTimeout(async () => {
                try {
                    const response = await fetch(`${URLROOT}/search?q=${encodeURIComponent(query)}`);
                    const data = await response.json();

                    if (data.success) {
                        renderSearchResults(data.results);
                        resultsDiv.classList.remove('hidden');
                    }
                } catch (err) {
                    console.error(err);
                }
            }, 300);
        });

        // Close search results on click outside
        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !resultsDiv.contains(e.target)) {
                resultsDiv.classList.add('hidden');
            }
        });

        form?.addEventListener('submit', (e) => e.preventDefault());
    }
});

function renderSearchResults(results) {
    const div = document.getElementById('global-search-results');
    if (!div) return;

    div.dataset.rawResults = JSON.stringify(results);
    const currentFilter = div.dataset.activeFilter || 'all';

    let html = `
        <div class="px-4 pb-2 border-b border-slate-100 sticky top-0 bg-white z-10 flex gap-2">
            <button class="search-filter-btn px-3 py-1 text-xs font-semibold rounded-full border transition-colors ${currentFilter === 'all' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'}" data-filter="all">All</button>
            <button class="search-filter-btn px-3 py-1 text-xs font-semibold rounded-full border transition-colors ${currentFilter === 'people' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'}" data-filter="people">People</button>
            <button class="search-filter-btn px-3 py-1 text-xs font-semibold rounded-full border transition-colors ${currentFilter === 'companies' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'}" data-filter="companies">Companies</button>
        </div>
        <div class="py-2">
    `;

    let hasResults = false;

    if (results.people?.length > 0 && (currentFilter === 'all' || currentFilter === 'people')) {
        hasResults = true;
        html += `<div class="px-4 py-2 text-xs font-bold text-slate-500 uppercase tracking-wider">People</div>`;
        results.people.forEach(p => {
            const pic = pnProfilePicUrl(p);
            html += `
            <a href="${URLROOT}/user/profile?id=${p.user_id}" class="flex items-center gap-3 px-4 py-2 hover:bg-slate-50 transition-colors">
                <img src="${pic}" class="w-10 h-10 rounded-full object-cover shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 truncate flex items-center gap-2">
                        ${escapeHtml(p.full_name)}
                        <span class="text-[10px] font-bold bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded border border-indigo-100">User Profile</span>
                    </p>
                    <p class="text-xs text-slate-500 truncate">${escapeHtml(p.headline || '')}</p>
                </div>
            </a>`;
        });
    }

    if (results.jobs?.length > 0 && currentFilter === 'all') {
        hasResults = true;
        html += `<div class="px-4 py-2 mt-2 text-xs font-bold text-slate-500 uppercase tracking-wider border-t border-slate-100 pt-3">Jobs</div>`;
        results.jobs.forEach(j => {
            html += `
            <a href="${URLROOT}/user/jobs?id=${j.job_id}" class="flex items-center gap-3 px-4 py-2 hover:bg-slate-50 transition-colors">
                <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-slate-400">work</span></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 truncate">${escapeHtml(j.title)}</p>
                    <p class="text-xs text-slate-500 truncate">${escapeHtml(j.company_name)} • ${escapeHtml(j.location)}</p>
                </div>
            </a>`;
        });
    }

    if (results.companies?.length > 0 && (currentFilter === 'all' || currentFilter === 'companies')) {
        hasResults = true;
        html += `<div class="px-4 py-2 mt-2 text-xs font-bold text-slate-500 uppercase tracking-wider border-t border-slate-100 pt-3">Companies</div>`;
        results.companies.forEach(c => {
            const logo = pnCompanyLogoUrl(c);
            html += `
            <a href="${URLROOT}/company/show/${c.company_id}" class="flex items-center gap-3 px-4 py-2 hover:bg-slate-50 transition-colors">
                <img src="${logo}" class="w-10 h-10 rounded object-cover border border-slate-100 shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 truncate flex items-center gap-2">
                        ${escapeHtml(c.company_name)}
                        <span class="text-[10px] font-bold bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded border border-emerald-100">Company Page</span>
                    </p>
                    <p class="text-xs text-slate-500 truncate">${escapeHtml(c.industry || 'Company')}</p>
                </div>
            </a>`;
        });
    }

    if (results.posts?.length > 0 && currentFilter === 'all') {
        hasResults = true;
        html += `<div class="px-4 py-2 mt-2 text-xs font-bold text-slate-500 uppercase tracking-wider border-t border-slate-100 pt-3">Posts</div>`;
        results.posts.forEach(pt => {
            html += `
            <a href="${URLROOT}/user/feed" class="flex items-center gap-3 px-4 py-2 hover:bg-slate-50 transition-colors">
                <div class="w-10 h-10 rounded bg-slate-50 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-slate-400">article</span></div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-700 truncate">${escapeHtml(pt.full_name)}</p>
                    <p class="text-xs text-slate-500 truncate">${escapeHtml(pt.content)}</p>
                </div>
            </a>`;
        });
    }

    if (!hasResults) {
        html += `<div class="px-4 py-6 text-center text-slate-500 text-sm">No results found for "${escapeHtml(document.getElementById('global-search-input').value)}"</div>`;
    }

    html += `</div>`;
    div.innerHTML = html;

    div.querySelectorAll('.search-filter-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            div.dataset.activeFilter = e.target.dataset.filter;
            const rawData = JSON.parse(div.dataset.rawResults);
            renderSearchResults(rawData);
            document.getElementById('global-search-input').focus();
        });
    });
}

function escapeHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}
