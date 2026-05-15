/**
 * ProNetwork — Jobs page
 */
'use strict';

const JOBS_STORAGE_SAVED = 'pn_saved_jobs';
const JOBS_STORAGE_ALERTS = 'pn_job_alerts';

const jobsState = {
    panel: 'browse',
    search: '',
    jobType: '',
    jobs: [],
    selectedId: null,
    searchTimer: null
};

const PANEL_META = {
    browse: {
        title: 'Recommended for you',
        subtitle: 'Based on your profile and activity',
        showTypeFilters: true
    },
    saved: {
        title: 'Saved jobs',
        subtitle: 'Roles you bookmarked to review later',
        showTypeFilters: true
    },
    applied: {
        title: 'My applications',
        subtitle: 'Jobs you have already applied to',
        showTypeFilters: false
    },
    alerts: {
        title: 'Job alert matches',
        subtitle: 'Listings matching your saved keywords',
        showTypeFilters: true
    },
    salary: {
        title: 'Jobs with salary info',
        subtitle: 'Open roles that list compensation',
        showTypeFilters: true
    }
};

document.addEventListener('DOMContentLoaded', () => {
    if (!document.getElementById('jobs-container')) return;
    initJobsPage();
});

function initJobsPage() {
    bindJobsUi();
    refreshJobsBadges();
    renderAlertsSidebar();
    const deepId = new URLSearchParams(window.location.search).get('id');
    fetchJobs(deepId ? parseInt(deepId, 10) : null);
}

function bindJobsUi() {
    document.querySelectorAll('#jobs-sidebar-nav .jobs-nav-item').forEach(btn => {
        btn.addEventListener('click', () => setJobsPanel(btn.dataset.jobsPanel));
    });

    const searchInput = document.getElementById('jobs-search-input');
    const searchClear = document.getElementById('jobs-search-clear');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            jobsState.search = searchInput.value.trim();
            if (searchClear) searchClear.classList.toggle('hidden', !jobsState.search);
            clearTimeout(jobsState.searchTimer);
            jobsState.searchTimer = setTimeout(() => fetchJobs(jobsState.selectedId), 280);
        });
    }
    if (searchClear) {
        searchClear.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            jobsState.search = '';
            searchClear.classList.add('hidden');
            fetchJobs(jobsState.selectedId);
        });
    }

    document.querySelectorAll('#jobs-type-filters .jobs-filter-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            document.querySelectorAll('#jobs-type-filters .jobs-filter-chip').forEach(c => c.classList.remove('is-active'));
            chip.classList.add('is-active');
            jobsState.jobType = chip.dataset.jobType || '';
            fetchJobs(jobsState.selectedId);
        });
    });

    document.getElementById('jobs-add-alert-btn')?.addEventListener('click', openJobAlertModal);
    document.getElementById('jobs-mobile-close')?.addEventListener('click', closeMobileJobDetail);
    document.getElementById('jobs-mobile-backdrop')?.addEventListener('click', closeMobileJobDetail);
}

function setJobsPanel(panel) {
    jobsState.panel = panel;
    document.querySelectorAll('#jobs-sidebar-nav .jobs-nav-item').forEach(btn => {
        const active = btn.dataset.jobsPanel === panel;
        btn.classList.toggle('is-active', active);
        btn.classList.toggle('border-[#0A66C2]', active);
        btn.classList.toggle('bg-blue-50/60', active);
        btn.classList.toggle('border-transparent', !active);
        const icon = btn.querySelector('.material-symbols-outlined');
        if (icon) {
            icon.classList.toggle('text-[#0A66C2]', active);
            icon.classList.toggle('text-slate-500', !active);
        }
    });

    const alertsCard = document.getElementById('jobs-alerts-card');
    if (alertsCard) alertsCard.classList.toggle('hidden', panel !== 'alerts');

    const meta = PANEL_META[panel] || PANEL_META.browse;
    const titleEl = document.getElementById('jobs-list-title');
    const subEl = document.getElementById('jobs-list-subtitle');
    const filters = document.getElementById('jobs-type-filters');
    if (titleEl) titleEl.textContent = meta.title;
    if (subEl) subEl.textContent = meta.subtitle;
    if (filters) filters.classList.toggle('hidden', meta.showTypeFilters === false);

    if (panel === 'alerts') renderAlertsSidebar();
    fetchJobs(jobsState.selectedId);
}

