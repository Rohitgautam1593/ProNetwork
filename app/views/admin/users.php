<?php require APPROOT . '/views/layouts/admin_header.php'; ?>
<?php require APPROOT . '/views/layouts/admin_sidebar.php'; ?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 font-manrope">User Management</h1>
        <p class="text-slate-500 text-sm">Monitor and control access for all platform members.</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
            <input type="text" id="admin-user-search" placeholder="Search users..." class="pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all w-64">
        </div>
        <button class="bg-[#0A66C2] text-white px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 hover:bg-[#004182] transition-all">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            Add User
        </button>
    </div>
</div>

<?php flash('admin_message'); ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">User Details</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Platform Role</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Registration</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="admin-users-table" class="divide-y divide-slate-50">
                <?php foreach($data['users'] as $user): ?>
                <tr class="hover:bg-slate-50/80 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-400 text-sm overflow-hidden border border-slate-200">
                                <?php if(!empty($user['profile_pic'])): ?>
                                    <img src="<?php echo URLROOT . '/uploads/profiles/' . $user['profile_pic']; ?>" alt="" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900"><?php echo $user['full_name']; ?></p>
                                <p class="text-[11px] text-slate-500"><?php echo $user['email']; ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <select onchange="updateUserRole(<?php echo $user['user_id']; ?>, this.value)" class="text-[12px] font-bold border-none bg-slate-50 rounded-lg px-2 py-1 focus:ring-0 cursor-pointer hover:bg-slate-100 transition-colors">
                            <option value="Student" <?php echo $user['role'] == 'Student' ? 'selected' : ''; ?>>Student</option>
                            <option value="Professional" <?php echo $user['role'] == 'Professional' ? 'selected' : ''; ?>>Professional</option>
                            <option value="Recruiter" <?php echo $user['role'] == 'Recruiter' ? 'selected' : ''; ?>>Recruiter</option>
                        </select>
                        <?php if($user['is_admin']): ?>
                            <span class="ml-2 px-2 py-0.5 bg-purple-50 text-purple-600 text-[9px] font-black rounded uppercase tracking-tighter">Admin</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-xs text-slate-600 font-medium"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-green-50 text-green-600 text-[10px] font-bold rounded-lg border border-green-100">Active</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="openEditUserModal(<?php echo htmlspecialchars(json_encode($user)); ?>)" title="Edit User" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </button>
                            <?php if($user['user_id'] != $_SESSION['user_id'] && !$user['is_admin']): ?>
                            <button onclick="confirmDeleteUser(<?php echo $user['user_id']; ?>)" title="Delete User" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 z-[150] hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeEditUserModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-slate-900 font-manrope">Edit User Account</h3>
            <button onclick="closeEditUserModal()" class="p-2 hover:bg-slate-50 rounded-full text-slate-400 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <form id="editUserForm" class="space-y-4">
            <input type="hidden" id="edit-user-id">
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Full Name</label>
                <input type="text" id="edit-full-name" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Email Address</label>
                <input type="email" id="edit-email" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Platform Role</label>
                <select id="edit-role" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="Student">Student</option>
                    <option value="Professional">Professional</option>
                    <option value="Recruiter">Recruiter</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">New Password</label>
                <div class="relative">
                    <input type="password" id="edit-password" placeholder="Leave blank to keep current password" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all pr-10">
                    <button type="button" onclick="togglePasswordVisibility('edit-password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
                <p class="text-[10px] text-slate-400 mt-1 ml-1">Admin power: You can override user passwords here.</p>
            </div>
            
            <div class="pt-4 flex gap-3">
                <button type="button" onclick="closeEditUserModal()" class="flex-1 py-3 bg-slate-50 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-100 transition-all">Cancel</button>
                <button type="submit" class="flex-[2] py-3 bg-[#0A66C2] text-white rounded-xl text-sm font-bold hover:bg-[#004182] transition-all shadow-lg shadow-blue-500/20">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/assets/js/admin/admin.js"></script>

<?php require APPROOT . '/views/layouts/admin_footer.php'; ?>
