<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>

<div class="user-page-shell pt-2 pb-12">
    <div class="max-w-[1128px] mx-auto px-4">
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden mb-6">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h1 class="font-title-lg text-on-surface">Connections</h1>
                <span id="total-connections" class="text-sm text-slate-500 font-medium">0 connections</span>
            </div>
            <div class="p-4">
                <div id="connections-list" class="grid grid-cols-1 gap-4">
                    <div class="animate-pulse flex items-center space-x-4 py-4">
                        <div class="rounded-full bg-slate-200 h-16 w-16"></div>
                        <div class="flex-1 space-y-2 py-1">
                            <div class="h-4 bg-slate-200 rounded w-1/4"></div>
                            <div class="h-3 bg-slate-200 rounded w-1/2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-center"><a href="<?php echo URLROOT; ?>/user/network" class="text-sm font-semibold text-[#0A66C2] hover:underline">← Back to My Network</a></p>
    </div>
</div>

<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
