<?php require APPROOT . '/views/layouts/admin_header.php'; ?>
<?php require APPROOT . '/views/layouts/admin_sidebar.php'; ?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 font-manrope">Company Management</h1>
        <p class="text-slate-500 text-sm">Monitor and verify platform companies.</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Company Name</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Industry</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Size</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach($data['companies'] as $company): ?>
                <tr class="hover:bg-slate-50/80 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center font-bold text-slate-400 text-[10px] overflow-hidden border border-slate-200">
                                <?php echo strtoupper(substr($company['name'], 0, 1)); ?>
                            </div>
                            <span class="text-sm font-bold text-slate-900"><?php echo $company['name']; ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-600 font-medium"><?php echo $company['industry']; ?></td>
                    <td class="px-6 py-4 text-xs text-slate-500"><?php echo $company['size']; ?></td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="deleteCompany(<?php echo $company['company_id']; ?>)" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
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
