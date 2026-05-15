/**
 * ProNetwork — Messaging (real-time polling, media, emoji, GIF, calls)
 */
'use strict';

const MSG_POLL_MS = 2500;
const EDIT_WINDOW_MS = 120000;
const TENOR_KEY = 'LIVDSRZULELA';

const EMOJIS = ['😀','😂','🥰','😍','😊','😎','🤔','😢','😡','👍','👏','🙏','💪','🔥','❤️','💙','✨','🎉','💼','📎','✅','❌','⭐','🚀','💡','📌','🎯','☕','🍕','🏆','📱','💻','🌟','🙂','😅','🤝','👋','💬','📣'];

let currentChatUserId = null;
let currentChatUserName = '';
let currentChatUserPic = '';
let attachedMediaFile = null;
let editingMessageId = null;
let lastMessageFingerprint = '';
let lastPollSince = null;
let pollTimer = null;
let convPollTimer = null;
let callState = null;

let allConversations = [];
let allConnections = [];

document.addEventListener('DOMContentLoaded', () => {
    if (!document.getElementById('conversations-list')) return;
    initMessaging();
});

function initMessaging() {
    buildEmojiPicker();
    bindMessagingUi();
    fetchConversations();
    fetchConnections();

    const chatId = new URLSearchParams(window.location.search).get('chat');
    if (chatId) openChatById(chatId);

    convPollTimer = setInterval(fetchConversations, MSG_POLL_MS);

    document.addEventListener('click', e => {
        if (!e.target.closest('.msg-action-dropdown-btn') && !e.target.closest('.msg-action-dropdown')) {
            document.querySelectorAll('.msg-action-dropdown').forEach(d => d.classList.add('hidden'));
        }
        if (!e.target.closest('#emoji-picker') && !e.target.closest('#msg-emoji-btn')) {
            document.getElementById('emoji-picker')?.classList.add('hidden');
        }
        if (!e.target.closest('#gif-picker') && !e.target.closest('#msg-gif-btn')) {
            document.getElementById('gif-picker')?.classList.add('hidden');
        }
    });
}

function bindMessagingUi() {
    document.getElementById('msg-send-btn')?.addEventListener('click', sendMessage);
    document.getElementById('cancel-edit-btn')?.addEventListener('click', cancelEdit);
    document.getElementById('unblock-user-btn')?.addEventListener('click', unblockUser);

    const input = document.getElementById('msg-input');
    input?.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    input?.addEventListener('input', autoResizeMsgInput);

    document.getElementById('msg-attach-btn')?.addEventListener('click', () => document.getElementById('msg-media-input')?.click());
    document.getElementById('msg-media-input')?.addEventListener('change', onMediaSelected);
    document.getElementById('media-clear-btn')?.addEventListener('click', clearMediaAttachment);

    document.getElementById('msg-emoji-btn')?.addEventListener('click', e => {
        e.stopPropagation();
        togglePanel('emoji-picker');
        document.getElementById('gif-picker')?.classList.add('hidden');
    });
    document.getElementById('msg-gif-btn')?.addEventListener('click', e => {
        e.stopPropagation();
        togglePanel('gif-picker');
        document.getElementById('emoji-picker')?.classList.add('hidden');
        loadTrendingGifs();
    });
    document.getElementById('gif-search-input')?.addEventListener('input', debounce(e => searchGifs(e.target.value), 400));

    document.getElementById('tab-conv')?.addEventListener('click', () => switchSidebarTab('conv'));
    document.getElementById('tab-conn')?.addEventListener('click', () => switchSidebarTab('conn'));
    document.getElementById('msg-search')?.addEventListener('input', filterSidebarLists);

    document.getElementById('call-end-btn')?.addEventListener('click', endCall);
    document.getElementById('call-mute-btn')?.addEventListener('click', toggleCallMute);
    document.getElementById('call-video-toggle')?.addEventListener('click', toggleCallVideo);
}

function switchSidebarTab(tab) {
    const convTab = document.getElementById('tab-conv');
    const connTab = document.getElementById('tab-conn');
    const active = 'flex-1 py-1.5 text-sm font-bold bg-white text-slate-800 shadow-sm rounded-md';
    const idle = 'flex-1 py-1.5 text-sm font-bold text-slate-500 rounded-md';
    convTab.className = tab === 'conv' ? active : idle;
    connTab.className = tab === 'conn' ? active : idle;
    document.getElementById('conversations-list').classList.toggle('hidden', tab !== 'conv');
    document.getElementById('connections-list-msg').classList.toggle('hidden', tab !== 'conn');
}