function getSavedJobIds() {
    try {
        return JSON.parse(localStorage.getItem(JOBS_STORAGE_SAVED) || '[]').map(Number).filter(Boolean);
    } catch {
        return [];
    }
}

function setSavedJobIds(ids) {
    localStorage.setItem(JOBS_STORAGE_SAVED, JSON.stringify([...new Set(ids.map(Number))]));
    refreshJobsBadges();
}

function isJobSaved(jobId) {
    return getSavedJobIds().includes(Number(jobId));
}

function toggleSaveJob(jobId) {
    const id = Number(jobId);
    let ids = getSavedJobIds();
    if (ids.includes(id)) {
        ids = ids.filter(x => x !== id);
        jobsToast('Removed from saved jobs', 'info');
    } else {
        ids.push(id);
        jobsToast('Job saved', 'success');
    }
    setSavedJobIds(ids);
    updateSaveButtons(id);
    if (jobsState.panel === 'saved') fetchJobs(id);
}

function getJobAlerts() {
    try {
        return JSON.parse(localStorage.getItem(JOBS_STORAGE_ALERTS) || '[]');
    } catch {
        return [];
    }
}

function setJobAlerts(alerts) {
    localStorage.setItem(JOBS_STORAGE_ALERTS, JSON.stringify(alerts));
    refreshJobsBadges();
    renderAlertsSidebar();
}

function refreshJobsBadges() {
    const savedEl = document.getElementById('jobs-saved-count');
    const alertsEl = document.getElementById('jobs-alerts-count');
    if (savedEl) savedEl.textContent = String(getSavedJobIds().length);
    if (alertsEl) alertsEl.textContent = String(getJobAlerts().length);
    loadAppliedCount();
}

async function loadAppliedCount() {
    const el = document.getElementById('jobs-applied-count');
    if (!el) return;
    try {
        const res = await fetch(`${URLROOT}/job/applications`);
        const data = await res.json();
        if (data.success) el.textContent = String(data.applications?.length || 0);
    } catch {
        el.textContent = '0';
    }
}

function renderAlertsSidebar() {
    const list = document.getElementById('jobs-alerts-list');
    if (!list) return;
    const alerts = getJobAlerts();
    if (!alerts.length) {
        list.innerHTML = '<p class="text-xs text-slate-400 italic">No alerts yet. Add a keyword to get notified when new jobs match.</p>';
        return;
    }
    list.innerHTML = alerts.map((a, i) => `
        <div class="flex items-center justify-between gap-2 bg-slate-50 rounded-lg px-3 py-2 border border-slate-100">
            <span class="font-semibold text-slate-700 truncate">"${escapeHtml(a.keyword)}"</span>
            <button type="button" class="text-slate-400 hover:text-red-500 shrink-0" data-remove-alert="${i}" aria-label="Remove alert">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
    `).join('');
    list.querySelectorAll('[data-remove-alert]').forEach(btn => {
        btn.addEventListener('click', () => {
            const idx = parseInt(btn.dataset.removeAlert, 10);
            const next = getJobAlerts().filter((_, j) => j !== idx);
            setJobAlerts(next);
            jobsToast('Alert removed', 'info');
            if (jobsState.panel === 'alerts') fetchJobs(jobsState.selectedId);
        });
    });
}

async function openJobAlertModal() {
    const keyword = await pnModal({
        title: 'Create job alert',
        message: 'Enter a keyword (title, skill, or company). We will highlight matching jobs when you open Job alerts.',
        type: 'notifications',
        isPrompt: true,
        placeholder: 'e.g. React, Designer, Remote…',
        confirmText: 'Save alert',
        cancelText: 'Cancel'
    });
    if (keyword === null || !String(keyword).trim()) return;
    const alerts = getJobAlerts();
    const kw = String(keyword).trim();
    if (alerts.some(a => a.keyword.toLowerCase() === kw.toLowerCase())) {
        jobsToast('You already have this alert', 'info');
        return;
    }
    alerts.push({ keyword: kw, created: Date.now() });
    setJobAlerts(alerts);
    jobsToast('Job alert saved', 'success');
    setJobsPanel('alerts');
}

