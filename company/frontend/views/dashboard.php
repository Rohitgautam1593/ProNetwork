<?php require COMPANYROOT . '/frontend/views/layouts/header.php'; ?>
<?php require COMPANYROOT . '/frontend/views/layouts/navbar.php'; ?>
<?php
    $company = $data['company'] ?? [];
    $jobs = $data['jobs'] ?? [];
    $isOwner = $data['isOwner'] ?? false;
    
    $companyName = $company['company_name'] ?? 'Company';
    $industry = $company['industry'] ?? 'Industry not set';
    $description = $company['description'] ?? 'No company description available.';
    $website = $company['website'] ?? '#';
    $size = $company['size'] ?? 'Size not set';
    $founded = $company['founded_year'] ?? 'N/A';
    $followers = (int)($company['followers'] ?? 0);
    $logo = !empty($company['logo']) ? URLROOT . '/uploads/companies/' . $company['logo'] : 'https://ui-avatars.com/api/?name=' . urlencode($companyName);
?>
<!-- Dedicated Enterprise Employer Console Main Wrapper -->
<main class="pt-16 pb-24 bg-slate-900 min-h-screen text-slate-100 font-sans selection:bg-indigo-500 selection:text-white relative overflow-x-hidden">

    <!-- Ambient Glowing Accents -->
    <div class="absolute top-0 left-1/3 w-96 h-96 bg-indigo-500/10 rounded-full blur-[150px] pointer-events-none"></div>
    <div class="absolute top-1/2 right-10 w-96 h-96 bg-blue-500/10 rounded-full blur-[150px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 relative z-10">
        
        <!-- Stunning Premium Hero Banner -->
        <div class="bg-slate-800/90 backdrop-blur-xl rounded-3xl overflow-hidden shadow-2xl border border-slate-700/60 mb-8 transition-all">
            
            <!-- Cover Header Canvas -->
            <div class="relative h-60 w-full bg-gradient-to-r from-slate-950 via-indigo-950 to-slate-950 overflow-hidden flex items-center justify-center">
                <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_40%_40%,#6366f1_1px,transparent_1px)] bg-[size:24px_24px]"></div>
                <div class="absolute top-1/2 left-1/3 -translate-y-1/2 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl animate-pulse duration-1000"></div>

                <!-- Live Tag Floating inside banner -->
                <div class="absolute top-4 right-4 bg-white/10 backdrop-blur-md border border-white/10 px-3 py-1.5 rounded-full text-[11px] font-medium tracking-wide text-indigo-200 flex items-center gap-1.5 shadow-inner">
                    <span class="flex h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                    <span>Employer Console Active</span>
                </div>

                <!-- Embedded Logo Shell Frame -->
                <div class="absolute -bottom-12 left-8 p-2 bg-slate-800 rounded-3xl shadow-xl border border-slate-700/80">
                    <div class="w-28 h-28 sm:w-32 sm:h-32 bg-white flex items-center justify-center rounded-2xl overflow-hidden shadow-inner">
                        <img alt="<?php echo htmlspecialchars($companyName); ?> Logo" class="w-full h-full object-contain p-2 transform hover:scale-105 transition-transform duration-300" src="<?php echo htmlspecialchars($logo); ?>"/>
                    </div>
                </div>
            </div>

            <!-- Header Content Stack Stack -->
            <div class="pt-16 pb-8 px-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h1 class="text-3xl font-black text-white tracking-tight"><?php echo htmlspecialchars($companyName); ?></h1>
                            <span class="bg-indigo-500/20 text-indigo-300 text-xs font-bold px-3 py-0.5 rounded-full border border-indigo-500/30 flex items-center gap-1 shadow-2xs">
                                <span class="material-symbols-outlined text-[13px] text-indigo-400">verified</span> Verified Firm Workspace
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-300 max-w-3xl line-clamp-2 leading-relaxed font-normal"><?php echo htmlspecialchars($description); ?></p>
                        
                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400 pt-1 font-medium">
                            <span class="flex items-center gap-1 text-indigo-400 font-bold">
                                <span class="material-symbols-outlined text-[14px]">category</span> <?php echo htmlspecialchars($industry); ?>
                            </span>
                            <span>&bull;</span>
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">groups</span> <?php echo number_format($followers); ?> followers
                            </span>
                            <span>&bull;</span>
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">domain</span> <?php echo htmlspecialchars($size); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Direct Action Elements -->
                    <div class="flex flex-wrap items-center gap-3 shrink-0 w-full sm:w-auto justify-start sm:justify-end">
                        <?php if ($isOwner): ?>
                            <button onclick="openEditCompanyModal()" class="w-full sm:w-auto bg-slate-700/80 hover:bg-slate-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 border border-slate-600/80 shadow-sm active:scale-95">
                                <span class="material-symbols-outlined text-[16px] text-indigo-400">edit_square</span>
                                <span>Edit Profile Info</span>
                            </button>
                            
                            <button onclick="openAddJobModal()" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-md shadow-indigo-600/20 active:scale-95">
                                <span class="material-symbols-outlined text-[16px]">add_circle</span>
                                <span>Post New Job Opening</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sleek Navigation Tabs Layout -->
                <div class="mt-8 pt-4 border-t border-slate-700/60 flex gap-2 overflow-x-auto pb-2 custom-scrollbar">
                    <button data-tab="home" class="px-5 py-2.5 text-xs font-bold bg-indigo-600 text-white rounded-full shrink-0 transition-all flex items-center gap-1.5 shadow-md shadow-indigo-600/20">
                        <span class="material-symbols-outlined text-[16px]">home</span> Primary Portal
                    </button>
                    <button data-tab="about" class="px-5 py-2.5 text-xs font-bold text-slate-400 hover:text-slate-200 hover:bg-slate-700/50 rounded-full shrink-0 transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">info</span> Detailed Spec
                    </button>
                    <button data-tab="jobs" class="px-5 py-2.5 text-xs font-bold text-slate-400 hover:text-slate-200 hover:bg-slate-700/50 rounded-full shrink-0 transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">work</span> Managed Positions 
                        <span class="ml-1 bg-slate-700 text-indigo-300 px-2 py-0.5 text-[10px] rounded-full font-black"><?php echo count($jobs); ?></span>
                    </button>

                    <?php if ($isOwner): ?>
                        <button data-tab="portal" class="px-5 py-2.5 text-xs font-black bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500/20 border border-indigo-500/30 rounded-full shrink-0 transition-all flex items-center gap-1.5 ml-auto">
                            <span class="material-symbols-outlined text-[16px]">admin_panel_settings</span>
                            <span>Applicant Control Center</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Structure Sectioning Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-8">
            
            <!-- Core Work Viewports -->
            <div class="min-w-0">

                <!-- ════ TAB 1: HOME PORTAL VIEWPORT ════ -->
                <div id="tab-home" class="tab-content block space-y-6 animate-in fade-in duration-300">
                    
                    <!-- Quick About Summary Block -->
                    <div class="bg-slate-800/80 backdrop-blur-md p-6 rounded-2xl shadow-xl border border-slate-700/60 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-8 opacity-5 text-white pointer-events-none">
                            <span class="material-symbols-outlined text-8xl">corporate_fare</span>
                        </div>
                        <h2 class="text-xs font-black uppercase tracking-widest text-indigo-400 mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">corporate_fare</span> Corporate Profile Core
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal whitespace-pre-wrap relative z-10"><?php echo htmlspecialchars($description); ?></p>
                        <button data-tab-link="about" class="mt-4 text-xs font-bold text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 transition-colors group">
                            <span>Inspect Complete Attributes Scope</span>
                            <span class="material-symbols-outlined text-[14px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </button>
                    </div>

                    <!-- Direct Quick Add Callout Strip -->
                    <?php if ($isOwner): ?>
                        <div class="bg-gradient-to-r from-indigo-900 via-indigo-950 to-slate-900 rounded-2xl p-6 text-white shadow-xl border border-indigo-500/30 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative overflow-hidden">
                            <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
                            <div class="space-y-1 relative z-10 min-w-0 flex-1">
                                <span class="bg-indigo-400/20 text-indigo-300 text-[10px] font-black tracking-widest uppercase px-2.5 py-0.5 rounded-md border border-indigo-400/30 inline-block">Direct Sourcing Pipeline</span>
                                <h3 class="text-base font-bold tracking-tight text-white mt-1">Accelerate talent intake operations</h3>
                                <p class="text-xs text-indigo-200/80">Configure structured recruitment listings directly over platform pipelines without proxy delay overheads.</p>
                            </div>
                            <button onclick="openAddJobModal()" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs px-5 py-3 rounded-xl transition-all shrink-0 shadow-md shadow-indigo-600/20 active:scale-95 text-center relative z-10 flex items-center justify-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px]">add</span>
                                <span>Create New Listing</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Indexed Listings Shortlist Row -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between px-1">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Recently Authored Openings</h3>
                            <button data-tab-link="jobs" class="text-[11px] font-bold text-indigo-400 hover:underline">View complete roster (<?php echo count($jobs); ?>)</button>
                        </div>
                        
                        <?php if (!empty($jobs)): ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php foreach (array_slice($jobs, 0, 4) as $job): ?>
                                    <div class="bg-slate-800/60 p-4 rounded-xl border border-slate-700/50 hover:border-slate-600 transition-all flex flex-col justify-between gap-3">
                                        <div>
                                            <div class="flex items-center justify-between gap-2 mb-1">
                                                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-slate-900 text-indigo-300 border border-slate-700/80"><?php echo htmlspecialchars($job['job_type']); ?></span>
                                                <span class="text-[10px] text-slate-400 font-medium"><?php echo htmlspecialchars($job['experience_level']); ?> level</span>
                                            </div>
                                            <h4 class="text-xs font-bold text-white line-clamp-1"><?php echo htmlspecialchars($job['title']); ?></h4>
                                            <p class="text-[11px] text-slate-400 mt-1 line-clamp-2"><?php echo htmlspecialchars($job['description']); ?></p>
                                        </div>
                                        <div class="pt-2 border-t border-slate-700/40 flex items-center justify-between text-[11px] text-slate-400">
                                            <span class="flex items-center gap-1 truncate max-w-[140px]"><span class="material-symbols-outlined text-[12px] text-indigo-400">location_on</span> <?php echo htmlspecialchars($job['location'] ?? 'Remote'); ?></span>
                                            <strong class="text-indigo-400"><?php echo (int)($job['applicant_count'] ?? 0); ?> Applicants</strong>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="bg-slate-800/40 rounded-2xl p-8 text-center border border-slate-700/40">
                                <p class="text-xs text-slate-500 italic">No job openings created to broadcast yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ════ TAB 2: DETAILED SPECIFICATION VIEWPORT ════ -->
                <div id="tab-about" class="tab-content hidden space-y-6 animate-in fade-in duration-300">
                    <div class="bg-slate-800/80 backdrop-blur-md p-6 rounded-2xl shadow-xl border border-slate-700/60 space-y-6">
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-wider text-indigo-400 mb-2">Scope Identification Specifications</h3>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal whitespace-pre-wrap"><?php echo htmlspecialchars($description); ?></p>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-700/60">
                            <div class="bg-slate-900/60 p-3.5 rounded-xl border border-slate-700/40">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 block">External Target Link</span>
                                <a href="<?php echo htmlspecialchars($website); ?>" target="_blank" class="text-xs font-bold text-indigo-400 hover:underline truncate block mt-0.5"><?php echo htmlspecialchars(str_replace(['https://','http://'], '', $website)); ?></a>
                            </div>
                            <div class="bg-slate-900/60 p-3.5 rounded-xl border border-slate-700/40">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 block">Industry Node</span>
                                <span class="text-xs font-bold text-slate-200 truncate block mt-0.5"><?php echo htmlspecialchars($industry); ?></span>
                            </div>
                            <div class="bg-slate-900/60 p-3.5 rounded-xl border border-slate-700/40">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 block">Headcount Volume</span>
                                <span class="text-xs font-bold text-slate-200 truncate block mt-0.5"><?php echo htmlspecialchars($size); ?></span>
                            </div>
                            <div class="bg-slate-900/60 p-3.5 rounded-xl border border-slate-700/40">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 block">Founded Flag</span>
                                <span class="text-xs font-bold text-slate-200 truncate block mt-0.5"><?php echo htmlspecialchars($founded); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ════ TAB 3: MANAGED POSITIONS ROSTER VIEWPORT ════ -->
                <div id="tab-jobs" class="tab-content hidden space-y-4 animate-in fade-in duration-300">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-700/60 px-1">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Indexed Managed Targets (<?php echo count($jobs); ?>)</h3>
                        <?php if ($isOwner): ?>
                            <button onclick="openAddJobModal()" class="text-xs font-bold text-indigo-400 hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">add_circle</span> Post Opening
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($jobs)): ?>
                        <div class="space-y-3.5">
                            <?php foreach ($jobs as $job): ?>
                                <?php 
                                    $isClosed = ($job['status'] === 'Closed');
                                    $hasLimit = !empty($job['applicant_limit']);
                                    $count = (int)($job['applicant_count'] ?? 0);
                                    $limit = (int)($job['applicant_limit'] ?? 0);
                                    $limitReached = ($hasLimit && $count >= $limit);
                                ?>
                                <div class="bg-slate-800/80 backdrop-blur-md p-5 rounded-2xl shadow-xl border border-slate-700/60 hover:border-slate-500/60 transition-all flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                    <div class="space-y-2 min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="text-sm font-bold text-white truncate"><?php echo htmlspecialchars($job['title']); ?></h4>
                                            
                                            <?php if ($isClosed || $limitReached): ?>
                                                <span class="bg-rose-500/10 text-rose-400 text-[10px] font-black px-2 py-0.5 rounded border border-rose-500/20 uppercase tracking-widest">Closed / Full</span>
                                            <?php else: ?>
                                                <span class="bg-emerald-500/10 text-emerald-400 text-[10px] font-black px-2 py-0.5 rounded border border-emerald-500/20 uppercase tracking-widest">Actively Sourcing</span>
                                            <?php endif; ?>

                                            <span class="bg-slate-900 text-indigo-300 text-[10px] font-bold px-2.5 py-0.5 rounded-md border border-slate-700"><?php echo htmlspecialchars($job['job_type']); ?></span>
                                        </div>

                                        <p class="text-xs text-slate-300 line-clamp-2 leading-relaxed font-normal"><?php echo htmlspecialchars($job['description']); ?></p>
                                        
                                        <div class="flex flex-wrap items-center gap-4 text-[11px] text-slate-400 pt-1 font-medium">
                                            <span class="flex items-center gap-1 text-slate-300"><span class="material-symbols-outlined text-[14px] text-indigo-400">location_on</span> <?php echo htmlspecialchars($job['location'] ?? 'Remote Area'); ?></span>
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px] text-indigo-400">payments</span> <?php echo htmlspecialchars($job['salary_range'] ?? 'Competitive spec'); ?></span>
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px] text-indigo-400">school</span> <?php echo htmlspecialchars($job['experience_level']); ?> spec</span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-stretch sm:items-end gap-2 shrink-0 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-700/40 sm:border-transparent">
                                        <?php if ($hasLimit && !$isClosed): ?>
                                            <span class="text-[10px] text-slate-400 font-bold text-right block">
                                                Intake Volume: <strong class="text-indigo-400"><?php echo $count; ?> / <?php echo $limit; ?></strong> caps
                                            </span>
                                        <?php else: ?>
                                            <span class="text-[10px] text-slate-400 font-bold text-right block">
                                                Total Intakes: <strong class="text-indigo-400"><?php echo $count; ?></strong> logged
                                            </span>
                                        <?php endif; ?>

                                        <?php if ($isOwner): ?>
                                            <button onclick="selectTabDirectPortal(<?php echo $job['job_id']; ?>)" class="w-full sm:w-auto bg-slate-900 hover:bg-slate-950 text-indigo-300 font-bold text-xs px-4 py-2 rounded-xl border border-slate-700 shadow-2xs transition-all inline-flex items-center justify-center gap-1 active:scale-95">
                                                <span class="material-symbols-outlined text-[14px]">tune</span> <span>Configure / Review</span>
                                            </button>
                                        <?php else: ?>
                                            <a href="<?php echo URLROOT; ?>/job/apply/<?php echo $job['job_id']; ?>" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs px-5 py-2.5 rounded-xl text-center shadow-md shadow-indigo-600/20 transition-all inline-block active:scale-95">
                                                Apply Target
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="bg-slate-800/40 rounded-2xl p-12 text-center border border-slate-700/40">
                            <span class="material-symbols-outlined text-slate-600 text-4xl block mb-2">work_off</span>
                            <p class="text-xs text-slate-400 font-medium">No open hiring listings currently exposed on this pipeline gateway.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ════ TAB 4: EXCLUSIVE APPLICANT CONTROL CENTER VIEWPORT ════ -->
                <?php if ($isOwner): ?>
                    <div id="tab-portal" class="tab-content hidden space-y-6 animate-in fade-in duration-300">
                        
                        <!-- Operator Management Console Banner -->
                        <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-blue-700 text-white rounded-3xl p-6 shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative overflow-hidden border border-indigo-400/20">
                            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_70%_70%,white_1px,transparent_1px)] bg-[size:16px_16px]"></div>
                            <div class="relative z-10 space-y-1 min-w-0 flex-1">
                                <span class="bg-white/10 backdrop-blur-md text-white text-[9px] font-black tracking-widest uppercase px-2.5 py-0.5 rounded-full border border-white/10 block w-fit">Authorized Telemetry Control Node</span>
                                <h3 class="text-lg font-black tracking-tight text-white">Central Listing Controller Suite</h3>
                                <p class="text-xs text-indigo-100/90 font-normal leading-relaxed">Adjust individual post availability limits, switch intake channels asynchronously, inspect submitted resumes, and configure live platform locks.</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 relative z-10">
                                <button onclick="openAddJobModal()" class="bg-white text-indigo-700 hover:bg-indigo-50 font-bold text-xs px-4 py-3 rounded-xl transition-all shadow-md active:scale-95 inline-flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px]">add_circle</span> Post Opening
                                </button>
                            </div>
                        </div>

                        <!-- Config Dials Table Matrix -->
                        <div class="bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-xl border border-slate-700/60 overflow-hidden">
                            <div class="p-4 bg-slate-800/90 border-b border-slate-700/60 flex items-center justify-between">
                                <span class="text-xs font-black uppercase tracking-wider text-indigo-400">Inline Target Allocations & Diagnostic Controls</span>
                                <span class="text-[10px] font-medium text-slate-400">Synchronized background writes</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-700/40 text-[10px] font-black text-slate-400 uppercase tracking-wider bg-slate-900/40">
                                            <th class="p-4">Target Title String & Area</th>
                                            <th class="p-4">Intake Status</th>
                                            <th class="p-4">Applicant Cap Value</th>
                                            <th class="p-4 text-center">Telemetry Status</th>
                                            <th class="p-4 text-right">Roster Diagnostic Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-700/40 text-xs font-medium text-slate-200">
                                        <?php if (!empty($jobs)): ?>
                                            <?php foreach ($jobs as $job): ?>
                                                <?php 
                                                    $jid = $job['job_id'];
                                                    $isClosed = ($job['status'] === 'Closed');
                                                ?>
                                                <tr class="hover:bg-slate-700/30 transition-colors" id="row-job-<?php echo $jid; ?>">
                                                    <td class="p-4 max-w-[200px]">
                                                        <strong class="text-white block truncate"><?php echo htmlspecialchars($job['title']); ?></strong>
                                                        <span class="text-[11px] text-slate-400 block truncate"><?php echo htmlspecialchars($job['location'] ?? 'Remote Gateway'); ?></span>
                                                    </td>
                                                    
                                                    <td class="p-4">
                                                        <select id="status-<?php echo $jid; ?>" class="text-xs bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 font-bold text-slate-200 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-colors">
                                                            <option value="Live" <?php echo !$isClosed ? 'selected' : ''; ?>>Live Active</option>
                                                            <option value="Closed" <?php echo $isClosed ? 'selected' : ''; ?>>Closed Full</option>
                                                        </select>
                                                    </td>

                                                    <td class="p-4">
                                                        <div class="flex items-center gap-1.5 max-w-[120px]">
                                                            <input type="number" id="limit-<?php echo $jid; ?>" value="<?php echo htmlspecialchars($job['applicant_limit'] ?? ''); ?>" placeholder="No Cap" class="w-20 text-xs bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 font-bold text-slate-200 placeholder:text-slate-600 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-colors">
                                                            <button onclick="saveJobConfig(<?php echo $jid; ?>)" class="p-1.5 text-indigo-400 hover:bg-slate-700 rounded-lg transition-colors" title="Commit parameter">
                                                                <span class="material-symbols-outlined text-[15px] block">save</span>
                                                            </button>
                                                        </div>
                                                    </td>

                                                    <td class="p-4 text-center font-bold">
                                                        <span class="px-2.5 py-1 rounded-md text-[11px] <?php echo ((int)$job['applicant_count'] > 0) ? 'bg-indigo-500/10 text-indigo-300 border border-indigo-500/20' : 'bg-slate-900 text-slate-500'; ?>">
                                                            <?php echo (int)($job['applicant_count'] ?? 0); ?> Applied
                                                        </span>
                                                    </td>

                                                    <td class="p-4 text-right">
                                                        <button onclick="viewCandidates(<?php echo $jid; ?>, '<?php echo addslashes(htmlspecialchars($job['title'])); ?>')" class="bg-slate-900 hover:bg-slate-950 border border-slate-700 text-indigo-300 font-bold px-3.5 py-1.5 rounded-xl text-[11px] transition-all inline-flex items-center gap-1 shadow-2xs active:scale-95">
                                                            <span class="material-symbols-outlined text-[14px]">groups</span> <span>Review Submissions</span>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="p-8 text-center text-slate-500 italic">No job listing variables configured inline yet.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Global Environment Metadata Sidebar Stack -->
            <aside class="space-y-6">
                
                <!-- Spec Block Card -->
                <div class="bg-slate-800/80 backdrop-blur-md p-5 rounded-2xl shadow-xl border border-slate-700/60 space-y-4">
                    <h3 class="text-xs font-black text-indigo-400 uppercase tracking-widest flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[15px]">data_object</span> Sourcing Telemetry
                    </h3>
                    
                    <div class="space-y-3.5 text-xs">
                        <div class="border-b border-slate-700/40 pb-2.5">
                            <span class="text-[9px] font-black uppercase text-slate-500 block tracking-wider">Target Domain Reference</span>
                            <a class="text-indigo-400 font-bold hover:underline truncate block mt-0.5" href="<?php echo htmlspecialchars($website); ?>" target="_blank"><?php echo htmlspecialchars($website === '#' ? 'Domain Unassigned' : $website); ?></a>
                        </div>
                        <div class="border-b border-slate-700/40 pb-2.5">
                            <span class="text-[9px] font-black uppercase text-slate-500 block tracking-wider">Assigned Industry Spec</span>
                            <p class="font-bold text-slate-200 mt-0.5 truncate"><?php echo htmlspecialchars($industry); ?></p>
                        </div>
                        <div class="border-b border-slate-700/40 pb-2.5">
                            <span class="text-[9px] font-black uppercase text-slate-500 block tracking-wider">Workforce Scale</span>
                            <p class="font-bold text-slate-200 mt-0.5"><?php echo htmlspecialchars($size); ?></p>
                            <p class="text-[10px] text-indigo-300 font-black mt-1 bg-indigo-500/10 p-1.5 rounded-lg border border-indigo-500/20 block"><?php echo count($jobs); ?> target job channels</p>
                        </div>
                        <div>
                            <span class="text-[9px] font-black uppercase text-slate-500 block tracking-wider">Headquarters Node Flag</span>
                            <p class="font-bold text-slate-200 mt-0.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px] text-slate-400">public</span> Standard Production Cloud
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Live Quick Diagnostics Links Strip -->
                <?php if ($isOwner): ?>
                    <div class="bg-slate-800/80 backdrop-blur-md p-5 rounded-2xl shadow-xl border border-slate-700/60 space-y-3">
                        <h3 class="text-xs font-black text-indigo-400 uppercase tracking-widest flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px]">bolt</span> Quick Dashboard Actions
                        </h3>
                        <button onclick="openAddJobModal()" class="w-full bg-slate-900 hover:bg-slate-950 text-indigo-300 font-bold p-3 rounded-xl border border-slate-700 text-xs text-left transition-all flex items-center justify-between group active:scale-95">
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-indigo-400">add_task</span> Post New Listing
                            </span>
                            <span class="material-symbols-outlined text-[14px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                        </button>
                        <button onclick="openEditCompanyModal()" class="w-full bg-slate-900 hover:bg-slate-950 text-slate-300 font-bold p-3 rounded-xl border border-slate-700 text-xs text-left transition-all flex items-center justify-between group active:scale-95">
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-slate-400">edit_attributes</span> Sync Bio String
                            </span>
                            <span class="material-symbols-outlined text-[14px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                        </button>
                    </div>
                <?php endif; ?>

            </aside>
        </div>
    </div>

