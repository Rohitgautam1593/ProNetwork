/**
 * ProNetwork - Profile Logic
 * public/assets/js/profile.js
 */
'use strict';

let currentUserData = {};
let currentViewerData = {};
let currentExperience = [];
let currentEducation = [];
let currentConnections = [];
let currentProfileSettings = {};
let viewingOwnProfile = true;
let viewedUserId = null;

document.addEventListener('DOMContentLoaded', () => {
    if (window.location.pathname.includes('profile')) {
        initProfile();

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeAllModals();
                closePicMenu();
                closeMoreMenu();
            }
        });

        document.addEventListener('click', (e) => {
            const picMenu = document.getElementById('pic-options-menu');
            if (picMenu && !picMenu.classList.contains('hidden') && !e.target.closest('.group\\/pic')) {
                closePicMenu();
            }
            if (!e.target.closest('#profile-more-action') && !e.target.closest('#profile-more-menu')) {
                closeMoreMenu();
            }
        });
    }
});

async function initProfile() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const targetId = urlParams.get('id');
        const endpoint = targetId ? `${URLROOT}/user/data/${targetId}` : `${URLROOT}/user/me`;

        const [profileRes, meRes] = await Promise.all([
            fetch(endpoint),
            fetch(`${URLROOT}/user/me`)
        ]);
        const data = await profileRes.json();
        const meData = await meRes.json();

        if (!data.success || !data.user) {
            renderProfileLoadError(data.message || 'Profile not found.');
            return;
        }

        currentUserData = data.user;
        currentProfileSettings = data.settings || {};
        currentViewerData = meData.success ? meData.user : {};
        viewedUserId = currentUserData.user_id;
        viewingOwnProfile = meData.success && String(currentViewerData.user_id) === String(currentUserData.user_id);

        renderProfileData();
        wireStaticActions();
        bindContactInfo();

        await Promise.all([
            loadExperience(viewedUserId),
            loadEducation(viewedUserId),
            loadConnections(viewedUserId),
            configureProfileActions()
        ]);

        updateProfileStrength();

        if (viewingOwnProfile) {
            bindEditModals();
            bindImageUploads();
        } else {
            document.querySelectorAll('#open-profile-edit, #trigger-banner-upload, #open-experience, #open-education, [data-own-profile-action="true"]').forEach(el => el.remove());
            const picTrigger = document.querySelector('.group\\/pic > div');
            if (picTrigger) picTrigger.remove();
        }
    } catch (e) {
        console.error(e);
        renderProfileLoadError('Unable to load this profile right now.');
    }
}

function renderProfileLoadError(message) {
    const scope = document.getElementById('profile-page') || document;
    const name = scope.querySelector('[data-user-name="full"]');
    if (name) name.textContent = message;
}

function renderProfileData() {
    const u = currentUserData;
    const scope = document.getElementById('profile-page') || document;
    scope.querySelectorAll('[data-user-name="full"]').forEach(el => el.textContent = u.full_name || 'Profile');
    scope.querySelectorAll('[data-user-headline]').forEach(el => el.textContent = u.headline || 'Professional');
    scope.querySelectorAll('[data-user-location]').forEach(el => el.textContent = u.location || 'Location not set');
    scope.querySelectorAll('[data-user-bio]').forEach(el => el.textContent = u.bio || (viewingOwnProfile ? 'Add a summary about yourself.' : 'No summary added yet.'));

    const picUrl = pnProfilePicUrl(u);
    scope.querySelectorAll('img[data-user-pic="true"]').forEach(img => img.src = picUrl);
    const fullImg = document.getElementById('fullscreen-img');
    if (fullImg) fullImg.src = picUrl;

    const bannerImg = document.getElementById('profile-banner-img');
    if (bannerImg) bannerImg.src = pnCoverImageUrl(u);
}

function wireStaticActions() {
    document.getElementById('profile-connections-link')?.addEventListener('click', openConnectionsDestination);
    document.getElementById('show-all-connections')?.addEventListener('click', openConnectionsDestination);
    document.getElementById('header-latest-exp')?.addEventListener('click', () => scrollToSection('experience-container'));
    document.getElementById('header-latest-edu')?.addEventListener('click', () => scrollToSection('education-container'));
}

