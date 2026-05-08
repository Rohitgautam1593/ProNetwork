<?php require APPROOT . '/views/layouts/admin_header.php'; ?>
<?php require APPROOT . '/views/layouts/admin_sidebar.php'; ?>

<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 font-manrope">Dashboard Overview</h1>
    <p class="text-slate-500 text-sm">Welcome back, here's what's happening on ProNetwork today.</p>
</div>

<!-- Key Metrics -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <span class="material-symbols-outlined">group</span>
            </div>
            <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-full">+12%</span>
        </div>
        <p class="text-2xl font-black text-slate-900" id="stat-users"><?php echo number_format($data['stats']['users']); ?></p>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Total Users</p>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                <span class="material-symbols-outlined">dynamic_feed</span>
            </div>
            <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-full">+5%</span>
        </div>
        <p class="text-2xl font-black text-slate-900" id="stat-posts"><?php echo number_format($data['stats']['posts']); ?></p>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Total Posts</p>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <span class="material-symbols-outlined">work</span>
            </div>
            <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded-full">Stable</span>
        </div>
        <p class="text-2xl font-black text-slate-900" id="stat-jobs"><?php echo number_format($data['stats']['jobs']); ?></p>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Active Jobs</p>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <span class="material-symbols-outlined">business</span>
            </div>
            <span class="text-xs font-bold text-red-500 bg-red-50 px-2 py-1 rounded-full">-2%</span>
        </div>
        <p class="text-2xl font-black text-slate-900" id="stat-companies"><?php echo number_format($data['stats']['companies']); ?></p>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Companies</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Activity -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-900">Recent Activity</h3>
            <button class="text-[10px] font-bold text-[#0A66C2] uppercase tracking-widest hover:underline">View All</button>
        </div>
        <div class="p-6 space-y-6">
            <?php foreach($data['recent_users'] as $user): ?>
            <div class="flex gap-4">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex-shrink-0 flex items-center justify-center text-[#0A66C2]">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                </div>
                <div>
                    <p class="text-sm text-slate-600"><span class="font-bold text-slate-900"><?php echo $user['full_name']; ?></span> joined the platform.</p>
                    <p class="text-[10px] text-slate-400 mt-1"><?php echo date('M d, H:i', strtotime($user['created_at'])); ?></p>
                </div>
            </div>
            <?php endforeach; ?>

            <?php foreach($data['recent_posts'] as $post): ?>
            <div class="flex gap-4">
                <div class="w-8 h-8 rounded-full bg-green-100 flex-shrink-0 flex items-center justify-center text-green-600">
                    <span class="material-symbols-outlined text-[18px]">article</span>
                </div>
                <div>
                    <p class="text-sm text-slate-600"><span class="font-bold text-slate-900"><?php echo $post['author_name']; ?></span> shared a new post.</p>
                    <p class="text-[10px] text-slate-400 mt-1"><?php echo date('M d, H:i', strtotime($post['created_at'])); ?></p>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if(empty($data['recent_users']) && empty($data['recent_posts'])): ?>
                <p class="text-center text-slate-400 text-xs py-4">No recent activity detected.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[32px] text-slate-300">shield_person</span>
        </div>
        <h3 class="font-bold text-slate-900 mb-2">Administrative Actions</h3>
        <p class="text-sm text-slate-500 mb-6 max-w-[280px]">Need to perform bulk operations or system maintenance?</p>
        <div class="grid grid-cols-2 gap-3 w-full">
            <a href="<?php echo URLROOT; ?>/admin/users" class="p-3 bg-slate-50 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 transition-all flex flex-col items-center gap-2 group">
                <span class="material-symbols-outlined text-[20px] text-blue-500 group-hover:scale-110 transition-transform">manage_accounts</span>
                Users
            </a>
            <a href="<?php echo URLROOT; ?>/admin/posts" class="p-3 bg-slate-50 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 transition-all flex flex-col items-center gap-2 group">
                <span class="material-symbols-outlined text-[20px] text-purple-500 group-hover:scale-110 transition-transform">fact_check</span>
                Moderate
            </a>
            <a href="<?php echo URLROOT; ?>/admin/companies" class="p-3 bg-slate-50 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 transition-all flex flex-col items-center gap-2 group">
                <span class="material-symbols-outlined text-[20px] text-rose-500 group-hover:scale-110 transition-transform">business</span>
                Companies
            </a>
            <a href="<?php echo URLROOT; ?>/admin/jobs" class="p-3 bg-slate-50 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 transition-all flex flex-col items-center gap-2 group">
                <span class="material-symbols-outlined text-[20px] text-indigo-500 group-hover:scale-110 transition-transform">work</span>
                Jobs
            </a>
        </div>
    </div>
</div>

<script>
    async function refreshAdminStats() {
        try {
            const response = await fetch(`${URLROOT}/admin/stats_api`);
            const data = await response.json();
            
            // Update counts with a subtle fade effect if changed
            updateStat('stat-users', data.users);
            updateStat('stat-posts', data.posts);
            updateStat('stat-jobs', data.jobs);
            updateStat('stat-companies', data.companies);
            
        } catch (e) {
            console.error('Stats refresh failed', e);
        }
    }

    function updateStat(id, newValue) {
        const el = document.getElementById(id);
        const oldValue = el.innerText.replace(/,/g, '');
        if (oldValue != newValue) {
            el.classList.add('opacity-50', 'scale-95');
            setTimeout(() => {
                el.innerText = Number(newValue).toLocaleString();
                el.classList.remove('opacity-50', 'scale-95');
            }, 300);
        }
    }

    // Refresh every 15 seconds
    setInterval(refreshAdminStats, 15000);
</script>

<?php require APPROOT . '/views/layouts/admin_footer.php'; ?>