</main>

<!-- ════ PREMIUM VIEWPORT-SAFE EDIT PROFILE DATA MODAL ════ -->
<?php if ($isOwner): ?>
<div id="editCompanyModal" class="fixed inset-0 z-[250] hidden overflow-y-auto font-sans" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" onclick="closeEditCompanyModal()"></div>

    <div class="min-h-screen px-4 py-8 flex items-center justify-center relative z-10">
        <div class="relative w-full max-w-xl bg-slate-900 text-white rounded-3xl shadow-2xl overflow-hidden flex flex-col border border-slate-700 max-h-[calc(100vh-4rem)] animate-in fade-in zoom-in-95 duration-200">
            
            <div class="p-6 border-b border-slate-800 flex items-center justify-between shrink-0 bg-slate-950/50">
                <div>
                    <h3 class="text-base font-black text-white tracking-tight flex items-center gap-2">
                        <span class="material-symbols-outlined text-indigo-400 text-[18px]">settings</span> Edit Enterprise Scope
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Configure public targeting string variables.</p>
                </div>
                <button onclick="closeEditCompanyModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            </div>

            <form action="<?php echo URLROOT; ?>/company/update_profile" method="POST" class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Executive Summary Description</label>
                    <textarea name="description" rows="4" class="w-full text-xs bg-slate-950 border border-slate-700 rounded-xl p-3 text-slate-100 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"><?php echo htmlspecialchars($company['description'] ?? ''); ?></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Target Web Domain URL</label>
                        <input type="text" name="website" value="<?php echo htmlspecialchars($company['website'] ?? ''); ?>" placeholder="https://domain.com" class="w-full text-xs bg-slate-950 border border-slate-700 rounded-xl p-3 text-slate-100 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Industry Spec Node</label>
                        <input type="text" name="industry" value="<?php echo htmlspecialchars($company['industry'] ?? ''); ?>" placeholder="e.g. Analytics, Automation" class="w-full text-xs bg-slate-950 border border-slate-700 rounded-xl p-3 text-slate-100 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Workforce Scale Cluster</label>
                    <input type="text" name="size" value="<?php echo htmlspecialchars($company['size'] ?? ''); ?>" placeholder="e.g. 51-200 nodes" class="w-full text-xs bg-slate-950 border border-slate-700 rounded-xl p-3 text-slate-100 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <div class="pt-4 border-t border-slate-800 flex justify-end gap-2">
                    <button type="button" onclick="closeEditCompanyModal()" class="px-5 py-2.5 text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl transition-all">Abort</button>
                    <button type="submit" class="px-5 py-2.5 text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-md shadow-indigo-600/20 transition-all">Commit Save</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ════ NEW PREMIUM VIEWPORT-SAFE POST JOB OPENING MODAL ════ -->