function openConnectionsDestination() {
    if (viewingOwnProfile) {
        window.location.href = `${URLROOT}/network/connections_list`;
        return;
    }
    document.getElementById('connections-list-profile')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function scrollToSection(id) {
    document.getElementById(id)?.closest('section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

async function configureProfileActions() {
    const primary = document.getElementById('profile-primary-action');
    const secondary = document.getElementById('profile-secondary-action');
    const more = document.getElementById('profile-more-action');
    if (!primary || !secondary || !more) return;
    [primary, secondary].forEach((btn) => {
        btn.disabled = false;
        btn.classList.remove('opacity-60', 'opacity-70', 'cursor-not-allowed');
    });

    if (viewingOwnProfile) {
        primary.textContent = currentUserData.headline ? 'Edit headline' : 'Add headline';
        primary.onclick = () => document.getElementById('open-profile-edit')?.click();
        secondary.textContent = 'Add profile section';
        secondary.onclick = () => openModal('section-picker-modal');
        more.onclick = toggleMoreMenu;
        renderMoreMenu([
            { icon: 'visibility', label: 'View public profile', action: () => window.location.href = `${URLROOT}/user/profile?id=${currentUserData.user_id}` },
            { icon: 'content_copy', label: 'Copy profile link', action: copyProfileLink },
            { icon: 'settings', label: 'Settings', action: () => window.location.href = `${URLROOT}/user/settings` }
        ]);
        bindSectionPicker();
        return;
    }

    const status = await fetchConnectionStatus();
    if (status.state === 'Accepted') {
        primary.textContent = 'Message';
        primary.onclick = () => window.location.href = `${URLROOT}/user/messaging?chat=${viewedUserId}`;
        secondary.textContent = 'Contact info';
        secondary.onclick = openContactInfo;
    } else if (status.state === 'Pending' && status.direction === 'sent') {
        primary.textContent = 'Pending';
        primary.onclick = () => window.pnModal?.({
            title: 'Request pending',
            message: 'Your connection request has been sent and is waiting for a response.',
            confirmText: 'OK',
            cancelText: ''
        });
        secondary.textContent = 'Message';
        secondary.onclick = () => window.location.href = `${URLROOT}/user/messaging?chat=${viewedUserId}`;
    } else if (status.state === 'Pending' && status.direction === 'received') {
        primary.textContent = 'Accept';
        primary.onclick = () => respondToRequest('accept');
        secondary.textContent = 'Ignore';
        secondary.onclick = () => respondToRequest('reject');
    } else {
        primary.textContent = 'Connect';
        primary.onclick = sendProfileConnectionRequest;
        secondary.textContent = 'Message';
        secondary.onclick = () => window.location.href = `${URLROOT}/user/messaging?chat=${viewedUserId}`;
    }

    more.onclick = toggleMoreMenu;
    renderMoreMenu([
        { icon: 'contact_page', label: 'Contact info', action: () => openContactInfo() },
        { icon: 'content_copy', label: 'Copy profile link', action: copyProfileLink },
        { icon: 'flag', label: 'Report profile', action: reportViewedProfile }
    ]);
}

async function fetchConnectionStatus() {
    try {
        const res = await fetch(`${URLROOT}/network/status?id=${encodeURIComponent(viewedUserId)}`);
        const data = await res.json();
        return data.success ? data : { state: 'none', direction: 'none' };
    } catch {
        return { state: 'none', direction: 'none' };
    }
}

function renderMoreMenu(items) {
    const menu = document.getElementById('profile-more-menu');
    if (!menu) return;
    menu.innerHTML = '';
    items.forEach((item) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-3 transition-colors border-b border-slate-50 last:border-0';
        btn.innerHTML = `<span class="material-symbols-outlined text-slate-400 text-[18px]">${item.icon}</span>${escapeHtml(item.label)}`;
        btn.addEventListener('click', () => {
            closeMoreMenu();
            item.action();
        });
        menu.appendChild(btn);
    });
}

function toggleMoreMenu(e) {
    if (e) e.stopPropagation();
    document.getElementById('profile-more-menu')?.classList.toggle('hidden');
}

function closeMoreMenu() {
    document.getElementById('profile-more-menu')?.classList.add('hidden');
}

async function copyProfileLink() {
    const link = `${URLROOT}/user/profile?id=${currentUserData.user_id}`;
    try {
        await navigator.clipboard.writeText(link);
        toast('Profile link copied', 'success');
    } catch {
        await window.pnModal?.({ title: 'Profile link', message: link, confirmText: 'OK', cancelText: '' });
    }
}

async function reportViewedProfile() {
    const reason = await window.pnModal?.({
        title: 'Report profile',
        message: 'Tell us why you are reporting this profile.',
        type: 'flag',
        isPrompt: true,
        placeholder: 'Describe the concern...',
        confirmText: 'Submit report',
        cancelText: 'Cancel'
    });
    if (reason === null || reason === undefined) return;

    const res = await fetch(`${URLROOT}/user/report/${encodeURIComponent(viewedUserId)}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ reason: String(reason).trim() || 'Profile concern' })
    });
    const data = await res.json();
    if (data.success) {
        toast('Profile report submitted', 'success');
    } else {
        alert(data.message || 'Failed to submit report.');
    }
}

async function sendProfileConnectionRequest() {
    const primary = document.getElementById('profile-primary-action');
    if (!primary || primary.disabled) return;
    primary.disabled = true;
    primary.textContent = 'Sending...';
    try {
        const res = await fetch(`${URLROOT}/network/send_request`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: viewedUserId })
        });
        const data = await res.json();
        if (data.success) {
            primary.textContent = 'Pending';
            primary.disabled = false;
            primary.onclick = () => window.pnModal?.({
                title: 'Request pending',
                message: 'Your connection request has been sent and is waiting for a response.',
                confirmText: 'OK',
                cancelText: ''
            });
            toast('Connection request sent', 'success');
        } else {
            primary.disabled = false;
            primary.textContent = 'Connect';
            alert(data.message || 'Could not send request.');
        }
    } catch {
        primary.disabled = false;
        primary.textContent = 'Connect';
    }
}

async function respondToRequest(action) {
    const res = await fetch(`${URLROOT}/network/${action}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: viewedUserId })
    });
    const data = await res.json();
    if (data.success) {
        toast(action === 'accept' ? 'Connection accepted' : 'Invitation ignored', 'success');
        await configureProfileActions();
        await loadConnections(viewedUserId);
    } else {
        alert(data.message || 'Action failed.');
    }
}

