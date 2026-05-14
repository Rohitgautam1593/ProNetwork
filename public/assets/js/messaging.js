/**
 * ProNetwork — Messaging Logic
 * assets/js/messaging.js
 */
'use strict';

let currentChatUserId = null;
let currentChatUserName = '';
let currentChatUserPic = '';

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('conversations-list')) {
        initMessaging();
        
        // Check for ?chat=ID parameter
        const urlParams = new URLSearchParams(window.location.search);
        const chatId = urlParams.get('chat');
        if (chatId) {
            // Fetch user info first if needed, or just open chat if we have the ID
            // For now, we'll try to fetch user info to get the name/pic
            fetch(`${URLROOT}/user/data/${chatId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const u = data.user;
                        const picUrl = u.profile_pic ? (u.profile_pic.startsWith('http') ? u.profile_pic : `${URLROOT}/uploads/profiles/` + u.profile_pic) : '';
                        openChat(u.user_id, u.full_name, picUrl);
                    }
                });
        }

        // Polling for new messages every 5 seconds
        setInterval(() => {
            if (currentChatUserId) {
                fetchChatHistory(false);
            }
            fetchConversations();
        }, 5000);
    }
});

async function initMessaging() {
    await fetchConversations();
    await fetchConnections();
    
    const sendBtn = document.getElementById('msg-send-btn');
    if (sendBtn) {
        sendBtn.addEventListener('click', sendMessage);
    }

    // Tab switching
    const tabConv = document.getElementById('tab-conv');
    const tabConn = document.getElementById('tab-conn');
    const listConv = document.getElementById('conversations-list');
    const listConn = document.getElementById('connections-list-msg');

    tabConv?.addEventListener('click', () => {
        tabConv.classList.add('text-blue-700', 'border-b-2', 'border-blue-700');
        tabConv.classList.remove('text-slate-500');
        tabConn.classList.remove('text-blue-700', 'border-b-2', 'border-blue-700');
        tabConn.classList.add('text-slate-500');
        listConv.classList.remove('hidden');
        listConn.classList.add('hidden');
    });

    tabConn?.addEventListener('click', () => {
        tabConn.classList.add('text-blue-700', 'border-b-2', 'border-blue-700');
        tabConn.classList.remove('text-slate-500');
        tabConv.classList.remove('text-blue-700', 'border-b-2', 'border-blue-700');
        tabConv.classList.add('text-slate-500');
        listConn.classList.remove('hidden');
        listConv.classList.add('hidden');
    });
}

async function fetchConversations() {
    const list = document.getElementById('conversations-list');
    if (!list) return;
    
    try {
        const response = await fetch(`${URLROOT}/message/conversations`);
        const data = await response.json();
        
        if (data.success) {
            list.innerHTML = '';
            if (data.conversations.length === 0) {
                list.innerHTML = '<p class="text-center text-slate-500 py-6">No conversations yet.</p>';
                return;
            }
            
            data.conversations.forEach(conv => {
                const picUrl = conv.profile_pic ? (conv.profile_pic.startsWith('http') ? conv.profile_pic : `${URLROOT}/uploads/profiles/` + conv.profile_pic) : '';
                const picHtml = picUrl ? `<img src="${picUrl}" class="w-12 h-12 rounded-full object-cover">` : `<div class="w-12 h-12 rounded-full bg-slate-200 flex items-center justify-center"><span class="material-symbols-outlined text-slate-400">person</span></div>`;
                
                const timeAgo = conv.last_message_time ? formatTimeAgoMsg(new Date(conv.last_message_time)) : 'now';
                
                const div = document.createElement('div');
                div.className = 'px-4 py-3 hover:bg-slate-50 cursor-pointer transition-colors flex gap-3 border-b border-slate-50';
                div.innerHTML = `
${picHtml}
<div class="flex-1 min-w-0">
<div class="flex justify-between items-baseline mb-0.5">
<h3 class="font-title-md text-slate-900 truncate">${escapeHtml(conv.full_name)}</h3>
<span class="text-caption text-slate-500">${timeAgo}</span>
</div>
<p class="text-body-md text-slate-500 truncate">${escapeHtml(conv.last_message || 'Tap to view messages')}</p>
</div>`;
                div.addEventListener('click', () => {
                    document.querySelectorAll('#conversations-list > div').forEach(el => el.classList.remove('bg-slate-100', 'border-l-4', 'border-blue-700'));
                    div.classList.add('bg-slate-100', 'border-l-4', 'border-blue-700');
                    openChat(conv.user_id, conv.full_name, picUrl);
                });
                
                list.appendChild(div);
            });
        }
    } catch(err) {
        console.error(err);
    }
}

async function fetchConnections() {
    const list = document.getElementById('connections-list-msg');
    if (!list) return;

    try {
        const response = await fetch(`${URLROOT}/network/connections`);
        const data = await response.json();

        if (data.success) {
            list.innerHTML = '';
            if (data.connections.length === 0) {
                list.innerHTML = '<p class="text-center text-slate-500 py-6">No connections found.</p>';
                return;
            }

            data.connections.forEach(conn => {
                const picUrl = conn.profile_pic ? (conn.profile_pic.startsWith('http') ? conn.profile_pic : `${URLROOT}/uploads/profiles/` + conn.profile_pic) : '';
                const picHtml = picUrl ? `<img src="${picUrl}" class="w-10 h-10 rounded-full object-cover">` : `<div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center"><span class="material-symbols-outlined text-slate-400">person</span></div>`;
                
                const div = document.createElement('div');
                div.className = 'px-4 py-3 hover:bg-slate-50 cursor-pointer transition-colors flex items-center gap-3 border-b border-slate-50';
                div.innerHTML = `
                ${picHtml}
                <div class="flex-1 min-w-0">
                    <h3 class="font-title-md text-sm font-bold text-slate-900 truncate">${escapeHtml(conn.full_name)}</h3>
                    <p class="text-xs text-slate-500 truncate">${escapeHtml(conn.headline || 'Professional')}</p>
                </div>`;
                
                div.addEventListener('click', () => {
                    document.getElementById('tab-conv').click();
                    openChat(conn.user_id, conn.full_name, picUrl);
                });
                
                list.appendChild(div);
            });
        }
    } catch(err) {
        console.error(err);
    }
}

