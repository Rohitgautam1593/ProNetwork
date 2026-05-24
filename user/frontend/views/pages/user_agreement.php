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
              1. Introduction
            </h2>
            <p>
              Welcome to ProNetwork. By registering for or using our services, you enter into a legally binding agreement (even if you are using our services on behalf of a company). 
            </p>
            <p>
              Your agreement is with ProNetwork Corporation. If you do not agree to this User Agreement, do **not** click "Join Now" (or similar) and do not access or otherwise use any of our Services.
            </p>
          </section>

          <section class="policy-block space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              2. Members vs Visitors
            </h2>
            <p>
              When you register and join the ProNetwork Service, you become a **Member**. If you have chosen not to register for our Services, you may still access certain features as a **Visitor** or **Guest**. This Agreement applies to both.
            </p>
          </section>

          <section class="policy-block space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              3. Obligations & Rules of Conduct
            </h2>
            <p>
              To maintain a professional, secure network, you agree that you will:
            </p>
            <ul class="list-disc pl-5 space-y-2">
              <li>Provide accurate, up-to-date information on your profile and use your real name.</li>
              <li>Keep your account password secure and confidential.</li>
              <li>Not create a false identity, misrepresent your profile details, or create profiles for others without permission.</li>
              <li>Comply with all applicable laws, including, without limitation, privacy laws, intellectual property laws, anti-spam laws, and regulatory requirements.</li>
            </ul>
          </section>

          <section class="policy-block space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              4. Content & Intellectual Property
            </h2>
            <p>
              As between you and ProNetwork, you own the content and information that you submit or post to the Services. You grant ProNetwork a non-exclusive license to use, copy, modify, distribute, publish, and process information and content that you provide, without any further consent or compensation, subject to your settings and privacy configuration.
            </p>
          </section>

          <section class="policy-block space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              5. Disclaimer and Limit of Liability
            </h2>
            <p>
              PRO-NETWORK PROVIDES SERVICES ON AN "AS IS" AND "AS AVAILABLE" BASIS. TO THE MAXIMUM EXTENT PERMITTED BY LAW, PRO-NETWORK DISCLAIMS ALL WARRANTIES, EXPRESSED OR IMPLIED, INCLUDING WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.
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
