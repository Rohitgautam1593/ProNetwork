<?php require APPROOT . '/views/layouts/header.php'; ?>
<?php require APPROOT . '/views/layouts/navbar.php'; ?>
<?php
    $company = $data['company'] ?? [];
    $jobs = $data['jobs'] ?? [];
    $companyName = $company['company_name'] ?? 'Company';
    $industry = $company['industry'] ?? 'Industry not set';
    $description = $company['description'] ?? 'No company description available.';
    $website = $company['website'] ?? '#';
    $size = $company['size'] ?? 'Size not set';
    $followers = (int)($company['followers'] ?? 0);
    $logo = !empty($company['logo']) ? URLROOT . '/uploads/companies/' . $company['logo'] : 'https://ui-avatars.com/api/?name=' . urlencode($companyName);
?>
<!-- View Content -->
<main class="pt-16">

<!-- Company Header Card -->
<div class="bg-white rounded-lg overflow-hidden shadow-[0px_4px_12px_rgba(0,0,0,0.05)] mb-6">
<div class="relative h-48 w-full bg-slate-200">
<img alt="CloudScale Systems Cover" class="w-full h-full object-cover" data-alt="modern minimalist open-plan office interior with large windows and professional atmosphere at twilight" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCbVagRJxYoLGpj7ZML87QnovJUg-UjPxnpoCEKD_aYYhXaIfABj2kGNmhrv1UtJNAwMD8hgiZwMxTynyxB1xli_R-sCQPpuYEpt_XBk3Ru87M4iyzvstMJz4hs8b_95EbICzUqbPoD9F4ne0391-s9fvlDsPShG10CLqorzZlgrPAC_dSNUqWTXnQdv-JU5bKmNEE5CNOlauWYdKOD20UDA7cEm7kVW0bgNjcKWwGBUpWNPLP63ABf2fhWnr_rkLMh-5l98TqbdWEA"/>
<div class="absolute -bottom-16 left-6 p-1 bg-white rounded-lg">
<div class="w-32 h-32 bg-primary flex items-center justify-center rounded-lg overflow-hidden">
<img alt="<?php echo htmlspecialchars($companyName); ?> Logo" class="w-full h-full object-cover" src="<?php echo htmlspecialchars($logo); ?>"/>
</div>
</div>
</div>
<div class="pt-20 pb-6 px-6">
<div class="flex justify-between items-start">
<div>
<h1 class="font-display-md text-display-md text-on-surface"><?php echo htmlspecialchars($companyName); ?></h1>
<p class="font-body-md text-on-surface-variant mt-1"><?php echo htmlspecialchars($description); ?></p>
<p class="font-caption text-secondary mt-1"><?php echo htmlspecialchars($industry); ?> &middot; <?php echo number_format($followers); ?> followers</p>
</div>
<div class="flex gap-2">
<button class="bg-[#0A66C2] hover:bg-[#004182] text-white px-4 py-1.5 rounded-full font-label-lg transition-all flex items-center gap-1">
<span class="material-symbols-outlined text-lg" data-icon="add">add</span>
                            Follow
                        </button>
<button class="border border-[#0A66C2] text-[#0A66C2] hover:bg-blue-50 px-4 py-1.5 rounded-full font-label-lg transition-all flex items-center gap-1">
                            Visit website
                            <span class="material-symbols-outlined text-lg" data-icon="open_in_new">open_in_new</span>
</button>
</div>
</div>
<div class="mt-6 flex border-b border-gray-100">
<a class="px-4 py-3 font-label-lg text-black border-b-2 border-black" href="#">Home</a>
<a class="px-4 py-3 font-label-lg text-gray-500 hover:bg-gray-50" href="#">About</a>
<a class="px-4 py-3 font-label-lg text-gray-500 hover:bg-gray-50" href="#">Jobs</a>
<a class="px-4 py-3 font-label-lg text-gray-500 hover:bg-gray-50" href="#">Posts</a>
</div>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-[1fr_300px] gap-6">
<!-- Main Content Area -->
<div class="flex flex-col gap-6">
<!-- About Snippet -->
<section class="bg-white p-6 rounded-lg shadow-[0px_4px_12px_rgba(0,0,0,0.05)]">
<h2 class="font-title-lg text-title-lg mb-4">About</h2>
<p class="font-body-md text-on-surface-variant leading-relaxed">
                        <?php echo htmlspecialchars($description); ?>
                    </p>
