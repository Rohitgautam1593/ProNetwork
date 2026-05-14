<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>
<!-- View Content -->
<div class="user-page-shell pt-2 pb-12">

<div class="max-w-[1128px] mx-auto grid grid-cols-1 md:grid-cols-[225px_1fr] gap-6 items-start">
<!-- Side Navigation Bar -->
<aside class="w-[225px] sticky top-20 flex flex-col gap-2 bg-white rounded-lg border border-gray-200 shadow-[0px_4px_12px_rgba(0,0,0,0.05)] overflow-hidden divide-y divide-gray-100">
<div class="p-4">
<div class="flex items-center gap-3 mb-1">
<img alt="Company Logo" class="w-8 h-8 rounded" data-alt="modern tech company logo with abstract blue shapes and professional clean design" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBdkHr_5CTHJ_LGcK1oXDMJUlPbIXiEc0TXYICkHkxzQVHHQwLypL5DIQ1W2RvqfVFSAD98VicuyxJbIReQE6LYDX1_6N3G8ccJ7mP_8IOcNU6o53hmJmTIdCoGc5Q4s4m9jNwf0IO37M1-fL7I3Z73_OWZKlyYUuuQptfxEygOFTORnEnBK9CzRAuK7hRT2OWxVSxt1LVpa7JtoV3lmRBuiyWBrMDSUv7nTCMaC7Z_IUlW2puxi4PTEBg-AX_cF3Yu6lahErxoIFB5"/>
<h1 class="font-title-md text-lg font-bold text-[#0A66C2]">Settings</h1>
</div>
<p class="text-gray-500 text-xs font-body-md">Manage your experience</p>
</div>
<nav class="flex flex-col">
<a class="bg-blue-50 text-[#0A66C2] font-bold border-l-4 border-[#0A66C2] px-4 py-3 flex items-center gap-3 transition-colors" href="#">
<span class="material-symbols-outlined text-xl" data-icon="person">person</span>
<span class="font-label-lg">Account preferences</span>
</a>
<a class="text-gray-600 hover:bg-gray-50 px-4 py-3 flex items-center gap-3 transition-colors hover:underline decoration-2" href="#">
<span class="material-symbols-outlined text-xl" data-icon="lock">lock</span>
<span class="font-label-lg">Sign in &amp; security</span>
</a>
<a class="text-gray-600 hover:bg-gray-50 px-4 py-3 flex items-center gap-3 transition-colors hover:underline decoration-2" href="#">
<span class="material-symbols-outlined text-xl" data-icon="visibility">visibility</span>
<span class="font-label-lg">Visibility</span>
</a>
<a class="text-gray-600 hover:bg-gray-50 px-4 py-3 flex items-center gap-3 transition-colors hover:underline decoration-2" href="#">
<span class="material-symbols-outlined text-xl" data-icon="database">database</span>
<span class="font-label-lg">Data privacy</span>
</a>
</nav>
<div class="p-4">
<button class="w-full py-2 px-4 rounded-full border border-[#0A66C2] text-[#0A66C2] font-semibold text-sm hover:bg-blue-50 transition-colors">
                        Upgrade to Premium
                    </button>
