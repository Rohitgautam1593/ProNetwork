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

    let html = '';

    if (results.people?.length > 0) {
        html += `<div class="px-4 py-2 text-xs font-bold text-slate-500 uppercase tracking-wider">People</div>`;
        results.people.forEach(p => {
            const pic = p.profile_pic ? (p.profile_pic.startsWith('http') ? p.profile_pic : `${URLROOT}/uploads/profiles/` + p.profile_pic) : '';
            html += `
            <a href="${URLROOT}/user/profile/${p.user_id}" class="flex items-center gap-3 px-4 py-2 hover:bg-slate-50 transition-colors">
                ${pic ? `<img src="${pic}" class="w-10 h-10 rounded-full object-cover">` : `<div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center"><span class="material-symbols-outlined text-slate-400">person</span></div>`}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 truncate">${escapeHtml(p.full_name)}</p>
                    <p class="text-xs text-slate-500 truncate">${escapeHtml(p.headline || '')}</p>
                </div>
            </a>`;
        });
    }

    if (results.jobs?.length > 0) {
        html += `<div class="px-4 py-2 mt-2 text-xs font-bold text-slate-500 uppercase tracking-wider border-t border-slate-100 pt-3">Jobs</div>`;
        results.jobs.forEach(j => {
            html += `
            <a href="${URLROOT}/user/jobs?id=${j.job_id}" class="flex items-center gap-3 px-4 py-2 hover:bg-slate-50 transition-colors">
                <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center"><span class="material-symbols-outlined text-slate-400">work</span></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 truncate">${escapeHtml(j.title)}</p>
                    <p class="text-xs text-slate-500 truncate">${escapeHtml(j.company_name)} • ${escapeHtml(j.location)}</p>
                </div>
            </a>`;
        });
    }

    if (!html) {
        html = `<div class="px-4 py-6 text-center text-slate-500 text-sm">No results found for "${escapeHtml(document.getElementById('global-search-input').value)}"</div>`;
    }

    div.innerHTML = html;
}

function escapeHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}