async function openChat(userId, userName, userPic) {
    currentChatUserId = userId;
    currentChatUserName = userName;
    currentChatUserPic = userPic;
    
    // Update header
    const header = document.getElementById('chat-header');
    if (header) {
        header.classList.remove('hidden');
        header.innerHTML = `
<div class="flex items-center gap-3">
<div class="relative">
${userPic ? `<img src="${userPic}" class="w-10 h-10 rounded-full object-cover">` : `<div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center"><span class="material-symbols-outlined text-slate-400">person</span></div>`}
<div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></div>
</div>
<div>
<h2 class="font-title-md text-slate-900">${escapeHtml(userName)}</h2>
<p class="text-caption text-slate-500">Online</p>
</div>
</div>
<div class="flex items-center gap-4 text-slate-500">
<span class="material-symbols-outlined cursor-pointer hover:text-slate-700">videocam</span>
<span class="material-symbols-outlined cursor-pointer hover:text-slate-700">call</span>
<span class="material-symbols-outlined cursor-pointer hover:text-slate-700">more_horiz</span>
</div>`;
    }
    
    await fetchChatHistory();
}

async function fetchChatHistory(forceScroll = true) {
    const history = document.getElementById('chat-history');
    if (!history) return;
    if (!currentChatUserId) return;
    
    try {
        const response = await fetch(`${URLROOT}/message/history/${currentChatUserId}`);
        const data = await response.json();
        
        if (data.success) {
            const isAtBottom = history.scrollHeight - history.scrollTop <= history.clientHeight + 100;
            
            let html = '';
            if (data.messages.length === 0) {
                html = '<div class="flex justify-center h-full items-center"><p class="text-slate-400">No messages yet. Say hi!</p></div>';
            } else {
                data.messages.forEach(msg => {
                    const isMine = msg.sender_id != currentChatUserId;
                    const picUrl = msg.sender_pic ? (msg.sender_pic.startsWith('http') ? msg.sender_pic : `${URLROOT}/uploads/profiles/` + msg.sender_pic) : '';
                    
                    if (isMine) {
                        html += `
<div class="flex flex-row-reverse gap-3 max-w-[85%] self-end">
<div class="flex flex-col items-end gap-1">
<div class="bg-blue-700 text-white p-3 rounded-xl rounded-br-none shadow-sm">
<p class="text-body-md">${escapeHtml(msg.message_text)}</p>
</div>
<span class="text-caption text-slate-400 mr-1">${new Date(msg.sent_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
</div>
</div>`;
                    } else {
                        html += `
<div class="flex gap-3 max-w-[85%]">
${picUrl ? `<img src="${picUrl}" class="w-8 h-8 rounded-full self-end mb-2">` : `<div class="w-8 h-8 rounded-full self-end mb-2 bg-slate-200"></div>`}
<div class="flex flex-col gap-1">
<div class="bg-white border border-slate-200 p-3 rounded-xl rounded-bl-none shadow-sm">
<p class="text-body-md text-slate-800">${escapeHtml(msg.message_text)}</p>
</div>
<span class="text-caption text-slate-400 ml-1">${new Date(msg.sent_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
</div>
</div>`;
                    }
                });
            }
            
            if (history.innerHTML !== html) {
                history.innerHTML = html;
                if (forceScroll || isAtBottom) {
                    history.scrollTop = history.scrollHeight;
                }
            }
        }
    } catch(err) {
        console.error(err);
    }
}

async function sendMessage() {
    if (!currentChatUserId) return;
    const input = document.getElementById('msg-input');
    const text = input.value.trim();
    if (!text) return;
    const sendBtn = document.getElementById('msg-send-btn');
    
    try {
        if (sendBtn) sendBtn.disabled = true;
        const response = await fetch(`${URLROOT}/message/send`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({receiver_id: currentChatUserId, text: text})
        });
        const data = await response.json();
        
        if (data.success) {
            input.value = '';
            await fetchChatHistory();
            await fetchConversations(); // refresh sidebar to show newest on top
        } else if (typeof showToast === 'function') {
            showToast(data.message || 'Message could not be sent.', 'error');
        }
    } catch(err) {
        console.error(err);
    } finally {
        if (sendBtn) sendBtn.disabled = false;
    }
}

function formatTimeAgoMsg(date) {
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
