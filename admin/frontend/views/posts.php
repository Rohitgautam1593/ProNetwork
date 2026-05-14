<?php require ADMINROOT . '/frontend/views/layouts/admin_header.php'; ?>
<?php require ADMINROOT . '/frontend/views/layouts/admin_sidebar.php'; ?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 font-manrope">Post Moderation</h1>
        <p class="text-slate-500 text-sm">Review reported content and maintain community standards.</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
            <input type="text" id="admin-post-search" placeholder="Search by ID, author, content..." class="pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all w-64 md:w-72">
        </div>
        <span class="px-3 py-2 bg-blue-50 text-blue-700 text-xs font-bold rounded-xl border border-blue-100 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[16px]">article</span>
            <?php echo count($data['posts']); ?> Total Posts
        </span>
    </div>
</div>

<?php flash('admin_message'); ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Post ID</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Author Details</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Content Snippet</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Visibility</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Posted On</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="admin-posts-table" class="divide-y divide-slate-50">
                <?php foreach($data['posts'] as $post): ?>
                <tr id="admin-post-<?php echo $post['post_id']; ?>" class="hover:bg-slate-50/80 transition-colors group cursor-pointer" data-admin-preview data-preview-type="Post" data-preview-id="<?php echo (int)$post['post_id']; ?>">
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-mono font-bold border border-slate-200">
                            #<?php echo $post['post_id']; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-400 text-xs overflow-hidden border border-slate-200">
                                <?php echo strtoupper(substr($post['author_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900"><?php echo $post['author_name']; ?></p>
                                <p class="text-[11px] text-slate-500"><?php echo $post['author_email']; ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="max-w-xs md:max-w-md">
                            <p class="text-xs text-slate-700 truncate italic">
                                "<?php echo htmlspecialchars(mb_strimwidth($post['content'], 0, 65, '...')); ?>"
                            </p>
                            <?php if($post['post_image']): ?>
                                <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 bg-blue-50 text-blue-600 text-[9px] font-bold rounded">
                                    <span class="material-symbols-outlined text-[10px]">image</span> Image Attached
                                </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-bold text-slate-600"><?php echo $post['visibility']; ?></span>
                        <?php if(!empty($post['active_report_count'])): ?>
                            <span class="ml-1 px-1.5 py-0.5 bg-red-50 text-red-700 text-[9px] font-black rounded-md border border-red-100">
                                <?php echo (int)$post['active_report_count']; ?> Report<?php echo (int)$post['active_report_count'] === 1 ? '' : 's'; ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-xs text-slate-600 font-medium"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></p>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button data-admin-ignore-preview onclick="deletePost(<?php echo $post['post_id']; ?>)" title="Delete Post" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all active:scale-95">
                            <span class="material-symbols-outlined text-[20px]">delete_forever</span>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if(empty($data['posts'])): ?>
                    <tr id="no-posts-placeholder">
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm italic">
                            No posts on the platform yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('admin-post-search');
    const postsTable = document.getElementById('admin-posts-table');

    if (searchInput && postsTable) {
        searchInput.addEventListener('keyup', () => {
            const term = searchInput.value.toLowerCase();
            const rows = Array.from(postsTable.getElementsByTagName('tr'));

            rows.forEach((row) => {
                if (row.id === 'no-posts-placeholder') return;
                const textContent = row.innerText.toLowerCase();
                row.style.display = textContent.includes(term) ? '' : 'none';
            });
        });
    }
});
</script>

<script src="<?php echo str_replace('/public', '', URLROOT); ?>/admin/frontend/assets/js/admin.js?v=<?php echo time(); ?>"></script>

<?php require ADMINROOT . '/frontend/views/layouts/admin_footer.php'; ?>