function buildEmojiPicker() {
    const el = document.getElementById('emoji-picker');
    if (!el) return;
    el.innerHTML = EMOJIS.map(e => `<button type="button" class="msg-emoji-btn text-xl hover:bg-slate-100 rounded p-1" data-emoji="${e}">${e}</button>`).join('');
    el.querySelectorAll('[data-emoji]').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById('msg-input');
            if (input) {
                input.value += btn.dataset.emoji;
                input.focus();
                autoResizeMsgInput();
            }
            el.classList.add('hidden');
        });
    });
}

function togglePanel(id) {
    document.getElementById(id)?.classList.toggle('hidden');
}

async function loadTrendingGifs() {
    const box = document.getElementById('gif-results');
    if (!box) return;
    box.innerHTML = '<p class="col-span-3 text-center text-xs text-slate-400 py-4">Loading GIFs…</p>';
    try {
        const res = await fetch(`https://tenor.googleapis.com/v2/featured?key=${TENOR_KEY}&limit=12&media_filter=tinygif`);
        const data = await res.json();
        renderGifResults(data.results || []);
    } catch {
        box.innerHTML = '<p class="col-span-3 text-center text-xs text-slate-500 py-4">GIFs unavailable. Check your connection.</p>';
    }
}

async function searchGifs(q) {
    const box = document.getElementById('gif-results');
    if (!box) return;
    if (!q.trim()) {
        loadTrendingGifs();
        return;
    }
    box.innerHTML = '<p class="col-span-3 text-center text-xs text-slate-400 py-2">Searching…</p>';
    try {
        const res = await fetch(`https://tenor.googleapis.com/v2/search?key=${TENOR_KEY}&q=${encodeURIComponent(q)}&limit=12&media_filter=tinygif`);
        const data = await res.json();
        renderGifResults(data.results || []);
    } catch {
        box.innerHTML = '<p class="col-span-3 text-center text-xs text-red-500 py-2">Search failed</p>';
    }
}

function renderGifResults(results) {
    const box = document.getElementById('gif-results');
    if (!box) return;
    if (!results.length) {
        box.innerHTML = '<p class="col-span-3 text-center text-xs text-slate-400 py-4">No GIFs found</p>';
        return;
    }
    box.innerHTML = results.map(g => {
        const url = g.media_formats?.tinygif?.url || g.media_formats?.gif?.url || '';
        return `<button type="button" class="msg-gif-item rounded-lg overflow-hidden border border-slate-100 hover:ring-2 hover:ring-[#0A66C2]" data-gif-url="${escapeHtml(url)}"><img src="${escapeHtml(url)}" alt="GIF" class="w-full h-16 object-cover" loading="lazy"></button>`;
    }).join('');
    box.querySelectorAll('[data-gif-url]').forEach(btn => {
        btn.addEventListener('click', () => sendGif(btn.dataset.gifUrl));
    });
}

async function sendGif(url) {
    if (!currentChatUserId || !url) return;
    document.getElementById('gif-picker')?.classList.add('hidden');
    const formData = new FormData();
    formData.append('receiver_id', currentChatUserId);
    formData.append('text', ' ');
    formData.append('gif_url', url);
    try {
        const res = await fetch(`${URLROOT}/message/send`, { method: 'POST', body: formData });
        const d = await res.json();
        if (d.success) {
            await refreshChat(true);
            fetchConversations();
        } else msgToast(d.message || 'Failed to send GIF', 'error');
    } catch {
        msgToast('Network error', 'error');
    }
}

function onMediaSelected(e) {
    const file = e.target.files?.[0];
    if (!file) return;
    if (file.size > 15 * 1024 * 1024) {
        msgToast('File must be under 15MB', 'error');
        e.target.value = '';
        return;
    }
    attachedMediaFile = file;
    const container = document.getElementById('media-preview-container');
    const thumb = document.getElementById('media-preview-thumb');
    const nameEl = document.getElementById('media-preview-name');
    container?.classList.remove('hidden');
    if (nameEl) nameEl.textContent = file.name;
    if (thumb) {
        if (file.type.startsWith('image/')) {
            thumb.innerHTML = `<img src="${URL.createObjectURL(file)}" class="w-full h-full object-cover" alt="">`;
        } else {
            thumb.innerHTML = '<span class="material-symbols-outlined text-slate-500">draft</span>';
        }
    }
}

function clearMediaAttachment() {
    attachedMediaFile = null;
    const input = document.getElementById('msg-media-input');
    if (input) input.value = '';
    document.getElementById('media-preview-container')?.classList.add('hidden');
}

