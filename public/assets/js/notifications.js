/**
 * ProNetwork — Notifications Logic
 * assets/js/notifications.js
 */
'use strict';

let allNotifications = [];
let currentFilter = 'all';

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('notifications-list')) {
        initNotifications();
        initNotifSuggestions();
        
        // Polling for new notifications every 15 seconds
        setInterval(() => {
            initNotifications(false); // don't show loading spinner if we added one
        }, 15000);

        document.getElementById('mark-all-read')?.addEventListener('click', async () => {
            const res = await fetch(`${URLROOT}/notification/mark_read`);
            const data = await res.json();
            if (data.success) {
                // Update local array state
                allNotifications.forEach(n => n.is_read = 1);
                renderFilteredNotifications();
                notifToast('All notifications marked as read', 'success');
            }
        });

        // Setup filter click listeners
        const filterSpans = document.querySelectorAll('.notif-filter-btn');
        filterSpans.forEach(span => {
            span.addEventListener('click', () => {
                filterSpans.forEach(s => {
                    s.className = 'notif-filter-btn bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-semibold hover:bg-slate-200 cursor-pointer transition-colors';
                });
                span.className = 'notif-filter-btn bg-[#0A66C2] text-white px-3 py-1 rounded-full text-xs font-semibold cursor-pointer transition-colors';
                currentFilter = span.dataset.filter || 'all';
                renderFilteredNotifications();
            });
        });

        // Setup preference buttons
        const prefBtns = document.querySelectorAll('.notif-pref-btn');
        prefBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const action = btn.dataset.action || 'Preferences';
                notifToast(`${action} updated successfully.`, 'success');
            });
        });
    }
});

async function initNotifications(showLoading = true) {
    const list = document.getElementById('notifications-list');
    if (!list) return;
    
    try {
        const response = await fetch(`${URLROOT}/notification/fetch`);
        const data = await response.json();
        
        if (data.success) {
            allNotifications = data.notifications || [];
            renderFilteredNotifications();
            
            // Mark as read on initial view
            if (showLoading) {
                await fetch(`${URLROOT}/notification/mark_read`);
            }
        }
    } catch(err) {
        console.error(err);
    }
}

function renderFilteredNotifications() {
    const list = document.getElementById('notifications-list');
    if (!list) return;

    let filtered = allNotifications;
    if (currentFilter === 'posts') {
        filtered = allNotifications.filter(n => n.source_type === 'post' || n.type === 'Like' || n.type === 'Comment');
    } else if (currentFilter === 'mentions') {
        // Mentions or direct interactions
        filtered = allNotifications.filter(n => n.type === 'Comment' || n.type.includes('Connection'));
    }

    if (filtered.length === 0) {
        list.innerHTML = `<div class="bg-white p-12 text-center rounded-lg border border-slate-100"><p class="text-slate-500 font-medium text-sm">No notifications found in this category.</p></div>`;
        return;
    }

    let newHtml = '';
    filtered.forEach(notif => {
        const timeAgo = formatTimeAgoNotif(new Date(notif.created_at));
        
        let iconData = { icon: 'notifications', bg: 'bg-slate-100', color: 'text-slate-700' };
        if (notif.type === 'Like') iconData = { icon: 'thumb_up', bg: 'bg-blue-100', color: 'text-blue-700' };
        else if (notif.type === 'Comment') iconData = { icon: 'comment', bg: 'bg-amber-100', color: 'text-amber-700' };
        else if (notif.type === 'Connection_Accepted') iconData = { icon: 'person_add', bg: 'bg-purple-100', color: 'text-purple-700' };
        else if (notif.type === 'Connection_Request') iconData = { icon: 'group_add', bg: 'bg-purple-100', color: 'text-purple-700' };
        else if (notif.type === 'Job_Alert') iconData = { icon: 'work', bg: 'bg-emerald-100', color: 'text-emerald-700' };
        else if (notif.type === 'Application_Update') iconData = { icon: 'assignment_turned_in', bg: 'bg-teal-100', color: 'text-teal-700' };

        // Determine destination target URL
        let targetUrl = `${URLROOT}/user/feed`;
        if (notif.source_type === 'post' || notif.type === 'Like' || notif.type === 'Comment') {
            if (notif.source_id) {
                targetUrl = `${URLROOT}/post/show/${notif.source_id}`;
            } else {
                targetUrl = `${URLROOT}/user/feed`;
            }
        } else if (notif.source_type === 'connection' || notif.type.includes('Connection')) {
            targetUrl = `${URLROOT}/network`;
        } else if (notif.source_type === 'job' || notif.type.includes('Job') || notif.type.includes('Application')) {
            targetUrl = `${URLROOT}/job`;
        }

        newHtml += `
        <a href="${targetUrl}" class="block bg-white hover:bg-slate-50 p-4 transition-all relative border-b border-slate-100 ${notif.is_read ? 'opacity-80' : 'bg-blue-50/20'}">
            <div class="flex gap-3 items-start">
                <div class="flex-shrink-0 relative mt-0.5">
                    <div class="w-11 h-11 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center shadow-inner">
                        <span class="material-symbols-outlined text-slate-500 text-xl">person</span>
                    </div>
                    <div class="absolute -bottom-1 -right-1 ${iconData.bg} rounded-full p-1 border-2 border-white shadow-sm flex items-center justify-center">
                        <span class="material-symbols-outlined ${iconData.color} text-[10px]" style="font-variation-settings: 'FILL' 1;">${iconData.icon}</span>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-body-md text-sm text-slate-800 leading-snug break-words">
                        ${escapeHtml(notif.message)}
                    </p>
                    <span class="text-[11px] text-slate-400 font-caption mt-1 block">${timeAgo}</span>
                </div>
                <div class="flex flex-col items-end gap-2 shrink-0">
                    ${!notif.is_read ? '<span class="w-2.5 h-2.5 bg-[#0A66C2] rounded-full shadow-sm"></span>' : ''}
                    <button type="button" onclick="event.preventDefault(); event.stopPropagation(); removeNotification(${notif.notification_id});" class="text-slate-300 hover:text-slate-500 transition-colors p-1 rounded-full hover:bg-slate-100">
                        <span class="material-symbols-outlined text-sm block">close</span>
                    </button>
                </div>
            </div>
        </a>`;
    });

    list.innerHTML = newHtml;
}

