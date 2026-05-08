/**
 * ProNetwork — Jobs Logic
 * assets/js/jobs.js
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('jobs-container')) {
        fetchJobs();
    }
});

async function fetchJobs() {
    const container = document.getElementById('jobs-container');
    if (!container) return;

    try {
        const response = await fetch(`${URLROOT}/job/fetch`);
        const data = await response.json();

        if (data.success) {
            container.innerHTML = '';
            if (data.jobs.length === 0) {
                container.innerHTML = '<p class="text-center text-slate-500 py-8">No jobs found matching your criteria.</p>';
                return;
            }

            data.jobs.forEach((job, index) => {
                const card = document.createElement('div');
                const isActive = index === 0;
                card.className = `group flex gap-3 p-4 rounded-lg cursor-pointer transition-all border-b border-slate-100 last:border-0 ${isActive ? 'bg-blue-50 border-l-4 border-[#0A66C2]' : 'bg-white hover:bg-slate-50'}`;
                
                const logo = job.logo ? (job.logo.startsWith('http') ? job.logo : `${URLROOT}/uploads/companies/` + job.logo) : '';
                
                card.innerHTML = `
                    ${logo ? `<img src="${logo}" class="w-12 h-12 rounded bg-white border border-slate-100 object-contain">` : `<div class="w-12 h-12 rounded bg-slate-200 flex items-center justify-center"><span class="material-symbols-outlined text-slate-400">work</span></div>`}
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-[#0A66C2] group-hover:underline truncate">${escapeHtml(job.title)}</h3>
                        <p class="text-sm text-slate-900 truncate">${escapeHtml(job.company_name)}</p>
                        <p class="text-xs text-slate-500 truncate">${escapeHtml(job.location)} (${escapeHtml(job.job_type)})</p>
                        <div class="mt-2 flex items-center gap-2 text-xs">
                            <span class="text-green-700 font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs" style="font-variation-settings: 'FILL' 1;">bolt</span> Easy Apply
                            </span>
                            <span class="text-slate-500">${formatTimeAgoJobs(new Date(job.posted_at))}</span>
                        </div>
                    </div>
                `;

                card.addEventListener('click', () => {
                    document.querySelectorAll('#jobs-container > div').forEach(el => {
                        el.classList.remove('bg-blue-50', 'border-l-4', 'border-[#0A66C2]');
                        el.classList.add('bg-white');
                    });
                    card.classList.remove('bg-white');
                    card.classList.add('bg-blue-50', 'border-l-4', 'border-[#0A66C2]');
                    showJobDetail(job);
                });

                container.appendChild(card);
            });

            if (data.jobs.length > 0) {
                showJobDetail(data.jobs[0]);
            }
        }
    } catch (err) {
        console.error(err);
    }
}

function showJobDetail(job) {
    const detail = document.querySelector('aside.hidden.lg\\:block');
    if (!detail) return;

    const logo = job.logo ? (job.logo.startsWith('http') ? job.logo : `${URLROOT}/uploads/companies/` + job.logo) : '';

    detail.innerHTML = `
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden sticky top-20 flex flex-col h-[calc(100vh-100px)]">
            <div class="p-6 border-b border-slate-100">
                <div class="flex justify-between items-start mb-4">
                    ${logo ? `<img src="${logo}" class="w-16 h-16 rounded bg-white border border-slate-100 object-contain">` : `<div class="w-16 h-16 rounded bg-slate-200 flex items-center justify-center"><span class="material-symbols-outlined text-slate-400 text-3xl">work</span></div>`}
                </div>
                <h2 class="text-2xl font-bold mb-1">${escapeHtml(job.title)}</h2>
                <div class="text-sm mb-4">${escapeHtml(job.company_name)} • ${escapeHtml(job.location)} • <span class="text-green-700 font-semibold">Active hiring</span></div>
                <div class="flex gap-3">
                    <button class="flex-1 bg-[#0A66C2] text-white font-bold py-2 rounded-full hover:bg-[#004182] transition-colors">Apply</button>
                    <button class="flex-1 border border-[#0A66C2] text-[#0A66C2] font-bold py-2 rounded-full hover:bg-blue-50 transition-colors">Save</button>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-6">
                <div class="mb-6">
                    <h3 class="font-bold mb-3 text-lg">Job Description</h3>
                    <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                        ${escapeHtml(job.description || 'No description provided.')}
                    </div>
                </div>
                <div class="mb-6">
                    <h3 class="font-bold mb-3 text-lg">About the company</h3>
                    <p class="text-sm text-slate-700 leading-relaxed">
                        ${escapeHtml(job.company_description || 'Professional company on ProNetwork.')}
                    </p>
                </div>
            </div>
        </div>
    `;
}

function formatTimeAgoJobs(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    let interval = seconds / 86400;
    if (interval > 1) return Math.floor(interval) + " days ago";
    interval = seconds / 3600;
    if (interval > 1) return Math.floor(interval) + " hours ago";
    interval = seconds / 60;
    if (interval > 1) return Math.floor(interval) + " minutes ago";
    return "just now";
}

function escapeHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}