<button class="mt-4 text-[#0A66C2] font-label-lg hover:underline">See all details</button>
</section>
<!-- Recent Posts (Bento Style) -->
<section class="flex flex-col gap-4">
<h2 class="font-title-lg text-title-lg px-2">Recent Posts</h2>
<div class="grid grid-cols-1 gap-4">
<!-- Featured Post -->
<div class="bg-white rounded-lg shadow-[0px_4px_12px_rgba(0,0,0,0.05)] overflow-hidden">
<div class="p-4 flex items-center gap-3">
<img alt="<?php echo htmlspecialchars($companyName); ?>" class="w-10 h-10 rounded" src="<?php echo htmlspecialchars($logo); ?>"/>
<div>
<h4 class="font-title-md text-sm"><?php echo htmlspecialchars($companyName); ?></h4>
<p class="font-caption text-secondary"><?php echo number_format($followers); ?> followers &middot; Live company page</p>
</div>
</div>
<div class="px-4 pb-3">
<p class="font-body-md text-on-surface">We are thrilled to announce our latest partnership with GlobalNexus to redefine the edge computing landscape. Our combined expertise will unlock 40% faster latency for distributed teams.</p>
</div>
<img alt="Partnership announcement" class="w-full aspect-video object-cover" data-alt="glowing digital network nodes across a world map representing global connectivity and data flow" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBv8HQwd3nEMcbVsmNr9ZFfOUfKYe5HTL4pvfdWvk-MrO-RlInJe0iXFHXsNXHbP8di0Q2WjnAYDXc3oJ_Ji2Gx4EdyBVJmPddf4peOlDYui3fdEMEOYCyE0GyWT-kxhqIuBBHNxZvEjzgGtsVv3vFGggsQFO9KpcXXO4rhdUAdNKUrOc1IBgv9NhvIYn5tEVFgT9t4Y8QKuhEy0wK-hLAA0dR6xPMhxgagKpSzRkC6v2FPc0x7TLw8Td0kC1qm42bZYOLDKS28iMyz"/>
<div class="p-3 flex items-center justify-between border-t border-gray-50">
<div class="flex items-center gap-1">
<span class="material-symbols-outlined text-primary text-sm" data-icon="thumb_up" style="font-variation-settings: 'FILL' 1;">thumb_up</span>
<span class="material-symbols-outlined text-error text-sm" data-icon="favorite" style="font-variation-settings: 'FILL' 1;">favorite</span>
<span class="text-caption text-secondary ml-1">452</span>
</div>
<div class="text-caption text-secondary">12 comments â€¢ 5 shares</div>
</div>
<div class="flex border-t border-gray-100 p-1">
<button class="flex-1 flex items-center justify-center gap-2 py-2 hover:bg-gray-50 text-gray-600 rounded">
<span class="material-symbols-outlined" data-icon="thumb_up">thumb_up</span>
<span class="font-label-md">Like</span>
</button>
<button class="flex-1 flex items-center justify-center gap-2 py-2 hover:bg-gray-50 text-gray-600 rounded">
<span class="material-symbols-outlined" data-icon="comment">comment</span>
<span class="font-label-md">Comment</span>
</button>
<button class="flex-1 flex items-center justify-center gap-2 py-2 hover:bg-gray-50 text-gray-600 rounded">
<span class="material-symbols-outlined" data-icon="share">share</span>
<span class="font-label-md">Share</span>
</button>
</div>
</div>
<!-- Secondary Grid Posts -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div class="bg-white p-6 rounded-lg shadow-[0px_4px_12px_rgba(0,0,0,0.05)] flex flex-col justify-between">
<div>
<span class="text-primary font-label-md">Article</span>
<h3 class="font-title-lg mt-2">The Future of Serverless: Why Scale Matters</h3>
<p class="font-body-md text-on-surface-variant mt-3 line-clamp-3">In our latest technical deep dive, we explore why architecture flexibility is the primary driver of digital transformation in 2024...</p>
</div>
<button class="mt-4 text-[#0A66C2] font-label-lg w-fit">Read article</button>
</div>
<div class="bg-white p-6 rounded-lg shadow-[0px_4px_12px_rgba(0,0,0,0.05)] border-l-4 border-primary flex flex-col justify-between">
<div>
<span class="text-secondary font-label-md">Job Opportunity</span>
<h3 class="font-title-lg mt-2"><?php echo htmlspecialchars($jobs[0]['title'] ?? 'No open role yet'); ?></h3>
<p class="font-body-md text-on-surface-variant mt-3"><?php echo htmlspecialchars($jobs[0]['location'] ?? 'Location not set'); ?> &middot; <?php echo htmlspecialchars($jobs[0]['salary_range'] ?? 'Salary not disclosed'); ?></p>
</div>
<button class="mt-4 bg-primary text-white px-4 py-1.5 rounded-full font-label-lg w-fit">Apply Now</button>
</div>
</div>
</div>
</section>
</div>
<!-- Sidebar -->
<aside class="flex flex-col gap-6">
<!-- Company Info Card -->
<div class="bg-white p-4 rounded-lg shadow-[0px_4px_12px_rgba(0,0,0,0.05)]">
<h3 class="font-title-md mb-4">Company Details</h3>
<div class="space-y-4">
<div>
<p class="font-label-md text-secondary uppercase tracking-wider text-[10px]">Website</p>
<a class="text-primary font-body-md hover:underline" href="<?php echo htmlspecialchars($website); ?>"><?php echo htmlspecialchars($website === '#' ? 'Website not set' : $website); ?></a>
</div>
<div>
<p class="font-label-md text-secondary uppercase tracking-wider text-[10px]">Industry</p>
<p class="font-body-md"><?php echo htmlspecialchars($industry); ?></p>
</div>
<div>
<p class="font-label-md text-secondary uppercase tracking-wider text-[10px]">Company size</p>
<p class="font-body-md"><?php echo htmlspecialchars($size); ?></p>
<p class="font-caption text-primary"><?php echo count($jobs); ?> open job<?php echo count($jobs) === 1 ? '' : 's'; ?> on ProNetwork</p>
</div>
<div>
<p class="font-label-md text-secondary uppercase tracking-wider text-[10px]">Headquarters</p>
<p class="font-body-md">San Francisco, California</p>
</div>
</div>
</div>
<!-- Similar Companies -->
<div class="bg-white p-4 rounded-lg shadow-[0px_4px_12px_rgba(0,0,0,0.05)]">
<h3 class="font-title-md mb-4">Pages people also viewed</h3>
<div class="space-y-4">
<div class="flex gap-3">
<div class="w-12 h-12 bg-slate-100 rounded flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-slate-400" data-icon="corporate_fare">corporate_fare</span>
</div>
<div>
<h4 class="font-label-lg text-on-surface">DataCore Solutions</h4>
<p class="font-caption text-secondary">IT Services &amp; Consulting</p>
<button class="mt-1 border border-secondary text-secondary px-3 py-0.5 rounded-full text-[11px] font-bold hover:bg-gray-50 flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]" data-icon="add">add</span> Follow
                                </button>
