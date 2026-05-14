<?php /* Company module footer */ ?>
  </main>
  <!-- Footer Component -->
  <footer class="w-full py-16 border-t border-gray-200 bg-white font-['Manrope'] antialiased mt-auto">
    <div class="max-w-[1128px] mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-12 mb-12">
      <div class="col-span-2 md:col-span-1">
        <span class="text-xl font-black text-[#0A66C2] mb-6 block">ProNetwork</span>
        <p class="text-gray-500 text-sm leading-relaxed">Connecting the world's professionals to make them more
          productive and successful.</p>
      </div>
      <div>
        <h4 class="font-title-md text-on-surface mb-4">Network</h4>
        <ul class="space-y-3">
          <li><a class="text-gray-500 text-xs hover:text-[#0A66C2] hover:underline transition-colors" href="#">About</a>
          </li>
          <li><a class="text-gray-500 text-xs hover:text-[#0A66C2] hover:underline transition-colors"
              href="#">Accessibility</a></li>
          <li><a class="text-gray-500 text-xs hover:text-[#0A66C2] hover:underline transition-colors" href="#">User
              Agreement</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-title-md text-on-surface mb-4">Legal</h4>
        <ul class="space-y-3">
          <li><a class="text-gray-500 text-xs hover:text-[#0A66C2] hover:underline transition-colors" href="#">Privacy
              Policy</a></li>
          <li><a class="text-gray-500 text-xs hover:text-[#0A66C2] hover:underline transition-colors" href="#">Cookie
              Policy</a></li>
          <li><a class="text-gray-500 text-xs hover:text-[#0A66C2] hover:underline transition-colors" href="#">Brand
              Policy</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-title-md text-on-surface mb-4">Support</h4>
        <ul class="space-y-3">
          <li><a class="text-gray-500 text-xs hover:text-[#0A66C2] hover:underline transition-colors" href="#">Guest
              Controls</a></li>
          <li><a class="text-gray-500 text-xs hover:text-[#0A66C2] hover:underline transition-colors" href="#">Community
              Guidelines</a></li>
        </ul>
      </div>
    </div>
    <div
      class="max-w-[1128px] mx-auto px-4 pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
      <p class="text-xs text-gray-500">&copy; 2026 ProNetwork Corporation</p>
      <div class="flex gap-6">
        <span class="material-symbols-outlined text-gray-400 cursor-pointer hover:text-[#0A66C2]"
          data-icon="language">language</span>
        <span class="material-symbols-outlined text-gray-400 cursor-pointer hover:text-[#0A66C2]"
          data-icon="help_outline">help_outline</span>
      </div>
    </div>
  </footer>

  <script src="<?php echo URLROOT; ?>/assets/js/forms.js?v=<?php echo time(); ?>"></script>
  <script src="<?php echo URLROOT; ?>/assets/js/main.js?v=<?php echo time(); ?>"></script>
  <script src="<?php echo URLROOT; ?>/assets/js/search.js?v=<?php echo time(); ?>"></script>
  
  <?php 
    $url = $_GET['url'] ?? '';
    if (strpos($url, 'user/feed') !== false) echo '<script src="' . URLROOT . '/assets/js/feed.js?v=' . time() . '"></script>';
    if (strpos($url, 'user/profile') !== false) echo '<script src="' . URLROOT . '/assets/js/profile.js?v=' . time() . '"></script>';
    if (strpos($url, 'network') !== false) echo '<script src="' . URLROOT . '/assets/js/network.js?v=' . time() . '"></script>';
    if (strpos($url, 'message') !== false || strpos($url, 'messaging') !== false) echo '<script src="' . URLROOT . '/assets/js/messaging.js?v=' . time() . '"></script>';
    if (strpos($url, 'notification') !== false) echo '<script src="' . URLROOT . '/assets/js/notifications.js?v=' . time() . '"></script>';
    if (strpos($url, 'job') !== false) echo '<script src="' . URLROOT . '/assets/js/jobs.js?v=' . time() . '"></script>';
  ?>
</body>
</html>