<div id="addJobModal" class="fixed inset-0 z-[250] hidden overflow-y-auto font-sans" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" onclick="closeAddJobModal()"></div>

    <div class="min-h-screen px-4 py-8 flex items-center justify-center relative z-10">
        <div class="relative w-full max-w-xl bg-slate-900 text-white rounded-3xl shadow-2xl overflow-hidden flex flex-col border border-slate-700 max-h-[calc(100vh-4rem)] animate-in fade-in zoom-in-95 duration-200">
            
            <div class="p-6 border-b border-slate-800 flex items-center justify-between shrink-0 bg-slate-950/50">
                <div>
                    <h3 class="text-base font-black text-white tracking-tight flex items-center gap-2">
                        <span class="material-symbols-outlined text-indigo-400 text-[18px]">add_task</span> Post Job Opening Channel
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Author structured pipeline configuration inline.</p>
                </div>
                <button onclick="closeAddJobModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            </div>

            <form action="<?php echo URLROOT; ?>/company/add_job" method="POST" class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Target Opening Title String <span class="text-rose-400">*</span></label>
                    <input type="text" name="title" required placeholder="e.g. Senior PHP Infrastructure Architect" class="w-full text-xs bg-slate-950 border border-slate-700 rounded-xl p-3 text-slate-100 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Employment Matrix Type</label>
                        <select name="job_type" class="w-full text-xs bg-slate-950 border border-slate-700 rounded-xl p-3 text-slate-100 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="Full-time">Full-time Node</option>
                            <option value="Part-time">Part-time Band</option>
                            <option value="Contract">Contract Scope</option>
                            <option value="Internship">Internship Intake</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Target Experience Clearance</label>
                        <select name="experience_level" class="w-full text-xs bg-slate-950 border border-slate-700 rounded-xl p-3 text-slate-100 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="Entry">Entry Cluster</option>
                            <option value="Mid">Mid Level Engineer</option>
                            <option value="Senior">Senior Architecture</option>
                            <option value="Director">Executive Spec</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Target Cluster Geography</label>
                        <input type="text" name="location" placeholder="e.g. Remote / On-site City" class="w-full text-xs bg-slate-950 border border-slate-700 rounded-xl p-3 text-slate-100 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Salary Disclosed Parameter</label>
                        <input type="text" name="salary_range" placeholder="e.g. ₹8 LPA - ₹14 LPA" class="w-full text-xs bg-slate-950 border border-slate-700 rounded-xl p-3 text-slate-100 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Applicant Cap Volume Limit (Optional)</label>
                    <input type="number" name="applicant_limit" placeholder="Leave empty for unconstrained auto intake" class="w-full text-xs bg-slate-950 border border-slate-700 rounded-xl p-3 text-slate-100 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Complete Job Responsibilities & Stack Requirements <span class="text-rose-400">*</span></label>
                    <textarea name="description" rows="5" required placeholder="Enumerate target telemetry scopes, cloud framework libraries, and integration duties..." class="w-full text-xs bg-slate-950 border border-slate-700 rounded-xl p-3 text-slate-100 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-800 flex justify-end gap-2">
                    <button type="button" onclick="closeAddJobModal()" class="px-5 py-2.5 text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl transition-all">Abort Action</button>
                    <button type="submit" class="px-5 py-2.5 text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-md shadow-indigo-600/20 transition-all">Publish Job Opening</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ════ PREMIUM DIAGNOSTIC CANDIDATES INSPECTOR MODAL ════ -->
