<?php require USERROOT . "/frontend/views/layouts/header.php"; ?>
<?php require USERROOT . "/frontend/views/layouts/navbar.php"; ?>

<div class="user-page-shell pt-6 pb-12">
  <div class="max-w-[1128px] mx-auto px-4 flex flex-col md:flex-row gap-6">
    <!-- Sidebar -->
    <?php require USERROOT . "/frontend/views/pages/sidebar.php"; ?>

    <!-- Main Content Panel -->
    <div class="flex-1 min-w-0">
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 md:p-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-6 mb-6 gap-4">
          <div>
            <span class="text-xs font-bold uppercase tracking-wider text-[#0A66C2]">ProNetwork Legal & Safety</span>
            <h1 class="text-3xl font-black text-slate-900 mt-1"><?php echo $data['title']; ?></h1>
          </div>
          <div class="flex flex-col sm:items-end gap-1.5 shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 rounded-full text-xs font-semibold text-slate-600">
              <span class="material-symbols-outlined text-[14px]">update</span>
              Updated May 2026
            </span>
          </div>
        </div>

        <!-- Search / Filter in Page -->
        <div class="relative mb-6">
          <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
          <input id="policy-search" type="search" placeholder="Search within this document..." class="w-full h-11 rounded-xl bg-slate-50 border border-slate-200 pl-11 pr-4 text-sm text-slate-800 focus:bg-white placeholder:text-slate-400 focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] transition-all">
        </div>

        <!-- Content -->
        <div id="policy-content" class="prose prose-slate max-w-none text-slate-600 text-sm leading-relaxed space-y-6">
          
          <section class="policy-block space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              1. What are Cookies?
            </h2>
            <p>
              Cookies are small text files sent by a website and stored on your web browser or device. They enable websites to store settings, user preferences, and session IDs so that you remain logged in and your customized settings are preserved when you reload the page.
            </p>
          </section>

          <section class="policy-block space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              2. How ProNetwork Uses Cookies
            </h2>
            <p>
              We use cookies and similar technologies (such as local storage) to recognize you when you visit our Services, remember your language preferences, and provide customized advertising.
            </p>
          </section>

          <section class="policy-block space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              3. Cookie Categories
            </h2>
            <p>
              The cookies used on ProNetwork generally fall into these groups:
            </p>
            <ul class="list-disc pl-5 space-y-2">
              <li><strong>Essential Cookies:</strong> Critical for account login authentication and security sessions.</li>
              <li><strong>Performance & Analytics:</strong> Help us measure visitor patterns and page speeds to improve interface design.</li>
              <li><strong>Preferences & Features:</strong> Remember your choices (such as dark mode preferences or search filters).</li>
            </ul>
          </section>

          <section class="policy-block space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              4. Managing Cookies
            </h2>
            <p>
              Most web browsers permit you to manage or delete cookies via your browser settings. However, if you disable essential cookies, please note that you may not be able to log in or use certain critical features of the ProNetwork application.
            </p>
          </section>

        </div>

        <!-- Feedback Widget -->
        <div class="border-t border-slate-100 mt-10 pt-8 flex flex-col items-center justify-center text-center gap-4">
          <h4 class="font-bold text-slate-800 text-sm">Was this page helpful?</h4>
          <div id="feedback-buttons" class="flex items-center gap-3">
            <button onclick="handleFeedback(true)" class="flex items-center gap-2 px-5 py-2 border border-slate-200 hover:border-blue-300 hover:bg-blue-50/50 rounded-full text-slate-600 hover:text-[#0A66C2] transition-all font-semibold text-xs active:scale-95">
              <span class="material-symbols-outlined text-[18px]">thumb_up</span> Yes
            </button>
            <button onclick="handleFeedback(false)" class="flex items-center gap-2 px-5 py-2 border border-slate-200 hover:border-red-200 hover:bg-red-50/50 rounded-full text-slate-600 hover:text-red-600 transition-all font-semibold text-xs active:scale-95">
              <span class="material-symbols-outlined text-[18px]">thumb_down</span> No
            </button>
          </div>
          <p id="feedback-thankyou" class="hidden text-sm font-bold text-green-600 flex items-center gap-1.5 animate-bounce">
            <span class="material-symbols-outlined">check_circle</span> Thank you for your feedback!
          </p>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
// Document filter logic
document.getElementById('policy-search')?.addEventListener('input', function(e) {
  const term = e.target.value.toLowerCase().trim();
  const blocks = document.querySelectorAll('#policy-content > section');
  
  blocks.forEach(block => {
    const text = block.textContent.toLowerCase();
    if (text.includes(term)) {
      block.style.display = '';
    } else {
      block.style.display = 'none';
    }
  });
});

function handleFeedback(wasHelpful) {
  document.getElementById('feedback-buttons').style.display = 'none';
  document.getElementById('feedback-thankyou').classList.remove('hidden');
}
</script>

<?php require USERROOT . "/frontend/views/layouts/footer.php"; ?>
