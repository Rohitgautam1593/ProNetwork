<aside class="w-64 bg-slate-900 text-slate-300 flex-shrink-0 sticky top-0 h-screen flex flex-col">
    <div class="p-6">
        <a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-xl font-black text-white font-manrope">ProAdmin</a>
    </div>
    
    <nav class="flex-1 px-4 space-y-1">
        <?php $current_url = $_SERVER['REQUEST_URI']; ?>
        <a href="<?php echo URLROOT; ?>/admin/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 hover:text-white transition-colors <?php echo (strpos($current_url, 'dashboard') !== false) ? 'bg-slate-800 text-white' : ''; ?>">
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            <span class="text-sm font-medium">Dashboard</span>
        </a>
        <a href="<?php echo URLROOT; ?>/admin/users" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 hover:text-white transition-colors <?php echo (strpos($current_url, 'users') !== false) ? 'bg-slate-800 text-white' : ''; ?>">
            <span class="material-symbols-outlined text-[20px]">group</span>
            <span class="text-sm font-medium">Users</span>
        </a>
        <a href="<?php echo URLROOT; ?>/admin/companies" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 hover:text-white transition-colors <?php echo (strpos($current_url, 'companies') !== false) ? 'bg-slate-800 text-white' : ''; ?>">
            <span class="material-symbols-outlined text-[20px]">business</span>
            <span class="text-sm font-medium">Companies</span>
        </a>
        <a href="<?php echo URLROOT; ?>/admin/posts" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 hover:text-white transition-colors <?php echo (strpos($current_url, 'posts') !== false) ? 'bg-slate-800 text-white' : ''; ?>">
            <span class="material-symbols-outlined text-[20px]">dynamic_feed</span>
            <span class="text-sm font-medium">Posts</span>
        </a>
        <a href="<?php echo URLROOT; ?>/admin/jobs" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 hover:text-white transition-colors <?php echo (strpos($current_url, 'jobs') !== false) ? 'bg-slate-800 text-white' : ''; ?>">
            <span class="material-symbols-outlined text-[20px]">work</span>
            <span class="text-sm font-medium">Jobs</span>
        </a>
        <a href="<?php echo URLROOT; ?>/admin/reports" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-800 hover:text-white transition-colors <?php echo (strpos($current_url, 'reports') !== false) ? 'bg-slate-800 text-white' : ''; ?>">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[20px]">report</span>
                <span class="text-sm font-medium">Reports</span>
            </div>
            <?php if(!empty($data['stats']['unread_reports'])): ?>
                <span class="bg-red-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full"><?php echo $data['stats']['unread_reports']; ?></span>
            <?php endif; ?>
        </a>
    </nav>

    <div class="p-4 border-t border-slate-800">
        <a href="<?php echo URLROOT; ?>/user/feed" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 hover:text-white transition-colors">
            <span class="material-symbols-outlined text-[20px]">visibility</span>
            <span class="text-sm font-medium">Back to Feed</span>
        </a>
        <a href="<?php echo URLROOT; ?>/auth/logout" class="flex items-center gap-3 px-3 py-2 rounded-lg text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            <span class="text-sm font-medium">Logout</span>
        </a>
    </div>
</aside>

<div class="flex-1 flex flex-col min-w-0 overflow-hidden">
    <!-- Top Header -->
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0">
        <div class="flex items-center gap-4">
            <button class="md:hidden text-slate-500">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <h2 class="text-sm font-semibold text-slate-700 hidden md:block uppercase tracking-wider">System Management</h2>
        </div>
        <div class="flex items-center gap-4">
            <a href="<?php echo URLROOT; ?>/admin/reports" class="text-slate-400 hover:text-slate-600 relative">
                <span class="material-symbols-outlined text-[22px]">notifications</span>
                <?php if(!empty($data['stats']['unread_reports'])): ?>
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border-2 border-white">
                        <?php echo $data['stats']['unread_reports']; ?>
                    </span>
                <?php endif; ?>
            </a>
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
    
    <div class="flex-1 overflow-y-auto p-6 md:p-8">
