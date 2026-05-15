<?php require USERROOT . "/frontend/views/layouts/header.php"; ?>
<?php require USERROOT . "/frontend/views/layouts/navbar.php"; ?>

<div class="user-page-shell pt-2 pb-12">
    <div class="max-w-[1128px] mx-auto px-4">
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden mb-6">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h1 class="font-title-lg text-on-surface">Invitations</h1>
                <span id="total-invitations" class="text-sm text-slate-500 font-medium">0 pending</span>
            </div>
            <div class="p-4">
                <div id="invitations-list" class="grid grid-cols-1 gap-4">
                    <p class="text-center text-slate-400 py-8 text-sm">Loading invitations…</p>
                </div>
            </div>
        </div>
        <p class="text-center mt-4"><a href="<?php echo URLROOT; ?>/user/network" class="text-sm font-semibold text-[#0A66C2] hover:underline">← Back to My Network</a></p>
    </div>
</div>

<?php require USERROOT . "/frontend/views/layouts/footer.php"; ?>