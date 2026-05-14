<?php
$pnNav = isset($_GET['url']) ? trim((string) $_GET['url'], '/') : '';
function pn_nav_cls(string $match): string {
    global $pnNav;
    return ($pnNav === $match)
        ? 'text-[#0A66C2] bg-blue-50/90 shadow-sm ring-1 ring-[#0A66C2]/20 rounded-xl'
        : 'text-slate-500 hover:text-slate-900';
}
?>
<header class="fixed top-0 w-full z-50 border-b border-gray-200 bg-white/95 backdrop-blur-md shadow-sm">
  <div class="flex items-center justify-between px-4 h-14 max-w-[1128px] mx-auto font-['Manrope'] antialiased">
    <div class="flex items-center gap-4 flex-1">
      <a href="<?php echo URLROOT; ?>" class="pn-nav-item text-2xl font-black text-[#0A66C2] flex-shrink-0 rounded-lg px-1 -ml-1">ProNetwork</a>
      <?php if(isLoggedIn()): ?>
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
      <?php if(isLoggedIn()): ?>
      <nav class="flex items-center gap-2 md:gap-4 h-full">
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[64px] <?php echo pn_nav_cls('user/feed'); ?> transition-all group rounded-lg py-0.5" href="<?php echo URLROOT; ?>/user/feed">
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform">home</span>
          <span class="text-[10px] font-medium hidden lg:block">Home</span>
        </a>
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[64px] <?php echo pn_nav_cls('user/network'); ?> transition-all group rounded-lg py-0.5" href="<?php echo URLROOT; ?>/user/network">
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform">group</span>
          <span class="text-[10px] font-medium hidden lg:block">My Network</span>
        </a>
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[64px] <?php echo pn_nav_cls('user/jobs'); ?> transition-all group rounded-lg py-0.5" href="<?php echo URLROOT; ?>/user/jobs">
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform">work</span>
          <span class="text-[10px] font-medium hidden lg:block">Jobs</span>
        </a>
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[64px] <?php echo pn_nav_cls('user/messaging'); ?> transition-all group rounded-lg py-0.5" href="<?php echo URLROOT; ?>/user/messaging">
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform">chat</span>
          <span class="text-[10px] font-medium hidden lg:block">Messaging</span>
        </a>
        <a class="pn-nav-item flex flex-col items-center justify-center min-w-[64px] <?php echo pn_nav_cls('user/notifications'); ?> transition-all group rounded-lg py-0.5" href="<?php echo URLROOT; ?>/user/notifications">
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform">notifications</span>
          <span class="text-[10px] font-medium hidden lg:block">Notifications</span>
        </a>
        <?php if(!empty($_SESSION['is_admin'])): ?>
        <div class="h-10 w-[1px] bg-slate-200 mx-1 hidden lg:block"></div>
        <a class="flex flex-col items-center justify-center min-w-[64px] text-[#0A66C2] hover:text-[#004182] transition-all group" href="<?php echo URLROOT; ?>/admin/dashboard">
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform font-bold">admin_panel_settings</span>
          <span class="text-[10px] font-bold hidden lg:block">Admin</span>
        </a>
        <?php endif; ?>
        <?php if(hasRole('Company')): ?>
        <div class="h-10 w-[1px] bg-slate-200 mx-1 hidden lg:block"></div>
        <a class="flex flex-col items-center justify-center min-w-[64px] text-indigo-600 hover:text-indigo-800 transition-all group" href="<?php echo URLROOT; ?>/company/dashboard">
          <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform font-bold">corporate_fare</span>
          <span class="text-[10px] font-bold hidden lg:block">Company Hub</span>
        </a>
        <?php endif; ?>
        <div class="h-10 w-[1px] bg-slate-200 mx-2 hidden lg:block"></div>
        <a href="<?php echo URLROOT; ?>/user/profile" class="pn-nav-item flex flex-col items-center justify-center min-w-[64px] <?php echo pn_nav_cls('user/profile'); ?> group rounded-lg py-0.5">
          <div class="w-6 h-6 rounded-full overflow-hidden bg-slate-200 border border-slate-300 group-hover:border-slate-500 transition-all">
              <img data-user-pic="true" src="" alt="" class="w-full h-full object-cover">
          </div>
          <span class="text-[10px] font-medium flex items-center">Me <span class="material-symbols-outlined text-[14px]">arrow_drop_down</span></span>
        </a>
        <a href="<?php echo URLROOT; ?>/user/settings" class="<?php echo $pnNav === 'user/settings' ? 'text-[#0A66C2]' : 'text-slate-400'; ?> hover:text-[#0A66C2] transition-colors ml-1" title="Settings">
          <span class="material-symbols-outlined text-[22px]">settings</span>
        </a>
        <a href="<?php echo URLROOT; ?>/auth/logout" class="text-slate-400 hover:text-red-600 transition-colors ml-2" title="Logout">
          <span class="material-symbols-outlined text-[22px]">logout</span>
        </a>
      </nav>
      <?php else: ?>
          <a href="<?php echo URLROOT; ?>/auth/login" class="pn-nav-item px-5 py-1.5 rounded-full font-bold text-[#0A66C2] hover:bg-blue-50 transition-all">Sign in</a>
          <a href="<?php echo URLROOT; ?>/auth/register" class="pn-nav-item px-5 py-1.5 rounded-full font-bold bg-[#0A66C2] text-white hover:bg-[#004182] transition-all shadow-sm hover:shadow-md">Join now</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<main class="pt-14">
