<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>

<div class="user-page-shell pb-12 pt-2">
    <div class="max-w-[1128px] mx-auto grid grid-cols-1 md:grid-cols-12 gap-6 px-4">
        <!-- Left Sidebar: Quick Nav -->
        <aside class="md:col-span-3 flex flex-col space-y-3">
            <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden w-full sticky top-20 border border-slate-200 dark:border-slate-800 shadow-sm p-4">
                <a href="<?php echo URLROOT; ?>/user/notifications" class="flex items-center gap-2 text-sm font-semibold text-[#0A66C2] hover:underline mb-3">
                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                    Back to Notifications
                </a>
                <a href="<?php echo URLROOT; ?>/user/feed" class="flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-[#0A66C2] transition-colors">
                    <span class="material-symbols-outlined text-lg">feed</span>
                    Go to Feed
                </a>
                <hr class="my-3 border-slate-100 dark:border-slate-800" />
                <h3 class="font-title-md text-xs font-bold text-slate-900 dark:text-white mb-2">Notification Context</h3>
                <p class="text-xs text-slate-500 leading-relaxed font-manrope">
                    You are viewing a specific post targeted from your notification alerts. You can react, comment, or share directly from this view.
                </p>
            </div>
        </aside>

        <!-- Center: Standalone Post Card -->
        <div class="md:col-span-6 flex flex-col space-y-4">
            <script>
                window.IS_SINGLE_POST = true;
                window.SINGLE_POST_DATA = <?php echo json_encode($data['post']); ?>;
            </script>
            <div id="feed-container" class="space-y-4">
                <!-- Single post card loaded dynamically via feed.js -->
            </div>
        </div>

        <!-- Right Sidebar: Context Tips -->
        <aside class="md:col-span-3 flex flex-col space-y-3">
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-4 sticky top-20">
                <h3 class="font-manrope text-sm font-bold text-slate-900 dark:text-white mb-3 flex items-center justify-between">
                    <span>Interaction Details</span>
                    <span class="material-symbols-outlined text-slate-400 text-base">info</span>
                </h3>
                <div class="space-y-3 text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    <div class="flex gap-2 items-start">
                        <span class="material-symbols-outlined text-blue-600 text-sm shrink-0 mt-0.5">thumb_up</span>
                        <span>Reactions notify the original post author instantly.</span>
                    </div>
                    <div class="flex gap-2 items-start">
                        <span class="material-symbols-outlined text-amber-600 text-sm shrink-0 mt-0.5">forum</span>
                        <span>Comments open automated real-time discussion threads.</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