</div>
</div>
<div class="flex gap-3">
<div class="w-12 h-12 bg-slate-100 rounded flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-slate-400" data-icon="cloud">cloud</span>
</div>
<div>
<h4 class="font-label-lg text-on-surface">NetStream Infra</h4>
<p class="font-caption text-secondary">Software Development</p>
<button class="mt-1 border border-secondary text-secondary px-3 py-0.5 rounded-full text-[11px] font-bold hover:bg-gray-50 flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]" data-icon="add">add</span> Follow
                                </button>
</div>
</div>
<div class="flex gap-3">
<div class="w-12 h-12 bg-slate-100 rounded flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-slate-400" data-icon="hub">hub</span>
</div>
<div>
<h4 class="font-label-lg text-on-surface">Apex Cloud Tech</h4>
<p class="font-caption text-secondary">Telecommunications</p>
<button class="mt-1 border border-secondary text-secondary px-3 py-0.5 rounded-full text-[11px] font-bold hover:bg-gray-50 flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]" data-icon="add">add</span> Follow
                                </button>
</div>
</div>
</div>
<button class="w-full mt-4 py-2 text-secondary font-label-lg hover:bg-gray-50 rounded transition-colors flex items-center justify-center gap-1">
                        Show more
                        <span class="material-symbols-outlined text-sm" data-icon="expand_more">expand_more</span>
</button>
</div>
</aside>
</div>

</main>
<?php require APPROOT . '/views/layouts/footer.php'; ?>
