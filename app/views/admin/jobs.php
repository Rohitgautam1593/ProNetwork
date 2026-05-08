<?php require APPROOT . '/views/layouts/admin_header.php'; ?>
<?php require APPROOT . '/views/layouts/admin_sidebar.php'; ?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 font-manrope">Job Management</h1>
        <p class="text-slate-500 text-sm">Review active job listings across the platform.</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Job Title</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Company</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Type</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Applicants</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach($data['jobs'] as $job): ?>
                <tr class="hover:bg-slate-50/80 transition-colors group">
                    <td class="px-6 py-4 text-sm font-bold text-slate-900"><?php echo $job['title']; ?></td>
                    <td class="px-6 py-4 text-xs text-slate-600"><?php echo $job['company_name']; ?></td>
                    <td class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest"><?php echo $job['job_type']; ?></td>
                    <td class="px-6 py-4 text-xs text-slate-500"><?php echo $job['applicant_count']; ?></td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="deleteJob(<?php echo $job['job_id']; ?>)" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/assets/js/admin/admin.js"></script>

<?php require APPROOT . '/views/layouts/admin_footer.php'; ?>
