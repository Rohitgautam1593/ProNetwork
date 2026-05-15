<?php
$pnNavActive = [
    'home'          => pn_nav_home_active(),
    'network'       => pn_route_matches('user/network'),
    'jobs'          => pn_route_matches('user/jobs'),
    'messaging'     => pn_route_matches('user/messaging'),
    'notifications' => pn_route_matches('user/notifications'),
    'profile'       => pn_route_matches('user/profile'),
    'settings'      => pn_route_matches('user/settings'),
    'admin'         => pn_nav_admin_active(),
    'company'       => pn_nav_company_active(),
];
$pnNavAria = static function (bool $active): string {
    return $active ? ' aria-current="page"' : '';
};
$pnNavFill = static function (bool $active): string {
    return $active ? ' style="font-variation-settings: \'FILL\' 1"' : '';
};
?>
<header class="fixed top-0 w-full z-50 border-b border-gray-200 bg-white/95 backdrop-blur-md shadow-sm">
  <div class="flex items-center justify-between px-4 h-14 max-w-[1128px] mx-auto font-['Manrope'] antialiased">
    <div class="flex items-center gap-4 flex-1">
      <a href="<?php echo URLROOT; ?>" class="pn-nav-item text-2xl font-black text-[#0A66C2] flex-shrink-0 rounded-lg px-1 -ml-1">ProNetwork</a>
      <?php if (isLoggedIn()) : ?>
      <div class="relative hidden md:block w-full max-w-[280px]" id="global-search">
        <form id="global-search-form" class="relative" autocomplete="off">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-[18px]">search</span>
          <input
            id="global-search-input"
            type="search"
            class="w-full h-9 rounded bg-[#eef3f8] border-none pl-10 pr-3 text-xs text-slate-900 placeholder:text-slate-500 focus:bg-white focus:ring-2 focus:ring-[#0A66C2] transition-all"
            placeholder="Search"
          />
        </form>
        <div id="global-search-results" class="hidden absolute left-0 top-11 w-[380px] max-h-[520px] overflow-y-auto bg-white border border-slate-200 rounded-lg shadow-xl z-[70] py-2"></div>
      </div>
      <?php endif; ?>
    </div>

    <div class="flex items-center gap-2 md:gap-6">
      <?php if (isLoggedIn()) : ?>
      <nav class="flex items-center gap-2 md:gap-4 h-full" aria-label="Main">
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[64px] <?php echo pn_nav_item_cls($pnNavActive['home']); ?> transition-all group rounded-lg py-0.5" href="<?php echo URLROOT; ?>/user/feed"<?php echo $pnNavAria($pnNavActive['home']); ?>>
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform"<?php echo $pnNavFill($pnNavActive['home']); ?>>home</span>
          <span class="text-[10px] font-medium hidden lg:block">Home</span>
        </a>
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[64px] <?php echo pn_nav_item_cls($pnNavActive['network']); ?> transition-all group rounded-lg py-0.5" href="<?php echo URLROOT; ?>/user/network"<?php echo $pnNavAria($pnNavActive['network']); ?>>
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform"<?php echo $pnNavFill($pnNavActive['network']); ?>>group</span>
          <span class="text-[10px] font-medium hidden lg:block">My Network</span>
        </a>
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[64px] <?php echo pn_nav_item_cls($pnNavActive['jobs']); ?> transition-all group rounded-lg py-0.5" href="<?php echo URLROOT; ?>/user/jobs"<?php echo $pnNavAria($pnNavActive['jobs']); ?>>
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform"<?php echo $pnNavFill($pnNavActive['jobs']); ?>>work</span>
          <span class="text-[10px] font-medium hidden lg:block">Jobs</span>
        </a>
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[64px] <?php echo pn_nav_item_cls($pnNavActive['messaging']); ?> transition-all group rounded-lg py-0.5" href="<?php echo URLROOT; ?>/user/messaging"<?php echo $pnNavAria($pnNavActive['messaging']); ?>>
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform"<?php echo $pnNavFill($pnNavActive['messaging']); ?>>chat</span>
          <span class="text-[10px] font-medium hidden lg:block">Messaging</span>
        </a>
        <a class="pn-nav-item relative flex flex-col items-center justify-center min-w-[64px] <?php echo pn_nav_item_cls($pnNavActive['notifications']); ?> transition-all group rounded-lg py-0.5" href="<?php echo URLROOT; ?>/user/notifications"<?php echo $pnNavAria($pnNavActive['notifications']); ?>>
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform"<?php echo $pnNavFill($pnNavActive['notifications']); ?>>notifications</span>
          <span id="nav-notif-badge" class="hidden absolute top-0 right-1 min-w-[18px] h-[18px] px-1 flex items-center justify-center bg-red-500 text-white text-[10px] font-black rounded-full border-2 border-white">0</span>
          <span class="text-[10px] font-medium hidden lg:block">Notifications</span>
        </a>
        <?php if (!empty($_SESSION['is_admin'])) : ?>
        <div class="h-10 w-[1px] bg-slate-200 mx-1 hidden lg:block"></div>
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[64px] <?php echo pn_nav_item_cls($pnNavActive['admin']); ?> transition-all group rounded-lg py-0.5" href="<?php echo URLROOT; ?>/admin/dashboard"<?php echo $pnNavAria($pnNavActive['admin']); ?>>
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform font-bold"<?php echo $pnNavFill($pnNavActive['admin']); ?>>admin_panel_settings</span>
          <span class="text-[10px] font-bold hidden lg:block">Admin</span>
        </a>
        <?php endif; ?>
        <?php if (hasRole('Company')) : ?>
        <div class="h-10 w-[1px] bg-slate-200 mx-1 hidden lg:block"></div>
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[64px] <?php echo $pnNavActive['company'] ? 'is-active text-indigo-800 bg-indigo-50 shadow-sm ring-1 ring-indigo-600/25 rounded-xl' : 'text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50/60'; ?> transition-all group rounded-lg py-0.5" href="<?php echo URLROOT; ?>/company/dashboard"<?php echo $pnNavAria($pnNavActive['company']); ?>>
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform font-bold"<?php echo $pnNavFill($pnNavActive['company']); ?>>domain</span>
          <span class="text-[10px] font-bold hidden lg:block">Company</span>
        </a>
        <?php endif; ?>
        <div class="h-10 w-[1px] bg-slate-200 mx-2 hidden lg:block"></div>
        <a href="<?php echo URLROOT; ?>/user/profile" class="pn-nav-item flex flex-col items-center justify-center min-w-[64px] <?php echo pn_nav_item_cls($pnNavActive['profile']); ?> group rounded-lg py-0.5"<?php echo $pnNavAria($pnNavActive['profile']); ?>>
          <div class="w-6 h-6 rounded-full overflow-hidden bg-slate-200 border border-slate-300 group-hover:border-slate-500 transition-all <?php echo $pnNavActive['profile'] ? 'ring-2 ring-[#0A66C2]/40' : ''; ?>">
            <img data-user-pic="true" src="" alt="" class="w-full h-full object-cover">
          </div>
          <span class="text-[10px] font-medium flex items-center">Me <span class="material-symbols-outlined text-[14px]">arrow_drop_down</span></span>
        </a>
        <a href="<?php echo URLROOT; ?>/user/settings" class="pn-nav-item <?php echo pn_nav_icon_cls($pnNavActive['settings']); ?> transition-colors ml-1" title="Settings"<?php echo $pnNavAria($pnNavActive['settings']); ?>>
          <span class="material-symbols-outlined text-[22px]"<?php echo $pnNavFill($pnNavActive['settings']); ?>>settings</span>
        </a>
        <a href="<?php echo URLROOT; ?>/auth/logout" class="text-slate-400 hover:text-red-600 transition-colors ml-2" title="Logout">
          <span class="material-symbols-outlined text-[22px]">logout</span>
        </a>
      </nav>
      <?php else : ?>
      <a href="<?php echo URLROOT; ?>/auth/login" class="pn-nav-item px-5 py-1.5 rounded-full font-bold text-[#0A66C2] hover:bg-blue-50 transition-all">Sign in</a>
      <a href="<?php echo URLROOT; ?>/auth/register" class="pn-nav-item px-5 py-1.5 rounded-full font-bold bg-[#0A66C2] text-white hover:bg-[#004182] transition-all shadow-sm hover:shadow-md">Join now</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<main class="pt-14">