function bindSectionPicker() {
    document.getElementById('close-section-picker')?.addEventListener('click', () => closeModal('section-picker-modal'));
    document.querySelectorAll('[data-section-action]').forEach((btn) => {
        btn.addEventListener('click', () => {
            closeModal('section-picker-modal');
            const action = btn.dataset.sectionAction;
            if (action === 'intro') document.getElementById('open-profile-edit')?.click();
            if (action === 'experience') openExperienceModal();
            if (action === 'education') openEducationModal();
        });
    });
}

function bindContactInfo() {
    document.getElementById('open-contact-info')?.addEventListener('click', openContactInfo);
    document.getElementById('close-contact-info')?.addEventListener('click', () => closeModal('contact-info-modal'));
}

function openContactInfo() {
    const u = currentUserData;
    const profileLink = `${URLROOT}/user/profile?id=${encodeURIComponent(u.user_id)}`;
    const canShowEmail = viewingOwnProfile || String(currentProfileSettings.show_email || '0') === '1';
    const canShowPhone = viewingOwnProfile || String(currentProfileSettings.show_phone || '0') === '1';
    const rows = [
        { icon: 'mail', label: 'Email', value: canShowEmail ? u.email : '', href: canShowEmail && u.email ? `mailto:${u.email}` : '' },
        { icon: 'call', label: 'Phone', value: canShowPhone ? u.phone : '', href: canShowPhone && u.phone ? `tel:${u.phone}` : '' },
        { icon: 'language', label: 'Website', value: u.website, href: normalizeWebsite(u.website) },
        { icon: 'link', label: 'Profile', value: profileLink, href: profileLink }
    ];
    const content = document.getElementById('contact-info-content');
    if (content) {
        content.innerHTML = rows.map(row => {
            const value = row.value || 'Not added';
            const valueHtml = row.href
                ? `<a href="${escapeHtml(row.href)}" class="text-[#0A66C2] font-semibold hover:underline break-all">${escapeHtml(value)}</a>`
                : `<span class="text-slate-500">${escapeHtml(value)}</span>`;
            return `
                <div class="flex gap-3">
                    <span class="material-symbols-outlined text-slate-400 mt-0.5">${row.icon}</span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-900">${escapeHtml(row.label)}</p>
                        <p class="text-sm">${valueHtml}</p>
                    </div>
                </div>`;
        }).join('');
    }
    openModal('contact-info-modal');
}

