<?php require ADMINROOT . '/frontend/views/layouts/admin_header.php'; ?>
<?php require ADMINROOT . '/frontend/views/layouts/admin_sidebar.php'; ?>
<?php
$activityStyles = [
    'User' => ['icon' => 'person_add', 'wrap' => 'bg-blue-50 text-[#0A66C2] ring-blue-100', 'text' => 'joined the platform.'],
    'Post' => ['icon' => 'article', 'wrap' => 'bg-emerald-50 text-emerald-600 ring-emerald-100', 'text' => 'shared a new post.'],
    'Report' => ['icon' => 'flag', 'wrap' => 'bg-rose-50 text-rose-600 ring-rose-100', 'text' => ''],
];
?>

<div class="admin-hero mb-8 rounded-3xl overflow-hidden border border-slate-800 shadow-sm">
    <div class="relative p-6 sm:p-8">
        <div class="relative z-10 max-w-3xl">
            <span
                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-cyan-200 text-[11px] font-black uppercase tracking-widest border border-white/15">
                <span class="material-symbols-outlined text-[16px]">verified</span>
                Live Admin Overview
            </span>
            <h1 class="mt-4 text-3xl sm:text-4xl font-black text-white font-manrope">Dashboard Overview</h1>
            <p class="mt-2 text-slate-300 text-sm sm:text-base max-w-2xl">Welcome back. Here is the clearest snapshot of
                members, content, hiring activity, and moderation work across ProNetwork.</p>
        </div>
    </div>
</div>

<!-- Key Metrics -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 lg:gap-5 mb-8">
    <div class="admin-stat-card admin-stat-card-blue bg-white p-5 rounded-2xl shadow-sm border border-white">
        <div class="flex items-center justify-between mb-4">
            <div
                class="w-11 h-11 rounded-2xl bg-blue-50 text-[#0A66C2] flex items-center justify-center ring-1 ring-blue-100">
                <span class="material-symbols-outlined">group</span>
            </div>
            <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-full">+12%</span>
        </div>
        <p class="text-3xl font-black text-slate-950 tracking-normal" id="stat-users">
            <?php echo number_format($data['stats']['users']); ?></p>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Total Users</p>
    </div>

    <div class="admin-stat-card admin-stat-card-violet bg-white p-5 rounded-2xl shadow-sm border border-white">
        <div class="flex items-center justify-between mb-4">
            <div
                class="w-11 h-11 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center ring-1 ring-violet-100">
                <span class="material-symbols-outlined">dynamic_feed</span>
            </div>
            <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-full">+5%</span>
        </div>
        <p class="text-3xl font-black text-slate-950 tracking-normal" id="stat-posts">
            <?php echo number_format($data['stats']['posts']); ?></p>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Total Posts</p>
    </div>

    <div class="admin-stat-card admin-stat-card-cyan bg-white p-5 rounded-2xl shadow-sm border border-white">
        <div class="flex items-center justify-between mb-4">
            <div
                class="w-11 h-11 rounded-2xl bg-cyan-50 text-cyan-700 flex items-center justify-center ring-1 ring-cyan-100">
                <span class="material-symbols-outlined">work</span>
            </div>
            <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded-full">Stable</span>
        </div>
        <p class="text-3xl font-black text-slate-950 tracking-normal" id="stat-jobs">
            <?php echo number_format($data['stats']['jobs']); ?></p>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Active Jobs</p>
    </div>

    <div class="admin-stat-card admin-stat-card-rose bg-white p-5 rounded-2xl shadow-sm border border-white">
        <div class="flex items-center justify-between mb-4">
            <div
                class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center ring-1 ring-rose-100">
                <span class="material-symbols-outlined">report</span>
            </div>
            <span
                class="text-xs font-bold text-red-500 bg-red-50 px-2 py-1 rounded-full"><?php echo (int) $data['stats']['repeat_report_targets']; ?>
                repeat</span>
        </div>
        <p class="text-3xl font-black text-slate-950 tracking-normal" id="stat-reports">
            <?php echo number_format($data['stats']['unread_reports']); ?></p>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Pending Reports</p>
    </div>

    <div class="admin-stat-card admin-stat-card-slate bg-white p-5 rounded-2xl shadow-sm border border-white">
        <div class="flex items-center justify-between mb-4">
            <div
                class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center ring-1 ring-slate-200">
                <span class="material-symbols-outlined">business</span>
            </div>
            <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded-full">Live</span>
        </div>
        <p class="text-3xl font-black text-slate-950 tracking-normal" id="stat-companies">
            <?php echo number_format($data['stats']['companies']); ?></p>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Companies</p>
    </div>