<div id="candidatesModal" class="fixed inset-0 z-[250] hidden overflow-y-auto font-sans" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" onclick="closeCandidatesModal()"></div>

    <div class="min-h-screen px-4 py-8 flex items-center justify-center relative z-10">
        <div class="relative w-full max-w-3xl bg-slate-900 text-white rounded-3xl shadow-2xl overflow-hidden flex flex-col border border-slate-700 max-h-[calc(100vh-4rem)] animate-in fade-in zoom-in-95 duration-200">
            
            <div class="p-6 border-b border-slate-800 flex items-center justify-between shrink-0 bg-slate-950/50">
                <div class="min-w-0 pr-4">
                    <h3 class="text-base font-black tracking-tight text-white truncate flex items-center gap-2" id="cand-job-title">
                        <span class="material-symbols-outlined text-indigo-400 text-[18px]">groups</span> Sourced Intake Submissions
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Candidate profiles with attached credentials safely decoded.</p>
                </div>
                <button onclick="closeCandidatesModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            </div>

            <div id="candidates-wrap" class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar">
                <!-- Rendered asynchronously -->
            </div>

            <div class="p-4 border-t border-slate-800 flex justify-end shrink-0 bg-slate-950/30">
                <button onclick="closeCandidatesModal()" class="px-5 py-2 text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl transition-all">Conclude View</button>
            </div>

        </div>
    </div>