function normalizeWebsite(value) {
    if (!value) return '';
    return /^https?:\/\//i.test(value) ? value : `https://${value}`;
}

function togglePicMenu(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    document.getElementById('pic-options-menu')?.classList.toggle('hidden');
}

function closePicMenu() {
    document.getElementById('pic-options-menu')?.classList.add('hidden');
}

function viewProfilePic() {
    closePicMenu();
    const modal = document.getElementById('image-viewer-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    requestAnimationFrame(() => {
        modal.classList.add('opacity-100');
        document.getElementById('fullscreen-img').classList.remove('scale-95');
    });
}

function closeImageViewer() {
    const modal = document.getElementById('image-viewer-modal');
    modal.classList.remove('opacity-100');
    document.getElementById('fullscreen-img').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    const content = modal.querySelector('div');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    requestAnimationFrame(() => {
        modal.classList.add('opacity-100');
        content?.classList.remove('scale-95');
    });
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    const content = modal.querySelector('div');
    modal.classList.remove('opacity-100');
    content?.classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 200);
}

function closeAllModals() {
    ['profile-edit-modal', 'experience-modal', 'education-modal', 'image-viewer-modal', 'contact-info-modal', 'section-picker-modal'].forEach(id => {
        const el = document.getElementById(id);
        if (el && !el.classList.contains('hidden')) {
            if (id === 'image-viewer-modal') closeImageViewer();
            else closeModal(id);
        }
    });
}

function bindEditModals() {
    document.getElementById('open-profile-edit')?.addEventListener('click', () => {
        setValue('edit-full-name', currentUserData.full_name);
        setValue('edit-headline', currentUserData.headline);
        setValue('edit-location', currentUserData.location);
        setValue('edit-industry', currentUserData.industry);
        setValue('edit-phone', currentUserData.phone);
        setValue('edit-website', currentUserData.website);
        setValue('edit-bio', currentUserData.bio);
        document.getElementById('profile-edit-error')?.classList.add('hidden');
        openModal('profile-edit-modal');
    });
    document.getElementById('close-profile-edit')?.addEventListener('click', () => closeModal('profile-edit-modal'));

    document.getElementById('save-profile-edit')?.addEventListener('click', saveProfileEdit);
    document.getElementById('open-experience')?.addEventListener('click', () => openExperienceModal());
    document.getElementById('close-experience')?.addEventListener('click', () => closeModal('experience-modal'));
    document.getElementById('save-experience')?.addEventListener('click', saveExperience);
    document.getElementById('delete-experience')?.addEventListener('click', deleteExperience);
    document.getElementById('open-education')?.addEventListener('click', () => openEducationModal());
    document.getElementById('close-education')?.addEventListener('click', () => closeModal('education-modal'));
    document.getElementById('save-education')?.addEventListener('click', saveEducation);
    document.getElementById('delete-education')?.addEventListener('click', deleteEducation);
}

async function saveProfileEdit(e) {
    const btn = e.currentTarget;
    const err = document.getElementById('profile-edit-error');
    const payload = {
        fullName: valueOf('edit-full-name'),
        headline: valueOf('edit-headline'),
        location: valueOf('edit-location'),
        industry: valueOf('edit-industry'),
        phone: valueOf('edit-phone'),
        website: valueOf('edit-website'),
        bio: valueOf('edit-bio')
    };
    if (!payload.fullName || !payload.headline) {
        err?.classList.remove('hidden');
        return;
    }

    setButtonBusy(btn, 'Saving...');
    try {
        const res = await fetch(`${URLROOT}/user/update`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
            currentUserData = data.user;
            syncCurrentUserShell();
            renderProfileData();
            updateProfileStrength();
            closeModal('profile-edit-modal');
            toast('Profile updated', 'success');
        } else {
            alert(data.message || 'Failed to update profile.');
        }
    } finally {
        btn.innerHTML = 'Save';
    }
}