function buildFetchQuery() {
    const params = new URLSearchParams();
    if (jobsState.search) params.set('q', jobsState.search);
    if (jobsState.jobType && jobsState.panel !== 'applied') params.set('job_type', jobsState.jobType);

    if (jobsState.panel === 'salary') params.set('with_salary', '1');
    if (jobsState.panel === 'applied') params.set('applied_only', '1');
    if (jobsState.panel === 'saved') {
        const ids = getSavedJobIds();
        if (!ids.length) return null;
        params.set('ids', ids.join(','));
    }
    return params;
}

function filterByAlerts(jobs) {
    const alerts = getJobAlerts();
    if (!alerts.length) return [];
    return jobs.filter(job => {
        const hay = `${job.title} ${job.company_name} ${job.location} ${job.description || ''}`.toLowerCase();
        return alerts.some(a => hay.includes(a.keyword.toLowerCase()));
    });
}

async function fetchJobs(selectJobId = null) {
    const container = document.getElementById('jobs-container');
    const countEl = document.getElementById('jobs-result-count');
    if (!container) return;

    if (jobsState.panel === 'saved' && !getSavedJobIds().length) {
        jobsState.jobs = [];
        renderJobsList([], selectJobId);
        if (countEl) countEl.textContent = '0';
        return;
    }

    container.innerHTML = `
        <div class="flex flex-col items-center justify-center py-16 gap-3">
            <div class="animate-spin rounded-full h-10 w-10 border-2 border-slate-200 border-t-[#0A66C2]"></div>
            <p class="text-sm text-slate-500">Loading opportunities…</p>
        </div>`;

    try {
        const params = buildFetchQuery();
        if (params === null) {
            jobsState.jobs = [];
            renderJobsList([], selectJobId);
            if (countEl) countEl.textContent = '0';
            return;
        }

        const url = `${URLROOT}/job/fetch${params.toString() ? '?' + params.toString() : ''}`;
        const response = await fetch(url);
        const data = await response.json();

        if (!data.success) throw new Error('fetch failed');

        let jobs = data.jobs || [];
        if (jobsState.panel === 'alerts') jobs = filterByAlerts(jobs);

        jobsState.jobs = jobs;
        if (countEl) countEl.textContent = String(jobs.length);

        let pickId = selectJobId;
        if (pickId && !jobs.some(j => Number(j.job_id) === Number(pickId))) pickId = null;
        if (!pickId && jobs.length) pickId = jobs[0].job_id;

        renderJobsList(jobs, pickId);
    } catch (err) {
        console.error(err);
        container.innerHTML = `
            <div class="jobs-empty-state">
                <span class="material-symbols-outlined text-4xl text-red-300 mb-2">error</span>
                <p class="font-bold text-slate-700">Could not load jobs</p>
                <button type="button" class="mt-3 text-sm font-bold text-[#0A66C2] hover:underline" id="jobs-retry-btn">Try again</button>
            </div>`;
        document.getElementById('jobs-retry-btn')?.addEventListener('click', () => fetchJobs(selectJobId));
    }
}