</div>
<?php endif; ?>

<!-- System Telemetry UI Interfacing Scripts -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // ── Dedicated Workspace Tab Switcher Logic ──
    const tabs = document.querySelectorAll('[data-tab]');
    const contents = document.querySelectorAll('.tab-content');

    function selectTab(target) {
        tabs.forEach(t => {
            const isP = (t.getAttribute('data-tab') === 'portal');
            t.className = "px-5 py-2.5 text-xs font-bold shrink-0 transition-all flex items-center gap-1.5 rounded-full";
            if (isP) {
                t.classList.add('bg-indigo-500/10', 'text-indigo-400', 'hover:bg-indigo-500/20', 'border', 'border-indigo-500/30', 'ml-auto');
            } else {
                t.classList.add('text-slate-400', 'hover:text-slate-200', 'hover:bg-slate-700/50');
            }
        });

        const activeTabButton = document.querySelector(`[data-tab="${target}"]`);
        if (activeTabButton) {
            if (target === 'portal') {
                activeTabButton.className = "px-5 py-2.5 text-xs font-black shrink-0 transition-all flex items-center gap-1.5 rounded-full bg-indigo-600 text-white shadow-md shadow-indigo-600/20 ml-auto";
            } else {
                activeTabButton.className = "px-5 py-2.5 text-xs font-bold shrink-0 transition-all flex items-center gap-1.5 rounded-full bg-indigo-600 text-white shadow-md shadow-indigo-600/20";
            }
        }

        contents.forEach(c => c.classList.add('hidden'));
        
        let matched = document.getElementById(`tab-${target}`);
        if (target === 'about') {
            const allContents = document.querySelectorAll('.tab-content');
            allContents.forEach(el => {
                if(el.id === 'tab-about' || (el.querySelector('h3') && el.querySelector('h3').textContent.includes('Identification'))) {
                    matched = el;
                }
            });
        }
        if (matched) {
            matched.classList.remove('hidden');
        }
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();
            selectTab(tab.getAttribute('data-tab'));
        });
    });

    document.querySelectorAll('[data-tab-link]').forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            selectTab(trigger.getAttribute('data-tab-link'));
        });
    });
});

