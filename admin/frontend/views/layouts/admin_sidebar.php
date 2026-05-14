<aside id="admin-sidebar" class="admin-sidebar w-72 lg:w-64 text-slate-300 flex-shrink-0 fixed lg:sticky top-0 left-0 h-screen flex flex-col z-[90] -translate-x-full lg:translate-x-0 transition-transform duration-300">
    <div class="p-6">
        <a href="<?php echo URLROOT; ?>/admin/dashboard" class="flex items-center gap-3 text-xl font-black text-white font-manrope">
            <span class="w-10 h-10 rounded-2xl bg-cyan-400 text-slate-950 flex items-center justify-center shadow-lg shadow-cyan-950/30">
                <span class="material-symbols-outlined text-[22px]">admin_panel_settings</span>
            </span>
            ProAdmin
        </a>
        <p class="mt-3 text-[11px] text-slate-500 font-bold uppercase tracking-widest">Control Center</p>
    </div>

    <nav class="flex-1 px-4 space-y-1">
        <?php $current_url = $_SERVER['REQUEST_URI']; ?>
        <a href="<?php echo URLROOT; ?>/admin/dashboard" class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition-colors <?php echo (strpos($current_url, 'dashboard') !== false) ? 'is-active text-white' : ''; ?>">
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            <span class="text-sm font-medium">Dashboard</span>
        </a>
        <a href="<?php echo URLROOT; ?>/admin/users" class="admin-nav-link flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition-colors <?php echo (strpos($current_url, 'users') !== false) ? 'is-active text-white' : ''; ?>">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[20px]">group</span>
                <span class="text-sm font-medium">Users</span>
            </div>
            <?php if(!empty($data['stats']['pending_users'])): ?>
                <span class="bg-amber-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full"><?php echo $data['stats']['pending_users']; ?></span>
            <?php endif; ?>
        </a>
        <a href="<?php echo URLROOT; ?>/admin/companies" class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition-colors <?php echo (strpos($current_url, 'companies') !== false) ? 'is-active text-white' : ''; ?>">
            <span class="material-symbols-outlined text-[20px]">business</span>
            <span class="text-sm font-medium">Companies</span>
        </a>
        <a href="<?php echo URLROOT; ?>/admin/posts" class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition-colors <?php echo (strpos($current_url, 'posts') !== false) ? 'is-active text-white' : ''; ?>">
            <span class="material-symbols-outlined text-[20px]">dynamic_feed</span>
            <span class="text-sm font-medium">Posts</span>
        </a>
        <a href="<?php echo URLROOT; ?>/admin/jobs" class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition-colors <?php echo (strpos($current_url, 'jobs') !== false) ? 'is-active text-white' : ''; ?>">
            <span class="material-symbols-outlined text-[20px]">work</span>
            <span class="text-sm font-medium">Jobs</span>
        </a>
        <a href="<?php echo URLROOT; ?>/admin/reports" class="admin-nav-link flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition-colors <?php echo (strpos($current_url, 'reports') !== false) ? 'is-active text-white' : ''; ?>">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[20px]">report</span>
                <span class="text-sm font-medium">Reports</span>
            </div>
            <?php if(!empty($data['stats']['unread_reports'])): ?>
                <span class="bg-red-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full"><?php echo $data['stats']['unread_reports']; ?></span>
            <?php endif; ?>
        </a>
    </nav>

    <div class="p-4 border-t border-white/10">
        <a href="<?php echo URLROOT; ?>/user/feed" class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition-colors">
            <span class="material-symbols-outlined text-[20px]">visibility</span>
            <span class="text-sm font-medium">Back to Feed</span>
        </a>
        <a href="<?php echo URLROOT; ?>/auth/logout" class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-300 hover:bg-red-500/10 hover:text-red-200 transition-colors">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            <span class="text-sm font-medium">Logout</span>
        </a>
    </div>
</aside>

<div class="flex-1 flex flex-col min-w-0 overflow-hidden lg:ml-0">
    <!-- Top Header -->
    <header class="admin-topbar h-16 bg-white/85 backdrop-blur-xl border-b border-slate-200/70 flex items-center justify-between px-4 sm:px-6 flex-shrink-0 sticky top-0 z-40">
        <div class="flex items-center gap-4">
            <button id="admin-menu-btn" class="lg:hidden text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-xl p-2 transition-colors" aria-label="Open admin menu" aria-expanded="false">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <h2 class="text-sm font-semibold text-slate-700 hidden md:block uppercase tracking-wider">System Management</h2>
        </div>
        <div class="flex items-center gap-4">
            <div class="relative">
                <button id="admin-notif-btn" class="text-slate-400 hover:text-slate-600 transition-all flex items-center justify-center p-1.5 rounded-full hover:bg-slate-100">
                    <span class="material-symbols-outlined text-[22px]">notifications</span>
                    <?php if(!empty($data['stats']['total_admin_notifications'])): ?>
                        <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-[9px] font-black rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                            <?php echo $data['stats']['total_admin_notifications']; ?>
                        </span>
                    <?php endif; ?>
                </button>

                <!-- Notification Dropdown -->
                <div id="admin-notif-dropdown" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.1)] border border-slate-100 z-50 overflow-hidden animate-in fade-in zoom-in duration-200 origin-top-right">
                    <div class="px-5 py-4 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest">Admin Alerts</h3>
                        <span class="text-[10px] font-bold text-slate-400"><?php echo $data['stats']['total_admin_notifications']; ?> Action Items</span>
                    </div>
                    <div class="max-h-[300px] overflow-y-auto">
                        <?php if(!empty($data['stats']['pending_users'])): ?>
                            <a href="<?php echo URLROOT; ?>/admin/users" class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 flex-shrink-0">
                                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-900 truncate">New User Registrations</p>
                                    <p class="text-xs text-slate-500 truncate"><?php echo $data['stats']['pending_users']; ?> accounts awaiting approval</p>
                                </div>
                            </a>
                        <?php endif; ?>

                        <?php if(!empty($data['stats']['unread_reports'])): ?>
                            <a href="<?php echo URLROOT; ?>/admin/reports" class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0">
                                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-600 flex-shrink-0">
                                    <span class="material-symbols-outlined text-[20px]">flag</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-900 truncate">Community Reports</p>
                                    <p class="text-xs text-slate-500 truncate"><?php echo $data['stats']['unread_reports']; ?> content flags to review</p>
                                </div>
                            </a>
                        <?php endif; ?>

                        <?php if($data['stats']['total_admin_notifications'] == 0): ?>
                            <div class="px-5 py-8 text-center">
                                <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                                    <span class="material-symbols-outlined text-[24px]">check_circle</span>
                                </div>
                                <p class="text-sm text-slate-500">Everything caught up!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-3 bg-slate-50/30 text-center border-t border-slate-50">
                        <a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-[11px] font-bold text-[#0A66C2] hover:underline">View System Dashboard</a>
                    </div>
                </div>
            </div>
            <div class="h-8 w-[1px] bg-slate-200 mx-2"></div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-slate-900"><?php echo $_SESSION['user_name']; ?></p>
                    <p class="text-[10px] text-slate-500">Administrator</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 overflow-hidden">
                    <img data-user-pic="true" src="" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </header>

    <div class="admin-content flex-1 overflow-y-auto p-4 sm:p-6 md:p-8">