function renderJobsList(jobs, selectJobId) {
    const container = document.getElementById('jobs-container');
    if (!container) return;

    if (!jobs.length) {
        const emptyStates = {
            saved: { icon: 'bookmark', title: 'No saved jobs', hint: 'Click Save on any listing to bookmark it here.' },
            applied: { icon: 'assignment_turned_in', title: 'No applications yet', hint: 'Apply to a role and track it here.' },
            alerts: { icon: 'notifications', title: 'No matches', hint: 'Create an alert or broaden your keywords.' },
            salary: { icon: 'payments', title: 'No salary listings', hint: 'Try browsing all jobs instead.' },
            browse: { icon: 'work', title: 'No jobs found', hint: 'Adjust search or filters to see more results.' }
        };
        const emptyCopy = emptyStates[jobsState.panel] || emptyStates.browse;

        container.innerHTML = `
            <div class="jobs-empty-state">
                <span class="material-symbols-outlined text-5xl text-slate-300 mb-3">${emptyCopy.icon}</span>
                <p class="font-bold text-slate-800">${emptyCopy.title}</p>
                <p class="text-sm text-slate-500 mt-1 max-w-xs">${emptyCopy.hint}</p>
                ${jobsState.panel === 'saved' ? `<button type="button" class="mt-4 jobs-filter-chip is-active" id="jobs-browse-from-empty">Browse jobs</button>` : ''}
                ${jobsState.panel === 'alerts' ? `<button type="button" class="mt-4 jobs-filter-chip is-active" id="jobs-alert-from-empty">Create alert</button>` : ''}
            </div>`;
        document.getElementById('jobs-browse-from-empty')?.addEventListener('click', () => setJobsPanel('browse'));
        document.getElementById('jobs-alert-from-empty')?.addEventListener('click', openJobAlertModal);
        clearJobDetailPlaceholder();
        return;
    }

    container.innerHTML = '';
    jobs.forEach(job => {
        const card = document.createElement('article');
        const active = Number(selectJobId) === Number(job.job_id);
        card.className = `jobs-list-card group ${active ? 'is-active' : ''}`;
        card.dataset.jobId = job.job_id;
        card.setAttribute('role', 'button');
        card.setAttribute('tabindex', '0');
        card.setAttribute('aria-pressed', active ? 'true' : 'false');

        const logo = pnCompanyLogoUrl(job);
        const saved = isJobSaved(job.job_id);
        const applied = !!job.has_applied;
        const easyApply = job.easy_apply == 1 || job.easy_apply === true;

        card.innerHTML = `
            <div class="jobs-list-card__indicator" aria-hidden="true"></div>
            <div class="flex gap-3 pr-1">
                <div class="w-12 h-12 rounded-lg overflow-hidden bg-white border border-slate-200 shadow-sm shrink-0 flex items-center justify-center">
                    <img src="${logo}" alt="" class="w-full h-full object-contain p-1" loading="lazy">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="font-bold text-base text-slate-900 group-hover:text-[#0A66C2] transition-colors truncate leading-tight">${escapeHtml(job.title)}</h3>
                        <button type="button" class="jobs-save-btn shrink-0 w-8 h-8 rounded-full flex items-center justify-center hover:bg-amber-50 ${saved ? 'is-saved' : 'text-slate-400'}" data-save-job="${job.job_id}" aria-label="${saved ? 'Unsave job' : 'Save job'}" title="${saved ? 'Saved' : 'Save job'}">
                            <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' ${saved ? 1 : 0};">${saved ? 'bookmark' : 'bookmark_add'}</span>
                        </button>
                    </div>
                    <p class="text-sm font-medium text-slate-700 truncate">${escapeHtml(job.company_name)}</p>
                    <p class="text-xs text-slate-500 truncate mt-0.5">${escapeHtml(job.location || 'Location flexible')} · ${escapeHtml(job.job_type)}</p>
                    ${job.salary_range ? `<p class="text-xs font-bold text-emerald-700 mt-1 truncate">${escapeHtml(job.salary_range)}</p>` : ''}
                    <div class="mt-3 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            ${easyApply ? `<span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[10px] font-bold"><span class="material-symbols-outlined text-[12px]" style="font-variation-settings:'FILL' 1">bolt</span> Easy Apply</span>` : ''}
                            ${applied ? `<span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold">Applied</span>` : ''}
                            ${job.status === 'Closed' ? `<span class="inline-flex px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold">Closed</span>` : ''}
                        </div>
                        <span class="text-[10px] font-semibold text-slate-400 shrink-0">${formatTimeAgoJobs(new Date(job.posted_at))}</span>
                    </div>
                </div>
            </div>`;

        const select = () => selectJobCard(job, card);
        card.addEventListener('click', e => {
            if (e.target.closest('[data-save-job]')) return;
            select();
        });
        card.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                select();
            }
        });
        card.querySelector('[data-save-job]')?.addEventListener('click', e => {
            e.stopPropagation();
            toggleSaveJob(job.job_id);
        });

        container.appendChild(card);
    });

    const pick = jobs.find(j => Number(j.job_id) === Number(selectJobId)) || jobs[0];
    if (pick) selectJobCard(pick, container.querySelector(`[data-job-id="${pick.job_id}"]`));
}

function selectJobCard(job, cardEl) {
    jobsState.selectedId = job.job_id;
    document.querySelectorAll('.jobs-list-card').forEach(el => {
        el.classList.remove('is-active');
        el.setAttribute('aria-pressed', 'false');
    });
    if (cardEl) {
        cardEl.classList.add('is-active');
        cardEl.setAttribute('aria-pressed', 'true');
    }
    const url = new URL(window.location.href);
    url.searchParams.set('id', job.job_id);
    window.history.replaceState({}, '', url);
    showJobDetail(job);
}

function updateSaveButtons(jobId) {
    const saved = isJobSaved(jobId);
    document.querySelectorAll(`[data-save-job="${jobId}"], #job-save-btn`).forEach(btn => {
        btn.classList.toggle('is-saved', saved);
        btn.classList.toggle('text-amber-600', saved);
        const icon = btn.querySelector('.material-symbols-outlined');
        if (icon) {
            icon.textContent = saved ? 'bookmark' : 'bookmark_add';
            icon.style.fontVariationSettings = saved ? "'FILL' 1" : "'FILL' 0";
        }
        if (btn.id === 'job-save-btn') btn.setAttribute('aria-label', saved ? 'Unsave job' : 'Save job');
    });
    const detailSaveLabel = document.getElementById('job-save-btn-label');
    if (detailSaveLabel) detailSaveLabel.textContent = saved ? 'Saved' : 'Save';
}