</div>
</aside>
<!-- Settings Content -->
<section class="flex flex-col gap-6">
<!-- Header Card -->
<div class="bg-white rounded-lg p-6 shadow-[0px_4px_12px_rgba(0,0,0,0.05)] border border-gray-100">
<h2 class="font-display-md text-2xl mb-1">Account preferences</h2>
<p class="text-gray-500 font-body-md">Options for managing your account and your experience on ProNetwork.</p>
</div>
<!-- Section: Profile Information -->
<div class="bg-white rounded-lg shadow-[0px_4px_12px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
<div class="p-6 border-b border-gray-100">
<h3 class="font-title-lg text-lg text-[#0A66C2]">Profile information</h3>
</div>
<div class="divide-y divide-gray-100">
<!-- Item 1 -->
<div class="p-6 flex justify-between items-start hover:bg-gray-50 transition-colors group">
<div class="flex flex-col gap-1">
<span class="font-title-md text-on-surface">Name, location, and industry</span>
<span data-user-summary class="text-gray-500 font-body-md text-sm">Loading…</span>
</div>
<button type="button" data-section="account" class="settings-edit-trigger text-[#0A66C2] font-semibold hover:underline px-3 py-1 rounded transition-colors group-hover:bg-blue-50">Edit</button>
</div>
<!-- Item 2 -->
<div class="p-6 flex justify-between items-start hover:bg-gray-50 transition-colors group">
<div class="flex flex-col gap-1">
<span class="font-title-md text-on-surface">Personal demographics</span>
<span class="text-gray-500 font-body-md">Add info about your gender, race, disability, or veteran status</span>
</div>
<button type="button" data-section="later" class="settings-edit-trigger text-[#0A66C2] font-semibold hover:underline px-3 py-1 rounded transition-colors group-hover:bg-blue-50">Edit</button>
</div>
<!-- Item 3 -->
<div class="p-6 flex justify-between items-start hover:bg-gray-50 transition-colors group">
<div class="flex flex-col gap-1">
<span class="font-title-md text-on-surface">Verifications</span>
<span class="text-gray-500 font-body-md">Manage your workplace, identity, and education verifications</span>
</div>
<button type="button" data-section="later" class="settings-edit-trigger text-[#0A66C2] font-semibold hover:underline px-3 py-1 rounded transition-colors group-hover:bg-blue-50">Edit</button>
</div>
</div>
</div>
<!-- Section: Display -->
<div class="bg-white rounded-lg shadow-[0px_4px_12px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
<div class="p-6 border-b border-gray-100">
<h3 class="font-title-lg text-lg text-[#0A66C2]">Display</h3>
</div>
<div class="divide-y divide-gray-100">
<div class="p-6 flex justify-between items-start hover:bg-gray-50 transition-colors group">
<div class="flex flex-col gap-1">
<span class="font-title-md text-on-surface">Dark mode</span>
<span class="text-gray-500 font-body-md">Off</span>
</div>
<button type="button" data-section="later" class="settings-edit-trigger text-[#0A66C2] font-semibold hover:underline px-3 py-1 rounded transition-colors group-hover:bg-blue-50">Edit</button>
</div>
</div>
</div>
<!-- Section: General Preferences -->
<div class="bg-white rounded-lg shadow-[0px_4px_12px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
<div class="p-6 border-b border-gray-100">
<h3 class="font-title-lg text-lg text-[#0A66C2]">General preferences</h3>
</div>
<div class="divide-y divide-gray-100">
<div class="p-6 flex justify-between items-start hover:bg-gray-50 transition-colors group">
<div class="flex flex-col gap-1">
<span class="font-title-md text-on-surface">Language</span>
<span class="text-gray-500 font-body-md">English (US)</span>
</div>
<button type="button" data-section="later" class="settings-edit-trigger text-[#0A66C2] font-semibold hover:underline px-3 py-1 rounded transition-colors group-hover:bg-blue-50">Edit</button>
</div>
<div class="p-6 flex justify-between items-start hover:bg-gray-50 transition-colors group">
<div class="flex flex-col gap-1">
<span class="font-title-md text-on-surface">Content language</span>
<span class="text-gray-500 font-body-md">Manage the languages you see on ProNetwork</span>
</div>
<button type="button" data-section="later" class="settings-edit-trigger text-[#0A66C2] font-semibold hover:underline px-3 py-1 rounded transition-colors group-hover:bg-blue-50">Edit</button>
</div>
<div class="p-6 flex justify-between items-start hover:bg-gray-50 transition-colors group">
<div class="flex flex-col gap-1">
<span class="font-title-md text-on-surface">Autoplay videos</span>
<span class="text-gray-500 font-body-md">On</span>
</div>
<button type="button" data-section="later" class="settings-edit-trigger text-[#0A66C2] font-semibold hover:underline px-3 py-1 rounded transition-colors group-hover:bg-blue-50">Edit</button>
</div>
</div>
</div>
</section>
</div>

</div>
<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
