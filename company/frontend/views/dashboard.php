<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>
<?php
    $company = $data['company'] ?? [];
    $jobs = $data['jobs'] ?? [];
    $isOwner = $data['isOwner'] ?? false;
    $isFollowing = $data['isFollowing'] ?? false;
    
    $companyId = $company['company_id'] ?? 0;
    $companyName = $company['company_name'] ?? 'Company';
    $industry = $company['industry'] ?? 'Industry not set';
    $description = $company['description'] ?? 'No company description available.';
    $website = $company['website'] ?? '#';
    $size = $company['size'] ?? 'Size not set';
    $founded = $company['founded_year'] ?? 'N/A';
    $followers = (int)($company['followers'] ?? 0);
    $logo = pn_company_logo_url($company);
    $banner = pn_company_banner_url($company);
?>

<main class="bg-surface-container-low min-h-screen pt-4 pb-12">
    <div class="max-w-[1128px] mx-auto px-4">
        
        <!-- Premium Hero Banner -->
        <div class="bg-white rounded-xl border border-outline-variant/30 ambient-shadow overflow-hidden mb-6">
            
            <!-- Cover Photo -->
            <div class="h-48 md:h-64 bg-surface-variant relative overflow-hidden">
                <img src="<?php echo htmlspecialchars($banner); ?>" alt="" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-tr from-black/55 via-black/10 to-white/20"></div>
                <img src="<?php echo htmlspecialchars($logo); ?>" alt="" class="absolute right-8 top-1/2 -translate-y-1/2 w-28 h-28 md:w-40 md:h-40 object-contain opacity-90 drop-shadow-xl">
            </div>

            <!-- Header Content -->
            <div class="relative px-6 pb-6">
                <!-- Logo -->
                <div class="absolute -top-16 md:-top-20 p-1.5 bg-white rounded-lg shadow-sm">
                    <div class="w-24 h-24 md:w-32 md:h-32 bg-white flex items-center justify-center rounded overflow-hidden border border-outline-variant/30">
                        <img alt="<?php echo htmlspecialchars($companyName); ?> Logo" class="w-full h-full object-cover" src="<?php echo htmlspecialchars($logo); ?>"/>
                    </div>
                </div>

                <div class="pt-12 md:pt-16 flex flex-col md:flex-row justify-between items-start gap-4">
                    <div class="space-y-2">
                        <h1 class="text-2xl font-display-md text-on-surface tracking-tight"><?php echo htmlspecialchars($companyName); ?></h1>
                        <p class="text-sm font-body-md text-on-surface-variant max-w-2xl line-clamp-2"><?php echo htmlspecialchars($description); ?></p>
                        
                        <div class="flex flex-wrap items-center gap-2 text-sm text-secondary pt-1 font-body-md">
                            <span><?php echo htmlspecialchars($industry); ?></span>
                            <span class="text-outline-variant">&bull;</span>
                            <span class="font-semibold text-on-surface"><?php echo number_format($followers); ?> followers</span>
                            <span class="text-outline-variant">&bull;</span>
                            <span><?php echo htmlspecialchars($size); ?></span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <?php if ($isOwner): ?>
                            <button onclick="openEditCompanyModal()" class="text-primary font-label-md px-5 py-2 rounded-full border border-primary hover:bg-primary-fixed hover:border-primary-container transition-colors flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                Edit Page
                            </button>
                            <button onclick="openAddJobModal()" class="bg-primary text-white font-label-md px-5 py-2 rounded-full hover:bg-[#004182] transition-colors shadow-sm flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px]">add</span>
                                Post Job
                            </button>
                        <?php else: ?>
                            <button id="follow-btn" data-id="<?php echo $companyId; ?>" class="flex items-center justify-center gap-1.5 px-6 py-2 rounded-full font-label-lg transition-all duration-300 shadow-sm <?php echo $isFollowing ? 'border-2 border-outline text-secondary hover:border-outline-variant hover:bg-surface-variant' : 'bg-primary text-white hover:bg-[#004182]'; ?>">
                                <span class="material-symbols-outlined text-lg"><?php echo $isFollowing ? 'check' : 'add'; ?></span>
                                <span id="follow-text"><?php echo $isFollowing ? 'Following' : 'Follow'; ?></span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <div class="mt-6 border-t border-outline-variant/30 flex gap-1 overflow-x-auto pt-2">
                    <button type="button" data-tab="home" class="tab-btn px-4 py-3 text-sm font-label-lg text-primary border-b-2 border-primary transition-all whitespace-nowrap">
                        Home
                    </button>
                    <button type="button" data-tab="about" class="tab-btn px-4 py-3 text-sm font-label-lg text-secondary hover:text-on-surface hover:bg-surface-container transition-all border-b-2 border-transparent rounded-t-lg whitespace-nowrap">
                        About
                    </button>
                    <button type="button" data-tab="jobs" class="tab-btn px-4 py-3 text-sm font-label-lg text-secondary hover:text-on-surface hover:bg-surface-container transition-all border-b-2 border-transparent rounded-t-lg flex items-center gap-1.5 whitespace-nowrap">
                        Jobs
                        <span class="bg-surface-container-high text-on-surface-variant px-2 py-0.5 text-xs rounded-full font-medium"><?php echo count($jobs); ?></span>
                    </button>
                    <?php if ($isOwner): ?>
                        <button type="button" data-tab="portal" class="tab-btn px-4 py-3 text-sm font-label-lg text-secondary hover:text-on-surface hover:bg-surface-container transition-all border-b-2 border-transparent rounded-t-lg flex items-center gap-1.5 whitespace-nowrap">
                            <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
                            Hiring Desk
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6">
            
            <!-- Left Content Column -->
            <div class="min-w-0 space-y-6">

                <!-- TAB: HOME -->
                <div id="tab-home" class="tab-content block space-y-6">
                    <div class="bg-white p-6 rounded-xl border border-outline-variant/30 ambient-shadow">
                        <h2 class="text-xl font-title-lg text-on-surface mb-4">Overview</h2>
                        <p class="text-sm font-body-lg text-on-surface-variant leading-relaxed whitespace-pre-wrap"><?php echo htmlspecialchars($description); ?></p>
                        <button data-tab-link="about" class="mt-4 text-sm font-label-lg text-primary hover:underline flex items-center gap-1">
                            See all details <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </button>
                    </div>

                    <div class="bg-white p-6 rounded-xl border border-outline-variant/30 ambient-shadow">
                        <div class="flex items-center justify-between mb-4 border-b border-outline-variant/20 pb-3">
                            <h2 class="text-xl font-title-lg text-on-surface">Recently posted jobs</h2>
                            <button data-tab-link="jobs" class="text-sm font-label-lg text-secondary hover:bg-surface-container px-3 py-1.5 rounded transition-colors">See all jobs</button>
                        </div>
                        
                        <?php if (!empty($jobs)): ?>
                            <div class="space-y-4">
                                <?php foreach (array_slice($jobs, 0, 3) as $job): ?>
                                    <div class="flex gap-4 p-3 rounded-lg hover:bg-surface-container-low transition-colors group">
                                        <div class="w-12 h-12 rounded bg-surface-variant flex items-center justify-center shrink-0">
                                            <img src="<?php echo htmlspecialchars($logo); ?>" alt="" class="w-full h-full object-contain p-1.5">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <a href="<?php echo URLROOT; ?>/job/apply/<?php echo $job['job_id']; ?>" class="block font-title-md text-primary hover:underline truncate"><?php echo htmlspecialchars($job['title']); ?></a>
                                            <p class="font-body-sm text-on-surface-variant text-sm mt-0.5 truncate"><?php echo htmlspecialchars($companyName); ?></p>
                                            <p class="font-body-sm text-secondary text-xs mt-1 flex items-center gap-1">
                                                <span><?php echo htmlspecialchars($job['location'] ?? 'Remote'); ?></span>
                                                <span>&bull;</span>
                                                <span><?php echo htmlspecialchars($job['job_type']); ?></span>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-6">
                                <p class="text-sm font-body-md text-secondary">No job openings at the moment.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- TAB: ABOUT -->
                <div id="tab-about" class="tab-content hidden space-y-6">
                    <div class="bg-white p-6 rounded-xl border border-outline-variant/30 ambient-shadow">
                        <h2 class="text-xl font-title-lg text-on-surface mb-6">About</h2>
                        <p class="text-sm font-body-lg text-on-surface-variant leading-relaxed whitespace-pre-wrap mb-8"><?php echo htmlspecialchars($description); ?></p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8 border-t border-outline-variant/20 pt-6">
                            <div>
                                <h3 class="text-sm font-label-lg text-on-surface mb-1">Website</h3>
                                <a href="<?php echo htmlspecialchars($website); ?>" target="_blank" class="text-sm font-body-md text-primary hover:underline break-all"><?php echo htmlspecialchars($website); ?></a>
                            </div>
                            <div>
                                <h3 class="text-sm font-label-lg text-on-surface mb-1">Industry</h3>
                                <p class="text-sm font-body-md text-on-surface-variant"><?php echo htmlspecialchars($industry); ?></p>
                            </div>
                            <div>
                                <h3 class="text-sm font-label-lg text-on-surface mb-1">Company size</h3>
                                <p class="text-sm font-body-md text-on-surface-variant"><?php echo htmlspecialchars($size); ?></p>
                            </div>
                            <div>
                                <h3 class="text-sm font-label-lg text-on-surface mb-1">Founded</h3>
                                <p class="text-sm font-body-md text-on-surface-variant"><?php echo htmlspecialchars($founded); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB: JOBS -->
                <div id="tab-jobs" class="tab-content hidden space-y-4">
                    <div class="bg-white rounded-xl border border-outline-variant/30 ambient-shadow overflow-hidden">
                        <div class="p-4 border-b border-outline-variant/20 flex items-center justify-between bg-surface-container-low">
                            <h2 class="font-title-lg text-on-surface">Openings (<?php echo count($jobs); ?>)</h2>
                            <?php if ($isOwner): ?>
                                <button onclick="openAddJobModal()" class="text-sm font-label-lg text-primary hover:bg-surface-variant px-3 py-1.5 rounded transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[18px]">add</span> Post job
                                </button>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($jobs)): ?>
                            <div class="divide-y divide-outline-variant/20">
                                <?php foreach ($jobs as $job): ?>
                                    <div class="p-6 hover:bg-surface-container-low transition-colors flex flex-col sm:flex-row justify-between gap-4">
                                        <div class="space-y-1">
                                            <a href="<?php echo URLROOT; ?>/job/apply/<?php echo $job['job_id']; ?>" class="font-title-lg text-primary hover:underline inline-block"><?php echo htmlspecialchars($job['title']); ?></a>
                                            <p class="font-body-md text-on-surface-variant text-sm"><?php echo htmlspecialchars($job['location'] ?? 'Remote'); ?> &bull; <?php echo htmlspecialchars($job['job_type']); ?></p>
                                            <p class="font-body-sm text-secondary text-xs pt-2">Posted on <?php echo date('M j, Y', strtotime($job['posted_at'])); ?></p>
                                        </div>
                                        <div class="shrink-0">
                                            <?php if ($isOwner): ?>
                                                <button onclick="document.querySelector('[data-tab=\'portal\']').click()" class="text-primary font-label-md px-4 py-1.5 rounded-full border border-primary hover:bg-primary-fixed hover:border-primary-container transition-colors">
                                                    Manage
                                                </button>
                                            <?php else: ?>
                                                <a href="<?php echo URLROOT; ?>/job/apply/<?php echo $job['job_id']; ?>" class="inline-block bg-primary text-white font-label-md px-5 py-2 rounded-full hover:bg-[#004182] transition-colors shadow-sm">
                                                    Apply
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="p-12 text-center">
                                <span class="material-symbols-outlined text-4xl text-outline-variant mb-2">work_off</span>
                                <h3 class="font-title-md text-on-surface">No open jobs</h3>
                                <p class="text-sm text-secondary mt-1">There are no job openings at the moment.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- TAB: HIRING DESK (Owner Only) -->
                <?php if ($isOwner): ?>
                <div id="tab-portal" class="tab-content hidden space-y-6">
                    <div class="bg-white rounded-xl border border-outline-variant/30 ambient-shadow overflow-hidden">
                        <div class="p-6 border-b border-outline-variant/20 bg-surface-container-low flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h2 class="font-title-lg text-on-surface">Hiring Dashboard</h2>
                                <p class="text-sm font-body-md text-secondary mt-1">Manage listings and review applicants.</p>
                            </div>
                            <button onclick="openAddJobModal()" class="bg-primary text-white font-label-md px-4 py-2 rounded-full hover:bg-[#004182] transition-colors flex items-center gap-1.5 shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">add</span> Post New Job
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-surface-container border-b border-outline-variant/30 text-xs font-label-lg text-on-surface-variant uppercase tracking-wider">
                                        <th class="p-4 font-semibold">Job Title</th>
                                        <th class="p-4 font-semibold">Status</th>
                                        <th class="p-4 font-semibold">Applicants</th>
                                        <th class="p-4 font-semibold text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/20">
                                    <?php if (!empty($jobs)): ?>
                                        <?php foreach ($jobs as $job): ?>
                                            <tr class="hover:bg-surface-container-low transition-colors" id="row-job-<?php echo $job['job_id']; ?>">
                                                <td class="p-4">
                                                    <p class="font-title-md text-on-surface"><?php echo htmlspecialchars($job['title']); ?></p>
                                                    <p class="font-body-sm text-xs text-secondary mt-0.5"><?php echo htmlspecialchars($job['location'] ?? 'Remote'); ?></p>
                                                </td>
                                                <td class="p-4">
                                                    <select id="status-<?php echo $job['job_id']; ?>" onchange="saveJobConfig(<?php echo $job['job_id']; ?>)" class="text-sm border border-outline-variant rounded-md py-1 px-2 focus:ring-primary focus:border-primary">
                                                        <option value="Live" <?php echo $job['status'] !== 'Closed' ? 'selected' : ''; ?>>Active</option>
                                                        <option value="Closed" <?php echo $job['status'] === 'Closed' ? 'selected' : ''; ?>>Closed</option>
                                                    </select>
                                                </td>
                                                <td class="p-4">
                                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-medium <?php echo ((int)$job['applicant_count'] > 0) ? 'bg-primary-fixed text-primary-container' : 'bg-surface-variant text-secondary'; ?>">
                                                        <?php echo (int)($job['applicant_count'] ?? 0); ?> applied
                                                    </span>
                                                </td>
                                                <td class="p-4 text-right">
                                                    <button onclick="viewCandidates(<?php echo $job['job_id']; ?>, '<?php echo addslashes(htmlspecialchars($job['title'])); ?>')" class="text-primary font-label-md hover:bg-primary-fixed px-3 py-1.5 rounded-full transition-colors border border-transparent hover:border-primary-container inline-flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[16px]">groups</span> Review
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="p-8 text-center text-secondary font-body-md italic">No active job listings.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Sidebar -->
            <aside class="space-y-6">
                <div class="bg-white rounded-xl border border-outline-variant/30 ambient-shadow p-5">
                    <h3 class="font-title-md text-on-surface mb-4">About this page</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-label-md text-secondary uppercase tracking-wider mb-1">Website</p>
                            <a href="<?php echo htmlspecialchars($website); ?>" target="_blank" class="text-sm font-body-md text-primary hover:underline truncate block"><?php echo htmlspecialchars($website); ?></a>
                        </div>
                        <div>
                            <p class="text-xs font-label-md text-secondary uppercase tracking-wider mb-1">Industry</p>
                            <p class="text-sm font-body-md text-on-surface"><?php echo htmlspecialchars($industry); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-label-md text-secondary uppercase tracking-wider mb-1">Company size</p>
                            <p class="text-sm font-body-md text-on-surface"><?php echo htmlspecialchars($size); ?></p>
                        </div>
                        <div class="pt-2 border-t border-outline-variant/20">
                            <a href="#" data-tab-link="about" class="text-sm font-label-md text-primary hover:underline">Show more details</a>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</main>

