<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>

<div class="user-page-shell pt-2 pb-12 font-['Manrope']" id="messaging-page">
    <div class="max-w-[1128px] mx-auto grid grid-cols-1 md:grid-cols-12 gap-6 px-4">

        <!-- Conversations sidebar -->
        <div class="md:col-span-4 bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col overflow-hidden h-[calc(100vh-100px)] sticky top-20">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-white to-slate-50">
                <h2 class="text-lg font-black text-slate-800">Messaging</h2>
                <span id="msg-live-indicator" class="flex items-center gap-1 text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> Live
                </span>
            </div>

            <div class="p-3 border-b border-slate-100 bg-slate-50/50 space-y-3">
                <div class="flex bg-slate-200/70 p-1 rounded-lg">
                    <button type="button" id="tab-conv" class="flex-1 py-1.5 text-sm font-bold bg-white text-slate-800 shadow-sm rounded-md">Chats</button>
                    <button type="button" id="tab-conn" class="flex-1 py-1.5 text-sm font-bold text-slate-500 rounded-md">Connections</button>
                </div>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input id="msg-search" type="search" class="w-full bg-white border border-slate-200 rounded-full py-2 pl-10 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A66C2]/20 focus:border-[#0A66C2]" placeholder="Search chats…" />
                </div>
            </div>

            <div id="conversations-list" class="flex-1 overflow-y-auto">
                <div class="flex justify-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-2 border-slate-200 border-t-[#0A66C2]"></div>
                </div>
            </div>
            <div id="connections-list-msg" class="flex-1 overflow-y-auto hidden"></div>
        </div>

        <!-- Chat pane -->
        <div class="md:col-span-8 bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col overflow-hidden h-[calc(100vh-100px)] sticky top-20 relative">

            <div id="chat-blocked-overlay" class="absolute inset-0 bg-white/90 backdrop-blur-sm z-50 flex items-center justify-center hidden">
                <div class="bg-white border border-slate-200 shadow-xl rounded-xl p-6 text-center max-w-sm mx-4">
                    <span class="material-symbols-outlined text-4xl text-red-500 mb-2">block</span>
                    <h3 id="blocked-overlay-title" class="font-bold text-slate-800 text-lg">Messaging unavailable</h3>
                    <p id="blocked-overlay-text" class="text-slate-500 text-sm mb-4">You cannot send or receive messages in this conversation.</p>
                    <button type="button" id="unblock-user-btn" class="hidden text-sm font-bold text-[#0A66C2] hover:underline">Unblock user</button>
                </div>
            </div>

            <div id="chat-header" class="px-5 py-3 border-b border-slate-100 flex items-center justify-between bg-white z-10 hidden shadow-sm min-h-[72px]"></div>

            <div id="edit-mode-banner" class="hidden px-5 py-2 bg-amber-50 border-b border-amber-100 flex items-center justify-between text-sm">
                <span class="font-semibold text-amber-800 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">edit</span> Editing message
                </span>
                <button type="button" id="cancel-edit-btn" class="font-bold text-amber-700 hover:underline">Cancel</button>
            </div>

            <div id="chat-history" class="flex-1 overflow-y-auto p-5 flex flex-col gap-3 bg-slate-50/60 msg-chat-history">
                <div class="flex flex-col items-center justify-center h-full text-center py-12">
                    <div class="w-20 h-20 bg-blue-50 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-4xl text-[#0A66C2]/60">forum</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Your messages</h3>
                    <p class="text-sm text-slate-500 mt-1 max-w-[260px]">Select a chat or start messaging a connection.</p>
                </div>
            </div>

            <div id="messaging-area" class="p-3 bg-white border-t border-slate-100 z-10 hidden relative">
                <div id="media-preview-container" class="hidden mb-2 p-2 bg-slate-50 rounded-lg border border-slate-200 flex items-center gap-3">
                    <div id="media-preview-thumb" class="w-12 h-12 rounded bg-slate-200 overflow-hidden shrink-0 flex items-center justify-center">
                        <span class="material-symbols-outlined text-slate-400">draft</span>
                    </div>
                    <span id="media-preview-name" class="text-sm font-semibold text-slate-700 truncate flex-1">file</span>
                    <button type="button" id="media-clear-btn" class="text-slate-400 hover:text-red-500 p-1" aria-label="Remove attachment">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div id="emoji-picker" class="msg-picker-panel hidden mb-2 p-3 bg-white border border-slate-200 rounded-xl shadow-lg max-h-40 overflow-y-auto"></div>
                <div id="gif-picker" class="msg-picker-panel hidden mb-2 p-3 bg-white border border-slate-200 rounded-xl shadow-lg">
                    <div class="flex gap-2 mb-2">
                        <input type="search" id="gif-search-input" class="flex-1 text-sm border border-slate-200 rounded-full px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#0A66C2]/20" placeholder="Search GIFs…" />
                    </div>
                    <div id="gif-results" class="grid grid-cols-3 gap-2 max-h-36 overflow-y-auto"></div>
                </div>

                <div class="flex flex-col bg-slate-50 rounded-xl border border-slate-200 focus-within:border-[#0A66C2] focus-within:ring-1 focus-within:ring-[#0A66C2] overflow-hidden">
                    <input type="file" id="msg-media-input" class="hidden" accept="image/*,video/mp4,video/webm,audio/*,.pdf,.doc,.docx" />
                    <textarea id="msg-input" class="w-full bg-transparent border-none focus:outline-none resize-none text-sm p-3 max-h-[120px] placeholder:text-slate-400" placeholder="Write a message…" rows="2"></textarea>
                    <div class="flex items-center justify-between px-2 pb-2">
                        <div class="flex items-center gap-0.5">
                            <button type="button" id="msg-attach-btn" class="msg-toolbar-btn" title="Attach file">
                                <span class="material-symbols-outlined text-[20px]">attach_file</span>
                            </button>
                            <button type="button" id="msg-emoji-btn" class="msg-toolbar-btn" title="Emoji">
                                <span class="material-symbols-outlined text-[20px]">mood</span>
                            </button>
                            <button type="button" id="msg-gif-btn" class="msg-toolbar-btn" title="GIF">
                                <span class="material-symbols-outlined text-[20px]">gif_box</span>
                            </button>
                        </div>
                        <button type="button" id="msg-send-btn" class="bg-gradient-to-r from-[#0A66C2] to-blue-600 text-white font-bold text-sm px-5 py-2 rounded-full shadow-sm hover:shadow-md transition-all flex items-center gap-1">
                            Send <span class="material-symbols-outlined text-[16px]">send</span>
                        </button>
                    </div>
                </div>
                <p class="text-[10px] text-slate-400 text-right mt-1 mr-1">Enter to send · Shift+Enter for new line · Edit within 2 min</p>
            </div>
        </div>
    </div>
