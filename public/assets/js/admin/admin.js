/**
 * ProNetwork Admin Actions
 *
 * This file handles the small actions used across admin pages:
 * opening modals, confirming destructive actions, previewing records,
 * filtering users, and submitting user forms.
 */
'use strict';

const ADMIN_ENTITY_STYLES = {
    User: {
        icon: 'person',
        label: 'User Profile',
        bg: 'linear-gradient(135deg, #0A66C2 0%, #38bdf8 100%)'
    },
    Company: {
        icon: 'business',
        label: 'Company',
        bg: 'linear-gradient(135deg, #e11d48 0%, #fb7185 100%)'
    },
    Post: {
        icon: 'article',
        label: 'Community Post',
        bg: 'linear-gradient(135deg, #059669 0%, #34d399 100%)'
    },
    Job: {
        icon: 'work',
        label: 'Job Listing',
        bg: 'linear-gradient(135deg, #0891b2 0%, #22d3ee 100%)'
    }
};

const DEFAULT_ENTITY_STYLE = {
    icon: 'database',
    label: 'Record',
    bg: 'linear-gradient(135deg, #0f172a, #334155)'
};

const TOAST_STYLES = {
    error: { bg: 'bg-red-600', icon: 'error' },
    success: { bg: 'bg-green-600', icon: 'check_circle' },
    info: { bg: 'bg-blue-600', icon: 'info' }
};

function $(id) {
    return document.getElementById(id);
}

function showElement(id) {
    const element = $(id);
    if (element) element.classList.remove('hidden');
}

function hideElement(id) {
    const element = $(id);
    if (element) element.classList.add('hidden');
}

function reloadSoon(delay = 500) {
    setTimeout(() => location.reload(), delay);
}

async function ask(options) {
    return pnModal({
        cancelText: 'Cancel',
        ...options
    });
}

async function goToAfterConfirm(url, options) {
    if (await ask(options)) {
        window.location.href = url;
    }
}

async function postJson(url, body = null) {
    const fetchOptions = { method: 'POST' };

    if (body) {
        fetchOptions.headers = { 'Content-Type': 'application/json' };
        fetchOptions.body = JSON.stringify(body);
    }

    const response = await fetch(url, fetchOptions);
    return response.json();
}

function setBodyLocked(isLocked) {
    document.body.style.overflow = isLocked ? 'hidden' : '';
}

window.openEditUserModal = function(user) {
    $('edit-user-id').value = user.user_id;
    $('edit-full-name').value = user.full_name;
    $('edit-email').value = user.email;
    $('edit-role').value = user.is_admin ? 'Admin' : user.role;
    $('edit-password').value = '';
    showElement('editUserModal');
};

window.closeEditUserModal = function() {
    hideElement('editUserModal');
};

window.openAddUserModal = function() {
    showElement('addUserModal');
};

window.closeAddUserModal = function() {
    hideElement('addUserModal');
};

async function updateUserRole(userId, newRole) {
    const confirmed = await ask({
        title: 'Change User Role',
        message: `Are you sure you want to change this user's role to ${newRole}?`,
        type: 'info',
        confirmText: 'Yes, Change Role'
    });

    if (!confirmed) {
        location.reload();
        return;
    }

    try {
        const data = await postJson(`${URLROOT}/admin/update_role`, {
            user_id: userId,
            role: newRole
        });

        if (data.success) {
            adminToast('User role updated!', 'success');
            return;
        }

        adminToast('Failed to update role.', 'error');
        location.reload();
    } catch (error) {
        adminToast('Server error.', 'error');
        location.reload();
    }
}

function confirmApproveUser(userId) {
    return goToAfterConfirm(`${URLROOT}/admin/approve_user/${userId}`, {
        title: 'Approve User',
        message: 'Are you sure you want to approve this user? They will gain full access to the platform.',
        type: 'success',
        confirmText: 'Yes, Approve'
    });
}

function confirmRejectUser(userId) {
    return goToAfterConfirm(`${URLROOT}/admin/reject_user/${userId}`, {
        title: 'Reject User',
        message: 'Are you sure you want to reject this user registration?',
        type: 'warning',
        confirmText: 'Yes, Reject',
        isDanger: true
    });
}

function confirmDeleteUser(userId) {
    return goToAfterConfirm(`${URLROOT}/admin/delete_user/${userId}`, {
        title: 'CRITICAL: Delete User',
        message: 'Are you sure you want to delete this user? This action cannot be undone and will remove all their data.',
        type: 'warning',
        confirmText: 'Yes, Delete Permanently',
        isDanger: true
    });
}

