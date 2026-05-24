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
              1. Our Community Vision
            </h2>
            <p>
              ProNetwork connects professionals to help them become more productive and successful. To achieve this, our members must communicate and interact in a trustworthy, respectful, and professional manner. These Community Guidelines apply to all content shared on our platform.
            </p>
          </section>

          <section class="policy-block space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              2. Be Trustworthy
            </h2>
            <p>
              Only register with your real name and actual professional history. We do not tolerate false profiles, scammers, spam posts, multi-level marketing (MLM) schemes, or deceptive content. Do not attempt to compromise accounts or hack the platform.
            </p>
          </section>

          <section class="policy-block space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              3. Be Professional & Respectful
            </h2>
            <p>
              We do not tolerate harassment, bullying, discrimination, hate speech, threats, or any form of abuse. Keep comments and conversations constructive. Avoid posting obscene or sexually explicit content, graphics, or imagery.
            </p>
          </section>

          <section class="policy-block space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              4. Respect Others' Intellectual Property
            </h2>
            <p>
              Do not post articles, pictures, or videos that infringe the copyrights, trademarks, or trade secrets of other people or companies. Always attribute content to its original creator.
            </p>
          </section>

          <section class="policy-block space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              5. Reporting and Enforcement
            </h2>
            <p>
              If you see content that violates these guidelines, you can report it via the "Report" option next to any post or comment. ProNetwork moderators will review the reported content and take appropriate action, up to and including account suspension or permanent termination.
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
