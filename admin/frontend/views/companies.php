<?php require ADMINROOT . '/frontend/views/layouts/admin_header.php'; ?>
<?php require ADMINROOT . '/frontend/views/layouts/admin_sidebar.php'; ?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 font-manrope">Company Management</h1>
        <p class="text-slate-500 text-sm">Monitor and verify platform companies.</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
            <input type="text" id="admin-company-search" placeholder="Search companies..." class="pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all w-64 md:w-72">
        </div>
    </div>
</div>

<?php flash('admin_message'); ?>

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
            <tbody id="admin-companies-table" class="divide-y divide-slate-50">
                <?php foreach($data['companies'] as $company): ?>
                <tr class="hover:bg-slate-50/80 transition-colors group cursor-pointer" data-admin-preview data-preview-type="Company" data-preview-id="<?php echo (int)$company['company_id']; ?>">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-slate-50 flex items-center justify-center font-bold text-slate-400 text-[10px] overflow-hidden border border-slate-100 ring-4 ring-slate-50/50 flex-shrink-0">
                                <?php if(!empty($company['logo_path'])): ?>
                                    <img src="<?php echo (strpos($company['logo_path'], 'http') === 0) ? $company['logo_path'] : URLROOT . '/uploads/companies/' . $company['logo_path']; ?>" 
                                         alt="" class="w-full h-full object-contain p-1">
                                <?php else: ?>
                                    <?php echo strtoupper(substr($company['name'], 0, 1)); ?>
                                <?php endif; ?>
                            </div>
                            <span class="text-sm font-bold text-slate-900"><?php echo $company['name']; ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-600 font-medium"><?php echo $company['industry']; ?></td>
                    <td class="px-6 py-4 text-xs text-slate-500"><?php echo $company['size']; ?></td>
                    <td class="px-6 py-4 text-right">
                        <button data-admin-ignore-preview onclick="deleteCompany(<?php echo $company['company_id']; ?>)" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($data['companies'])): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-sm italic">No companies registered yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/assets/js/admin/admin.js?v=<?php echo time(); ?>"></script>

<?php require ADMINROOT . '/frontend/views/layouts/admin_footer.php'; ?>