</div>

<!-- Pending Approvals Banner -->
<?php if (!empty($data['stats']['pending_users'])): ?>
    <div
        class="mb-8 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                <span class="material-symbols-outlined">pending_actions</span>
            </div>
            <div>
                <p class="text-sm font-bold text-amber-900"><?php echo $data['stats']['pending_users']; ?> user(s) awaiting
                    approval</p>
                <p class="text-xs text-amber-600">New registrations need your review before they can access the platform.
                </p>
            </div>
        </div>
        <a href="<?php echo URLROOT; ?>/admin/users"
            class="px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-bold hover:bg-amber-600 transition-all">Review
            Now</a>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 xl:grid-cols-[1.15fr_0.85fr] gap-6 lg:gap-8">
    <!-- Recent Activity -->
    <div class="admin-panel bg-white rounded-2xl shadow-sm border border-white overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-950 font-manrope">Recent Activity</h3>
                <p class="text-xs text-slate-500 mt-1">Newest signups, posts, and moderation signals.</p>
            </div>
            <button onclick="openActivityModal()"
                class="px-3 py-2 rounded-xl text-[10px] font-bold text-[#0A66C2] uppercase tracking-widest hover:bg-blue-50 transition-colors">View
                All</button>
        </div>
        <div class="p-6 space-y-5">
            <?php foreach ($data['recent_activity'] as $item): ?>
                <div class="admin-activity-item flex gap-4">
                    <?php
                    $style = $activityStyles[$item['type']] ?? ['icon' => 'notifications', 'wrap' => 'bg-slate-100 text-slate-600 ring-slate-200', 'text' => 'triggered an event.'];
                    if ($item['type'] == 'User') {
                        $text = '<span class="font-bold text-slate-900">' . htmlspecialchars($item['main_text']) . '</span> joined the platform.';
                    } elseif ($item['type'] == 'Post') {
                        $text = '<span class="font-bold text-slate-900">' . htmlspecialchars($item['main_text']) . '</span> shared a new post.';
                    } elseif ($item['type'] == 'Report') {
                        $text = '<span class="font-bold text-slate-900">' . htmlspecialchars($item['main_text']) . '</span> ' . htmlspecialchars($item['sub_text']) . '.';
                    } else {
                        $text = '<span class="font-bold text-slate-900">' . htmlspecialchars($item['main_text']) . '</span> ' . htmlspecialchars($style['text']);
                    }
                    ?>
                    <div
                        class="w-10 h-10 rounded-2xl <?php echo $style['wrap']; ?> ring-1 flex-shrink-0 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[19px]"><?php echo $style['icon']; ?></span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm text-slate-600"><?php echo $text; ?></p>
                        <p class="text-[10px] text-slate-400 mt-1">
                            <?php echo date('M d, H:i', strtotime($item['created_at'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($data['recent_activity'])): ?>
                <p class="text-center text-slate-400 text-xs py-4">No recent activity detected.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div
        class="admin-panel admin-actions-panel bg-white rounded-2xl shadow-sm border border-white p-6 flex flex-col justify-between">
        <div class="text-center pt-3">
            <div
                class="w-16 h-16 rounded-3xl bg-slate-950 text-cyan-200 flex items-center justify-center mb-4 mx-auto shadow-lg shadow-slate-950/25">
                <span class="material-symbols-outlined text-[32px]">shield_person</span>
            </div>
            <h3 class="font-bold text-slate-950 mb-2 font-manrope">Administrative Actions</h3>
            <p class="text-sm text-slate-500 mb-6 max-w-[280px] mx-auto">Need to perform bulk operations or system
                maintenance?</p>
        </div>
        <div class="grid grid-cols-2 gap-3 w-full">
            <a href="<?php echo URLROOT; ?>/admin/users"
                class="admin-action-tile p-4 bg-slate-50 rounded-2xl text-xs font-bold text-slate-700 hover:bg-blue-50 transition-all flex flex-col items-center gap-2 group border border-slate-100">
                <span
                    class="material-symbols-outlined text-[20px] text-blue-500 group-hover:scale-110 transition-transform">manage_accounts</span>
                Users
            </a>
            <a href="<?php echo URLROOT; ?>/admin/reports"
                class="admin-action-tile p-4 bg-slate-50 rounded-2xl text-xs font-bold text-slate-700 hover:bg-violet-50 transition-all flex flex-col items-center gap-2 group border border-slate-100">
                <span
                    class="material-symbols-outlined text-[20px] text-violet-500 group-hover:scale-110 transition-transform">fact_check</span>
                Reports
            </a>
            <a href="<?php echo URLROOT; ?>/admin/companies"
                class="admin-action-tile p-4 bg-slate-50 rounded-2xl text-xs font-bold text-slate-700 hover:bg-rose-50 transition-all flex flex-col items-center gap-2 group border border-slate-100">
                <span
                    class="material-symbols-outlined text-[20px] text-rose-500 group-hover:scale-110 transition-transform">business</span>
                Companies
            </a>
            <a href="<?php echo URLROOT; ?>/admin/jobs"
                class="admin-action-tile p-4 bg-slate-50 rounded-2xl text-xs font-bold text-slate-700 hover:bg-cyan-50 transition-all flex flex-col items-center gap-2 group border border-slate-100">
                <span
                    class="material-symbols-outlined text-[20px] text-cyan-600 group-hover:scale-110 transition-transform">work</span>
                Jobs
            </a>
        </div>
    </div>
</div>

<!-- ═══ Platform Live Data Explorer ═══ -->
<div class="mt-10">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-black text-slate-900 font-manrope flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0A66C2] text-[22px]">dataset</span>
                Platform Live Data Explorer
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Click any row to inspect full details in a rich diagnostic modal.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <!-- USERS CARD -->
        <div class="bg-white rounded-2xl border border-blue-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 bg-gradient-to-r from-blue-600 to-blue-500 flex items-center justify-between">
                <span class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">group</span> Registered Users
                </span>
                <a href="<?php echo URLROOT; ?>/admin/users"
                    class="text-[10px] font-bold text-blue-100 hover:text-white flex items-center gap-1">Manage All
                    <span class="material-symbols-outlined text-[12px]">arrow_forward</span></a>
            </div>
            <div class="divide-y divide-slate-50">
                <?php foreach ($data['users'] as $usr): ?>
                    <div onclick="openEntityModal('User', <?php echo (int) $usr['user_id']; ?>)"
                        class="flex items-center gap-3 px-5 py-3 hover:bg-blue-50/60 cursor-pointer group transition-all duration-150">
                        <div
                            class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-black text-sm flex-shrink-0 flex items-center justify-center overflow-hidden ring-2 ring-blue-50">
                            <?php if (!empty($usr['profile_pic'])): ?>
                                <img src="<?php echo strpos($usr['profile_pic'], 'http') === 0 ? $usr['profile_pic'] : URLROOT . '/uploads/profiles/' . $usr['profile_pic']; ?>"
                                    class="w-full h-full object-cover">
                            <?php else:
                                echo strtoupper(substr($usr['full_name'], 0, 1)); endif; ?>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p
                                class="text-sm font-bold text-slate-900 truncate group-hover:text-blue-700 transition-colors">
                                <?php echo htmlspecialchars($usr['full_name']); ?></p>
                            <p class="text-[11px] text-slate-400 truncate"><?php echo htmlspecialchars($usr['email']); ?>
                            </p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span
                                class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-blue-50 text-blue-600 border border-blue-100"><?php echo htmlspecialchars($usr['role']); ?></span>
                            <span
                                class="material-symbols-outlined text-[14px] text-slate-300 group-hover:text-blue-500 transition-colors">open_in_new</span>
                        </div>
                    </div>
                <?php endforeach;
                if (empty($data['users'])): ?>
                    <p class="text-center text-slate-400 text-xs py-5 italic">No users found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- COMPANIES CARD -->
        <div class="bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 bg-gradient-to-r from-rose-600 to-pink-500 flex items-center justify-between">
                <span class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">business</span> Companies
                </span>
                <a href="<?php echo URLROOT; ?>/admin/companies"
                    class="text-[10px] font-bold text-rose-100 hover:text-white flex items-center gap-1">Manage All
                    <span class="material-symbols-outlined text-[12px]">arrow_forward</span></a>
            </div>
            <div class="divide-y divide-slate-50">
                <?php foreach ($data['companies'] as $cmp): ?>
                    <div onclick="openEntityModal('Company', <?php echo (int) $cmp['company_id']; ?>)"
                        class="flex items-center gap-3 px-5 py-3 hover:bg-rose-50/60 cursor-pointer group transition-all duration-150">
                        <div
                            class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex-shrink-0 flex items-center justify-center overflow-hidden ring-2 ring-rose-50">
                            <?php if (!empty($cmp['logo_path'])): ?>
                                <img src="<?php echo strpos($cmp['logo_path'], 'http') === 0 ? $cmp['logo_path'] : URLROOT . '/uploads/companies/' . $cmp['logo_path']; ?>"
                                    class="w-full h-full object-cover">
                            <?php else: ?><span
                                    class="material-symbols-outlined text-[17px]">apartment</span><?php endif; ?>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p
                                class="text-sm font-bold text-slate-900 truncate group-hover:text-rose-700 transition-colors">
                                <?php echo htmlspecialchars($cmp['name']); ?></p>
                            <p class="text-[11px] text-slate-400 truncate">
                                <?php echo htmlspecialchars($cmp['industry'] ?? 'Corporate'); ?></p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span
                                class="text-[10px] text-slate-400 font-medium"><?php echo date('M d', strtotime($cmp['created_at'])); ?></span>
                            <span
                                class="material-symbols-outlined text-[14px] text-slate-300 group-hover:text-rose-500 transition-colors">open_in_new</span>
                        </div>
                    </div>
                <?php endforeach;
                if (empty($data['companies'])): ?>
                    <p class="text-center text-slate-400 text-xs py-5 italic">No companies found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- POSTS CARD -->
        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-500 flex items-center justify-between">
                <span class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">article</span> Community Posts
                </span>
                <a href="<?php echo URLROOT; ?>/admin/posts"
                    class="text-[10px] font-bold text-emerald-100 hover:text-white flex items-center gap-1">Manage All
                    <span class="material-symbols-outlined text-[12px]">arrow_forward</span></a>
            </div>
            <div class="divide-y divide-slate-50">
                <?php foreach ($data['posts'] as $pst): ?>
                    <div onclick="openEntityModal('Post', <?php echo (int) $pst['post_id']; ?>)"
                        class="flex items-center gap-3 px-5 py-3 hover:bg-emerald-50/60 cursor-pointer group transition-all duration-150">
                        <div
                            class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex-shrink-0 flex items-center justify-center ring-2 ring-emerald-50">
                            <span class="material-symbols-outlined text-[17px]">notes</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p
                                class="text-sm text-slate-800 font-medium truncate group-hover:text-emerald-700 transition-colors">
                                <?php echo htmlspecialchars($pst['content']); ?></p>
                            <p class="text-[11px] text-slate-400 mt-0.5">By <span
                                    class="font-bold"><?php echo htmlspecialchars($pst['author_name']); ?></span></p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span
                                class="text-[10px] px-2 py-0.5 rounded font-black bg-emerald-50 text-emerald-600 border border-emerald-100">#<?php echo $pst['post_id']; ?></span>
                            <span
                                class="material-symbols-outlined text-[14px] text-slate-300 group-hover:text-emerald-500 transition-colors">open_in_new</span>
                        </div>
                    </div>
                <?php endforeach;
                if (empty($data['posts'])): ?>
                    <p class="text-center text-slate-400 text-xs py-5 italic">No posts found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- JOBS CARD -->
        <div class="bg-white rounded-2xl border border-cyan-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 bg-gradient-to-r from-cyan-600 to-sky-500 flex items-center justify-between">
                <span class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">work</span> Active Jobs
                </span>
                <a href="<?php echo URLROOT; ?>/admin/jobs"
                    class="text-[10px] font-bold text-cyan-100 hover:text-white flex items-center gap-1">Manage All
                    <span class="material-symbols-outlined text-[12px]">arrow_forward</span></a>
            </div>
            <div class="divide-y divide-slate-50">
                <?php foreach ($data['jobs'] as $jb): ?>
                    <div onclick="openEntityModal('Job', <?php echo (int) $jb['job_id']; ?>)"
                        class="flex items-center gap-3 px-5 py-3 hover:bg-cyan-50/60 cursor-pointer group transition-all duration-150">
                        <div
                            class="w-9 h-9 rounded-xl bg-cyan-100 text-cyan-700 flex-shrink-0 flex items-center justify-center ring-2 ring-cyan-50">
                            <span class="material-symbols-outlined text-[17px]">cases</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p
                                class="text-sm font-bold text-slate-900 truncate group-hover:text-cyan-700 transition-colors">
                                <?php echo htmlspecialchars($jb['title']); ?></p>
                            <p class="text-[11px] text-slate-400 truncate">
                                <?php echo htmlspecialchars($jb['company_name']); ?> &bull;
                                <?php echo htmlspecialchars($jb['location'] ?? 'Remote'); ?></p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span
                                class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-cyan-50 text-cyan-700 border border-cyan-100"><?php echo htmlspecialchars($jb['job_type'] ?? 'Full-time'); ?></span>
                            <span
                                class="material-symbols-outlined text-[14px] text-slate-300 group-hover:text-cyan-500 transition-colors">open_in_new</span>
                        </div>
                    </div>
                <?php endforeach;
                if (empty($data['jobs'])): ?>
                    <p class="text-center text-slate-400 text-xs py-5 italic">No jobs found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ═══ Entity Inspector Modal (Compact Premium) ═══ -->
<div id="entityModal" class="fixed inset-0 z-[250] hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeEntityModal()"></div>

    <!-- Scrolling Centered Container -->
    <div class="min-h-screen px-4 py-8 flex items-center justify-center">
        <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col border border-white/80 max-h-[calc(100vh-4rem)]" id="em-card">

            <!-- ── Premium Colored Banner Header ── -->
            <div id="em-header"
                class="relative flex items-center gap-4 px-6 py-5 flex-shrink-0 transition-all duration-300">
                <!-- Decorative dot matrix pattern -->
                <div class="absolute inset-0 opacity-10"
                    style="background-image:radial-gradient(circle,#fff 1.5px,transparent 1.5px);background-size:18px 18px;">
                </div>

                <!-- Dynamic Avatar Frame -->
                <div id="em-avatar" class="relative w-14 h-14 rounded-2xl flex-shrink-0 flex items-center justify-center
                            overflow-hidden shadow-inner ring-4 ring-white/25 bg-white/20 text-white font-black text-xl">
                </div>

                <!-- Metadata Headers -->
                <div class="relative min-w-0 flex-1">
                    <div id="em-pill" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md
                                text-[10px] font-black uppercase tracking-widest
                                bg-white/20 text-white backdrop-blur-md border border-white/30 mb-1.5 shadow-sm"></div>
                    <h2 id="em-title" class="text-lg font-black text-white font-manrope leading-tight truncate"></h2>
                    <p id="em-subtitle" class="text-xs text-white/80 truncate mt-0.5 font-medium"></p>
                </div>

                <!-- Dismiss action trigger -->
                <button onclick="closeEntityModal()" class="relative flex-shrink-0 w-8 h-8 flex items-center justify-center
                               rounded-full bg-white/10 hover:bg-white/25 text-white transition-all duration-150">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <!-- ── Scrollable Body Stack ── -->
            <div class="flex-1 overflow-y-auto p-0 bg-white custom-scrollbar">
                <div class="flex flex-col md:flex-row gap-0 divide-y md:divide-y-0 md:divide-x divide-slate-100">

                    <!-- Left Column: Primary Content & Actions -->
                    <div class="flex-1 p-5 space-y-4 min-w-0 flex flex-col justify-between">
                        <div class="space-y-4">
                            <!-- Optional Media Post Preview -->
                            <div id="em-img-wrap" class="hidden">
                                <div class="bg-slate-50/80 rounded-2xl p-2 border border-slate-100 flex items-center justify-center overflow-hidden">
                                    <img id="em-img" src="" alt=""
                                        class="w-full max-h-64 object-contain rounded-xl shadow-sm">
                                </div>
                            </div>

                            <!-- Main Details Content Body -->
                            <div>
                                <p id="em-content-label"
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">notes</span> Description
                                </p>
                                <div id="em-content" class="text-xs text-slate-700 leading-relaxed whitespace-pre-wrap
                                            bg-slate-50/75 border border-slate-100 rounded-2xl p-4
                                            max-h-48 overflow-y-auto custom-scrollbar font-normal overflow-wrap-anywhere"></div>
                            </div>
                        </div>

                        <!-- Embedded Dynamic Actions Row -->
                        <div id="em-actions" class="flex flex-wrap items-center gap-2 pt-3 border-t border-slate-100 mt-5">
                        </div>
                    </div>

                    <!-- Right Column: Micro-Attributes Sidebar -->
                    <div class="w-full md:w-56 flex-shrink-0 bg-slate-50/30 p-5 space-y-3">
                        <p
                            class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px]">info</span> Meta Info
                        </p>
                        <dl id="em-meta" class="space-y-2.5"></dl>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ── Sleek Type Background Gradients ── */
    #em-header.t-user {
        background: linear-gradient(135deg, #0A66C2 0%, #38bdf8 100%);
    }

    #em-header.t-company {
        background: linear-gradient(135deg, #e11d48 0%, #fb7185 100%);
    }

    #em-header.t-post {
        background: linear-gradient(135deg, #059669 0%, #34d399 100%);
    }

    #em-header.t-job {
        background: linear-gradient(135deg, #0891b2 0%, #22d3ee 100%);
    }

    /* ── Tailored Meta Key/Value Layout ── */
    #em-meta .mr {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 8px 12px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    #em-meta .mr dt {
        font-size: 9px;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    #em-meta .mr dd {
        font-size: 11px;
        font-weight: 700;
        color: #334155;
        margin-top: 2px;
        word-break: normal;
        overflow-wrap: anywhere;
    }

    /* ── Primary & Danger Action Buttons ── */
    .em-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 800;
        color: #ffffff;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .em-btn-primary:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .em-btn-danger {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 800;
        background: #fff0f2;
        color: #e11d48;
        border: 1px solid #ffe4e6;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .em-btn-danger:hover {
        background: #ffe4e6;
        transform: translateY(-1px);
    }

    /* ── Smooth Elastic Appear Transition ── */
    #em-card {
        animation: emScaleIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes emScaleIn {
        from {
            opacity: 0;
            transform: translateY(12px) scale(0.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
</style>

<!-- Activity Modal -->
<div id="activityModal" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeActivityModal()"></div>
    <div class="min-h-screen px-4 py-8 flex items-center justify-center">
        <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-100 flex flex-col max-h-[calc(100vh-4rem)] overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between flex-shrink-0">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 font-manrope">System Activity Log</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Real-time audit trail of all platform events.</p>
                </div>
                <button onclick="closeActivityModal()"
                    class="p-2 hover:bg-slate-50 rounded-full text-slate-400 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div id="activity-modal-content" class="flex-1 overflow-y-auto p-6 space-y-6 scroll-smooth custom-scrollbar">
                <!-- Items will be loaded here -->
            </div>

            <div id="activity-loading" class="hidden p-4 text-center flex-shrink-0 border-t border-slate-50">
                <div class="inline-block w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full animate-spin">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let activityPage = 1;
    let activityLoading = false;
    let hasMoreActivity = true;

    async function openActivityModal() {
        const modal = document.getElementById('activityModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Reset and load first page
        document.getElementById('activity-modal-content').innerHTML = '';
        activityPage = 1;
        hasMoreActivity = true;
        await loadMoreActivity();
    }

    function closeActivityModal() {
        document.getElementById('activityModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    async function loadMoreActivity() {
        if (activityLoading || !hasMoreActivity) return;

        activityLoading = true;
        document.getElementById('activity-loading').classList.remove('hidden');

        try {
            const response = await fetch(`${URLROOT}/admin/activity_api?page=${activityPage}`);
            const data = await response.json();

            if (data.success) {
                const container = document.getElementById('activity-modal-content');
                data.activities.forEach(item => {
                    const itemHtml = renderActivityItem(item);
                    container.insertAdjacentHTML('beforeend', itemHtml);
                });

                hasMoreActivity = data.has_more;
                activityPage++;
            }
        } catch (e) {
            console.error('Failed to load activity', e);
        } finally {
            activityLoading = false;
            document.getElementById('activity-loading').classList.add('hidden');
        }
    }

    function renderActivityItem(item) {
        let text = '';

        if (item.type === 'User') {
            text = `<span class="font-bold text-slate-900">${escapeHtml(item.main_text)}</span> joined the platform.`;
        } else if (item.type === 'Post') {
            text = `<span class="font-bold text-slate-900">${escapeHtml(item.main_text)}</span> shared a new post.`;
        } else if (item.type === 'Report') {
            text = `<span class="font-bold text-slate-900">${escapeHtml(item.main_text)}</span> ${escapeHtml(item.sub_text)}.`;
        } else {
            text = `<span class="font-bold text-slate-900">${escapeHtml(item.main_text || 'System')}</span> triggered an event.`;
        }

        const date = new Date(item.created_at).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });

        const styleMap = {
            User: { icon: 'person_add', wrap: 'bg-blue-50 text-[#0A66C2] ring-blue-100' },
            Post: { icon: 'article', wrap: 'bg-emerald-50 text-emerald-600 ring-emerald-100' },
            Report: { icon: 'flag', wrap: 'bg-rose-50 text-rose-600 ring-rose-100' }
        };
        const style = styleMap[item.type] || { icon: 'notifications', wrap: 'bg-slate-100 text-slate-600 ring-slate-200' };

        return `
            <div class="flex gap-4 animate-in fade-in slide-in-from-bottom-2 duration-300">
                <div class="w-10 h-10 rounded-2xl ${style.wrap} ring-1 flex-shrink-0 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">${style.icon}</span>
                </div>
                <div class="flex-1 pb-4 border-b border-slate-50 last:border-0">
                    <p class="text-sm text-slate-600">${text}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-[10px] text-slate-400 font-medium uppercase tracking-widest">${date}</p>
                        <span class="text-[10px] text-slate-300">&bull;</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">${item.type}</span>
                    </div>
                </div>
            </div>
        `;
    }

    // Add scroll listener for infinite scroll
    const modalContent = document.getElementById('activity-modal-content');
    if (modalContent) {
        modalContent.addEventListener('scroll', (e) => {
            const { scrollTop, scrollHeight, clientHeight } = e.target;
            if (scrollHeight - scrollTop <= clientHeight + 100) {
                loadMoreActivity();
            }
        });
    }

    async function refreshAdminStats() {
        try {
            const response = await fetch(`${URLROOT}/admin/stats_api`);
            const data = await response.json();

            // Update counts with a subtle fade effect if changed
            updateStat('stat-users', data.users);
            updateStat('stat-posts', data.posts);
            updateStat('stat-jobs', data.jobs);
            updateStat('stat-reports', data.unread_reports);
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

    // ── Entity Inspector Modal ────────────────────────────────────────────
    const EM_CFG = {
        User: { label: 'User Profile', icon: 'person', bg: '#0A66C2' },
        Company: { label: 'Company', icon: 'apartment', bg: '#be123c' },
        Post: { label: 'Community Post', icon: 'article', bg: '#047857' },
        Job: { label: 'Job Listing', icon: 'work', bg: '#0e7490' },
    };

    async function openEntityModal(type, id) {
        try {
            const res = await fetch(`${URLROOT}/admin/get_entity_info/${type}/${id}`);
            const data = await res.json();
            if (!data.success) { alert(data.message || 'Could not load entity.'); return; }

            const cfg = EM_CFG[type] || EM_CFG.User;
            const t = type.toLowerCase();

            // Header gradient
            const hdr = document.getElementById('em-header');
            hdr.className = `relative flex items-center gap-3 px-5 py-4 flex-shrink-0 t-${t}`;

            // Type pill
            document.getElementById('em-pill').innerHTML =
                `<span class="material-symbols-outlined" style="font-size:11px">${cfg.icon}</span>${cfg.label}`;

            // Avatar — logo for company/job, photo for user, icon for post
            const av = document.getElementById('em-avatar');
            if (data.image && (type === 'Company' || type === 'Job')) {
                av.innerHTML = `<img src="${data.image}" class="w-full h-full object-contain p-1 bg-white" alt="" onerror="this.parentElement.innerHTML='<span class=\\'material-symbols-outlined\\' style=\\'font-size:24px\\'>${cfg.icon}</span>'">`;
            } else if (data.image && type === 'User') {
                av.innerHTML = `<img src="${data.image}" class="w-full h-full object-cover" alt="">`;
            } else if (type === 'User') {
                av.innerHTML = `<span style="font-size:22px;font-weight:900">${(data.title || '?')[0].toUpperCase()}</span>`;
            } else {
                av.innerHTML = `<span class="material-symbols-outlined" style="font-size:24px">${cfg.icon}</span>`;
            }

            document.getElementById('em-title').textContent = data.title || '—';
            document.getElementById('em-subtitle').textContent = data.subtitle || '—';

            // Media image (post image only)
            const imgWrap = document.getElementById('em-img-wrap');
            const imgEl = document.getElementById('em-img');
            if (data.image && type === 'Post') {
                imgEl.src = data.image;
                imgWrap.classList.remove('hidden');
            } else {
                imgWrap.classList.add('hidden');
            }

            // Content label per type
            const labels = { User: 'Bio', Company: 'About', Post: 'Post Content', Job: 'Job Description' };
            document.getElementById('em-content-label').textContent = labels[type] || 'Description';
            document.getElementById('em-content').textContent = data.content || 'No description available.';

            // Meta attributes
            document.getElementById('em-meta').innerHTML =
                Object.entries(data.meta || {}).map(([k, v]) =>
                    `<div class="mr"><dt>${k}</dt><dd>${String(v || '—')}</dd></div>`
                ).join('');

            // Action buttons
            document.getElementById('em-actions').innerHTML = `
                <a href="${data.actions.manage_url}"
                   class="em-btn-primary" style="background:${cfg.bg}">
                    <span class="material-symbols-outlined" style="font-size:14px">open_in_new</span>
                    Manage
                </a>
                <button onclick="deleteEntityFromModal('${data.actions.delete_type}',${data.actions.delete_id})"
                        class="em-btn-danger">
                    <span class="material-symbols-outlined" style="font-size:14px">delete</span>
                    Delete
                </button>
            `;

            document.getElementById('entityModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } catch (e) {
            console.error(e);
            alert('Server error loading entity details.');
        }
    }

    function closeEntityModal() {
        document.getElementById('entityModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    async function deleteEntityFromModal(type, id) {
        const confirmed = await pnModal({
            title: `Delete ${type}`,
            message: `Permanently remove this ${type}? This cannot be undone.`,
            type: 'warning', confirmText: 'Delete', cancelText: 'Cancel', isDanger: true
        });
        if (!confirmed) return;
        try {
            if (type === 'User') { window.location.href = `${URLROOT}/admin/delete_user/${id}`; return; }
            const map = { Company: 'delete_company', Post: 'delete_post', Job: 'delete_job' };
            const res = await fetch(`${URLROOT}/admin/${map[type]}/${id}`, { method: 'POST' });
            const data = await res.json();
            if (data.success) { closeEntityModal(); setTimeout(() => location.reload(), 400); }
            else { alert('Could not delete record.'); }
        } catch (e) { alert('Server error.'); }
    }

</script>

<?php require ADMINROOT . '/frontend/views/layouts/admin_footer.php'; ?>