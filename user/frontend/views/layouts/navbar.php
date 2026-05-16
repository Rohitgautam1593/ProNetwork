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
<header class="pn-topbar fixed top-0 w-full z-50 border-b border-slate-200 bg-white/95 backdrop-blur-xl shadow-sm">
  <div class="flex items-center justify-between px-4 h-16 max-w-[1180px] mx-auto font-['Manrope'] antialiased">
    <div class="flex items-center gap-4 flex-1 min-w-0">
      <a href="<?php echo URLROOT; ?>" class="pn-brand flex items-center gap-2 flex-shrink-0 rounded-xl px-1 -ml-1" aria-label="ProNetwork home">
        <span class="pn-brand-mark">P</span>
        <span class="text-xl font-black text-slate-950 tracking-tight hidden sm:block">ProNetwork</span>
      </a>
      <?php if (isLoggedIn()) : ?>
      <div class="relative hidden md:block w-full max-w-[340px]" id="global-search">
        <form id="global-search-form" class="relative" autocomplete="off">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-[18px]">search</span>
          <input
            id="global-search-input"
            type="search"
            class="pn-search-input w-full h-10 rounded-xl bg-slate-100 border border-transparent pl-10 pr-3 text-sm text-slate-900 placeholder:text-slate-500 focus:bg-white focus:ring-2 focus:ring-[#0A66C2]/20 focus:border-[#0A66C2] transition-all"
            placeholder="Search people, jobs, posts"
          />
        </form>
        <div id="global-search-results" class="hidden absolute left-0 top-12 w-[420px] max-h-[520px] overflow-y-auto bg-white border border-slate-200 rounded-2xl shadow-2xl z-[70] py-2"></div>
      </div>
      <?php endif; ?>
    </div>

    <div class="flex items-center gap-2 md:gap-4">
      <?php if (isLoggedIn()) : ?>
      <nav class="pn-main-nav hidden md:flex items-center gap-1.5 h-full" aria-label="Main">
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[56px] <?php echo pn_nav_item_cls($pnNavActive['home']); ?> transition-all group rounded-xl py-1" href="<?php echo URLROOT; ?>/user/feed"<?php echo $pnNavAria($pnNavActive['home']); ?>>
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform"<?php echo $pnNavFill($pnNavActive['home']); ?>>home</span>
          <span class="text-[10px] font-bold hidden xl:block">Home</span>
        </a>
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[56px] <?php echo pn_nav_item_cls($pnNavActive['network']); ?> transition-all group rounded-xl py-1" href="<?php echo URLROOT; ?>/user/network"<?php echo $pnNavAria($pnNavActive['network']); ?>>
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform"<?php echo $pnNavFill($pnNavActive['network']); ?>>group</span>
          <span class="text-[10px] font-bold hidden xl:block">Network</span>
        </a>
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[56px] <?php echo pn_nav_item_cls($pnNavActive['jobs']); ?> transition-all group rounded-xl py-1" href="<?php echo URLROOT; ?>/user/jobs"<?php echo $pnNavAria($pnNavActive['jobs']); ?>>
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform"<?php echo $pnNavFill($pnNavActive['jobs']); ?>>work</span>
          <span class="text-[10px] font-bold hidden xl:block">Jobs</span>
        </a>
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[56px] <?php echo pn_nav_item_cls($pnNavActive['messaging']); ?> transition-all group rounded-xl py-1" href="<?php echo URLROOT; ?>/user/messaging"<?php echo $pnNavAria($pnNavActive['messaging']); ?>>
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform"<?php echo $pnNavFill($pnNavActive['messaging']); ?>>chat</span>
          <span class="text-[10px] font-bold hidden xl:block">Messages</span>
        </a>
        <a class="pn-nav-item relative flex flex-col items-center justify-center min-w-[56px] <?php echo pn_nav_item_cls($pnNavActive['notifications']); ?> transition-all group rounded-xl py-1" href="<?php echo URLROOT; ?>/user/notifications"<?php echo $pnNavAria($pnNavActive['notifications']); ?>>
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform"<?php echo $pnNavFill($pnNavActive['notifications']); ?>>notifications</span>
          <span id="nav-notif-badge" class="hidden absolute top-0 right-1 min-w-[18px] h-[18px] px-1 flex items-center justify-center bg-red-500 text-white text-[10px] font-black rounded-full border-2 border-white">0</span>
          <span class="text-[10px] font-bold hidden xl:block">Alerts</span>
        </a>
        <?php if (!empty($_SESSION['is_admin'])) : ?>
        <div class="h-9 w-[1px] bg-slate-200 mx-1 hidden lg:block"></div>
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[56px] <?php echo pn_nav_item_cls($pnNavActive['admin']); ?> transition-all group rounded-xl py-1" href="<?php echo URLROOT; ?>/admin/dashboard"<?php echo $pnNavAria($pnNavActive['admin']); ?>>
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform font-bold"<?php echo $pnNavFill($pnNavActive['admin']); ?>>admin_panel_settings</span>
          <span class="text-[10px] font-bold hidden xl:block">Admin</span>
        </a>
        <?php endif; ?>
        <?php if (hasRole('Company')) : ?>
        <div class="h-9 w-[1px] bg-slate-200 mx-1 hidden lg:block"></div>
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[56px] <?php echo $pnNavActive['company'] ? 'is-active text-indigo-800 bg-indigo-50 shadow-sm ring-1 ring-indigo-600/25 rounded-xl' : 'text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50/60'; ?> transition-all group rounded-xl py-1" href="<?php echo URLROOT; ?>/company/dashboard"<?php echo $pnNavAria($pnNavActive['company']); ?>>
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform font-bold"<?php echo $pnNavFill($pnNavActive['company']); ?>>domain</span>
          <span class="text-[10px] font-bold hidden xl:block">Company</span>
        </a>
        <?php endif; ?>
        <div class="h-9 w-[1px] bg-slate-200 mx-1 hidden lg:block"></div>
        <div class="relative pn-profile-menu-wrap">
          <button id="pn-profile-menu-toggle" type="button" class="pn-profile-menu-toggle <?php echo ($pnNavActive['profile'] || $pnNavActive['settings']) ? 'is-active' : ''; ?>" aria-expanded="false" aria-haspopup="true">
            <span class="w-8 h-8 rounded-full overflow-hidden bg-slate-200 border border-slate-300">
              <img data-user-pic="true" src="" alt="" class="w-full h-full object-cover">
            </span>
            <span class="hidden lg:flex flex-col text-left leading-tight min-w-0">
              <span class="text-[11px] font-black text-slate-900">Me</span>
              <span class="text-[10px] font-semibold text-slate-500">Account</span>
            </span>
            <span class="material-symbols-outlined text-[18px] text-slate-500">expand_more</span>
          </button>
          <div id="pn-profile-menu" class="pn-profile-menu hidden" role="menu">
            <div class="px-4 py-3 border-b border-slate-100">
              <div class="flex items-center gap-3">
                <img data-user-pic="true" src="" alt="" class="w-11 h-11 rounded-full object-cover bg-slate-100 border border-slate-200">
                <div class="min-w-0">
                  <p data-user-name="full" class="text-sm font-black text-slate-900 truncate">Me</p>
                  <p data-user-headline class="text-xs text-slate-500 truncate">Professional</p>
                </div>
              </div>
            </div>
            <a role="menuitem" class="pn-profile-menu-item" href="<?php echo URLROOT; ?>/user/profile">
              <span class="material-symbols-outlined text-[19px]">account_circle</span>
              View profile
            </a>
            <a role="menuitem" class="pn-profile-menu-item" href="<?php echo URLROOT; ?>/user/settings">
              <span class="material-symbols-outlined text-[19px]">settings</span>
              Settings
            </a>
            <a role="menuitem" class="pn-profile-menu-item text-red-600 hover:bg-red-50 hover:text-red-700" href="<?php echo URLROOT; ?>/auth/logout">
              <span class="material-symbols-outlined text-[19px]">logout</span>
              Sign out
            </a>
          </div>
        </div>
      </nav>
      <a href="<?php echo URLROOT; ?>/user/profile" class="pn-mobile-avatar-link md:hidden w-10 h-10 rounded-full overflow-hidden bg-slate-200 border border-slate-300 shadow-sm ring-2 ring-white" aria-label="Open profile">
        <img data-user-pic="true" src="" alt="" class="w-full h-full object-cover">
      </a>
      <?php else : ?>
      <a href="<?php echo URLROOT; ?>/auth/login" class="pn-nav-item px-5 py-1.5 rounded-full font-bold text-[#0A66C2] hover:bg-blue-50 transition-all">Sign in</a>
      <a href="<?php echo URLROOT; ?>/auth/register" class="pn-nav-item px-5 py-1.5 rounded-full font-bold bg-[#0A66C2] text-white hover:bg-[#004182] transition-all shadow-sm hover:shadow-md">Join now</a>
      <?php endif; ?>
    </div>
  </div>
  <?php if (isLoggedIn()) : ?>
  <div class="md:hidden px-4 pb-3 max-w-[1180px] mx-auto">
    <div class="relative" id="global-search-mobile">
      <form class="relative" autocomplete="off">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-[18px]">search</span>
        <input id="global-search-input-mobile" type="search" class="w-full h-10 rounded-xl bg-slate-100 border border-transparent pl-10 pr-3 text-sm text-slate-900 placeholder:text-slate-500 focus:bg-white focus:ring-2 focus:ring-[#0A66C2]/20 focus:border-[#0A66C2] transition-all" placeholder="Search ProNetwork">
      </form>
      <div id="global-search-results-mobile" class="hidden absolute left-0 right-0 top-12 max-h-[420px] overflow-y-auto bg-white border border-slate-200 rounded-2xl shadow-2xl z-[70] py-2"></div>
    </div>
  </div>
  <?php endif; ?>
