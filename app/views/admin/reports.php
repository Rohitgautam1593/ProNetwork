<?php require APPROOT . '/views/layouts/admin_header.php'; ?>
<?php require APPROOT . '/views/layouts/admin_sidebar.php'; ?>

<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 font-manrope">User Reports</h1>
    <p class="text-slate-500 text-sm">Review community flags and handle moderation requests.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Reporter</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Target</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Reason</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach($data['reports'] as $report): ?>
                <tr class="hover:bg-slate-50/80 transition-colors group">
                    <td class="px-6 py-4 text-sm font-bold text-slate-900"><?php echo $report['reporter_name']; ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold rounded uppercase"><?php echo $report['target_type']; ?> #<?php echo $report['target_id']; ?></span>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-600 italic">"<?php echo $report['reason']; ?>"</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold rounded-lg border border-amber-100"><?php echo $report['status']; ?></span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <button class="p-2 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all" title="Resolve">
                                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                            </button>
                            <button class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Dismiss">
                                <span class="material-symbols-outlined text-[20px]">cancel</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($data['reports'])): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm italic">No active reports to review.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require APPROOT . '/views/layouts/admin_footer.php'; ?>