function openExperienceModal(exp = null) {
    setValue('exp-id', exp?.exp_id || '');
    setValue('exp-title', exp?.job_title || '');
    setValue('exp-company', exp?.company || '');
    setValue('exp-start', exp?.start_date || '');
    setValue('exp-end', exp?.end_date || '');
    setValue('exp-description', exp?.description || '');
    const current = document.getElementById('exp-current');
    if (current) current.checked = Boolean(Number(exp?.is_current || 0));
    document.getElementById('experience-modal-title').textContent = exp ? 'Edit experience' : 'Add experience';
    document.getElementById('save-experience').textContent = exp ? 'Save Changes' : 'Save Experience';
    document.getElementById('delete-experience')?.classList.toggle('hidden', !exp);
    openModal('experience-modal');
}

async function saveExperience(e) {
    const btn = e.currentTarget;
    const expId = valueOf('exp-id');
    const payload = {
        exp_id: expId,
        job_title: valueOf('exp-title'),
        company: valueOf('exp-company'),
        start_date: valueOf('exp-start'),
        end_date: valueOf('exp-end'),
        is_current: document.getElementById('exp-current')?.checked ? 1 : 0,
        description: valueOf('exp-description')
    };
    if (!payload.job_title || !payload.company || !payload.start_date) {
        alert('Title, company, and start date are required.');
        return;
    }

    setButtonBusy(btn, 'Saving...');
    try {
        const endpoint = expId ? `${URLROOT}/user/update_experience` : `${URLROOT}/user/add_experience`;
        const res = await fetch(endpoint, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
        const data = await res.json();
        if (data.success) {
            closeModal('experience-modal');
            await loadExperience(viewedUserId);
            updateProfileStrength();
            toast(expId ? 'Experience updated' : 'Experience added', 'success');
        } else {
            alert(data.message || 'Could not save experience.');
        }
    } finally {
        btn.textContent = expId ? 'Save Changes' : 'Save Experience';
    }
}

async function deleteExperience() {
    const expId = valueOf('exp-id');
    if (!expId) return;
    const ok = await confirm('Delete this experience?');
    if (!ok) return;
    const res = await fetch(`${URLROOT}/user/delete_experience`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ exp_id: expId })
    });
    const data = await res.json();
    if (data.success) {
        closeModal('experience-modal');
        await loadExperience(viewedUserId);
        updateProfileStrength();
        toast('Experience deleted', 'success');
    } else {
        alert(data.message || 'Could not delete experience.');
    }
}

function openEducationModal(edu = null) {
    setValue('edu-id', edu?.edu_id || '');
    setValue('edu-school', edu?.institution || '');
    setValue('edu-degree', edu?.degree || '');
    setValue('edu-field', edu?.field || '');
    setValue('edu-start', edu?.start_year || '');
    setValue('edu-end', edu?.end_year || '');
    document.getElementById('education-modal-title').textContent = edu ? 'Edit education' : 'Add education';
    document.getElementById('save-education').textContent = edu ? 'Save Changes' : 'Save Education';
    document.getElementById('delete-education')?.classList.toggle('hidden', !edu);
    openModal('education-modal');
}

async function saveEducation(e) {
    const btn = e.currentTarget;
    const eduId = valueOf('edu-id');
    const payload = {
        edu_id: eduId,
        institution: valueOf('edu-school'),
        degree: valueOf('edu-degree'),
        field: valueOf('edu-field'),
        start_year: valueOf('edu-start'),
        end_year: valueOf('edu-end')
    };
    if (!payload.institution) {
        alert('Institution is required.');
        return;
    }

    setButtonBusy(btn, 'Saving...');
    try {
        const endpoint = eduId ? `${URLROOT}/user/update_education` : `${URLROOT}/user/add_education`;
        const res = await fetch(endpoint, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
        const data = await res.json();
        if (data.success) {
            closeModal('education-modal');
            await loadEducation(viewedUserId);
            updateProfileStrength();
            toast(eduId ? 'Education updated' : 'Education added', 'success');
        } else {
            alert(data.message || 'Could not save education.');
        }
    } finally {
        btn.textContent = eduId ? 'Save Changes' : 'Save Education';
    }
}

async function deleteEducation() {
    const eduId = valueOf('edu-id');
    if (!eduId) return;
    const ok = await confirm('Delete this education?');
    if (!ok) return;
    const res = await fetch(`${URLROOT}/user/delete_education`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ edu_id: eduId })
    });
    const data = await res.json();
    if (data.success) {
        closeModal('education-modal');
        await loadEducation(viewedUserId);
        updateProfileStrength();
        toast('Education deleted', 'success');
    } else {
        alert(data.message || 'Could not delete education.');
    }
}

