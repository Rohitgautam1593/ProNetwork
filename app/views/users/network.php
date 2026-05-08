<?php require APPROOT . '/views/layouts/header.php'; ?>
<?php require APPROOT . '/views/layouts/navbar.php'; ?>
<!-- View Content -->
<main class="pt-16">

<div class="max-w-[1128px] mx-auto px-4 grid grid-cols-1 md:grid-cols-12 gap-6">
<!-- Left Sidebar -->
<aside class="md:col-span-3">
<div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden sticky top-20">
<div class="p-4 border-b border-slate-100">
                    <h2 class="font-title-md text-on-surface">Manage my network</h2>
                </div>
                <nav class="flex flex-col">
                    <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 transition-colors" href="#">
                        <span class="material-symbols-outlined text-slate-500">group</span>
                        <span class="flex-1 font-label-lg">Connections</span>
                        <span class="text-sm text-slate-400">1,248</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 transition-colors" href="#">
                        <span class="material-symbols-outlined text-slate-500">person_search</span>
                        <span class="flex-1 font-label-lg">Teammates</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 transition-colors" href="#">
                        <span class="material-symbols-outlined text-slate-500">groups</span>
                        <span class="flex-1 font-label-lg">Groups</span>
                        <span class="text-sm text-slate-400">12</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 transition-colors" href="#">
                        <span class="material-symbols-outlined text-slate-500">event</span>
                        <span class="flex-1 font-label-lg">Events</span>
                        <span class="text-sm text-slate-400">3</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 transition-colors" href="#">
                        <span class="material-symbols-outlined text-slate-500">domain</span>
                        <span class="flex-1 font-label-lg">Pages</span>
                        <span class="text-sm text-slate-400">45</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 transition-colors" href="#">
                        <span class="material-symbols-outlined text-slate-500">newspaper</span>
                        <span class="flex-1 font-label-lg">Newsletters</span>
                        <span class="text-sm text-slate-400">8</span>
                    </a>
                </nav>
                <div class="p-4 border-t border-slate-100">
                    <button class="text-primary font-semibold hover:underline text-sm">Show less</button>
                </div>
<div class="mt-4 p-4 text-center">
<div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm">
<p class="text-xs text-slate-500 mb-2">Ad</p>
<p class="text-sm font-medium mb-4">Alex, unlock your full potential with ProNetwork Premium</p>
<div class="flex justify-center mb-4">
<div class="relative">
<img class="w-14 h-14 rounded-full border-2 border-white shadow-md" data-alt="Circular profile photo of a professional man" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB73fbd1kE5QCf0A1QPjYpazyY-Im9sH5JUiwf-IQdNpPBoOXGztweFO5J-XqmNQoFOfJNT0pZ9NMAhLpxiwP1vD8sn2y79xoobB0az3_Rf-7f_dg7V54QQEVZdid28duo0lbphemwZEN6K7CtJwWKeYosIBYvrwm6dLoYt2NmwnRbW8799U6OqKtq7pzjpYhNkfo-WDYJzr5qDxnNN2UixcN0f9we5YbIkr4AAHbnwSiibTeK4assKAy0MGJwjx2iHx7yQcDnd3fSf"/>
<div class="absolute -right-2 top-0 bg-amber-400 w-6 h-6 rounded-full flex items-center justify-center border-2 border-white">
<span class="material-symbols-outlined text-xs text-white" style="font-variation-settings: 'FILL' 1;">star</span>
</div>
</div>
</div>
<button class="border-2 border-primary text-primary hover:bg-blue-50 px-4 py-1.5 rounded-full font-semibold transition-all">Try for Free</button>
</div>
</div>
</aside>
<!-- Main Content -->
<div class="md:col-span-9 space-y-6">
<!-- Invitations Section -->
<section class="bg-white rounded-lg border border-slate-200 shadow-sm">
<div class="flex items-center justify-between p-4 border-b border-slate-100">
<h2 class="font-title-md">Invitations</h2>
<button class="text-slate-600 font-semibold hover:bg-slate-100 px-3 py-1 rounded transition-colors text-sm">See all 4</button>
</div>
<div id="invitations-container" class="divide-y divide-slate-100">
    <!-- Dynamic connections will be loaded here -->
</div>
</section>
<!-- People You May Know Grid -->
<section class="bg-white rounded-lg border border-slate-200 shadow-sm p-4">
<div class="flex items-center justify-between mb-4">
<h2 class="font-title-md">People you may know based on your activity</h2>
<button class="text-slate-600 font-semibold hover:bg-slate-100 px-3 py-1 rounded transition-colors text-sm">See all</button>
</div>
<div id="suggestions-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Dynamic suggestions will be loaded here -->
</div>
</section>
</div>
</div>

</main>
<?php require APPROOT . '/views/layouts/footer.php'; ?>