<!-- Modals -->
<?php if ($isOwner): ?>
<!-- Edit Modal -->
<div id="editCompanyModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeEditCompanyModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-xl shadow-2xl flex flex-col max-h-[90vh]">
        <div class="p-5 border-b border-outline-variant/20 flex justify-between items-center">
            <h2 class="font-title-lg text-on-surface">Edit Company Details</h2>
            <button onclick="closeEditCompanyModal()" class="w-8 h-8 rounded-full hover:bg-surface-container flex items-center justify-center text-secondary">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto">
            <form action="<?php echo URLROOT; ?>/company/update_profile" method="POST" enctype="multipart/form-data" class="space-y-5">
                <div>
                    <label class="block font-label-lg text-on-surface mb-1">Company Logo</label>
                    <div class="flex items-center gap-4">
                        <img src="<?php echo htmlspecialchars($logo); ?>" alt="" class="w-16 h-16 rounded-lg object-contain bg-white border border-outline-variant">
                        <input type="file" name="logo" accept="image/*" class="block w-full text-sm">
                    </div>
                </div>
                <div>
                    <label class="block font-label-lg text-on-surface mb-1">Description</label>
                    <textarea name="description" rows="4" class="w-full p-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm bg-white"><?php echo htmlspecialchars($company['description'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block font-label-lg text-on-surface mb-1">Website URL</label>
                    <input type="text" name="website" value="<?php echo htmlspecialchars($company['website'] ?? ''); ?>" class="w-full h-10 px-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm bg-white">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-label-lg text-on-surface mb-1">Industry</label>
                        <input type="text" name="industry" value="<?php echo htmlspecialchars($company['industry'] ?? ''); ?>" class="w-full h-10 px-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm bg-white">
                    </div>
                    <div>
                        <label class="block font-label-lg text-on-surface mb-1">Company Size</label>
                        <input type="text" name="size" value="<?php echo htmlspecialchars($company['size'] ?? ''); ?>" class="w-full h-10 px-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm bg-white">
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3 border-t border-outline-variant/20 mt-6">
                    <button type="button" onclick="closeEditCompanyModal()" class="px-5 py-2 rounded-full font-label-md text-secondary hover:bg-surface-variant transition-colors border border-outline-variant">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-full font-label-md bg-primary text-white hover:bg-[#004182] transition-colors shadow-sm">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Job Modal -->
<div id="addJobModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeAddJobModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-xl shadow-2xl flex flex-col max-h-[90vh]">
        <div class="p-5 border-b border-outline-variant/20 flex justify-between items-center">
            <h2 class="font-title-lg text-on-surface">Post a Job</h2>
            <button onclick="closeAddJobModal()" class="w-8 h-8 rounded-full hover:bg-surface-container flex items-center justify-center text-secondary">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto">
            <form action="<?php echo URLROOT; ?>/company/add_job" method="POST" class="space-y-5">
                <div>
                    <label class="block font-label-lg text-on-surface mb-1">Job Title *</label>
                    <input type="text" name="title" required class="w-full h-10 px-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm bg-white">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-label-lg text-on-surface mb-1">Employment Type</label>
                        <select name="job_type" class="w-full h-10 px-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm bg-white">
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-label-lg text-on-surface mb-1">Experience Level</label>
                        <select name="experience_level" class="w-full h-10 px-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm bg-white">
                            <option value="Entry">Entry Level</option>
                            <option value="Associate">Associate</option>
                            <option value="Mid-Senior">Mid-Senior level</option>
                            <option value="Director">Director</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-label-lg text-on-surface mb-1">Location</label>
                        <input type="text" name="location" placeholder="e.g. Remote" class="w-full h-10 px-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm bg-white">
                    </div>
                    <div>
                        <label class="block font-label-lg text-on-surface mb-1">Salary Range</label>
                        <input type="text" name="salary_range" placeholder="e.g. $80k - $100k" class="w-full h-10 px-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm bg-white">
                    </div>
                </div>
                <div>
                    <label class="block font-label-lg text-on-surface mb-1">Job Description *</label>
                    <textarea name="description" rows="5" required class="w-full p-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm bg-white"></textarea>
                </div>
                <div class="pt-4 flex justify-end gap-3 border-t border-outline-variant/20 mt-6">
                    <button type="button" onclick="closeAddJobModal()" class="px-5 py-2 rounded-full font-label-md text-secondary hover:bg-surface-variant transition-colors border border-outline-variant">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-full font-label-md bg-primary text-white hover:bg-[#004182] transition-colors shadow-sm">Post Job</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Candidates Modal -->
<div id="candidatesModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeCandidatesModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl bg-white rounded-xl shadow-2xl flex flex-col max-h-[90vh]">
        <div class="p-5 border-b border-outline-variant/20 flex justify-between items-center bg-surface-container-low">
            <h2 class="font-title-lg text-on-surface" id="cand-job-title">Applicants</h2>
            <button onclick="closeCandidatesModal()" class="w-8 h-8 rounded-full hover:bg-surface-variant flex items-center justify-center text-secondary">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto" id="candidates-wrap">
            <!-- Loaded via JS -->
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Tab Logic
    const tabs = document.querySelectorAll('[data-tab]');
    const contents = document.querySelectorAll('.tab-content');

    function selectTab(target) {
        tabs.forEach(t => {
            t.classList.remove('text-primary', 'border-primary');
            t.classList.add('text-secondary', 'border-transparent');
        });
        
        const activeTabButton = document.querySelector(`[data-tab="${target}"]`);
        if (activeTabButton) {
            activeTabButton.classList.remove('text-secondary', 'border-transparent');
            activeTabButton.classList.add('text-primary', 'border-primary');
        }

        contents.forEach(c => c.classList.add('hidden'));
        const activeContent = document.getElementById(`tab-${target}`);
        if (activeContent) activeContent.classList.remove('hidden');
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
            window.scrollTo({ top: 300, behavior: 'smooth' });
        });
    });

    // Follow Logic
    const followBtn = document.getElementById('follow-btn');
    if (followBtn) {
        followBtn.addEventListener('click', async function() {
            const companyId = this.getAttribute('data-id');
            const isFollowing = this.classList.contains('border-2');
            const url = isFollowing ? `${URLROOT}/company/unfollow/${companyId}` : `${URLROOT}/company/follow/${companyId}`;
            
            try {
                const res = await fetch(url, { method: 'POST' });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    if(data.message === 'Unauthorized') window.location.href = `${URLROOT}/auth/login`;
                }
            } catch (e) {
                console.error(e);
            }
        });
    }
});