</div>

<!-- Call modal -->
<div id="call-modal" class="fixed inset-0 bg-slate-900 z-[10000] hidden flex flex-col text-white">
    <div class="flex-1 relative flex items-center justify-center overflow-hidden">
        <video id="call-remote-video" class="absolute inset-0 w-full h-full object-cover opacity-30" playsinline></video>
        <video id="call-local-video" class="absolute bottom-24 right-6 w-36 h-48 rounded-xl object-cover border-2 border-white/30 shadow-2xl hidden" playsinline muted></video>
        <div class="relative z-10 flex flex-col items-center text-center px-6">
            <img id="call-avatar" src="" alt="" class="w-28 h-28 rounded-full border-4 border-white/20 shadow-2xl object-cover mb-4">
            <h2 id="call-name" class="text-2xl font-black">Calling…</h2>
            <p id="call-status" class="text-slate-300 mt-1">Connecting…</p>
            <p id="call-timer" class="text-sm text-slate-400 mt-2 font-mono hidden">00:00</p>
        </div>
    </div>
    <div class="shrink-0 py-8 flex items-center justify-center gap-5 bg-gradient-to-t from-black/80 to-transparent">
        <button type="button" id="call-mute-btn" class="w-14 h-14 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center" title="Mute">
            <span class="material-symbols-outlined text-2xl" id="call-mute-icon">mic</span>
        </button>
        <button type="button" id="call-end-btn" class="w-16 h-16 rounded-full bg-red-600 hover:bg-red-500 flex items-center justify-center shadow-lg" title="End call">
            <span class="material-symbols-outlined text-3xl">call_end</span>
        </button>
        <button type="button" id="call-video-toggle" class="w-14 h-14 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center" title="Toggle camera">
            <span class="material-symbols-outlined text-2xl" id="call-video-icon">videocam</span>
        </button>
    </div>
</div>

<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
