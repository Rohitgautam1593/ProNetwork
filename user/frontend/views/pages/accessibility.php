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
              Our Commitment
            </h2>
            <p>
              ProNetwork is committed to ensuring digital accessibility for people with disabilities. We are continually improving the user experience for everyone, and applying the relevant accessibility standards to make our professional networking platform inclusive and equitable.
            </p>
          </section>

          <section class="policy-block space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              Conformance Status
            </h2>
            <p>
              The Web Content Accessibility Guidelines (WCAG) defines requirements for designers and developers to improve accessibility for people with disabilities. It defines three levels of conformance: Level A, Level AA, and Level AAA. 
            </p>
            <p>
              ProNetwork is partially conformant with **WCAG 2.1 Level AA**. Partially conformant means that some parts of the content do not fully conform to the accessibility standard (for instance, some third-party media elements or older layouts currently being updated).
            </p>
          </section>

          <section class="policy-block space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              Technical Specifications
            </h2>
            <p>
              Accessibility of ProNetwork relies on the following technologies to work with the particular combination of web browser and any assistive technologies or plugins installed on your computer:
            </p>
            <ul class="list-disc pl-5 space-y-1">
              <li>HTML</li>
              <li>WAI-ARIA</li>
              <li>CSS</li>
              <li>JavaScript</li>
            </ul>
            <p>
              These technologies are relied upon for conformance with the accessibility standards used.
            </p>
          </section>

          <section class="policy-block space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              Feedback & Contact Information
            </h2>
            <p>
              We welcome your feedback on the accessibility of ProNetwork. Please let us know if you encounter accessibility barriers:
            </p>
            <div class="bg-blue-50/50 rounded-2xl border border-blue-100 p-4 space-y-2 text-slate-700">
              <p class="flex items-center gap-2 text-xs">
                <span class="material-symbols-outlined text-[16px] text-[#0A66C2]">mail</span>
                <strong>Email:</strong> accessibility@pronetwork.com
              </p>
              <p class="flex items-center gap-2 text-xs">
                <span class="material-symbols-outlined text-[16px] text-[#0A66C2]">location_on</span>
                <strong>Address:</strong> 100 Professional Way, Suite 400, San Francisco, CA
              </p>
            </div>
          </section>

          <!-- Interactive Accordion FAQ -->
          <section class="policy-block space-y-4 pt-4 border-t border-slate-100">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              Frequently Asked Questions
            </h2>
            
            <div class="space-y-2">
              <div class="border border-slate-200 rounded-xl overflow-hidden">
                <button onclick="toggleAccordion('faq-1')" class="w-full flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100/70 text-left font-bold text-slate-800 transition-colors">
                  <span>How can I zoom in or change text sizes in ProNetwork?</span>
                  <span id="icon-faq-1" class="material-symbols-outlined text-[20px] transition-transform">expand_more</span>
                </button>
                <div id="faq-1" class="hidden p-4 text-xs text-slate-600 bg-white border-t border-slate-100 leading-relaxed">
                  You can use your browser's default zoom settings by pressing <kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-300 rounded text-[10px]">Ctrl</kbd> + <kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-300 rounded text-[10px]">+</kbd> (on Windows/Linux) or <kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-300 rounded text-[10px]">Cmd</kbd> + <kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-300 rounded text-[10px]">+</kbd> (on macOS). Our layout is fully responsive and adjusts dynamically to zoom levels up to 200%.
                </div>
              </div>

              <div class="border border-slate-200 rounded-xl overflow-hidden">
                <button onclick="toggleAccordion('faq-2')" class="w-full flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100/70 text-left font-bold text-slate-800 transition-colors">
                  <span>Does the platform support keyboard navigation?</span>
                  <span id="icon-faq-2" class="material-symbols-outlined text-[20px] transition-transform">expand_more</span>
                </button>
                <div id="faq-2" class="hidden p-4 text-xs text-slate-600 bg-white border-t border-slate-100 leading-relaxed">
                  Yes, we are actively standardizing our interactive structures to support standard keyboard tab orders, outline focus rings, and skip-to-content links. We highly recommend using screen readers like NVDA, JAWS, or VoiceOver for the best browsing experience.
                </div>
              </div>
            </div>
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

function toggleAccordion(id) {
  const body = document.getElementById(id);
  const icon = document.getElementById('icon-' + id);
  if (body.classList.contains('hidden')) {
    body.classList.remove('hidden');
    icon.style.transform = 'rotate(180deg)';
  } else {
    body.classList.add('hidden');
    icon.style.transform = '';
  }
}

function handleFeedback(wasHelpful) {
  document.getElementById('feedback-buttons').style.display = 'none';
  document.getElementById('feedback-thankyou').classList.remove('hidden');
}
</script>

<?php require USERROOT . "/frontend/views/layouts/footer.php"; ?>
