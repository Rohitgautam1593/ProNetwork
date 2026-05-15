<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>

<div class="user-page-shell pt-2 pb-12 font-['Manrope']" id="notifications-page">
    <div class="max-w-[1128px] mx-auto grid grid-cols-1 md:grid-cols-12 gap-6 px-4">

        <aside class="md:col-span-3 flex flex-col gap-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden sticky top-20">
                <div class="p-4 border-b border-slate-100 bg-gradient-to-br from-white to-slate-50">
                    <h2 class="text-lg font-black text-slate-800">Notifications</h2>
                    <p class="text-xs text-slate-500 mt-1">Manage alerts and preferences</p>
                </div>
                <nav class="flex flex-col p-2 gap-1" id="notif-pref-nav">
                    <button type="button" data-pref-panel="feed" class="notif-pref-btn is-active w-full flex items-center gap-3 px-4 py-3 rounded-lg text-left">
                        <span class="material-symbols-outlined text-[20px]">notifications</span>
                        <span class="text-sm font-bold">All notifications</span>
                    </button>
                    <button type="button" data-pref-panel="settings" class="notif-pref-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 font-semibold hover:bg-slate-50 text-left">
                        <span class="material-symbols-outlined text-[20px]">tune</span>
                        <span class="text-sm">Alert settings</span>
                    </button>
                    <button type="button" data-pref-panel="email" class="notif-pref-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 font-semibold hover:bg-slate-50 text-left">
                        <span class="material-symbols-outlined text-[20px]">mail</span>
                        <span class="text-sm">Email</span>
                    </button>
                    <button type="button" data-pref-panel="push" class="notif-pref-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 font-semibold hover:bg-slate-50 text-left">
                        <span class="material-symbols-outlined text-[20px]">smartphone</span>
                        <span class="text-sm">Push</span>
                    </button>
                </nav>
                <div class="p-3 border-t border-slate-100 space-y-2 bg-slate-50/50">
                    <button type="button" id="mark-all-read" class="w-full bg-white border border-slate-200 text-slate-700 font-bold text-sm py-2.5 rounded-full hover:border-[#0A66C2] hover:text-[#0A66C2] transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">done_all</span> Mark all read
                    </button>
                    <button type="button" id="clear-all-notifs" class="w-full text-red-600 font-bold text-sm py-2 rounded-full hover:bg-red-50 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">delete_sweep</span> Clear all
                    </button>
                </div>
            </div>
        </aside>

        <section class="md:col-span-6 flex flex-col gap-4" id="notif-main-feed">
            <div class="bg-white p-2 rounded-xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-2 sticky top-20 z-10">
                <div class="flex items-center gap-1 flex-wrap">
                    <button type="button" data-filter="all" class="notif-filter-btn is-active">All</button>
                    <button type="button" data-filter="posts" class="notif-filter-btn">My posts</button>
                    <button type="button" data-filter="network" class="notif-filter-btn">Network</button>
                    <button type="button" data-filter="jobs" class="notif-filter-btn">Jobs</button>
                </div>
                <div class="flex items-center gap-2 pr-1">
                    <span id="notif-unread-badge" class="hidden text-xs font-bold bg-[#0A66C2] text-white px-2.5 py-0.5 rounded-full">0 new</span>
                    <span id="notif-live-dot" class="flex items-center gap-1 text-[10px] font-bold text-green-600">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> Live
                    </span>
                </div>
            </div>

            <div id="notifications-list" class="flex flex-col gap-2 min-h-[200px]">
                <div class="flex justify-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-2 border-slate-200 border-t-[#0A66C2]"></div>
                </div>
            </div>

            <button type="button" id="load-more-notifs" class="hidden w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3 rounded-xl text-sm transition-colors">
                Load more
            </button>
        </section>

        <!-- Preferences panels (hidden by default) -->
        <section class="md:col-span-6 hidden flex-col gap-4" id="notif-pref-panels">
            <div id="notif-panel-settings" class="hidden bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="font-bold text-slate-900 text-lg">Alert settings</h3>
                <p class="text-sm text-slate-500">Choose what you want to be notified about on ProNetwork.</p>
                <div id="notif-settings-toggles" class="space-y-3"></div>
                <button type="button" id="save-notif-settings" class="bg-[#0A66C2] text-white font-bold px-6 py-2.5 rounded-full hover:bg-[#004182]">Save preferences</button>
            </div>
            <div id="notif-panel-email" class="hidden bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="font-bold text-slate-900 text-lg">Email notifications</h3>
                <p class="text-sm text-slate-500">Control which updates are sent to your inbox.</p>
                <div id="notif-email-toggles" class="space-y-3"></div>
                <button type="button" id="save-notif-email" class="bg-[#0A66C2] text-white font-bold px-6 py-2.5 rounded-full hover:bg-[#004182]">Save email prefs</button>
            </div>
            <div id="notif-panel-push" class="hidden bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="font-bold text-slate-900 text-lg">Push notifications</h3>
                <p class="text-sm text-slate-500">Browser push for instant alerts (when supported).</p>
                <button type="button" id="enable-push-btn" class="border-2 border-[#0A66C2] text-[#0A66C2] font-bold px-6 py-2.5 rounded-full hover:bg-blue-50 flex items-center gap-2">
                    <span class="material-symbols-outlined">notifications_active</span> Enable push
                </button>
                <p id="push-status-text" class="text-xs text-slate-500"></p>
            </div>
        </section>

        <aside class="md:col-span-3 hidden md:flex flex-col gap-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sticky top-20">
                <h3 class="text-sm font-black text-slate-800 mb-4">Grow your network</h3>
                <div id="notif-suggestions" class="flex flex-col gap-4">
                    <p class="text-sm text-slate-400">Loading…</p>
                </div>
                <a href="<?php echo URLROOT; ?>/user/network" class="mt-4 block text-center text-sm font-bold text-[#0A66C2] hover:underline">See all on My Network</a>
            </div>
        </aside>
    </div>
</div>

<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