function bindImageUploads() {
    const picInput = document.getElementById('profile-pic-input');
    const coverInput = document.getElementById('banner-upload-input');

    document.getElementById('trigger-banner-upload')?.addEventListener('click', () => coverInput.click());

    picInput?.addEventListener('change', async (e) => {
        if (!e.target.files[0]) return;
        const fd = new FormData();
        fd.append('profile_pic', e.target.files[0]);
        try {
            toast('Uploading profile picture...', 'info');
            const res = await fetch(`${URLROOT}/user/upload_pic`, { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                currentUserData.profile_pic = data.fileName;
                syncCurrentUserShell();
                renderProfileData();
                toast('Profile picture updated', 'success');
            } else {
                alert(data.message || 'Upload failed.');
            }
        } finally {
            picInput.value = '';
        }
    });

    coverInput?.addEventListener('change', async (e) => {
        if (!e.target.files[0]) return;
        const fd = new FormData();
        fd.append('cover_image', e.target.files[0]);
        try {
            toast('Uploading cover photo...', 'info');
            const res = await fetch(`${URLROOT}/user/upload_cover`, { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                currentUserData.cover_image = data.fileName;
                syncCurrentUserShell();
                renderProfileData();
                toast('Cover photo updated', 'success');
            } else {
                alert(data.message || 'Upload failed.');
            }
        } finally {
            coverInput.value = '';
        }
    });
}

function syncCurrentUserShell() {
    if (!viewingOwnProfile) return;
    if (typeof setUserState === 'function') setUserState(currentUserData);
    if (typeof populateUserData === 'function') populateUserData(currentUserData);
}

async function loadExperience(targetId = null) {
    const container = document.getElementById('experience-container');
    if (!container) return;
    try {
        const endpoint = targetId ? `${URLROOT}/user/experience?id=${encodeURIComponent(targetId)}` : `${URLROOT}/user/experience`;
        const response = await fetch(endpoint);
        const result = await response.json();

        if (result.success) {
            currentExperience = result.data || [];
            const header = document.getElementById('header-latest-exp');
            const headerText = document.getElementById('header-exp-text');
            const headerImg = document.getElementById('header-exp-img');

            if (!currentExperience.length) {
                if (header) header.classList.add('hidden');
                container.innerHTML = `
                    <div class="flex flex-col items-center justify-center p-8 text-center bg-slate-50 rounded-xl border border-slate-100">
                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">work_history</span>
                        <h4 class="text-sm font-bold text-slate-700">No experience to show</h4>
                        ${viewingOwnProfile ? '<p class="text-xs text-slate-500 mt-1">Add your professional experience to stand out.</p>' : ''}
                    </div>`;
                return;
            }

            const latest = currentExperience[0];
            header?.classList.remove('hidden');
            headerImg?.classList.remove('hidden');
            if (headerText) headerText.textContent = latest.company;

            container.innerHTML = currentExperience.map((exp, index) => `
                <div class="flex gap-4 relative group/item">
                    ${index !== currentExperience.length - 1 ? '<div class="absolute left-6 top-14 bottom-[-1.5rem] w-px bg-slate-200"></div>' : ''}
                    <div class="w-12 h-12 bg-slate-100 rounded flex items-center justify-center shrink-0 z-10 border border-slate-200 shadow-sm">
                        <span class="material-symbols-outlined text-slate-400">business</span>
                    </div>
                    <div class="flex-1 pb-2">
                        <div class="flex justify-between items-start gap-4">
                            <div>
                                <h4 class="font-bold text-slate-900 text-base">${escapeHtml(exp.job_title)}</h4>
                                <p class="text-sm font-medium text-slate-700">${escapeHtml(exp.company)}</p>
                                <p class="text-xs font-bold text-slate-500 mt-1">${formatDate(exp.start_date)} - ${Number(exp.is_current) ? 'Present' : formatDate(exp.end_date)}</p>
                            </div>
                            ${viewingOwnProfile ? `
                                <button type="button" class="edit-exp-btn w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 opacity-0 group-hover/item:opacity-100 transition-all" data-exp-id="${exp.exp_id}" aria-label="Edit experience">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                            ` : ''}
                        </div>
                        ${exp.description ? `<p class="text-sm text-slate-600 mt-3 leading-relaxed whitespace-pre-wrap">${escapeHtml(exp.description)}</p>` : ''}
                    </div>
                </div>
            `).join('');

            container.querySelectorAll('.edit-exp-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const exp = currentExperience.find(item => String(item.exp_id) === String(btn.dataset.expId));
                    openExperienceModal(exp);
                });
            });
        }
    } catch (err) {
        console.error(err);
    }
}