function clearJobDetailPlaceholder() {
    const detail = document.getElementById('job-detail-container');
    if (!detail) return;
    detail.className = 'jobs-detail-panel bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col items-center justify-center sticky top-20 text-center p-8 min-h-[320px]';
    detail.innerHTML = `
        <div class="w-20 h-20 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl flex items-center justify-center mb-5">
            <span class="material-symbols-outlined text-4xl text-[#0A66C2]/60">work</span>
        </div>
        <h2 class="text-lg font-bold text-slate-800 mb-2">Select a job</h2>
        <p class="text-slate-500 text-sm max-w-xs">Choose a listing to view details and apply.</p>`;
    closeMobileJobDetail();
}

async function showJobDetail(job) {
    let freshJob = job;
    try {
        const r = await fetch(`${URLROOT}/job/detail/${job.job_id}`);
        const d = await r.json();
        if (d.success && d.job) freshJob = d.job;
    } catch (e) { /* use cached */ }

    const html = buildJobDetailHtml(freshJob);
    const detailContainer = document.getElementById('job-detail-container');
    if (detailContainer) {
        detailContainer.className = 'jobs-detail-panel bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col sticky top-20 max-h-[calc(100vh-100px)]';
        detailContainer.innerHTML = html;
        wireJobDetailActions(detailContainer, freshJob);
    }

    const mobileBody = document.getElementById('jobs-mobile-detail-body');
    if (mobileBody && window.matchMedia('(max-width: 767px)').matches) {
        mobileBody.innerHTML = html;
        wireJobDetailActions(mobileBody, freshJob);
        openMobileJobDetail();
    }
}

