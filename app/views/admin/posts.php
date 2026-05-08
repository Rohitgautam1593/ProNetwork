<?php require APPROOT . '/views/layouts/admin_header.php'; ?>
<?php require APPROOT . '/views/layouts/admin_sidebar.php'; ?>

<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 font-manrope">Post Moderation</h1>
    <p class="text-slate-500 text-sm">Review reported content and maintain community standards.</p>
</div>

<div class="space-y-4">
    <?php foreach($data['posts'] as $post): ?>
    <div id="admin-post-<?php echo $post['post_id']; ?>" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col md:flex-row gap-6 hover:border-slate-200 transition-colors">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center font-bold text-slate-400 text-xs">
                    <?php echo strtoupper(substr($post['author_name'], 0, 1)); ?>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900 text-sm"><?php echo $post['author_name']; ?></span>
                        <span class="px-1.5 py-0.5 bg-blue-50 text-blue-600 text-[9px] font-black rounded uppercase tracking-tighter"><?php echo $post['author_email']; ?></span>
                    </div>
                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-widest mt-0.5"><?php echo date('M d, Y • H:i', strtotime($post['created_at'])); ?></p>
                </div>
            </div>
            <div class="text-slate-700 text-sm leading-relaxed mb-4 p-4 bg-slate-50/50 rounded-xl border border-slate-100 italic">
                "<?php echo $post['content']; ?>"
            </div>
            <?php if($post['post_image']): ?>
                <div class="mt-4 relative group">
                    <img src="<?php echo URLROOT; ?>/uploads/posts/<?php echo $post['post_image']; ?>" class="max-h-64 rounded-xl border border-slate-100 shadow-sm">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-xl">
                        <a href="<?php echo URLROOT; ?>/uploads/posts/<?php echo $post['post_image']; ?>" target="_blank" class="px-4 py-2 bg-white rounded-lg text-xs font-bold text-slate-900 shadow-xl">View Full Size</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="flex flex-col gap-2 min-w-[140px] justify-center">
            <button onclick="deletePost(<?php echo $post['post_id']; ?>)" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl font-bold text-xs transition-all active:scale-95">
                <span class="material-symbols-outlined text-[18px]">delete_forever</span>
                Remove Post
            </button>
            <button class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-slate-50 text-slate-600 hover:bg-slate-100 rounded-xl font-bold text-xs transition-all">
                <span class="material-symbols-outlined text-[18px]">info</span>
                Logs
            </button>
            <div class="mt-4 p-3 bg-blue-50/30 rounded-xl border border-blue-50/50">
                <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-1 text-center">Status</p>
                <div class="flex items-center justify-center gap-1.5">
                    <div class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></div>
                    <span class="text-[10px] font-bold text-blue-600">Pending Review</span>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script src="<?php echo URLROOT; ?>/assets/js/admin/admin.js"></script>

<?php require APPROOT . '/views/layouts/admin_footer.php'; ?>
