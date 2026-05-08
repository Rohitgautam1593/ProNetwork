<?php require APPROOT . '/views/layouts/header.php'; ?>
<?php require APPROOT . '/views/layouts/navbar.php'; ?>
<!-- View Content -->
<main class="pt-16">

<div class="w-full max-w-[1128px] grid grid-cols-1 md:grid-cols-12 gap-gutter min-h-[calc(100vh-120px)]">
<!-- Left Pane: Conversation List (4 columns) -->
<div class="md:col-span-4 bg-white rounded-lg border border-slate-200 shadow-sm flex flex-col overflow-hidden">
<div class="p-4 border-b border-slate-100 flex items-center justify-between">
<h2 class="font-title-md text-slate-900">Messaging</h2>
<span class="material-symbols-outlined text-slate-500 cursor-pointer hover:bg-slate-100 p-1 rounded-full">more_horiz</span>
</div>
<div class="p-3">
    <div class="flex border-b border-slate-100 mb-3">
        <button id="tab-conv" class="flex-1 py-2 text-sm font-bold text-blue-700 border-b-2 border-blue-700 transition-all">Chats</button>
        <button id="tab-conn" class="flex-1 py-2 text-sm font-bold text-slate-500 hover:text-slate-700 transition-all">Connections</button>
    </div>
    <div class="relative">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
        <input id="msg-search" class="w-full bg-slate-50 border border-slate-200 rounded py-2 pl-10 pr-4 text-body-md focus:ring-1 focus:ring-primary focus:bg-white" placeholder="Search" type="text"/>
    </div>
</div>
<div id="conversations-list" class="flex-1 overflow-y-auto custom-scrollbar">
    <!-- Dynamic conversations will be loaded here -->
</div>
<div id="connections-list-msg" class="flex-1 overflow-y-auto custom-scrollbar hidden">
    <!-- Dynamic connections will be loaded here -->
</div>
</div>
<!-- Right Pane: Active Chat (8 columns) -->
<div class="md:col-span-8 bg-white rounded-lg border border-slate-200 shadow-sm flex flex-col overflow-hidden h-full max-h-[calc(100vh-120px)]">
<!-- Chat Header -->
<div id="chat-header" class="px-6 py-3 border-b border-slate-100 flex items-center justify-between hidden">
    <!-- Dynamic header will be loaded here -->
</div>
<!-- Chat History Area -->
<div id="chat-history" class="flex-1 overflow-y-auto p-6 flex flex-col gap-6 custom-scrollbar bg-slate-50/50">
    <div class="flex justify-center h-full items-center"><p class="text-slate-400">Select a conversation to start messaging</p></div>
</div>
<!-- Chat Input Area -->
<div id="messaging-area" class="p-4 bg-white border-t border-slate-100">
<div class="flex flex-col gap-3 bg-slate-50 rounded-lg p-3 border border-slate-200 focus-within:border-primary focus-within:ring-1 focus-within:ring-primary transition-all">
<textarea id="msg-input" class="w-full bg-transparent border-none focus:ring-0 resize-none text-body-md placeholder:text-slate-400" placeholder="Write a message..." rows="2"></textarea>
<div class="flex items-center justify-between pt-2 border-t border-slate-200/60">
<div class="flex items-center gap-3 text-slate-500">
<button type="button" class="hover:text-blue-700 transition-colors">
<span class="material-symbols-outlined">image</span>
</button>
<button type="button" class="hover:text-blue-700 transition-colors">
<span class="material-symbols-outlined">attachment</span>
</button>
<button type="button" class="hover:text-blue-700 transition-colors">
<span class="material-symbols-outlined">mood</span>
</button>
<button type="button" class="hover:text-blue-700 transition-colors">
<span class="material-symbols-outlined">gif_box</span>
</button>
</div>
<button id="msg-send-btn" type="button" class="bg-blue-700 hover:bg-blue-800 text-white font-title-md px-6 py-1.5 rounded-full transition-all active:scale-95 disabled:opacity-50">
                                Send
                            </button>
</div>
</div>
</div>
</div>
</div>

</main>
<?php require APPROOT . '/views/layouts/footer.php'; ?>