function buildJobDetailHtml(freshJob) {
    const logo = pnCompanyLogoUrl(freshJob);
    const banner = pnCompanyBannerUrl(freshJob);
    const isClosed = freshJob.status === 'Closed';
    const limit = freshJob.applicant_limit ? parseInt(freshJob.applicant_limit, 10) : null;
    const count = parseInt(freshJob.applicant_count || 0, 10);
    const isFull = limit !== null && count >= limit;
    const hasApplied = !!freshJob.has_applied;
    const disabled = isClosed || isFull || hasApplied;
    const saved = isJobSaved(freshJob.job_id);
    const companyUrl = `${URLROOT}/company/show/${freshJob.company_id}`;
    const shareUrl = `${URLROOT}/user/jobs?id=${freshJob.job_id}`;

    let statusBadge = '<span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-bold">Active</span>';
    if (isClosed) statusBadge = '<span class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-bold">Closed</span>';
    else if (isFull) statusBadge = '<span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 px-2 py-0.5 rounded text-xs font-bold">Full</span>';

    let applyBtnClass = 'bg-gradient-to-r from-[#0A66C2] to-blue-600 text-white hover:shadow-lg hover:-translate-y-0.5';
    let applyBtnText = 'Apply now';
    if (hasApplied) {
        applyBtnClass = 'bg-green-600 text-white cursor-default opacity-90';
        applyBtnText = '<span class="material-symbols-outlined text-[18px]">check_circle</span> Applied';
    } else if (isClosed || isFull) {
        applyBtnClass = 'bg-slate-200 text-slate-500 cursor-not-allowed';
        applyBtnText = isClosed ? 'Listing closed' : 'Position full';
    }

    const exp = freshJob.experience_level ? `<span class="jobs-detail-pill">${escapeHtml(freshJob.experience_level)}</span>` : '';
    const salary = freshJob.salary_range
        ? `<span class="jobs-detail-pill jobs-detail-pill--salary"><span class="material-symbols-outlined text-[16px]">payments</span>${escapeHtml(freshJob.salary_range)}</span>`
        : '';

    return `
        <div class="relative shrink-0 border-b border-slate-100">
            <div class="h-28 bg-slate-800 relative">
                <img src="${banner}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-60">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            </div>
            <div class="absolute top-16 left-5 w-16 h-16 bg-white rounded-xl shadow-md border-4 border-white flex items-center justify-center overflow-hidden">
                <img src="${logo}" alt="" class="w-full h-full object-contain">
            </div>
            <div class="pt-10 px-5 pb-5 bg-white">
                <h2 class="text-xl font-black text-slate-900 mb-1 leading-tight">${escapeHtml(freshJob.title)}</h2>
                <div class="text-sm text-slate-600 mb-3 flex flex-wrap items-center gap-2">
                    <a href="${companyUrl}" class="text-[#0A66C2] font-bold hover:underline">${escapeHtml(freshJob.company_name)}</a>
                    <span class="text-slate-300">·</span>
                    <span>${escapeHtml(freshJob.location || 'Remote / flexible')}</span>
                    <span class="text-slate-300">·</span>
                    <span>${formatTimeAgoJobs(new Date(freshJob.posted_at))}</span>
                    <span class="text-slate-300">·</span>
                    ${statusBadge}
                </div>
                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="jobs-detail-pill"><span class="material-symbols-outlined text-[16px]">work</span>${escapeHtml(freshJob.job_type)}</span>
                    ${exp}
                    ${salary}
                    <span class="jobs-detail-pill"><span class="material-symbols-outlined text-[16px]">groups</span>${count}${limit ? ` / ${limit}` : ''} applicants</span>
                </div>
                ${limit ? `
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mb-4 max-w-xs">
                    <div class="bg-[#0A66C2] h-full rounded-full transition-all" style="width:${Math.min(100, (count / limit) * 100)}%"></div>
                </div>` : ''}
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="job-apply-btn" ${disabled ? 'disabled' : ''} class="jobs-detail-cta flex items-center justify-center gap-2 font-bold py-2.5 px-6 rounded-full transition-all ${applyBtnClass}">${applyBtnText}</button>
                    <button type="button" id="job-save-btn" class="jobs-detail-secondary flex items-center justify-center gap-1.5 font-bold py-2.5 px-5 rounded-full border-2 border-slate-200 hover:border-amber-300 hover:bg-amber-50 transition-all ${saved ? 'is-saved text-amber-700 border-amber-200 bg-amber-50' : 'text-slate-600'}">
                        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' ${saved ? 1 : 0}">bookmark</span>
                        <span id="job-save-btn-label">${saved ? 'Saved' : 'Save'}</span>
                    </button>
                    <button type="button" id="job-share-btn" class="jobs-detail-icon-btn" title="Copy link">
                        <span class="material-symbols-outlined text-[20px]">share</span>
                    </button>
                    <button type="button" id="job-report-btn" class="jobs-detail-icon-btn jobs-detail-icon-btn--danger" title="Report listing">
                        <span class="material-symbols-outlined text-[20px]">flag</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto p-5 bg-slate-50/40 space-y-4">
            <section class="bg-white rounded-xl border border-slate-100 p-5 shadow-sm">
                <h3 class="font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#0A66C2]">description</span> About the role
                </h3>
                <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">${escapeHtml(freshJob.description || 'No description provided.')}</div>
            </section>
            <section class="bg-white rounded-xl border border-slate-100 p-5 shadow-sm">
                <h3 class="font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#0A66C2]">domain</span> About ${escapeHtml(freshJob.company_name)}
                </h3>
                <p class="text-sm text-slate-700 leading-relaxed">${escapeHtml(freshJob.company_description || 'Professional company on ProNetwork.')}</p>
                <a href="${companyUrl}" class="mt-4 inline-flex items-center gap-1 text-sm font-bold text-[#0A66C2] hover:underline">
                    Visit company page <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </section>
        </div>`;
}

