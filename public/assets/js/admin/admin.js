/**
 * ProNetwork — Admin Actions
 * public/assets/js/admin.js
 */
'use strict';

async function updateUserRole(userId, newRole) {
    if (!confirm(`Are you sure you want to change this user's role to ${newRole}?`)) {
        location.reload();
        return;
    }

    try {
        const response = await fetch(`${URLROOT}/admin/update_role`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId, role: newRole })
        });
        const data = await response.json();
        if (data.success) {
            showToast('User role updated!', 'success');
        } else {
            showToast('Failed to update role.', 'error');
            location.reload();
        }
    } catch (e) {
        showToast('Server error.', 'error');
        location.reload();
    }
}

function confirmDeleteUser(userId) {
    if (confirm('CRITICAL: Are you sure you want to delete this user? This action cannot be undone.')) {
        window.location.href = `${URLROOT}/admin/delete_user/${userId}`;
    }
}

async function deletePost(postId) {
    if (!confirm('Are you sure you want to delete this post?')) return;

    try {
        const response = await fetch(`${URLROOT}/admin/delete_post/${postId}`, {
            method: 'POST'
        });
        const data = await response.json();
        if (data.success) {
            const el = document.getElementById(`admin-post-${postId}`);
            if (el) {
                el.style.opacity = '0.5';
                el.style.pointerEvents = 'none';
                setTimeout(() => el.remove(), 500);
            }
            showToast('Post deleted.', 'success');
        } else {
            showToast('Failed to delete post.', 'error');
        }
    } catch (e) {
        showToast('Server error.', 'error');
    }
}

async function deleteCompany(id) {
    if (!confirm('Are you sure you want to delete this company?')) return;
    try {
        const response = await fetch(`${URLROOT}/admin/delete_company/${id}`, { method: 'POST' });
        const data = await response.json();
        if (data.success) {
            showToast('Company deleted.', 'success');
            location.reload();
        }
    } catch (e) {}
}

async function deleteJob(id) {
    if (!confirm('Are you sure you want to delete this job?')) return;
    try {
        const response = await fetch(`${URLROOT}/admin/delete_job/${id}`, { method: 'POST' });
        const data = await response.json();
        if (data.success) {
            showToast('Job deleted.', 'success');
            location.reload();
        }
    } catch (e) {}
}

// Simple Toast Helper (assuming showToast exists in global scope or adding a fallback)
// Simple Toast Helper (assuming showToast exists in global scope or adding a fallback)
function showToast(msg, type = 'info') {
    if (typeof window.showToast === 'function') {
        window.showToast(msg, type);
    } else {
        alert(`${type.toUpperCase()}: ${msg}`);
    }
}

// Real-time Search for Users
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('admin-user-search');
    const userTable = document.getElementById('admin-users-table');

    if (searchInput && userTable) {
        searchInput.addEventListener('keyup', (e) => {
            const term = e.target.value.toLowerCase();
            const rows = userTable.getElementsByTagName('tr');

            Array.from(rows).forEach(row => {
                const name = row.querySelector('p.text-sm.font-bold').innerText.toLowerCase();
                const email = row.querySelector('p.text-\\[11px\\].text-slate-500').innerText.toLowerCase();

                if (name.includes(term) || email.includes(term)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Edit User Modal Logic
    const editModal = document.getElementById('editUserModal');
    const editForm = document.getElementById('editUserForm');

    window.openEditUserModal = function(user) {
        document.getElementById('edit-user-id').value = user.user_id;
        document.getElementById('edit-full-name').value = user.full_name;
        document.getElementById('edit-email').value = user.email;
        document.getElementById('edit-role').value = user.is_admin ? 'Admin' : user.role;
        document.getElementById('edit-password').value = ''; // Reset password field
        editModal.classList.remove('hidden');
    }

    window.closeEditUserModal = function() {
        editModal.classList.add('hidden');
    }

    if (editForm) {
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('edit-user-id').value;
            const data = {
                user_id: id,
                full_name: document.getElementById('edit-full-name').value,
                email: document.getElementById('edit-email').value,
                role: document.getElementById('edit-role').value,
                password: document.getElementById('edit-password').value
            };

            if (data.password && data.password.length < 6) {
                showToast('Password must be at least 6 characters.', 'error');
                return;
            }

            try {
                const res = await fetch(`${URLROOT}/admin/update_user`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await res.json();
                if (result.success) {
                    showToast('User updated successfully!', 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showToast(result.error || 'Failed to update user.', 'error');
                }
            } catch (err) {
                showToast('Server error.', 'error');
            }
        });
    }
});

function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('.material-symbols-outlined');
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
}