async function loadEducation(targetId = null) {
    const container = document.getElementById('education-container');
    if (!container) return;
    try {
        const endpoint = targetId ? `${URLROOT}/user/education?id=${encodeURIComponent(targetId)}` : `${URLROOT}/user/education`;
        const response = await fetch(endpoint);
        const result = await response.json();

        if (result.success) {
            currentEducation = result.data || [];
            const header = document.getElementById('header-latest-edu');
            const headerText = document.getElementById('header-edu-text');
            const headerIcon = document.getElementById('header-edu-icon');

            if (!currentEducation.length) {
                if (header) header.classList.add('hidden');
                container.innerHTML = `
                    <div class="flex flex-col items-center justify-center p-8 text-center bg-slate-50 rounded-xl border border-slate-100">
                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">school</span>
                        <h4 class="text-sm font-bold text-slate-700">No education added</h4>
                        ${viewingOwnProfile ? '<p class="text-xs text-slate-500 mt-1">Add your education history.</p>' : ''}
                    </div>`;
                return;
            }

            const latest = currentEducation[0];
            header?.classList.remove('hidden');
            headerIcon?.classList.remove('hidden');
            if (headerText) headerText.textContent = latest.institution;

            container.innerHTML = currentEducation.map((edu, index) => `
                <div class="flex gap-4 relative group/item">
                    ${index !== currentEducation.length - 1 ? '<div class="absolute left-6 top-14 bottom-[-1.5rem] w-px bg-slate-200"></div>' : ''}
                    <div class="w-12 h-12 bg-slate-100 rounded flex items-center justify-center shrink-0 z-10 border border-slate-200 shadow-sm">
                        <span class="material-symbols-outlined text-slate-400">school</span>
                    </div>
                    <div class="flex-1 pb-2">
                        <div class="flex justify-between items-start gap-4">
                            <div>
                                <h4 class="font-bold text-slate-900 text-base">${escapeHtml(edu.institution)}</h4>
                                <p class="text-sm font-medium text-slate-700">${escapeHtml([edu.degree, edu.field].filter(Boolean).join(', ') || 'Education')}</p>
                                <p class="text-xs font-bold text-slate-500 mt-1">${[edu.start_year, edu.end_year].filter(Boolean).join(' - ') || 'Dates not set'}</p>
                            </div>
                            ${viewingOwnProfile ? `
                                <button type="button" class="edit-edu-btn w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 opacity-0 group-hover/item:opacity-100 transition-all" data-edu-id="${edu.edu_id}" aria-label="Edit education">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `).join('');

            container.querySelectorAll('.edit-edu-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const edu = currentEducation.find(item => String(item.edu_id) === String(btn.dataset.eduId));
                    openEducationModal(edu);
                });
            });
        }
    } catch (err) {
        console.error(err);
    }
}

async function loadConnections(targetId = null) {
    const container = document.getElementById('connections-list-profile');
    const countEl = document.getElementById('profile-connections-count');
    const headerCount = document.getElementById('profile-header-connections-count');
    if (!container) return;
    try {
        const endpoint = targetId ? `${URLROOT}/network/connections?id=${encodeURIComponent(targetId)}` : `${URLROOT}/network/connections`;
        const response = await fetch(endpoint);
        const result = await response.json();
        if (result.success) {
            currentConnections = result.connections || [];
            const label = `${currentConnections.length.toLocaleString()} connection${currentConnections.length === 1 ? '' : 's'}`;
            if (countEl) countEl.textContent = currentConnections.length.toLocaleString();
            if (headerCount) headerCount.textContent = label;
            if (!currentConnections.length) {
                container.innerHTML = '<p class="text-sm text-slate-500 py-4 font-medium text-center">No connections yet.</p>';
                return;
            }
            container.innerHTML = currentConnections.slice(0, 6).map(conn => {
                const picUrl = pnProfilePicUrl(conn);
                return `
                <div class="flex items-center justify-between py-1.5 border-b border-slate-50 last:border-0 group/conn cursor-pointer" data-profile-url="${URLROOT}/user/profile?id=${conn.user_id}">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="${picUrl}" class="w-10 h-10 rounded-full object-cover shadow-sm border border-slate-100" alt="${escapeHtml(conn.full_name)}">
                        <div class="min-w-0">
                            <h4 class="text-sm font-bold text-slate-900 group-hover/conn:text-[#0A66C2] transition-colors truncate">${escapeHtml(conn.full_name)}</h4>
                            <p class="text-xs text-slate-500 truncate">${escapeHtml(conn.headline || 'Professional')}</p>
                        </div>
                    </div>
                    <button type="button" data-message-id="${conn.user_id}" class="text-slate-400 hover:text-[#0A66C2] hover:bg-blue-50 w-8 h-8 rounded-full flex items-center justify-center transition-all opacity-0 group-hover/conn:opacity-100 scale-95 group-hover/conn:scale-100" aria-label="Message ${escapeHtml(conn.full_name)}">
                        <span class="material-symbols-outlined text-[18px]">message</span>
                    </button>
                </div>`;
            }).join('');

            container.querySelectorAll('[data-profile-url]').forEach((row) => {
                row.addEventListener('click', () => window.location.href = row.dataset.profileUrl);
            });
            container.querySelectorAll('[data-message-id]').forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    window.location.href = `${URLROOT}/user/messaging?chat=${btn.dataset.messageId}`;
                });
            });
        }
    } catch (err) {
        console.error(err);
    }
}

function updateProfileStrength() {
    const checks = [
        Boolean(currentUserData.full_name),
        Boolean(currentUserData.headline),
        Boolean(currentUserData.location),
        Boolean(currentUserData.bio),
        Boolean(currentUserData.profile_pic),
        Boolean(currentUserData.cover_image),
        currentExperience.length > 0,
        currentEducation.length > 0,
        currentConnections.length > 0,
        Boolean(currentUserData.email)
    ];
    const score = Math.round((checks.filter(Boolean).length / checks.length) * 100);
    const label = score >= 85 ? 'Expert' : score >= 60 ? 'Strong' : score >= 35 ? 'Intermediate' : 'Starter';
    const bar = document.getElementById('profile-strength-bar');
    const labelEl = document.getElementById('profile-strength-label');
    const copy = document.getElementById('profile-strength-copy');
    if (bar) bar.style.width = `${score}%`;
    if (labelEl) labelEl.textContent = label;
    if (copy) {
        copy.textContent = viewingOwnProfile
            ? nextStrengthSuggestion(score)
            : `${currentUserData.full_name || 'This member'} has completed ${score}% of their profile signals.`;
    }
}

function nextStrengthSuggestion(score) {
    if (score >= 85) return 'Your profile is in great shape. Keep it fresh as your career changes.';
    if (!currentUserData.profile_pic) return 'Add a profile picture so people can recognize you.';
    if (!currentUserData.bio) return 'Add an about section to explain what you do and what you care about.';
    if (!currentExperience.length) return 'Add experience to make your professional path visible.';
    if (!currentEducation.length) return 'Add education to round out your background.';
    if (!currentConnections.length) return 'Build your network so your profile has more reach.';
    return `Your profile is ${score}% complete. A few more details will make it stronger.`;
}

function setButtonBusy(btn, text) {
    btn.innerHTML = `<span class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span> ${escapeHtml(text)}`;
}

function setValue(id, value) {
    const el = document.getElementById(id);
    if (el) el.value = value || '';
}

function valueOf(id) {
    return (document.getElementById(id)?.value || '').trim();
}

function toast(message, type = 'info') {
    if (typeof jobsToast === 'function') jobsToast(message, type);
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(`${dateStr}T00:00:00`);
    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
}

function escapeHtml(s) {
    if (!s) return '';
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}