function wireJobDetailActions(root, freshJob) {
    const applyBtn = root.querySelector('#job-apply-btn');
    if (applyBtn && !applyBtn.disabled) {
        applyBtn.addEventListener('click', () => openApplyModal(freshJob));
    }
    root.querySelector('#job-save-btn')?.addEventListener('click', () => toggleSaveJob(freshJob.job_id));
    root.querySelector('#job-share-btn')?.addEventListener('click', async () => {
        const link = `${URLROOT}/user/jobs?id=${freshJob.job_id}`;
        try {
            await navigator.clipboard.writeText(link);
            jobsToast('Link copied to clipboard', 'success');
        } catch {
            jobsToast(link, 'info');
        }
    });
    root.querySelector('#job-report-btn')?.addEventListener('click', () => reportJob(freshJob.job_id));
}

function openMobileJobDetail() {
    const backdrop = document.getElementById('jobs-mobile-backdrop');
    const drawer = document.getElementById('jobs-mobile-detail');
    if (!backdrop || !drawer) return;
    backdrop.classList.remove('hidden');
    drawer.classList.remove('hidden');
    requestAnimationFrame(() => {
        drawer.classList.remove('translate-y-full');
        drawer.classList.add('translate-y-0');
    });
    document.body.style.overflow = 'hidden';
}

function closeMobileJobDetail() {
    const backdrop = document.getElementById('jobs-mobile-backdrop');
    const drawer = document.getElementById('jobs-mobile-detail');
    if (!drawer) return;
    drawer.classList.add('translate-y-full');
    drawer.classList.remove('translate-y-0');
    setTimeout(() => {
        backdrop?.classList.add('hidden');
        drawer.classList.add('hidden');
        document.body.style.overflow = '';
    }, 280);
}