// Modal Logic
function openEditCompanyModal() { document.getElementById('editCompanyModal').classList.remove('hidden'); }
function closeEditCompanyModal() { document.getElementById('editCompanyModal').classList.add('hidden'); }
function openAddJobModal() { document.getElementById('addJobModal').classList.remove('hidden'); }
function closeAddJobModal() { document.getElementById('addJobModal').classList.add('hidden'); }
function closeCandidatesModal() { document.getElementById('candidatesModal').classList.add('hidden'); }

async function saveJobConfig(jobId) {
    const statusVal = document.getElementById(`status-${jobId}`).value;
    try {
        const formData = new FormData();
        formData.append('status', statusVal);
        await fetch(`${URLROOT}/company/update_job/${jobId}`, {
            method: 'POST',
            body: formData
        });
    } catch (e) {
        console.error(e);
    }
}

async function viewCandidates(jobId, jobTitle) {
    const titleEl = document.getElementById('cand-job-title');
    const wrapEl = document.getElementById('candidates-wrap');
    const modalEl = document.getElementById('candidatesModal');
    
    if (!modalEl || !wrapEl) return;
    
    if (titleEl) titleEl.textContent = `Applicants for: ${jobTitle}`;
    wrapEl.innerHTML = `<div class="p-8 text-center"><div class="inline-block w-8 h-8 border-2 border-primary border-t-transparent rounded-full animate-spin"></div></div>`;
    modalEl.classList.remove('hidden');

    try {
        const res = await fetch(`${URLROOT}/company/get_applicants/${jobId}`);
        const data = await res.json();

        if (data.success && data.applicants && data.applicants.length > 0) {
            wrapEl.innerHTML = data.applicants.map(app => `
                <div class="p-4 border border-outline-variant/30 rounded-lg mb-4 flex items-start gap-4 hover:bg-surface-container-low transition-colors">
                    <img src="${app.profile_pic ? `${URLROOT}/uploads/profiles/${app.profile_pic}` : pnProfilePicUrl({ full_name: `${app.first_name} ${app.last_name}` })}" class="w-12 h-12 rounded-full object-cover shrink-0 border border-outline-variant/30">
                    <div class="flex-1 min-w-0">
                        <p class="font-title-md text-on-surface truncate">${app.first_name} ${app.last_name}</p>
                        <p class="font-body-md text-sm text-on-surface-variant truncate">${app.email}</p>
                        ${app.phone ? `<p class="font-body-sm text-xs text-secondary mt-1">📞 ${app.phone}</p>` : ''}
                        ${app.cover_letter ? `<p class="font-body-md text-sm text-on-surface-variant mt-2 bg-surface-container-low p-3 rounded border border-outline-variant/20 italic">"${app.cover_letter}"</p>` : ''}
                    </div>
                    <div class="shrink-0">
                        ${app.resume_path ? `
                            <a href="${URLROOT}/uploads/resumes/${app.resume_path}" target="_blank" download class="text-primary font-label-md px-4 py-2 rounded border border-primary hover:bg-primary-fixed transition-colors flex items-center gap-1">
                                <span class="material-symbols-outlined text-[18px]">download</span> Resume
                            </a>
                        ` : `<span class="text-xs text-secondary italic">No Resume</span>`}
                    </div>
                </div>
            `).join('');
        } else {
            wrapEl.innerHTML = `<div class="p-12 text-center text-secondary italic">No applicants yet.</div>`;
        }
    } catch (e) {
        console.error(e);
        wrapEl.innerHTML = `<div class="p-8 text-center text-error font-body-md">Failed to load applicants.</div>`;
    }
}
</script>

<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
