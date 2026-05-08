/**
 * ProNetwork — Profile Logic
 * public/assets/js/profile.js
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
    if (window.location.pathname.includes('profile')) {
        initProfile();
        loadExperience();
        loadEducation();
        loadConnections();
    }
});

async function initProfile() {
    try {
        const res = await fetch(`${URLROOT}/user/me`);
        const data = await res.json();
        if (data.success) {
            const u = data.user;
            document.querySelectorAll('[data-user-name="full"]').forEach(el => el.textContent = u.full_name);
            document.querySelectorAll('[data-user-headline]').forEach(el => el.textContent = u.headline || 'Professional');
            document.querySelectorAll('[data-user-location]').forEach(el => el.textContent = u.location || 'Location not set');
            document.querySelectorAll('[data-user-bio]').forEach(el => el.textContent = u.bio || 'No bio yet.');
            document.querySelectorAll('[data-user-email]').forEach(el => el.textContent = u.email);
            
            if (u.profile_pic) {
                const picUrl = u.profile_pic.startsWith('http') ? u.profile_pic : `${URLROOT}/uploads/profiles/` + u.profile_pic;
                document.querySelectorAll('img[data-user-pic="true"]').forEach(img => img.src = picUrl);
            }
            if (u.cover_image) {
                const coverUrl = u.cover_image.startsWith('http') ? u.cover_image : `${URLROOT}/uploads/covers/` + u.cover_image;
                const bannerImg = document.getElementById('profile-banner-img');
                if (bannerImg) bannerImg.src = coverUrl;
            }
        }
    } catch(e) {}
}

async function loadExperience() {
    const container = document.getElementById('experience-container');
    if (!container) return;
    try {
        const response = await fetch(`${URLROOT}/user/experience`);
        const result = await response.json();
        if (result.success) {
            if (result.data.length === 0) {
                container.innerHTML = '<p class="text-sm text-slate-500 py-4">No experience added yet.</p>';
                return;
            }
            container.innerHTML = result.data.map(exp => `
                <div class="flex space-x-4">
                    <div class="w-12 h-12 bg-slate-100 rounded flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-slate-400">business</span>
                    </div>
                    <div class="flex-1 border-b border-slate-100 pb-4">
                        <h4 class="font-bold text-slate-900">${escapeHtml(exp.job_title)}</h4>
                        <p class="text-sm text-slate-700">${escapeHtml(exp.company)}</p>
                        <p class="text-xs text-slate-500 mt-1">${formatDate(exp.start_date)} — ${exp.is_current ? 'Present' : formatDate(exp.end_date)}</p>
                        ${exp.description ? `<p class="text-sm text-slate-600 mt-2">${escapeHtml(exp.description)}</p>` : ''}
                    </div>
                </div>
            `).join('<div class="h-4"></div>');
        }
    } catch (err) {}
}

async function loadEducation() {
    const container = document.getElementById('education-container');
    if (!container) return;
    try {
        const response = await fetch(`${URLROOT}/user/education`);
        const result = await response.json();
        if (result.success) {
            if (result.data.length === 0) {
                container.innerHTML = '<p class="text-sm text-slate-500 py-4">No education added yet.</p>';
                return;
            }
            container.innerHTML = result.data.map(edu => `
                <div class="flex space-x-4">
                    <div class="w-12 h-12 bg-slate-100 rounded flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-slate-400">school</span>
                    </div>
                    <div class="flex-1 border-b border-slate-100 pb-4">
                        <h4 class="font-bold text-slate-900">${escapeHtml(edu.institution)}</h4>
                        <p class="text-sm text-slate-700">${escapeHtml(edu.degree)}, ${escapeHtml(edu.field)}</p>
                        <p class="text-xs text-slate-500 mt-1">${edu.start_year} — ${edu.end_year || 'Present'}</p>
                    </div>
                </div>
            `).join('<div class="h-4"></div>');
        }
    } catch (err) {}
}

async function loadConnections() {
    const container = document.getElementById('connections-list-profile');
    const countEl = document.getElementById('profile-connections-count');
    if (!container) return;
    try {
        const response = await fetch(`${URLROOT}/network/connections`);
        const result = await response.json();
        if (result.success) {
            if (countEl) countEl.textContent = result.connections.length + ' connections';
            if (result.connections.length === 0) {
                container.innerHTML = '<p class="text-sm text-slate-500 py-4">No connections yet.</p>';
                return;
            }
            container.innerHTML = result.connections.map(conn => {
                const picUrl = conn.profile_pic ? (conn.profile_pic.startsWith('http') ? conn.profile_pic : `${URLROOT}/uploads/profiles/` + conn.profile_pic) : '';
                return `
                <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100">
                            ${picUrl ? `<img src="${picUrl}" class="w-full h-full object-cover">` : `<span class="material-symbols-outlined text-slate-400 flex items-center justify-center h-full">person</span>`}
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">${escapeHtml(conn.full_name)}</h4>
                        </div>
                    </div>
                    <a href="${URLROOT}/user/messaging?chat=${conn.user_id}" class="text-primary hover:bg-blue-50 p-2 rounded-full transition-colors">
                        <span class="material-symbols-outlined text-[20px]">message</span>
                    </a>
                </div>
                `;
            }).join('');
        }
    } catch (err) {}
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
}

function escapeHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}