function autoResizeMsgInput() {
    const el = document.getElementById('msg-input');
    if (!el) return;
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

async function openChatById(userId) {
    try {
        const res = await fetch(`${URLROOT}/user/data/${userId}`);
        const data = await res.json();
        if (data.success) {
            const u = data.user;
            openChat(u.user_id, u.full_name, pnProfilePicUrl(u));
        }
    } catch (e) {
        console.error(e);
    }
}

async function fetchConversations() {
    try {
        const res = await fetch(`${URLROOT}/message/conversations`);
        const data = await res.json();
        if (data.success) {
            allConversations = data.conversations || [];
            renderConversationsList();
        }
    } catch (e) {
        console.error(e);
    }
}

async function fetchConnections() {
    const list = document.getElementById('connections-list-msg');
    if (!list) return;
    try {
        const res = await fetch(`${URLROOT}/network/connections`);
        const data = await res.json();
        if (data.success) {
            allConnections = data.connections || [];
            renderConnectionsList();
        }
    } catch (e) {
        console.error(e);
    }
}

function filterSidebarLists() {
    const q = (document.getElementById('msg-search')?.value || '').toLowerCase().trim();
    renderConversationsList(q);
    renderConnectionsList(q);
}

function renderConversationsList(filter = '') {
    const list = document.getElementById('conversations-list');
    if (!list) return;
    let items = allConversations;
    if (filter) {
        items = items.filter(c =>
            (c.full_name || '').toLowerCase().includes(filter) ||
            (c.last_message || '').toLowerCase().includes(filter)
        );
    }
    if (!items.length) {
        list.innerHTML = `<div class="msg-empty-sidebar"><span class="material-symbols-outlined text-4xl text-slate-300 mb-2">forum</span><p class="text-sm text-slate-500">${filter ? 'No matching chats' : 'No conversations yet'}</p></div>`;
        return;
    }
    list.innerHTML = items.map(conv => conversationRowHtml(conv)).join('');
    list.querySelectorAll('[data-open-chat]').forEach(el => {
        el.addEventListener('click', () => openChat(
            el.dataset.openChat,
            el.dataset.userName,
            el.dataset.userPic
        ));
    });
}

function renderConnectionsList(filter = '') {
    const list = document.getElementById('connections-list-msg');
    if (!list) return;
    let items = allConnections;
    if (filter) {
        items = items.filter(c => (c.full_name || '').toLowerCase().includes(filter));
    }
    if (!items.length) {
        list.innerHTML = `<div class="msg-empty-sidebar"><span class="material-symbols-outlined text-4xl text-slate-300 mb-2">people</span><p class="text-sm text-slate-500">${filter ? 'No matches' : 'No connections'}</p></div>`;
        return;
    }
    list.innerHTML = items.map(conn => {
        const pic = pnProfilePicUrl(conn);
        return `
            <div class="msg-conv-row group" data-open-chat="${conn.user_id}" data-user-name="${escapeHtml(conn.full_name)}" data-user-pic="${pic}">
                <img src="${pic}" alt="" class="w-10 h-10 rounded-full object-cover border border-slate-100 shrink-0">
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-sm text-slate-900 truncate">${escapeHtml(conn.full_name)}</h3>
                    <p class="text-xs text-slate-500 truncate">${escapeHtml(conn.headline || 'Connection')}</p>
                </div>
                <span class="material-symbols-outlined text-slate-300 group-hover:text-[#0A66C2] text-[20px]">chat</span>
            </div>`;
    }).join('');
    list.querySelectorAll('[data-open-chat]').forEach(el => {
        el.addEventListener('click', () => {
            switchSidebarTab('conv');
            openChat(el.dataset.openChat, el.dataset.userName, el.dataset.userPic);
        });
    });
}

function conversationRowHtml(conv) {
    const pic = pnProfilePicUrl(conv);
    const active = Number(currentChatUserId) === Number(conv.user_id);
    const timeAgo = conv.last_message_time ? formatTimeAgoMsg(new Date(conv.last_message_time)) : '';
    return `
        <div class="msg-conv-row group ${active ? 'is-active' : ''}" data-open-chat="${conv.user_id}" data-user-name="${escapeHtml(conv.full_name)}" data-user-pic="${pic}">
            <img src="${pic}" alt="" class="w-11 h-11 rounded-full object-cover border border-slate-100 shrink-0">
            <div class="flex-1 min-w-0">
                <div class="flex justify-between gap-2">
                    <h3 class="font-bold text-sm text-slate-900 truncate">${escapeHtml(conv.full_name)}</h3>
                    <span class="text-[10px] text-slate-400 shrink-0">${timeAgo}</span>
                </div>
                <p class="text-xs text-slate-500 truncate">${escapeHtml(conv.last_message || 'Start chatting')}</p>
            </div>
        </div>`;
}

async function openChat(userId, userName, userPic) {
    currentChatUserId = Number(userId);
    currentChatUserName = userName;
    currentChatUserPic = userPic;
    lastMessageFingerprint = '';
    lastPollSince = null;
    cancelEdit();
    clearMediaAttachment();

    const url = new URL(window.location.href);
    url.searchParams.set('chat', userId);
    window.history.replaceState({}, '', url);

    document.getElementById('messaging-area')?.classList.remove('hidden');
    renderChatHeader();
    await refreshChat(true);
    applyBlockUi(await fetchBlockStatus());

    if (pollTimer) clearInterval(pollTimer);
    pollTimer = setInterval(() => pollNewMessages(), MSG_POLL_MS);

    document.getElementById('msg-input')?.focus();
    fetchConversations();
}

function renderChatHeader() {
    const header = document.getElementById('chat-header');
    if (!header) return;
    header.classList.remove('hidden');
    header.innerHTML = `
        <div class="flex items-center gap-3 min-w-0 cursor-pointer" data-profile-link="${currentChatUserId}">
            <img src="${currentChatUserPic}" alt="" class="w-11 h-11 rounded-full object-cover border border-slate-200">
            <div class="min-w-0">
                <h2 class="font-bold text-slate-900 truncate">${escapeHtml(currentChatUserName)}</h2>
                <p class="text-xs text-green-600 font-medium">Active now</p>
            </div>
        </div>
        <div class="flex items-center gap-1 shrink-0">
            <button type="button" class="msg-header-btn" data-call="video" title="Video call"><span class="material-symbols-outlined">videocam</span></button>
            <button type="button" class="msg-header-btn" data-call="voice" title="Voice call"><span class="material-symbols-outlined">call</span></button>
            <div class="relative">
                <button type="button" class="msg-header-btn msg-action-dropdown-btn" data-menu="header-menu"><span class="material-symbols-outlined">more_vert</span></button>
                <div id="header-menu" class="msg-action-dropdown hidden absolute right-0 top-full mt-1 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 py-1 text-sm">
                    <button type="button" class="msg-menu-item" data-action="view-profile"><span class="material-symbols-outlined text-[18px]">person</span> View profile</button>
                    <button type="button" class="msg-menu-item" data-action="clear-chat"><span class="material-symbols-outlined text-[18px]">delete_sweep</span> Delete conversation</button>
                    <button type="button" class="msg-menu-item" data-action="block"><span class="material-symbols-outlined text-[18px]">block</span> Block user</button>
                    <button type="button" class="msg-menu-item hidden" id="header-unblock-btn" data-action="unblock"><span class="material-symbols-outlined text-[18px]">lock_open</span> Unblock user</button>
                </div>
            </div>
        </div>`;

    header.querySelector('[data-profile-link]')?.addEventListener('click', () => {
        window.location.href = `${URLROOT}/user/profile?id=${currentChatUserId}`;
    });
    header.querySelectorAll('[data-call]').forEach(btn => {
        btn.addEventListener('click', () => startCall(btn.dataset.call));
    });
    header.querySelector('[data-menu]')?.addEventListener('click', e => {
        e.stopPropagation();
        toggleDropdown('header-menu');
    });
    header.querySelector('[data-action="view-profile"]')?.addEventListener('click', () => {
        window.location.href = `${URLROOT}/user/profile?id=${currentChatUserId}`;
    });
    header.querySelector('[data-action="clear-chat"]')?.addEventListener('click', clearConversation);
    header.querySelector('[data-action="block"]')?.addEventListener('click', blockUser);
    header.querySelector('[data-action="unblock"]')?.addEventListener('click', unblockUser);
}

async function fetchBlockStatus() {
    try {
        const res = await fetch(`${URLROOT}/message/status/${currentChatUserId}`);
        const data = await res.json();
        return data.success ? data.block_status : { blocked: false };
    } catch {
        return { blocked: false };
    }
}

function applyBlockUi(status) {
    const overlay = document.getElementById('chat-blocked-overlay');
    const inputArea = document.getElementById('messaging-area');
    const title = document.getElementById('blocked-overlay-title');
    const text = document.getElementById('blocked-overlay-text');
    const unblockBtn = document.getElementById('unblock-user-btn');
    const headerUnblock = document.getElementById('header-unblock-btn');

    if (!status?.blocked) {
        overlay?.classList.add('hidden');
        inputArea?.classList.remove('hidden');
        headerUnblock?.classList.add('hidden');
        return;
    }

    overlay?.classList.remove('hidden');
    inputArea?.classList.add('hidden');
    if (status.blocked_by_me) {
        title.textContent = 'You blocked this user';
        text.textContent = 'Unblock to send messages again.';
        unblockBtn?.classList.remove('hidden');
        headerUnblock?.classList.remove('hidden');
    } else {
        title.textContent = 'You cannot reply';
        text.textContent = 'This user has restricted messaging with you.';
        unblockBtn?.classList.add('hidden');
        headerUnblock?.classList.add('hidden');
    }
}

async function refreshChat(forceScroll = true) {
    if (!currentChatUserId) return;
    try {
        const res = await fetch(`${URLROOT}/message/history/${currentChatUserId}`);
        const data = await res.json();
        if (!data.success) return;

        if (data.block_status) applyBlockUi(data.block_status);

        const messages = data.messages || [];
        if (messages.length) {
            lastPollSince = messages[messages.length - 1].sent_at;
        }
        renderMessages(messages, forceScroll);
    } catch (e) {
        console.error(e);
    }
}

async function pollNewMessages() {
    if (!currentChatUserId) return;
    try {
        const url = lastPollSince
            ? `${URLROOT}/message/poll/${currentChatUserId}?since=${encodeURIComponent(lastPollSince)}`
            : `${URLROOT}/message/poll/${currentChatUserId}`;
        const res = await fetch(url);
        const data = await res.json();
        if (!data.success) return;

        if (data.block_status?.blocked) {
            applyBlockUi(data.block_status);
            return;
        }

        const incoming = data.messages || [];
        if (incoming.length) {
            lastPollSince = incoming[incoming.length - 1].sent_at;
            const history = document.getElementById('chat-history');
            const existingIds = new Set([...history.querySelectorAll('[data-message-id]')].map(el => el.dataset.messageId));
            const newOnes = incoming.filter(m => !existingIds.has(String(m.message_id)));

            if (newOnes.length && lastMessageFingerprint) {
                appendMessages(newOnes);
                scrollChatToBottom();
            } else if (!lastMessageFingerprint) {
                renderMessages(incoming, false);
            } else {
                renderMessages(await mergeFullHistory(), false);
            }
        }
        fetchConversations();
    } catch (e) {
        console.error(e);
    }
}

async function mergeFullHistory() {
    const res = await fetch(`${URLROOT}/message/history/${currentChatUserId}`);
    const data = await res.json();
    return data.messages || [];
}

function messageFingerprint(messages) {
    return messages.map(m => `${m.message_id}:${m.is_deleted}:${m.is_edited}:${m.message_text}:${m.sent_at}`).join('|');
}

function renderMessages(messages, forceScroll) {
    const history = document.getElementById('chat-history');
    if (!history) return;

    const fp = messageFingerprint(messages);
    if (fp === lastMessageFingerprint && !forceScroll) return;
    lastMessageFingerprint = fp;

    if (!messages.length) {
        history.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full text-center py-8">
                <img src="${currentChatUserPic}" alt="" class="w-20 h-20 rounded-full border-4 border-white shadow-md mb-3 object-cover">
                <h3 class="font-bold text-slate-800">Say hello to ${escapeHtml(currentChatUserName)}</h3>
                <p class="text-sm text-slate-500 mt-1">Send a message, emoji, GIF, or file to start.</p>
            </div>`;
        return;
    }

    history.innerHTML = messages.map(m => messageBubbleHtml(m)).join('');
    bindMessageActions(history);
    if (forceScroll) scrollChatToBottom();
}

function appendMessages(messages) {
    const history = document.getElementById('chat-history');
    if (!history) return;
    const empty = history.querySelector('.flex.flex-col.items-center.justify-center.h-full');
    if (empty) empty.remove();
    messages.forEach(m => {
        history.insertAdjacentHTML('beforeend', messageBubbleHtml(m));
    });
    bindMessageActions(history);
    lastMessageFingerprint = ''; // force refresh on next full load
}

function bindMessageActions(root) {
    root.querySelectorAll('.msg-action-dropdown-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            toggleDropdown(btn.dataset.menu);
        });
    });
    root.querySelectorAll('[data-edit-msg]').forEach(btn => {
        btn.addEventListener('click', () => initEdit(btn.dataset.editMsg, btn.dataset.editText));
    });
    root.querySelectorAll('[data-unsend-msg]').forEach(btn => {
        btn.addEventListener('click', () => unsendMessage(btn.dataset.unsendMsg));
    });
    root.querySelectorAll('[data-profile-link]').forEach(el => {
        el.addEventListener('click', () => {
            window.location.href = `${URLROOT}/user/profile?id=${el.dataset.profileLink}`;
        });
    });
}

function messageBubbleHtml(msg) {
    const isMine = Number(msg.sender_id) === Number(CURRENT_USER_ID);
    const isDeleted = Number(msg.is_deleted) === 1;
    const timeStr = new Date(msg.sent_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const canEdit = isMine && !isDeleted && (Date.now() - new Date(msg.sent_at).getTime()) < EDIT_WINDOW_MS && !msg.media_path;
    const pic = msg.sender_pic ? pnProfilePicUrl({ profile_pic: msg.sender_pic, full_name: msg.sender_name }) : pnUiAvatarUrl(msg.sender_name);
    const mediaHtml = renderMessageMedia(msg, isDeleted);
    const editedLabel = Number(msg.is_edited) === 1 ? '<span class="italic opacity-80">Edited</span>' : '';

    if (isMine) {
        const menu = isDeleted ? '' : `
            <div class="relative shrink-0 self-center opacity-0 group-hover:opacity-100 transition-opacity">
                <button type="button" class="msg-action-dropdown-btn w-7 h-7 rounded-full hover:bg-slate-200 text-slate-400 flex items-center justify-center" data-menu="msg-menu-${msg.message_id}">
                    <span class="material-symbols-outlined text-[16px]">more_vert</span>
                </button>
                <div id="msg-menu-${msg.message_id}" class="msg-action-dropdown hidden absolute right-0 bottom-full mb-1 w-36 bg-white border border-slate-200 rounded-lg shadow-lg z-50 py-1 text-xs">
                    ${canEdit ? `<button type="button" class="msg-menu-item w-full" data-edit-msg="${msg.message_id}" data-edit-text="${escapeAttr(msg.message_text)}"><span class="material-symbols-outlined text-[14px]">edit</span> Edit</button>` : ''}
                    <button type="button" class="msg-menu-item w-full text-red-600" data-unsend-msg="${msg.message_id}"><span class="material-symbols-outlined text-[14px]">delete</span> Unsend</button>
                </div>
            </div>`;

        return `
            <div class="msg-row msg-row--mine group" data-message-id="${msg.message_id}">
                ${menu}
                <div class="msg-bubble msg-bubble--mine ${isDeleted ? 'msg-bubble--deleted' : ''}">
                    ${mediaHtml}
                    <p class="text-sm whitespace-pre-wrap break-words">${escapeHtml(msg.message_text.trim() || (isDeleted ? '' : ' '))}</p>
                </div>
                <div class="msg-meta msg-meta--mine">
                    ${editedLabel}
                    <span>${timeStr}</span>
                    <span class="material-symbols-outlined text-[14px]">done_all</span>
                </div>
            </div>`;
    }

    return `
        <div class="msg-row msg-row--theirs group" data-message-id="${msg.message_id}">
            <img src="${pic}" alt="" class="w-8 h-8 rounded-full object-cover self-end mb-5 cursor-pointer shrink-0" data-profile-link="${msg.sender_id}">
            <div>
                <div class="msg-bubble msg-bubble--theirs ${isDeleted ? 'msg-bubble--deleted' : ''}">
                    ${mediaHtml}
                    <p class="text-sm whitespace-pre-wrap break-words">${escapeHtml(msg.message_text.trim() || '')}</p>
                </div>
                <div class="msg-meta">${timeStr} ${editedLabel}</div>
            </div>
        </div>`;
}

function renderMessageMedia(msg, isDeleted) {
    if (!msg.media_path || isDeleted) return '';
    const path = msg.media_path;

    if (path.startsWith('gif:')) {
        const url = path.slice(4);
        return `<img src="${escapeHtml(url)}" alt="GIF" class="msg-media-gif rounded-lg mb-1 max-w-[240px]" loading="lazy">`;
    }

    const url = `${URLROOT}/uploads/messages/${encodeURIComponent(path)}`;
    if (/\.(jpe?g|png|gif|webp)$/i.test(path)) {
        return `<a href="${url}" target="_blank" rel="noopener"><img src="${url}" alt="Image" class="msg-media-img rounded-lg mb-1 max-h-48 object-contain" loading="lazy"></a>`;
    }
    if (/\.(mp4|webm)$/i.test(path)) {
        return `<video src="${url}" controls class="msg-media-video rounded-lg mb-1 max-w-full max-h-48"></video>`;
    }
    if (/\.(mp3|wav|ogg)$/i.test(path)) {
        return `<audio src="${url}" controls class="msg-media-audio w-full max-w-xs mb-1"></audio>`;
    }
    const name = path.split('/').pop();
    return `<a href="${url}" target="_blank" rel="noopener" class="msg-file-link"><span class="material-symbols-outlined">draft</span><span>${escapeHtml(name)}</span></a>`;
}

async function sendMessage() {
    if (!currentChatUserId) return;
    const input = document.getElementById('msg-input');
    const text = (input?.value || '').trim();
    if (!text && !attachedMediaFile && !editingMessageId) return;

    const sendBtn = document.getElementById('msg-send-btn');
    if (sendBtn) {
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span>';
    }

    try {
        if (editingMessageId) {
            const res = await fetch(`${URLROOT}/message/edit`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message_id: editingMessageId, new_text: text })
            });
            const d = await res.json();
            if (d.success) {
                msgToast('Message updated', 'success');
                cancelEdit();
            } else {
                msgToast(d.message || 'Messages can only be edited within 2 minutes of sending.', 'error');
            }
        } else {
            const formData = new FormData();
            formData.append('receiver_id', currentChatUserId);
            formData.append('text', text || ' ');
            if (attachedMediaFile) formData.append('media', attachedMediaFile);

            const res = await fetch(`${URLROOT}/message/send`, { method: 'POST', body: formData });
            const d = await res.json();
            if (!d.success) {
                msgToast(d.message || 'Failed to send', 'error');
            } else {
                if (input) input.value = '';
                clearMediaAttachment();
                autoResizeMsgInput();
            }
        }
        lastMessageFingerprint = '';
        await refreshChat(true);
        fetchConversations();
    } catch (e) {
        console.error(e);
        msgToast('Network error', 'error');
    } finally {
        if (sendBtn) {
            sendBtn.disabled = false;
            sendBtn.innerHTML = 'Send <span class="material-symbols-outlined text-[16px]">send</span>';
        }
    }
}

function initEdit(id, text) {
    editingMessageId = Number(id);
    const input = document.getElementById('msg-input');
    if (input) {
        input.value = text;
        input.focus();
        autoResizeMsgInput();
    }
    document.getElementById('edit-mode-banner')?.classList.remove('hidden');
    document.querySelectorAll('.msg-action-dropdown').forEach(d => d.classList.add('hidden'));
    msgToast('Edit your message (within 2 minutes)', 'info');
}

function cancelEdit() {
    editingMessageId = null;
    document.getElementById('edit-mode-banner')?.classList.add('hidden');
}

async function unsendMessage(id) {
    const ok = await confirm('Unsend this message for everyone?');
    if (!ok) return;

    try {
        const res = await fetch(`${URLROOT}/message/unsend`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message_id: id })
        });
        const d = await res.json();
        if (d.success) {
            lastMessageFingerprint = '';
            await refreshChat(false);
            msgToast('Message unsent', 'success');
        } else msgToast(d.message, 'error');
    } catch {
        msgToast('Error unsending message', 'error');
    }
}

async function clearConversation() {
    const ok = await confirm('Permanently delete this entire conversation? This cannot be undone.');
    if (!ok) return;

    try {
        const res = await fetch(`${URLROOT}/message/clear_chat`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: currentChatUserId })
        });
        const d = await res.json();
        if (d.success) {
            msgToast('Conversation deleted', 'success');
            lastMessageFingerprint = '';
            lastPollSince = null;
            await refreshChat(true);
            fetchConversations();
        } else msgToast(d.message, 'error');
    } catch {
        msgToast('Error', 'error');
    }
}

async function blockUser() {
    if (!confirm(`Block ${currentChatUserName}? They will not be able to message you.`)) return;
    try {
        const res = await fetch(`${URLROOT}/message/block`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: currentChatUserId })
        });
        const d = await res.json();
        if (d.success) {
            msgToast('User blocked', 'success');
            applyBlockUi({ blocked: true, blocked_by_me: true });
            fetchConversations();
        } else msgToast(d.message, 'error');
    } catch {
        msgToast('Error', 'error');
    }
}

async function unblockUser() {
    try {
        const res = await fetch(`${URLROOT}/message/unblock`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: currentChatUserId })
        });
        const d = await res.json();
        if (d.success) {
            msgToast('User unblocked', 'success');
            applyBlockUi({ blocked: false });
            await refreshChat(true);
            fetchConversations();
        } else msgToast(d.message, 'error');
    } catch {
        msgToast('Error', 'error');
    }
}

/* ——— Calls (local media preview + simulated connect) ——— */
async function startCall(type) {
    endCall();
    callState = { type, muted: false, videoOff: type === 'voice', timer: null, seconds: 0, stream: null };

    const modal = document.getElementById('call-modal');
    const localVideo = document.getElementById('call-local-video');
    document.getElementById('call-avatar').src = currentChatUserPic;
    document.getElementById('call-name').textContent = currentChatUserName;
    document.getElementById('call-status').textContent = type === 'video' ? 'Starting video call…' : 'Starting voice call…';
    document.getElementById('call-timer').classList.add('hidden');

    modal.classList.remove('hidden');

    try {
        const constraints = type === 'video'
            ? { video: true, audio: true }
            : { video: false, audio: true };
        callState.stream = await navigator.mediaDevices.getUserMedia(constraints);
        if (type === 'video' && localVideo) {
            localVideo.srcObject = callState.stream;
            localVideo.classList.remove('hidden');
            localVideo.play().catch(() => {});
        }
    } catch {
        document.getElementById('call-status').textContent = 'Microphone/camera permission denied';
        return;
    }

    setTimeout(() => { document.getElementById('call-status').textContent = 'Ringing…'; }, 1200);
    setTimeout(() => {
        document.getElementById('call-status').textContent = 'Connected';
        const timerEl = document.getElementById('call-timer');
        timerEl.classList.remove('hidden');
        callState.timer = setInterval(() => {
            callState.seconds++;
            const m = String(Math.floor(callState.seconds / 60)).padStart(2, '0');
            const s = String(callState.seconds % 60).padStart(2, '0');
            timerEl.textContent = `${m}:${s}`;
        }, 1000);
        msgToast(`${type === 'video' ? 'Video' : 'Voice'} call connected (demo)`, 'success');
    }, 3500);
}

function endCall() {
    if (callState?.timer) clearInterval(callState.timer);
    if (callState?.stream) {
        callState.stream.getTracks().forEach(t => t.stop());
    }
    callState = null;
    const modal = document.getElementById('call-modal');
    const localVideo = document.getElementById('call-local-video');
    if (localVideo) {
        localVideo.srcObject = null;
        localVideo.classList.add('hidden');
    }
    modal?.classList.add('hidden');
}

function toggleCallMute() {
    if (!callState?.stream) return;
    callState.muted = !callState.muted;
    callState.stream.getAudioTracks().forEach(t => { t.enabled = !callState.muted; });
    const icon = document.getElementById('call-mute-icon');
    if (icon) icon.textContent = callState.muted ? 'mic_off' : 'mic';
}

function toggleCallVideo() {
    if (!callState?.stream) return;
    callState.videoOff = !callState.videoOff;
    callState.stream.getVideoTracks().forEach(t => { t.enabled = !callState.videoOff; });
    const icon = document.getElementById('call-video-icon');
    const localVideo = document.getElementById('call-local-video');
    if (icon) icon.textContent = callState.videoOff ? 'videocam_off' : 'videocam';
    if (localVideo) localVideo.classList.toggle('hidden', callState.videoOff);
}

function toggleDropdown(id) {
    document.querySelectorAll('.msg-action-dropdown').forEach(d => {
        if (d.id !== id) d.classList.add('hidden');
    });
    document.getElementById(id)?.classList.toggle('hidden');
}

function scrollChatToBottom() {
    const history = document.getElementById('chat-history');
    if (history) history.scrollTop = history.scrollHeight;
}

function formatTimeAgoMsg(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    if (seconds < 60) return 'now';
    const m = Math.floor(seconds / 60);
    if (m < 60) return `${m}m`;
    const h = Math.floor(m / 60);
    if (h < 24) return `${h}h`;
    return `${Math.floor(h / 24)}d`;
}

function escapeHtml(s) {
    if (!s) return '';
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function escapeAttr(s) {
    return escapeHtml(s).replace(/\n/g, '&#10;');
}

function debounce(fn, ms) {
    let t;
    return (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn(...args), ms);
    };
}

function msgToast(msg, type = 'info') {
    if (typeof jobsToast === 'function') {
        jobsToast(msg, type);
        return;
    }
    const t = document.createElement('div');
    t.className = `fixed bottom-6 left-1/2 -translate-x-1/2 z-[10001] px-4 py-2 rounded-full text-white text-sm font-bold ${type === 'error' ? 'bg-red-600' : type === 'success' ? 'bg-green-600' : 'bg-slate-800'}`;
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

window.openChat = openChat;
window.unsendMessage = unsendMessage;
