<?php require ADMINROOT . '/frontend/views/layouts/admin_header.php'; ?>
<?php require ADMINROOT . '/frontend/views/layouts/admin_sidebar.php'; ?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 font-manrope">Job Management</h1>
        <p class="text-slate-500 text-sm">Review active job listings across the platform.</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
            <input type="text" id="admin-job-search" placeholder="Search title, company, type..." class="pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all w-64 md:w-72">
        </div>
    </div>
</div>

<?php flash('admin_message'); ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Job Title</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Company</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Type</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Applicants</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Reports</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="admin-jobs-table" class="divide-y divide-slate-50">
                <?php foreach($data['jobs'] as $job): ?>
                <tr class="hover:bg-slate-50/80 transition-colors group cursor-pointer" data-admin-preview data-preview-type="Job" data-preview-id="<?php echo (int)$job['job_id']; ?>">
                    <td class="px-6 py-4 text-sm font-bold text-slate-900"><?php echo $job['title']; ?></td>
                    <td class="px-6 py-4 text-xs text-slate-600"><?php echo $job['company_name']; ?></td>
                    <td class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest"><?php echo $job['job_type']; ?></td>
                    <td class="px-6 py-4 text-xs font-semibold text-slate-600">
                        <?php echo (int)$job['applicant_count']; ?> / <?php echo !empty($job['applicant_limit']) ? (int)$job['applicant_limit'] : '∞'; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php if($job['status'] === 'Closed'): ?>
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-lg border border-slate-200">Disabled</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-green-50 text-green-700 text-[10px] font-bold rounded-lg border border-green-100">Live</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php if(!empty($job['active_report_count'])): ?>
                            <span class="px-2 py-1 bg-red-50 text-red-700 text-[10px] font-black rounded-lg border border-red-100"><?php echo (int)$job['active_report_count']; ?> active</span>
                            <span class="ml-1 text-[10px] text-slate-400"><?php echo (int)$job['total_report_count']; ?> total</span>
                        <?php else: ?>
                            <span class="text-[10px] text-slate-400">No active reports</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <button data-admin-ignore-preview onclick="toggleJobStatus(<?php echo $job['job_id']; ?>)" title="<?php echo $job['status'] === 'Closed' ? 'Enable / Reopen Listing' : 'Disable / Close Listing'; ?>" class="p-1.5 <?php echo $job['status'] === 'Closed' ? 'text-green-600 hover:bg-green-50' : 'text-amber-600 hover:bg-amber-50'; ?> rounded-lg transition-all">
                                <span class="material-symbols-outlined text-[18px]"><?php echo $job['status'] === 'Closed' ? 'toggle_off' : 'toggle_on'; ?></span>
                            </button>
                            <button data-admin-ignore-preview onclick="deleteJob(<?php echo $job['job_id']; ?>)" title="Delete Job" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($data['jobs'])): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm italic">No job listings posted yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/assets/js/admin/admin.js?v=<?php echo time(); ?>"></script>

<?php require ADMINROOT . '/frontend/views/layouts/admin_footer.php'; ?>