async function removeNotification(id) {
    allNotifications = allNotifications.filter(n => n.notification_id != id);
    renderFilteredNotifications();
    notifToast('Notification dismissed', 'info');
}

async function initNotifSuggestions() {
    const sugCont = document.getElementById('notif-suggestions');
    if (!sugCont) return;
    try {
        const res = await fetch(`${URLROOT}/network/suggestions`);
        const data = await res.json();
        if (data.success) {
            sugCont.innerHTML = '';
            if (data.suggestions.length === 0) {
                sugCont.innerHTML = '<p class="text-xs text-slate-500">No suggestions right now.</p>';
                return;
            }
            data.suggestions.slice(0, 3).forEach(u => {
                const picUrl = u.profile_pic ? (u.profile_pic.startsWith('http') ? u.profile_pic : `${URLROOT}/uploads/profiles/` + u.profile_pic) : '';
                const picHtml = picUrl ? `<img src="${picUrl}" class="w-10 h-10 rounded-full object-cover">` : `<div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center"><span class="material-symbols-outlined text-slate-400 text-xl">person</span></div>`;
                sugCont.innerHTML += `
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-3 min-w-0">
                        ${picHtml}
                        <div class="min-w-0 flex-1">
                            <h4 class="text-sm font-bold text-slate-900 truncate">${escapeHtml(u.full_name)}</h4>
                            <p class="text-[11px] text-slate-500 leading-tight truncate">${escapeHtml(u.headline || 'Professional')}</p>
                            <a href="${URLROOT}/network" class="mt-1.5 inline-flex items-center space-x-1 border border-slate-400 rounded-full px-3 py-0.5 text-slate-600 text-[11px] font-bold hover:bg-slate-50 transition-colors">
                                <span class="material-symbols-outlined text-[13px]">person_add</span>
                                <span>Connect</span>
                            </a>
                        </div>
                    </div>
                </div>`;
            });
        }
    } catch(e){}
}

function notifToast(msg, type = 'info') {
    const existing = document.getElementById('notif-toast');
    if (existing) existing.remove();
    const t = document.createElement('div');
    t.id = 'notif-toast';
    const bg = type === 'error' ? 'bg-red-600' : type === 'success' ? 'bg-green-600' : 'bg-blue-600';
    const icon = type === 'error' ? 'error' : type === 'success' ? 'check_circle' : 'info';
    t.className = `fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-xl text-sm font-medium ${bg} text-white ring-1 ring-white/10 pn-toast-motion`;
    t.innerHTML = `<span class="material-symbols-outlined text-[20px]">${icon}</span><span>${escapeHtml(msg)}</span>`;
    document.body.appendChild(t);
    setTimeout(() => {
        t.style.opacity = '0';
        t.style.transform = 'translate3d(12px, 8px, 0) scale(0.96)';
        t.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
        setTimeout(() => t.remove(), 360);
    }, 3500);
}

function formatTimeAgoNotif(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    let interval = seconds / 86400;
    if (interval > 1) return Math.floor(interval) + "d ago";
    interval = seconds / 3600;
    if (interval > 1) return Math.floor(interval) + "h ago";
    interval = seconds / 60;
    if (interval > 1) return Math.floor(interval) + "m ago";
    return "just now";
}

function escapeHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}