</header>
<?php if (isLoggedIn()) : ?>
<nav class="pn-mobile-bottom-nav md:hidden" aria-label="Mobile main navigation">
  <a class="pn-mobile-nav-link <?php echo $pnNavActive['home'] ? 'is-active' : ''; ?>" href="<?php echo URLROOT; ?>/user/feed"<?php echo $pnNavAria($pnNavActive['home']); ?>>
    <span class="material-symbols-outlined"<?php echo $pnNavFill($pnNavActive['home']); ?>>home</span>
    <span>Home</span>
  </a>
  <a class="pn-mobile-nav-link <?php echo $pnNavActive['network'] ? 'is-active' : ''; ?>" href="<?php echo URLROOT; ?>/user/network"<?php echo $pnNavAria($pnNavActive['network']); ?>>
    <span class="material-symbols-outlined"<?php echo $pnNavFill($pnNavActive['network']); ?>>group</span>
    <span>Network</span>
  </a>
  <a class="pn-mobile-nav-link <?php echo $pnNavActive['jobs'] ? 'is-active' : ''; ?>" href="<?php echo URLROOT; ?>/user/jobs"<?php echo $pnNavAria($pnNavActive['jobs']); ?>>
    <span class="material-symbols-outlined"<?php echo $pnNavFill($pnNavActive['jobs']); ?>>work</span>
    <span>Jobs</span>
  </a>
  <a class="pn-mobile-nav-link <?php echo $pnNavActive['messaging'] ? 'is-active' : ''; ?>" href="<?php echo URLROOT; ?>/user/messaging"<?php echo $pnNavAria($pnNavActive['messaging']); ?>>
    <span class="material-symbols-outlined"<?php echo $pnNavFill($pnNavActive['messaging']); ?>>chat</span>
    <span>Messages</span>
  </a>
  <a class="pn-mobile-nav-link <?php echo $pnNavActive['notifications'] ? 'is-active' : ''; ?>" href="<?php echo URLROOT; ?>/user/notifications"<?php echo $pnNavAria($pnNavActive['notifications']); ?>>
    <span class="material-symbols-outlined"<?php echo $pnNavFill($pnNavActive['notifications']); ?>>notifications</span>
    <span>Alerts</span>
  </a>
  <a class="pn-mobile-nav-link <?php echo ($pnNavActive['profile'] || $pnNavActive['settings']) ? 'is-active' : ''; ?>" href="<?php echo URLROOT; ?>/user/profile"<?php echo $pnNavAria($pnNavActive['profile'] || $pnNavActive['settings']); ?>>
    <span class="material-symbols-outlined"<?php echo $pnNavFill($pnNavActive['profile'] || $pnNavActive['settings']); ?>>person</span>
    <span>Me</span>
  </a>
</nav>
<?php endif; ?>
<main class="<?php echo isLoggedIn() ? 'pt-28 md:pt-16' : 'pt-16'; ?>">
