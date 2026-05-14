<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>
<!-- View Content -->
<div class="user-page-shell pt-2 pb-12">
    <div class="max-w-[1128px] mx-auto grid grid-cols-1 md:grid-cols-12 gap-6 px-4">
        <!-- Left Sidebar: Settings -->
        <aside class="md:col-span-3 flex flex-col gap-2">
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden font-manrope text-sm sticky top-20">
                <div class="p-4 border-b border-slate-100">
                    <h2 class="font-title-md text-on-surface">Manage Notifications</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    <button data-action="General Settings" class="notif-pref-btn w-full flex items-center gap-3 px-4 py-3 bg-slate-100 text-[#0A66C2] font-bold transition-all duration-200 hover:bg-slate-50 text-left">
                        <span class="material-symbols-outlined text-xl">settings</span>
                        <span>Settings</span>
                    </button>
                    <button data-action="Email Preferences" class="notif-pref-btn w-full flex items-center gap-3 px-4 py-3 text-slate-600 transition-all duration-200 hover:bg-slate-50 text-left">
                        <span class="material-symbols-outlined text-xl">mail</span>
                        <span>Email Preferences</span>
                    </button>
                    <button data-action="Push Notifications" class="notif-pref-btn w-full flex items-center gap-3 px-4 py-3 text-slate-600 transition-all duration-200 hover:bg-slate-50 text-left">
                        <span class="material-symbols-outlined text-xl">smartphone</span>
                        <span>Push Notifications</span>
                    </button>
                </div>
                <div class="p-3 text-center border-t border-slate-100">
                    <button id="mark-all-read" class="text-[#0A66C2] font-semibold hover:underline text-sm py-1">
                        Mark all as read
                    </button>
                </div>
            </div>
        </aside>

        <!-- Central Feed: Notifications List -->
        <section class="md:col-span-6 flex flex-col gap-4">
            <!-- Filter Header -->
            <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex justify-between items-center">
                <div class="flex gap-2">
                    <span data-filter="all" class="notif-filter-btn bg-[#0A66C2] text-white px-3 py-1 rounded-full text-xs font-semibold cursor-pointer transition-colors">All</span>
                    <span data-filter="posts" class="notif-filter-btn bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-semibold hover:bg-slate-200 cursor-pointer transition-colors">My posts</span>
                    <span data-filter="mentions" class="notif-filter-btn bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-semibold hover:bg-slate-200 cursor-pointer transition-colors">Mentions</span>
                </div>
                <button class="text-slate-500 hover:text-slate-900 transition-colors">
                    <span class="material-symbols-outlined">tune</span>
                </button>
            </div>
            <!-- Notifications List -->
            <div id="notifications-list" class="flex flex-col gap-0.5 rounded-lg overflow-hidden border border-slate-200 shadow-sm">
                <!-- Dynamic notifications will be loaded here -->
            </div>
            <button onclick="notifToast('All previous notifications are already loaded.', 'info')" class="bg-white w-full py-3 rounded-lg border border-slate-200 shadow-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                View previous notifications
            </button>
        </section>

        <!-- Right Sidebar: Widgets -->
        <aside class="md:col-span-3 hidden md:flex flex-col gap-4">
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-4">
                <h3 class="font-title-md mb-4 flex justify-between items-center text-sm font-bold">
                    Add to your feed
                    <span class="material-symbols-outlined text-slate-400 text-lg">info</span>
                </h3>
                <div id="notif-suggestions" class="flex flex-col gap-4">
                    <!-- Suggestions can go here -->
                </div>
            </div>
            <div class="sticky top-20">
                <footer class="text-center">
                    <div class="flex flex-wrap justify-center gap-x-3 gap-y-1 text-[11px] text-slate-500">
                        <a class="hover:underline" href="#">About</a>
                        <a class="hover:underline" href="#">Accessibility</a>
                        <a class="hover:underline" href="#">Help Center</a>
                        <a class="hover:underline" href="#">Privacy & Terms</a>
                    </div>
                    <div class="mt-4 flex items-center justify-center gap-1">
                        <span class="text-[#0A66C2] font-black text-xs uppercase">ProNetwork</span>
                        <span class="text-[10px] text-slate-500">© 2024</span>
                    </div>
                </footer>
            </div>
        </aside>
    </div>
</div>
<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