async function deletePost(postId) {
    const confirmed = await ask({
        title: 'Delete Post',
        message: 'Are you sure you want to delete this post? This action cannot be undone.',
        type: 'warning',
        confirmText: 'Delete Post',
        isDanger: true
    });

    if (!confirmed) return;

    try {
        const data = await postJson(`${URLROOT}/admin/delete_post/${postId}`);

        if (data.success) {
            fadeOutAndRemove(`admin-post-${postId}`);
            adminToast('Post deleted.', 'success');
            return;
        }

        adminToast('Failed to delete post.', 'error');
    } catch (error) {
        adminToast('Server error.', 'error');
    }
}

function deleteCompany(id) {
    return deleteAdminRecord({
        url: `${URLROOT}/admin/delete_company/${id}`,
        title: 'Delete Company',
        message: 'Are you sure you want to delete this company? All associated jobs will also be deleted.',
        confirmText: 'Delete Company',
        successMessage: 'Company deleted.',
        errorMessage: 'Failed to delete company.'
    });
}

function deleteJob(id) {
    return deleteAdminRecord({
        url: `${URLROOT}/admin/delete_job/${id}`,
        title: 'Delete Job Listing',
        message: 'Are you sure you want to delete this job listing?',
        confirmText: 'Delete Job',
        successMessage: 'Job deleted.',
        errorMessage: 'Failed to delete job.'
    });
}

async function toggleJobStatus(id) {
    try {
        const data = await postJson(`${URLROOT}/admin/toggle_job_status/${id}`);
        if (data.success) {
            adminToast('Job status updated.', 'success');
            reloadSoon();
        } else {
            adminToast('Failed to update job status.', 'error');
        }
    } catch (error) {
        adminToast('Server error.', 'error');
    }
}

async function deleteAdminRecord(options) {
    const confirmed = await ask({
        title: options.title,
        message: options.message,
        type: 'warning',
        confirmText: options.confirmText,
        isDanger: true
    });

    if (!confirmed) return;

    try {
        const data = await postJson(options.url);

        if (data.success) {
            adminToast(options.successMessage, 'success');
            reloadSoon();
            return;
        }

        adminToast(options.errorMessage, 'error');
    } catch (error) {
        adminToast('Server error.', 'error');
    }
}

function fadeOutAndRemove(id) {
    const element = $(id);
    if (!element) return;

    element.style.opacity = '0.5';
    element.style.pointerEvents = 'none';
    setTimeout(() => element.remove(), 500);
}

