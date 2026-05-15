<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>
<!-- Hero Section -->

    <section class="relative bg-white overflow-hidden">
      <div class="max-w-[1128px] mx-auto px-4 py-20 md:py-32 grid md:grid-cols-2 gap-12 items-center">
        <div class="z-10">
          <h1 class="font-display-lg text-5xl md:text-6xl text-primary mb-6 leading-tight">
            Connect with professionals worldwide
          </h1>
          <p class="font-body-lg text-secondary mb-10 max-w-lg">
            Build your professional brand, stay informed about your industry, and discover your next great career
            opportunity.
          </p>
          <div class="flex flex-col sm:flex-row flex-wrap gap-3.5 mb-5">
            <a href="<?php echo URLROOT; ?>/auth/register"
              class="px-7 py-3.5 bg-primary-container text-white rounded-full font-title-md hover:bg-[#004182] active:scale-95 transition-all duration-300 text-center shadow-md shadow-primary-container/20 flex items-center justify-center gap-1.5 group">
              <span>Join now</span>
              <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
            <a href="<?php echo URLROOT; ?>/company/login"
              class="px-7 py-3.5 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-full font-title-md hover:opacity-95 active:scale-95 transition-all duration-300 text-center shadow-md shadow-slate-900/20 flex items-center justify-center gap-1.5 border border-slate-700/40 group">
              <span class="material-symbols-outlined text-base text-indigo-400 group-hover:scale-110 transition-transform">corporate_fare</span>
              <span>Continue as Company</span>
            </a>
            <button onclick="window.scrollTo({top: 600, behavior: 'smooth'})"
              class="px-7 py-3.5 border-2 border-primary-container/40 text-primary-container rounded-full font-title-md hover:bg-primary-fixed hover:border-primary-container transition-all flex items-center justify-center gap-1 text-center">
              <span>Learn more</span>
            </button>
          </div>
          <div class="flex items-center gap-2 text-xs text-secondary mt-2 backdrop-blur-xs py-1 rounded-lg">
            <span class="flex h-2 w-2 relative shrink-0">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span class="leading-tight">Hiring for your company? <a href="<?php echo URLROOT; ?>/company/index" class="text-primary font-semibold hover:underline">Create an employer workspace</a> — it takes a minute.</span>
          </div>
        </div>
        <div class="relative">
          <div
            class="absolute -top-10 -right-10 w-64 h-64 bg-primary-fixed rounded-full mix-blend-multiply filter blur-3xl opacity-30">
          </div>
          <div
            class="absolute -bottom-10 -left-10 w-64 h-64 bg-tertiary-fixed rounded-full mix-blend-multiply filter blur-3xl opacity-30">
          </div>
          <img class="rounded-xl shadow-2xl relative z-10 w-full aspect-[4/3] object-cover"
            data-alt="Professional workspace cover"
            src="<?php echo pn_cover_image_url(); ?>" />
        </div>
      </div>
    </section>
    <!-- Trusted Companies -->
    <section class="py-12 bg-surface-container-low border-y border-outline-variant/30">
      <div class="max-w-[1128px] mx-auto px-4">
        <p class="text-center font-label-md text-secondary uppercase tracking-widest mb-10">Trusted by industry leaders
        </p>
        <div class="flex flex-wrap justify-center md:justify-between items-center gap-8 opacity-60 grayscale">
          <div class="flex items-center gap-2">
            <img class="w-10 h-10 object-contain" src="<?php echo pn_upload_url('companies', 'logos/nexa.png'); ?>" alt="">
            <span class="font-title-lg">Nexa Analytics</span>
          </div>
          <div class="flex items-center gap-2">
            <img class="w-10 h-10 object-contain" src="<?php echo pn_upload_url('companies', 'logos/cloudscale.png'); ?>" alt="">
            <span class="font-title-lg">CloudScale</span>
          </div>
          <div class="flex items-center gap-2">
            <img class="w-10 h-10 object-contain" src="<?php echo pn_upload_url('companies', 'logos/greengrid.png'); ?>" alt="">
            <span class="font-title-lg">GreenGrid</span>
          </div>
          <div class="flex items-center gap-2">
            <img class="w-10 h-10 object-contain" src="<?php echo pn_upload_url('companies', 'logos/cloudscale.png'); ?>" alt="">
            <span class="font-title-lg">CloudNexus</span>
          </div>
          <div class="flex items-center gap-2">
            <img class="w-10 h-10 object-contain" src="<?php echo pn_upload_url('companies', 'logos/nexa.png'); ?>" alt="">
            <span class="font-title-lg">VoltDrive</span>
          </div>
        </div>
      </div>
    </section>
    <!-- Features Bento Grid -->
    <section class="py-24 bg-background">
      <div class="max-w-[1128px] mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="font-display-md text-4xl mb-4">Everything you need to succeed</h2>
          <p class="font-body-lg text-secondary">Advanced tools for the modern professional journey.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 h-auto md:h-[600px]">
          <!-- Networking Card -->
          <div
            class="md:col-span-8 bg-white p-8 rounded-xl ambient-shadow flex flex-col justify-between overflow-hidden relative border border-outline-variant/20">
            <div>
              <span class="material-symbols-outlined text-primary text-4xl mb-4" data-icon="hub">hub</span>
              <h3 class="font-title-lg text-2xl mb-2">Strategic Networking</h3>
              <p class="font-body-md text-on-surface-variant max-w-sm">Connect with peers, mentors, and decision-makers
                in your specific industry niche using our intelligent matching algorithm.</p>
            </div>
            <div class="mt-8 flex gap-2">
              <span class="px-4 py-1 rounded-full bg-primary-fixed text-on-primary-fixed-variant font-label-md">Verified
                Networks</span>
              <span
                class="px-4 py-1 rounded-full bg-secondary-container text-on-secondary-container font-label-md">Direct
                Messaging</span>
            </div>
            <img class="absolute -right-10 bottom-0 w-1/2 rounded-tl-xl object-cover shadow-lg hidden md:block"
              data-alt="Professional networking image"
              src="<?php echo pn_upload_url('posts', '1778244004_IMG-20240307-WA00139.jpg'); ?>" />
          </div>
          <!-- Growth Card -->
          <div
            class="md:col-span-4 bg-primary-container text-white p-8 rounded-xl ambient-shadow flex flex-col items-start border border-primary">
            <span class="material-symbols-outlined text-white text-4xl mb-4" data-icon="trending_up">trending_up</span>
            <h3 class="font-title-lg text-2xl mb-2">Professional Growth</h3>
            <p class="font-body-md opacity-90 mb-8">Access curated courses and industry insights to stay ahead of the
              curve in a rapidly changing market.</p>
            <button
              class="mt-auto px-6 py-2 bg-white text-primary-container rounded-full font-label-lg hover:bg-primary-fixed transition-colors">Start
              Learning</button>
          </div>
          <!-- Jobs Card -->
          <div class="md:col-span-4 bg-white p-8 rounded-xl ambient-shadow border border-outline-variant/20">
            <span class="material-symbols-outlined text-primary text-4xl mb-4" data-icon="work">work</span>
            <h3 class="font-title-lg text-2xl mb-2">Job Search</h3>
            <p class="font-body-md text-on-surface-variant">Find roles that match your skills and aspirations with
              personalized job alerts and one-click applications.</p>
          </div>
          <!-- Community Card -->
          <div
            class="md:col-span-8 bg-surface-container p-8 rounded-xl ambient-shadow border border-outline-variant/20 flex items-center justify-between">
            <div class="max-w-md">
              <span class="material-symbols-outlined text-primary text-4xl mb-4" data-icon="groups">groups</span>
              <h3 class="font-title-lg text-2xl mb-2">Vibrant Communities</h3>
              <p class="font-body-md text-on-surface-variant">Join groups tailored to your expertise and participate in
                high-level professional discussions.</p>
            </div>
            <div class="flex -space-x-4">
              <img class="w-12 h-12 rounded-full border-2 border-white object-cover"
                data-alt="Portrait headshot of a professional"
                src="<?php echo pn_upload_url('profiles', '1778246058_IMG-20240915-WA0040.jpg'); ?>" />
              <img class="w-12 h-12 rounded-full border-2 border-white object-cover"
                data-alt="Portrait headshot of a professional"
                src="<?php echo pn_upload_url('profiles', '1778650251_46eeb713fe06f091.jpg'); ?>" />
              <img class="w-12 h-12 rounded-full border-2 border-white object-cover"
                data-alt="Portrait headshot of a professional"
                src="<?php echo pn_upload_url('profiles', '1778245058_IMG_20240111_163428.jpg'); ?>" />
              <div
                class="w-12 h-12 rounded-full border-2 border-white bg-primary text-white flex items-center justify-center font-label-md">
                +2k</div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Testimonials -->
    <section class="py-24 bg-white">
      <div class="max-w-[1128px] mx-auto px-4">
        <h2 class="font-display-md text-3xl mb-12 text-center">Voices from the community</h2>
        <div class="grid md:grid-cols-3 gap-8">
          <!-- Testimonial 1 -->
          <div class="p-8 rounded-xl bg-surface-container-low border border-outline-variant/20 ambient-shadow">
            <div class="flex items-center gap-4 mb-6">
              <img class="w-16 h-16 rounded-full object-cover"
                data-alt="Professional headshot"
                src="<?php echo pn_upload_url('profiles', '1778246058_IMG-20240915-WA0040.jpg'); ?>" />
              <div>
                <p class="font-title-md">Marcus Chen</p>
                <p class="text-xs text-secondary uppercase font-label-md">Senior Architect</p>
              </div>
            </div>
            <p class="font-body-md text-on-surface-variant italic leading-relaxed">
              "ProNetwork revolutionized how I approach executive networking. Within three months, I secured a partnership
              that redefined my firm's trajectory."
            </p>
          </div>
          <!-- Testimonial 2 -->
          <div class="p-8 rounded-xl bg-surface-container-low border border-outline-variant/20 ambient-shadow">
            <div class="flex items-center gap-4 mb-6">
              <img class="w-16 h-16 rounded-full object-cover"
                data-alt="Professional headshot"
                src="<?php echo pn_upload_url('profiles', '1778650251_46eeb713fe06f091.jpg'); ?>" />
              <div>
                <p class="font-title-md">Sarah Jenkins</p>
                <p class="text-xs text-secondary uppercase font-label-md">Product Lead</p>
              </div>
            </div>
            <p class="font-body-md text-on-surface-variant italic leading-relaxed">
              "The quality of learning content here is unmatched. It's not just videos; it's a mentorship-driven
              ecosystem that values real growth."
            </p>
          </div>
          <!-- Testimonial 3 -->
          <div class="p-8 rounded-xl bg-surface-container-low border border-outline-variant/20 ambient-shadow">
            <div class="flex items-center gap-4 mb-6">
              <img class="w-16 h-16 rounded-full object-cover"
                data-alt="Professional headshot"
                src="<?php echo pn_upload_url('profiles', '1778245058_IMG_20240111_163428.jpg'); ?>" />
              <div>
                <p class="font-title-md">David Miller</p>
                <p class="text-xs text-secondary uppercase font-label-md">Freelance Specialist</p>
              </div>
            </div>
            <p class="font-body-md text-on-surface-variant italic leading-relaxed">
              "As a freelancer, finding reliable high-ticket clients was difficult. ProNetwork made my portfolio visible to
              the right people at the right time."
            </p>
          </div>
        </div>
      </div>
    </section>
    <!-- CTA Section -->
    <section class="py-24 bg-primary text-white">
      <div class="max-w-[1128px] mx-auto px-4 text-center">
        <h2 class="font-display-lg text-4xl md:text-5xl mb-6">Ready to elevate your career?</h2>
        <p class="font-body-lg mb-10 max-w-2xl mx-auto opacity-90">Join millions of professionals who are building their
          future on ProNetwork today.</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
          <button
            class="px-10 py-4 bg-white text-primary font-title-md rounded-full hover:bg-primary-fixed transition-all active:scale-95">Get
            Started Now</button>
          <button
            class="px-10 py-4 border-2 border-white text-white font-title-md rounded-full hover:bg-white/10 transition-all active:scale-95">Contact
            Sales</button>
        </div>
      </div>
    </section>
  <?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
