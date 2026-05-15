<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>
<!-- View Content -->
<div class="user-page-shell pt-2 pb-12">

<div class="max-w-[1128px] mx-auto px-4 grid grid-cols-1 md:grid-cols-12 gap-6">
<!-- Left Sidebar -->
<aside class="md:col-span-3">
<div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden sticky top-20">
<div class="p-4 border-b border-slate-100">
                    <h2 class="font-title-md text-on-surface">Manage my network</h2>
                </div>
                <nav class="flex flex-col pn-network-sidebar-nav">
                    <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 transition-colors" href="<?php echo URLROOT; ?>/network/connections_list">
                        <span class="material-symbols-outlined text-slate-500">group</span>
                        <span class="flex-1 font-label-lg">Connections</span>
                        <span id="connections-count" class="text-sm text-slate-400">0</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 transition-colors" href="<?php echo URLROOT; ?>/network/pages_list">
                        <span class="material-symbols-outlined text-slate-500">domain</span>
                        <span class="flex-1 font-label-lg">Pages</span>
                        <span id="pages-count" class="text-sm text-slate-400">0</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-2 text-[#0A66C2] font-semibold hover:bg-blue-50 transition-colors" href="<?php echo URLROOT; ?>/company">
                        <span class="material-symbols-outlined text-[#0A66C2] text-lg">explore</span>
                        <span class="flex-1 text-xs">Explore more companies</span>
                    </a>
                </nav>
                <div class="p-4 border-t border-slate-100">
                    <button type="button" class="pn-network-sidebar-toggle text-primary font-semibold hover:underline text-sm">Show less</button>
                </div>
<div class="mt-4 p-4 text-center">
<div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm">
<p class="text-xs text-slate-500 mb-2">Ad</p>
<p class="text-sm font-medium mb-4"><?php echo explode(' ', $_SESSION['user_name'])[0]; ?>, unlock your full potential with ProNetwork Premium</p>
<div class="flex justify-center mb-4">
<div class="relative">
<img class="w-14 h-14 rounded-full border-2 border-white shadow-md object-cover" data-user-pic="true" alt="Profile photo" src="<?php echo pn_profile_pic_url(); ?>"/>
<div class="absolute -right-2 top-0 bg-amber-400 w-6 h-6 rounded-full flex items-center justify-center border-2 border-white">
<span class="material-symbols-outlined text-xs text-white" style="font-variation-settings: 'FILL' 1;">star</span>
</div>
</div>
</div>
<button onclick="window.location.href='<?php echo URLROOT; ?>/premium'" class="border-2 border-primary text-primary hover:bg-blue-50 px-4 py-1.5 rounded-full font-semibold transition-all">Try for Free</button>
</div>
</div>
</aside>
<!-- Main Content -->
<div class="md:col-span-9 space-y-6">
<!-- Invitations Section -->
<section class="bg-white rounded-lg border border-slate-200 shadow-sm">
<div class="flex items-center justify-between p-4 border-b border-slate-100">
<h2 class="font-title-md">Invitations</h2>
<button onclick="window.location.href='<?php echo URLROOT; ?>/network/invitations_list'" id="see-all-invitations" class="text-slate-600 font-semibold hover:bg-slate-100 px-3 py-1 rounded transition-colors text-sm">See all 0</button>
</div>
<div id="invitations-container" class="divide-y divide-slate-100">
    <!-- Dynamic connections will be loaded here -->
</div>
</section>
<!-- Pages You Follow Section -->
<section class="bg-white rounded-lg border border-slate-200 shadow-sm p-4">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2">
            <h2 class="font-title-md">Pages you follow</h2>
            <span id="followed-pages-count" class="text-xs font-bold bg-slate-100 px-2 py-0.5 rounded text-slate-600">0</span>
        </div>
        <button onclick="window.location.href='<?php echo URLROOT; ?>/network/pages_list'" id="see-all-pages" class="text-slate-600 font-semibold hover:bg-slate-100 px-3 py-1 rounded transition-colors text-sm">
            See all
        </button>
    </div>
    <div id="followed-pages-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Dynamic pages will be loaded here -->
    </div>
</section>
<!-- People You May Know Grid -->
<section class="bg-white rounded-lg border border-slate-200 shadow-sm p-4">
<div class="flex items-center justify-between mb-4">
<h2 class="font-title-md">People you may know based on your activity</h2>
<button onclick="window.location.href='<?php echo URLROOT; ?>/network/suggestions_list'" class="text-slate-600 font-semibold hover:bg-slate-100 px-3 py-1 rounded transition-colors text-sm">See all</button>
</div>
<div id="suggestions-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Dynamic suggestions will be loaded here -->
</div>
</section>
</div>
</div>

</div>
<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