function adminToast(message, type = 'info') {
    const existingToast = $('admin-toast');
    if (existingToast) existingToast.remove();

    const style = TOAST_STYLES[type] || TOAST_STYLES.info;
    const toast = document.createElement('div');

    toast.id = 'admin-toast';
    toast.className = `fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg text-sm font-medium ${style.bg} text-white transition-all duration-300`;
    toast.innerHTML = `<span class="material-symbols-outlined text-[18px]">${style.icon}</span> ${escapeAdminHtml(message)}`;

    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

window.closeAdminEntityModal = function() {
    hideElement('adminEntityModal');
    setBodyLocked(false);
};

window.openAdminEntityModal = async function(type, id) {
    const modal = $('adminEntityModal');
    const loading = $('admin-entity-loading');
    const body = $('admin-entity-body');
    const error = $('admin-entity-error');

    if (!modal || !loading || !body || !error) return;

    modal.classList.remove('hidden');
    setBodyLocked(true);
    loading.classList.remove('hidden');
    body.classList.add('hidden');
    error.classList.add('hidden');

    try {
        const data = await fetchEntityInfo(type, id);
        renderAdminEntityModal(data);
        loading.classList.add('hidden');
        body.classList.remove('hidden');
    } catch (err) {
        loading.classList.add('hidden');
        $('admin-entity-error-message').textContent = err.message || 'Please try again.';
        error.classList.remove('hidden');
    }
};

async function fetchEntityInfo(type, id) {
    const url = `${URLROOT}/admin/get_entity_info/${encodeURIComponent(type)}/${encodeURIComponent(id)}`;
    const response = await fetch(url);
    const data = await response.json();

    if (!data.success) {
        throw new Error(data.message || 'No details were returned for this record.');
    }

    return data;
}

function renderAdminEntityModal(data) {
    const style = ADMIN_ENTITY_STYLES[data.type] || DEFAULT_ENTITY_STYLE;

    setEntityHeader(data, style);
    setEntityAvatar(data, style);
    setEntityImage(data);
    setEntityMeta(data.meta || {});

    $('admin-entity-content').textContent = data.content || 'No overview mapped.';
    $('admin-entity-manage').href = data.actions && data.actions.manage_url ? data.actions.manage_url : '#';

    const deleteBtn = $('admin-entity-delete-btn');
    const deleteText = $('admin-entity-delete-text');
    if (deleteBtn && deleteText) {
        if (data.actions && data.actions.delete_type && data.actions.delete_id) {
            deleteText.textContent = `Remove ${data.type}`;
            deleteBtn.onclick = () => {
                closeAdminEntityModal();
                if (data.type === 'Post') deletePost(data.actions.delete_id);
                else if (data.type === 'Company') deleteCompany(data.actions.delete_id);
                else if (data.type === 'Job') deleteJob(data.actions.delete_id);
                else if (data.type === 'User') confirmDeleteUser(data.actions.delete_id);
            };
            deleteBtn.classList.remove('hidden');
        } else {
            deleteBtn.classList.add('hidden');
            deleteBtn.onclick = null;
        }
    }
}

function setEntityHeader(data, style) {
    const hero = $('admin-entity-hero');
    if (hero) hero.style.background = style.bg;

    $('admin-entity-type').innerHTML = `
        <span class="material-symbols-outlined text-[11px] align-middle mr-0.5">${style.icon}</span>
        ${style.label} #${escapeAdminHtml(data.id)}
    `;
    $('admin-entity-title').textContent = data.title || 'Untitled block';
    $('admin-entity-subtitle').textContent = data.subtitle || '';
}

function setEntityAvatar(data, style) {
    const avatar = $('admin-entity-avatar');

    if (data.image && (data.type === 'Company' || data.type === 'Job')) {
        avatar.innerHTML = `<img src="${escapeAdminAttr(data.image)}" alt="" class="w-full h-full object-contain p-1 bg-white" onerror="this.parentElement.innerHTML='<span class=\\'material-symbols-outlined text-[28px]\\'>${style.icon}</span>'">`;
        return;
    }

    if (data.image && data.type === 'User') {
        avatar.innerHTML = `<img src="${escapeAdminAttr(data.image)}" alt="" class="w-full h-full object-cover">`;
        return;
    }

    if (data.type === 'User') {
        avatar.innerHTML = `<span class="text-xl font-black">${escapeAdminHtml((data.title || '?')[0].toUpperCase())}</span>`;
        return;
    }

    avatar.innerHTML = `<span class="material-symbols-outlined text-[28px]">${style.icon}</span>`;
}

function setEntityImage(data) {
    const imageWrap = $('admin-entity-image-wrap');
    const image = $('admin-entity-image');
    const imageLink = $('admin-entity-image-link');

    if (data.image && data.type === 'Post') {
        image.src = data.image;
        if (imageLink) imageLink.href = data.image;
        imageWrap.classList.remove('hidden');
        return;
    }

    image.removeAttribute('src');
    if (imageLink) imageLink.removeAttribute('href');
    imageWrap.classList.add('hidden');
}

function setEntityMeta(meta) {
    $('admin-entity-meta').innerHTML = Object.entries(meta).map(([key, value]) => `
        <div>
            <dt>${escapeAdminHtml(key)}</dt>
            <dd>${escapeAdminHtml(blankToNA(value))}</dd>
        </div>
    `).join('');
}

function blankToNA(value) {
    return value === null || value === undefined || value === '' ? 'N/A' : value;
}

document.addEventListener('DOMContentLoaded', () => {
    bindEntityPreviewClicks();
    bindUserSearch();
    bindCompanySearch();
    bindJobSearch();
    bindPostSearch();
    bindUserForms();
});

function bindEntityPreviewClicks() {
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-admin-preview]');
        if (!trigger) return;

        const ignored = event.target.closest('[data-admin-ignore-preview], button, a, select, input, textarea, label');
        if (ignored) return;

        openAdminEntityModal(trigger.dataset.previewType, trigger.dataset.previewId);
    });
}

function bindUserSearch() {
    const searchInput = $('admin-user-search');
    const userTable = $('admin-users-table');

    if (!searchInput || !userTable) return;

    searchInput.addEventListener('keyup', () => {
        const term = searchInput.value.toLowerCase();
        const rows = Array.from(userTable.getElementsByTagName('tr'));

        rows.forEach((row) => {
            const name = row.querySelector('p.text-sm.font-bold')?.innerText.toLowerCase() || '';
            const email = row.querySelector('p.text-\\[11px\\].text-slate-500')?.innerText.toLowerCase()
                || row.querySelectorAll('p')[1]?.innerText.toLowerCase()
                || '';

            row.style.display = name.includes(term) || email.includes(term) ? '' : 'none';
        });
    });
}

