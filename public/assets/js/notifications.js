/**
 * ProNetwork — Notifications Logic
 * assets/js/notifications.js
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('notifications-list')) {
        initNotifications();
        
        // Polling for new notifications every 15 seconds
        setInterval(() => {
            initNotifications(false); // don't show loading spinner if we added one
        }, 15000);

        document.getElementById('mark-all-read')?.addEventListener('click', async () => {
            const res = await fetch(`${URLROOT}/notification/mark_read`);
            const data = await res.json();
            if (data.success) {
                initNotifications();
            }
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
            const currentHtml = list.innerHTML;
            let newHtml = '';
            
            if (data.notifications.length === 0) {
                newHtml = '<div class="bg-white p-8 text-center"><p class="text-slate-500">You have no notifications right now.</p></div>';
            } else {
                data.notifications.forEach(notif => {
                    const timeAgo = formatTimeAgoNotif(new Date(notif.created_at));
                    
                    let iconData = { icon: 'notifications', bg: 'bg-slate-100', color: 'text-slate-700' };
                    if (notif.type === 'Like') iconData = { icon: 'thumb_up', bg: 'bg-blue-100', color: 'text-blue-700' };
                    else if (notif.type === 'Comment') iconData = { icon: 'comment', bg: 'bg-amber-100', color: 'text-amber-700' };
                    else if (notif.type === 'Connection_Accepted') iconData = { icon: 'person_add', bg: 'bg-purple-100', color: 'text-purple-700' };
                    
                    newHtml += `
<div class="bg-white hover:bg-slate-50 p-4 transition-colors relative border-b border-slate-100 ${notif.is_read ? 'opacity-80' : ''}">
<div class="flex gap-3">
<div class="flex-shrink-0 relative">
<div class="w-12 h-12 rounded-full bg-slate-200 flex items-center justify-center">
<span class="material-symbols-outlined text-slate-400">person</span>
</div>
<div class="absolute -bottom-1 -right-1 ${iconData.bg} rounded-full p-1 border-2 border-white">
<span class="material-symbols-outlined ${iconData.color} text-xs" style="font-variation-settings: 'FILL' 1;">${iconData.icon}</span>
</div>
</div>
<div class="flex-1">
<p class="font-body-md text-slate-800">
${escapeHtml(notif.message)}
</p>
<span class="text-xs text-slate-500 font-caption">${timeAgo}</span>
</div>
<div class="flex flex-col items-end gap-1">
${!notif.is_read ? '<span class="w-2 h-2 bg-[#0A66C2] rounded-full"></span>' : ''}
<span class="material-symbols-outlined text-slate-400 cursor-pointer hover:text-slate-600">more_horiz</span>
</div>
</div>
</div>`;
                });
            }

            if (list.innerHTML !== newHtml) {
                list.innerHTML = newHtml;
            }
            
            // Mark as read only if it's the initial load or tab is active
            if (showLoading) {
                await fetch(`${URLROOT}/notification/mark_read`);
            }
        }
    } catch(err) {
        console.error(err);
    }
}

function formatTimeAgoNotif(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    let interval = seconds / 86400;
    if (interval > 1) return Math.floor(interval) + "d";
    interval = seconds / 3600;
    if (interval > 1) return Math.floor(interval) + "h";
    interval = seconds / 60;
    if (interval > 1) return Math.floor(interval) + "m";
    return "now";
}

function escapeHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}