function openApplyModal(job) {
    const backdrop = document.createElement('div');
    backdrop.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-300';

    const u = getUserState() || {};
    const parts = (u.full_name || '').split(' ');
    const fName = parts[0] || '';
    const lName = parts.slice(1).join(' ') || '';
    const phoneVal = u.phone || '';

    backdrop.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform scale-95 transition-transform duration-300" id="apply-modal-content">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-bold text-slate-900">Apply to ${escapeHtml(job.company_name)}</h3>
                    <p class="text-sm font-medium text-slate-500 mt-1">${escapeHtml(job.title)}</p>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full p-1" id="apply-modal-close">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>
            <form id="apply-submission-form" class="p-6 space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">First name *</label>
                        <input type="text" name="first_name" required value="${escapeHtml(fName)}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0A66C2]/20 focus:border-[#0A66C2]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Last name *</label>
                        <input type="text" name="last_name" required value="${escapeHtml(lName)}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0A66C2]/20 focus:border-[#0A66C2]">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Phone</label>
                    <input type="text" name="phone" value="${escapeHtml(phoneVal)}" placeholder="+1 (555) 000-0000" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0A66C2]/20 focus:border-[#0A66C2]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Resume (PDF/Word) *</label>
                    <div class="relative group">
                        <input type="file" name="resume" required accept=".pdf,.doc,.docx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" id="resume-input">
                        <div class="w-full px-4 py-6 border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 group-hover:bg-blue-50 group-hover:border-blue-300 transition-all flex flex-col items-center text-center">
                            <span class="material-symbols-outlined text-3xl text-slate-400 group-hover:text-[#0A66C2] mb-2">upload_file</span>
                            <span class="text-sm font-bold text-slate-700" id="resume-label">Click or drag to upload</span>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Cover note (optional)</label>
                    <textarea name="cover_letter" rows="3" placeholder="Why you are a great fit…" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0A66C2]/20 resize-none"></textarea>
                </div>
            </form>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" class="px-6 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-200 rounded-full" id="apply-modal-cancel">Cancel</button>
                <button type="submit" form="apply-submission-form" class="px-8 py-2.5 text-sm font-bold bg-[#0A66C2] text-white hover:bg-[#004182] rounded-full" id="apply-modal-confirm">Submit application</button>
            </div>
        </div>`;

    document.body.appendChild(backdrop);
    const fileInput = backdrop.querySelector('#resume-input');
    const fileLabel = backdrop.querySelector('#resume-label');
    fileInput?.addEventListener('change', function () {
        if (fileLabel) fileLabel.textContent = this.files?.[0]?.name || 'Click or drag to upload';
    });

    requestAnimationFrame(() => {
        backdrop.classList.remove('opacity-0');
        backdrop.querySelector('#apply-modal-content')?.classList.replace('scale-95', 'scale-100');
    });

    const close = () => {
        backdrop.classList.add('opacity-0');
        setTimeout(() => backdrop.remove(), 300);
    };
    backdrop.querySelector('#apply-modal-cancel')?.addEventListener('click', close);
    backdrop.querySelector('#apply-modal-close')?.addEventListener('click', close);
    backdrop.addEventListener('click', e => { if (e.target === backdrop) close(); });

    backdrop.querySelector('#apply-submission-form')?.addEventListener('submit', async e => {
        e.preventDefault();
        const btn = backdrop.querySelector('#apply-modal-confirm');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Submitting…';
        }
        try {
            const res = await fetch(`${URLROOT}/job/apply/${job.job_id}`, { method: 'POST', body: new FormData(e.target) });
            const d = await res.json();
            if (d.success) {
                jobsToast(d.message || 'Application submitted!', 'success');
                close();
                fetchJobs(job.job_id);
                loadAppliedCount();
            } else {
                jobsToast(d.message || 'Failed to apply.', 'error');
                if (btn) { btn.disabled = false; btn.textContent = 'Submit application'; }
            }
        } catch {
            jobsToast('Network error.', 'error');
            if (btn) { btn.disabled = false; btn.textContent = 'Submit application'; }
        }
    });
}

function formatTimeAgoJobs(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    let interval = seconds / 86400;
    if (interval > 1) return Math.floor(interval) + 'd ago';
    interval = seconds / 3600;
    if (interval > 1) return Math.floor(interval) + 'h ago';
    interval = seconds / 60;
    if (interval > 1) return Math.floor(interval) + 'm ago';
    return 'Just now';
}

function escapeHtml(s) {
    if (!s) return '';
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}



window.reportJob = async function (jobId) {
    const reason = await pnModal({
        title: 'Report job listing',
        message: 'Tell us why you are reporting this job.',
        type: 'flag',
        isPrompt: true,
        placeholder: 'e.g. Scam, misleading, discrimination…',
        confirmText: 'Submit report',
        cancelText: 'Cancel'
    });
    if (reason === null) return;
    try {
        const res = await fetch(`${URLROOT}/job/report/${jobId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ reason: String(reason).trim() || 'Inappropriate job listing' })
        });
        const data = await res.json();
        jobsToast(data.success ? 'Report submitted. Admin will review.' : (data.message || 'Failed to report.'), data.success ? 'success' : 'error');
    } catch {
        jobsToast('Server error.', 'error');
    }
};

function jobsToast(msg, type = 'info') {
    document.getElementById('jobs-toast')?.remove();
    const t = document.createElement('div');
    t.id = 'jobs-toast';
    const bg = type === 'error' ? 'bg-red-600' : type === 'success' ? 'bg-green-600' : 'bg-slate-800';
    const icon = type === 'error' ? 'error' : type === 'success' ? 'check_circle' : 'info';
    t.className = `fixed bottom-6 left-1/2 -translate-x-1/2 z-[10000] flex items-center gap-3 px-5 py-3 rounded-full shadow-2xl text-sm font-bold ${bg} text-white transition-all duration-300 opacity-0 translate-y-4`;
    t.innerHTML = `<span class="material-symbols-outlined text-[20px]">${icon}</span> ${escapeHtml(msg)}`;
    document.body.appendChild(t);
    requestAnimationFrame(() => t.classList.remove('opacity-0', 'translate-y-4'));
    setTimeout(() => {
        t.classList.add('opacity-0', 'translate-y-4');
        setTimeout(() => t.remove(), 300);
    }, 4000);
}