function bindCompanySearch() {
    const searchInput = $('admin-company-search');
    const companyTable = $('admin-companies-table');

    if (!searchInput || !companyTable) return;

    searchInput.addEventListener('keyup', () => {
        const term = searchInput.value.toLowerCase();
        const rows = Array.from(companyTable.getElementsByTagName('tr'));

        rows.forEach((row) => {
            const textContent = row.innerText.toLowerCase();
            row.style.display = textContent.includes(term) ? '' : 'none';
        });
    });
}

function bindJobSearch() {
    const searchInput = $('admin-job-search');
    const jobTable = $('admin-jobs-table');

    if (!searchInput || !jobTable) return;

    searchInput.addEventListener('keyup', () => {
        const term = searchInput.value.toLowerCase();
        const rows = Array.from(jobTable.getElementsByTagName('tr'));

        rows.forEach((row) => {
            const textContent = row.innerText.toLowerCase();
            row.style.display = textContent.includes(term) ? '' : 'none';
        });
    });
}

function bindPostSearch() {
    const searchInput = $('admin-post-search');
    const postsTable = $('admin-posts-table');

    if (!searchInput || !postsTable) return;

    searchInput.addEventListener('keyup', () => {
        const term = searchInput.value.toLowerCase();
        const rows = Array.from(postsTable.getElementsByTagName('tr'));

        rows.forEach((row) => {
            if (row.id === 'no-posts-placeholder') return;
            const textContent = row.innerText.toLowerCase();
            row.style.display = textContent.includes(term) ? '' : 'none';
        });
    });
}


function bindUserForms() {
    const editForm = $('editUserForm');
    const addForm = $('addUserForm');

    if (editForm) editForm.addEventListener('submit', submitEditUserForm);
    if (addForm) addForm.addEventListener('submit', submitAddUserForm);
}

async function submitEditUserForm(event) {
    event.preventDefault();

    const data = {
        user_id: $('edit-user-id').value,
        full_name: $('edit-full-name').value,
        email: $('edit-email').value,
        role: $('edit-role').value,
        password: $('edit-password').value
    };

    if (!isPasswordValid(data.password, true)) return;

    await submitUserForm(`${URLROOT}/admin/update_user`, data, {
        success: 'User updated successfully!',
        fallbackError: 'Failed to update user.'
    });
}

async function submitAddUserForm(event) {
    event.preventDefault();

    const data = {
        full_name: $('add-full-name').value.trim(),
        email: $('add-email').value.trim(),
        password: $('add-password').value,
        role: $('add-role').value
    };

    if (!data.full_name || !data.email || !data.password) {
        adminToast('All fields are required.', 'error');
        return;
    }

    if (!isPasswordValid(data.password, false)) return;

    await submitUserForm(`${URLROOT}/admin/add_user`, data, {
        success: 'User created successfully!',
        fallbackError: 'Failed to create user.'
    });
}

function isPasswordValid(password, allowEmpty) {
    if (allowEmpty && !password) return true;

    if (password.length >= 6) return true;

    adminToast('Password must be at least 6 characters.', 'error');
    return false;
}

async function submitUserForm(url, data, messages) {
    try {
        const result = await postJson(url, data);

        if (result.success) {
            adminToast(messages.success, 'success');
            reloadSoon();
            return;
        }

        adminToast(result.error || messages.fallbackError, 'error');
    } catch (error) {
        adminToast('Server error.', 'error');
    }
}

function escapeAdminHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function escapeAdminAttr(value) {
    return escapeAdminHtml(value).replace(/`/g, '&#96;');
}

function togglePasswordVisibility(inputId, button) {
    const input = $(inputId);
    const icon = button.querySelector('.material-symbols-outlined');
    const isHidden = input.type === 'password';

    input.type = isHidden ? 'text' : 'password';
    icon.textContent = isHidden ? 'visibility_off' : 'visibility';
}

Object.assign(window, {
    updateUserRole,
    confirmApproveUser,
    confirmRejectUser,
    confirmDeleteUser,
    deletePost,
    deleteCompany,
    deleteJob,
    toggleJobStatus,
    adminToast,
    togglePasswordVisibility
});
