<?php
$current_page = $data['current_page'] ?? '';
$menu_items = [
    'about' => [
        'title' => 'About Us',
        'icon' => 'corporate_fare',
        'url' => str_replace('/public', '/blog/about-us/', URLROOT),
        'external' => true
    ],
    'accessibility' => [
        'title' => 'Accessibility',
        'icon' => 'accessibility',
        'url' => URLROOT . '/pages/accessibility',
        'external' => false
    ],
    'user_agreement' => [
        'title' => 'User Agreement',
        'icon' => 'gavel',
        'url' => URLROOT . '/pages/user_agreement',
        'external' => false
    ],
    'privacy_policy' => [
        'title' => 'Privacy Policy',
        'icon' => 'shield',
        'url' => URLROOT . '/pages/privacy_policy',
        'external' => false
    ],
    'cookie_policy' => [
        'title' => 'Cookie Policy',
        'icon' => 'cookie',
        'url' => URLROOT . '/pages/cookie_policy',
        'external' => false
    ],
    'brand_policy' => [
        'title' => 'Brand Policy',
        'icon' => 'verified',
        'url' => URLROOT . '/pages/brand_policy',
        'external' => false
    ],
    'guest_controls' => [
        'title' => 'Guest Controls',
        'icon' => 'tune',
        'url' => URLROOT . '/pages/guest_controls',
        'external' => false
    ],
    'community_guidelines' => [
        'title' => 'Community Guidelines',
        'icon' => 'groups',
        'url' => URLROOT . '/pages/community_guidelines',
        'external' => false
    ]
];
?>
<aside class="w-full md:w-80 shrink-0">
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-4 sticky top-20">
    <div class="mb-4 px-2">
      <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Policy & Safety</h3>
      <p class="text-xs text-slate-500">Guidelines & Agreements</p>
    </div>
    <nav class="space-y-1" aria-label="Policy pages">
      <?php foreach ($menu_items as $key => $item): ?>
        <?php 
          $active = ($current_page === $key); 
          $activeClass = $active 
            ? 'bg-blue-50 text-[#0A66C2] font-bold border-l-4 border-[#0A66C2]' 
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 border-l-4 border-transparent';
        ?>
        <a href="<?php echo $item['url']; ?>" 
           class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-all group <?php echo $activeClass; ?>"
           <?php echo $item['external'] ? 'target="_blank"' : ''; ?>>
          <span class="material-symbols-outlined text-[20px] transition-transform group-hover:scale-110 <?php echo $active ? 'text-[#0A66C2]' : 'text-slate-400 group-hover:text-slate-600'; ?>">
            <?php echo $item['icon']; ?>
          </span>
          <span class="flex-1 truncate"><?php echo $item['title']; ?></span>
          <?php if ($item['external']): ?>
            <span class="material-symbols-outlined text-[14px] text-slate-400 opacity-0 group-hover:opacity-100 transition-opacity">open_in_new</span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </div>
</aside>