// ── Secure Overlay Triggers ──
function openEditCompanyModal() {
    const m = document.getElementById('editCompanyModal');
    if(m) { m.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
}

function closeEditCompanyModal() {
    const m = document.getElementById('editCompanyModal');
    if(m) { m.classList.add('hidden'); document.body.style.overflow = ''; }
}

function openAddJobModal() {
    const m = document.getElementById('addJobModal');
    if(m) { m.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
}

function closeAddJobModal() {
    const m = document.getElementById('addJobModal');
    if(m) { m.classList.add('hidden'); document.body.style.overflow = ''; }
}

function closeCandidatesModal() {
    const m = document.getElementById('candidatesModal');
    if(m) { m.classList.add('hidden'); document.body.style.overflow = ''; }
}

// ── Direct Roster Trigger Hook ──
function selectTabDirectPortal(jobId) {
    // Jump straight to portal panel to manage job controls inline
    const trigger = document.querySelector('[data-tab="portal"]');
    if (trigger) {
        trigger.click();
        setTimeout(() => {
            const row = document.getElementById(`row-job-${jobId}`);
            if (row) {
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                row.classList.add('bg-indigo-500/20');
                setTimeout(() => row.classList.remove('bg-indigo-500/20'), 2000);
            }
        }, 150);
    }
}

// ── Inline Remote Param Update Writes ──
async function saveJobConfig(jobId) {
    const statusVal = document.getElementById(`status-${jobId}`).value;
    const limitInput = document.getElementById(`limit-${jobId}`).value;

    try {
        const formData = new FormData();
        formData.append('status', statusVal);
        formData.append('applicant_limit', limitInput);

        const res = await fetch(`${URLROOT}/company/update_job/${jobId}`, {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        
        if (data.success) {
            const btn = document.querySelector(`#limit-${jobId} + button`);
            if (btn) {
                const origHtml = btn.innerHTML;
                btn.innerHTML = `<span class="material-symbols-outlined text-[15px] text-emerald-400 block">check</span>`;
                setTimeout(() => btn.innerHTML = origHtml, 1200);
            }
        } else {
            alert('Failed saving config settings.');
        }
    } catch (e) {
        console.error(e);
        alert('Transmission array issue.');
    }
}

// ── Dynamic Remote Candidates Parsing & Render Array ──
async function viewCandidates(jobId, jobTitle) {
    const titleEl = document.getElementById('cand-job-title');
    const wrapEl = document.getElementById('candidates-wrap');
    const modalEl = document.getElementById('candidatesModal');
    
    if (!modalEl || !wrapEl) return;

    if (titleEl) titleEl.innerHTML = `<span class="material-symbols-outlined text-indigo-400 text-[18px]">groups</span> Sourced Intakes: ${escapeHtml(jobTitle)}`;
    wrapEl.innerHTML = `<div class="p-8 text-center"><div class="inline-block w-6 h-6 border-2 border-indigo-400 border-t-transparent rounded-full animate-spin"></div></div>`;
    
    modalEl.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    try {
        const res = await fetch(`${URLROOT}/company/get_applicants/${jobId}`);
        const data = await res.json();

        if (data.success && data.applicants && data.applicants.length > 0) {
            wrapEl.innerHTML = data.applicants.map(app => `
                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-start gap-3 min-w-0 flex-1">
                        <img class="w-10 h-10 rounded-full object-cover shrink-0 mt-0.5 border border-slate-700 shadow-2xs" 
                             src="${app.profile_pic ? `${URLROOT}/uploads/avatars/${app.profile_pic}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(app.first_name+' '+app.last_name)}&background=4f46e5&color=fff`}">
                        <div class="min-w-0 flex-1">
                            <strong class="text-xs text-white block truncate">${escapeHtml(app.first_name)} ${escapeHtml(app.last_name)}</strong>
                            <span class="text-[11px] text-slate-400 block truncate font-medium">${escapeHtml(app.email || 'No target tag attached')}</span>
                            ${app.phone ? `<span class="text-[10px] text-indigo-400 font-bold block mt-0.5 flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">call</span> ${escapeHtml(app.phone)}</span>` : ''}
                            ${app.cover_letter ? `<p class="text-[11px] text-slate-300 bg-slate-900/90 p-3 rounded-xl border border-slate-800 mt-2 font-normal leading-relaxed overflow-wrap-anywhere">${escapeHtml(app.cover_letter)}</p>` : ''}
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-stretch sm:items-end gap-2 shrink-0 w-full sm:w-auto">
                        <span class="text-[10px] text-slate-500 font-medium block text-right">${new Date(app.applied_at).toLocaleDateString(undefined, {month:'short', day:'numeric'})}</span>
                        ${app.resume_path ? `
                            <a href="${URLROOT}/uploads/resumes/${app.resume_path}" target="_blank" download
                               class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-3.5 py-2 rounded-xl text-[11px] shadow-md shadow-indigo-600/20 inline-flex items-center justify-center gap-1.5 transition-all active:scale-95">
                                <span class="material-symbols-outlined text-[14px]">download</span> <span>Candidate CV Payload</span>
                            </a>
                        ` : `<span class="text-[10px] text-slate-500 italic">No CV Attached</span>`}
                    </div>
                </div>
            `).join('');
        } else {
            wrapEl.innerHTML = `<div class="p-8 text-center text-slate-500 italic">No applicant form attachments registered on this target slot.</div>`;
        }
    } catch (e) {
        console.error(e);
        wrapEl.innerHTML = `<div class="p-8 text-center text-rose-400 font-bold">Network synchronization issue processing payload array.</div>`;
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
</script>

<?php require COMPANYROOT . '/frontend/views/layouts/footer.php'; ?>
