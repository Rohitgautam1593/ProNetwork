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

async function showJobDetail(job) {
    const detail = document.querySelector('aside.hidden.lg\\:block');
    if (!detail) return;

    // Fetch live job details to make sure counts/status are fresh
    let freshJob = job;
    try {
        const r = await fetch(`${URLROOT}/job/detail/${job.job_id}`);
        const d = await r.json();
        if (d.success && d.job) {
            freshJob = d.job;
        }
    } catch (e) {}

    const logo = freshJob.logo ? (freshJob.logo.startsWith('http') ? freshJob.logo : `${URLROOT}/uploads/companies/` + freshJob.logo) : '';

    const isClosed = freshJob.status === 'Closed';
    const limit = freshJob.applicant_limit ? parseInt(freshJob.applicant_limit) : null;
    const count = parseInt(freshJob.applicant_count || 0);
    const isFull = limit !== null && count >= limit;
    const hasApplied = !!freshJob.has_applied;
    const disabled = isClosed || isFull || hasApplied;

    let statusBadge = '<span class="text-green-700 font-semibold">Active hiring</span>';
    if (isClosed) {
        statusBadge = '<span class="text-red-600 font-semibold">Listing Closed</span>';
    } else if (isFull) {
        statusBadge = '<span class="text-amber-600 font-semibold">Capacity Full</span>';
    }

    let applyBtnText = 'Apply';
    if (hasApplied) applyBtnText = 'Applied';
    else if (isClosed) applyBtnText = 'Listing Closed';
    else if (isFull) applyBtnText = 'Position Full';

    detail.innerHTML = `
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden sticky top-20 flex flex-col h-[calc(100vh-100px)]">
            <div class="p-6 border-b border-slate-100">
                <div class="flex justify-between items-start mb-4">
                    ${logo ? `<img src="${logo}" class="w-16 h-16 rounded bg-white border border-slate-100 object-contain">` : `<div class="w-16 h-16 rounded bg-slate-200 flex items-center justify-center"><span class="material-symbols-outlined text-slate-400 text-3xl">work</span></div>`}
                </div>
                <h2 class="text-2xl font-bold mb-1">${escapeHtml(freshJob.title)}</h2>
                <div class="text-sm mb-3">${escapeHtml(freshJob.company_name)} • ${escapeHtml(freshJob.location)} • ${statusBadge}</div>
                
                ${limit ? `
                <div class="mb-4 bg-slate-50 rounded-lg p-2.5 border border-slate-100">
                    <div class="flex justify-between text-xs font-bold text-slate-600 mb-1">
                        <span>Applicants</span>
                        <span>${count} / ${limit} applied</span>
                    </div>
                    <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-blue-600 h-full rounded-full transition-all" style="width: ${Math.min(100, (count / limit) * 100)}%"></div>
                    </div>
                </div>
                ` : `
                <div class="mb-4 text-xs font-semibold text-slate-500">
                    ${count} applicant${count === 1 ? '' : 's'} applied
                </div>
                `}

                <div class="flex gap-3 mb-4">
                    <button id="job-apply-btn" ${disabled ? 'disabled' : ''} class="flex-1 ${disabled ? (hasApplied ? 'bg-green-700 text-white cursor-default' : 'bg-slate-300 text-slate-500 cursor-not-allowed') : 'bg-[#0A66C2] text-white hover:bg-[#004182]'} font-bold py-2 rounded-full transition-colors">${applyBtnText}</button>
                    <button class="flex-1 border border-[#0A66C2] text-[#0A66C2] font-bold py-2 rounded-full hover:bg-blue-50 transition-colors">Save</button>
                </div>
                <button onclick="reportJob(${freshJob.job_id})" class="w-full flex items-center justify-center gap-2 text-xs text-slate-500 hover:text-red-600 transition-colors py-1">
                    <span class="material-symbols-outlined text-[16px]">flag</span>
                    Report this job listing
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6">
                <div class="mb-6">
                    <h3 class="font-bold mb-3 text-lg">Job Description</h3>
                    <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                        ${escapeHtml(freshJob.description || 'No description provided.')}
                    </div>
                </div>
                <div class="mb-6">
                    <h3 class="font-bold mb-3 text-lg">About the company</h3>
                    <p class="text-sm text-slate-700 leading-relaxed">
                        ${escapeHtml(freshJob.company_description || 'Professional company on ProNetwork.')}
                    </p>
                </div>
            </div>
        </div>
    `;

    const applyBtn = detail.querySelector('#job-apply-btn');
    if (applyBtn && !disabled) {
        applyBtn.addEventListener('click', () => openApplyModal(freshJob));
    }
}

function openApplyModal(job) {
    const backdrop = document.createElement('div');
    backdrop.className = 'pn-modal-backdrop';
    
    // Auto-populate current user info from localStorage if present
    const u = localStorage.getItem('pn_user') ? JSON.parse(localStorage.getItem('pn_user')) : {};
    const parts = (u.full_name || '').split(' ');
    const fName = parts[0] || '';
    const lName = parts.slice(1).join(' ') || '';
    const phoneVal = u.phone || '';

    backdrop.innerHTML = `
        <div class="pn-modal-container max-w-lg w-full">
            <div class="pn-modal-header border-b border-slate-100 pb-4 mb-4">
                <div class="pn-modal-icon success">
                    <span class="material-symbols-outlined">description</span>
                </div>
                <div>
                    <h3 class="pn-modal-title text-xl font-bold">Apply for ${escapeHtml(job.title)}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">${escapeHtml(job.company_name)}</p>
                </div>
            </div>
            <form id="apply-submission-form" class="pn-modal-body text-left space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">First Name *</label>
                        <input type="text" name="first_name" required value="${escapeHtml(fName)}" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Last Name *</label>
                        <input type="text" name="last_name" required value="${escapeHtml(lName)}" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-blue-600">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="${escapeHtml(phoneVal)}" placeholder="+1 (555) 000-0000" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Upload Resume (PDF/Word) *</label>
                    <input type="file" name="resume" required accept=".pdf,.doc,.docx" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-lg p-1.5">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Cover Note (Optional)</label>
                    <textarea name="cover_letter" rows="3" placeholder="Explain why you are a strong fit..." class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-blue-600"></textarea>
                </div>
            </form>
            <div class="pn-modal-footer border-t border-slate-100 pt-4 mt-4 flex justify-end gap-3">
                <button type="button" class="pn-modal-btn pn-modal-btn-secondary px-4 py-2 text-sm rounded-full font-bold" id="apply-modal-cancel">Cancel</button>
                <button type="submit" form="apply-submission-form" class="pn-modal-btn pn-modal-btn-primary px-5 py-2 text-sm rounded-full font-bold bg-[#0A66C2] text-white hover:bg-[#004182]" id="apply-modal-confirm">Submit Application</button>
            </div>
        </div>
    `;

    document.body.appendChild(backdrop);
    setTimeout(() => backdrop.classList.add('active'), 10);

    const close = () => {
        backdrop.classList.remove('active');
        setTimeout(() => backdrop.remove(), 300);
    };

    backdrop.querySelector('#apply-modal-cancel').onclick = close;
    backdrop.onclick = (e) => { if (e.target === backdrop) close(); };

    const form = backdrop.querySelector('#apply-submission-form');
    form.onsubmit = async (e) => {
        e.preventDefault();
        const btn = backdrop.querySelector('#apply-modal-confirm');
        btn.disabled = true;
        btn.innerHTML = 'Submitting...';

        const formData = new FormData(form);
        try {
            const res = await fetch(`${URLROOT}/job/apply/${job.job_id}`, {
                method: 'POST',
                body: formData
            });
            const d = await res.json();
            if (d.success) {
                jobsToast(d.message || 'Application submitted successfully!', 'success');
                close();
                // Refresh list and side preview to update counts/buttons instantly
                fetchJobs();
            } else {
                jobsToast(d.message || 'Failed to apply.', 'error');
                btn.disabled = false;
                btn.innerHTML = 'Submit Application';
            }
        } catch(err) {
            jobsToast('Network error while submitting.', 'error');
            btn.disabled = false;
            btn.innerHTML = 'Submit Application';
        }
    };
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

window.reportJob = async function(jobId) {
    const reason = await pnModal({
        title: 'Report Job Listing',
        message: 'Please tell us why you are reporting this job. Our team will investigate shortly.',
        type: 'flag',
        isPrompt: true,
        placeholder: 'e.g. Scams, Misleading, Discrimination...',
        confirmText: 'Submit Report',
        cancelText: 'Cancel'
    });
    
    if (reason === null) return;
    
    try {
        const res = await fetch(`${URLROOT}/job/report/${jobId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ reason: reason.trim() || 'Inappropriate job listing' })
        });
        const data = await res.json();
        if (data.success) {
            jobsToast('Job reported. Admin will review.', 'success');
        } else {
            jobsToast(data.message || 'Failed to report.', 'error');
        }
    } catch(e) {
        jobsToast('Server error.', 'error');
    }
}

function jobsToast(msg, type = 'info') {
    const existing = document.getElementById('jobs-toast');
    if (existing) existing.remove();
    const t = document.createElement('div');
    t.id = 'jobs-toast';
    const bg = type === 'error' ? 'bg-red-600' : type === 'success' ? 'bg-green-600' : 'bg-blue-600';
    const icon = type === 'error' ? 'error' : type === 'success' ? 'check_circle' : 'info';
    t.className = `fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg text-sm font-medium ${bg} text-white transition-all duration-300`;
    t.innerHTML = `<span class="material-symbols-outlined text-[18px]">${icon}</span> ${msg}`;
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 4000);
}
