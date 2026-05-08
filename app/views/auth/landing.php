<?php require APPROOT . '/views/layouts/header.php'; ?>
<?php require APPROOT . '/views/layouts/navbar.php'; ?>
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
          <div class="flex flex-col sm:flex-row gap-4">
            <a href="<?php echo URLROOT; ?>/auth/register"
              class="px-8 py-4 bg-primary-container text-white rounded-full font-title-md hover:bg-[#004182] active:scale-95 transition-all text-center">
              Join now
            </a>
            <button
              class="px-8 py-4 border-2 border-primary-container text-primary-container rounded-full font-title-md hover:bg-primary-fixed transition-all">
              Learn more
            </button>
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
            data-alt="Group of diverse young professionals collaborating in a bright, modern open-plan office with glass walls and natural light"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoIQZTEpOASOuRcRC-6ZxKWVf2R3hCdoH48j5SLB0BB1K2l4Fm6DEUwNeWvRx1cruMoEy6frBoiZL3NEub6O_UxBNGkKK5JCEYjZrc7te3yzDQOewFFlV9mX7r7J4mdPS-XYeUTiujNdiAiVBmhMd8h2LuexNzlVXuIqI8xOM4s0Jxe84x2gWl2jjC6pCE4_mfWXYf_yN_HzxRj3r7xAHkJu-Yg1C19vzbznMq2ukTlzAiaPowCh7n8BgGE-7ubMoe7cqsgv9LiPAq" />
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
            <span class="material-symbols-outlined text-4xl" data-icon="corporate_fare">corporate_fare</span>
            <span class="font-title-lg">GlobalTech</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-4xl" data-icon="rocket_launch">rocket_launch</span>
            <span class="font-title-lg">AstroSystems</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-4xl" data-icon="eco">eco</span>
            <span class="font-title-lg">BioSphere</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-4xl" data-icon="cloud">cloud</span>
            <span class="font-title-lg">CloudNexus</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-4xl" data-icon="bolt">bolt</span>
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
              data-alt="Close up of two professionals shaking hands across a conference table in a sun-drenched office setting"
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuCpv19LTrU6f9UScNf7TH5jJ0qPX76OvlShsQDBrlx2fAN9RSwk0ejblatSejhkN5NYKURTOP4bva1rcAPgZn2EAXAtKLifl48pEfOtfNGIcF8EONauG0RIXY3GLf1Am2SlbyM_qe4bdrf8tnh27OYj2nfrVkxmcaAS7xfgmS37eQ7NwK6ny6f-FU6cWQafzrjcn5q_10-nV3pZr-MdVKJMw4HxVzcPVEuGSYfz3Hd-rLP2N2_Oxshk07UHFuGRp0iGyWeS_fsGM_wK" />
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
                data-alt="Portrait headshot of a smiling professional man"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCuN_qbvJKhwrag6ktKWvTMaXvu1voAVx4zZTb43jLj1Bm47z8jONeAoBNm8BAuE4a5o0UnlRBNJHu9zI1E9yAJ6zx1HZecCgBffEfEfIePMRDBHEVxqNlXhGb5BP4t78dgMFlR5OSmCqcl7MFkQmXpOq6Vp3Fd7cDwTaO3Qmd9HMQTEm9G5bjupFEbIMx6AMNfeT9JprqMueBu-TIZZHMsTfRDYw9zr1mxZcntE6XINJ0RLk8C-ItRITBm4S7y7HDPkFhVKOh8Qy6N" />
              <img class="w-12 h-12 rounded-full border-2 border-white object-cover"
                data-alt="Portrait headshot of a smiling professional woman"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDPHcxWyrhZXpL8YM7S_s-0xH_fFvZz-QOxcecX3Vc3UUdhWgJclqhnjOEHqtqGYb15L0Rh6B_oGtWQd5eEmWQNsjgpNVJuRH-TGYU0Xu-0uV6yVGohmYmlFt0WeYjSQX4krc9QxACEybSgxF4BHnb7-IPgPar2quJl7pUKh07gE7BDt6QH_xcQOA1aZsbixj39aTdk8PBfFjdrhfeYJ4Fk3xSG0_Pwkbp5WsCwHKwgZbIsZ461L6KcZ_f-BtIgzMZ5-4Bao-Oz1dfp" />
              <img class="w-12 h-12 rounded-full border-2 border-white object-cover"
                data-alt="Portrait headshot of a professional man with glasses"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuB-MXBn8JCVRAWM66-d8-v-_p0fUq96npTInMGYYlPNWxbuLWH3YAnvufCl0-QBsh3RT0ZmRPpie0fC7_QQ44Tola7renSw6dIJ-J4kD803SC46qMnwBCOPd34qymKaum8rTP6Gc52Eyn53gbkJekPt7Gh5tzBzhHAuhrxWRr6TncY3WzkEv_baU5Z34chRID4yGnrjbUCTeVSXgKZCMhX9Lligb1knpjs43IpPaC1TJfVDdf08Z00DNM-Q59UFRCmpknJV5SLOoneR" />
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
                data-alt="Professional man in a suit smiling confidently, executive setting"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuA152EHvAo4rES34bebRISi0lkWONn5b0IlLonUTyCX8k3Rq7z0BW79ErPin5-SnMg0ATW1VgKp15ZqwLPrdHBTB7GDnz5qslELl2lXlXZXxumaYfAnQJxWar31HsJdZjg1WSyEQThl94lwZNZEuPkSQeC6jY-sQHICT5CCfFxi25qsoFUdQ6aXqtgWX5Qy4Qt_sjni11Kmb45yX9UQBXIZMUyYmQtDJlRi3pLtQeBO8ISY7wshOJpyK_s79awqN0thBceEFVSeAlE7" />
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
                data-alt="Professional woman looking focused and friendly in a modern office background"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBrA-DIl2RqA6Hz6nvYot4wlj9tkdcU-NL-YFnHItUnmF3qcblmnhGkxIEBvHy7rkmWG_o9l9508-8lTKiNfsBaw4y4V2ySD0BS6vSXqqFixHiHs-qwNR2-cf2dFafnrVtCYBDQWShqbKXhdgFS5n-JusKWmMEOUDPQXU9DKFG4OPWxy1qubmaOR5rUg3-bIwEkiqnkKqzzpDWUZ68oNnNXoZIivtoSH2hprOnDBS0QCAZsxEdXak1Bo7U15jCkPmptfktgI6y724Q-" />
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
                data-alt="Young professional man working on a laptop in a trendy cafe environment"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuD-Fd7Azkd_p250h0lkNA6NAeRmSru6AGEWAbU5BsCVNYWppiaOCtdiDtDmOUTWtt72l9__ISJa7fwGK2M74NIIlZUbkrM5c9UFnfTKxGACmFUttrshZSds9or2S4zy9IykEN-tOzd9R5JiJeDu5t9wVgnSUNaOuDJOQQi2kuUbM_ESkJQomlR5pnZfiBLq8OHiKO2w35AfPiiqh0gT5-XksyPN58097YdqgR0mYeYYS1UgAyv48J7Z8I1zac1MG82J5xxpYkSvQG2m" />
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
  <?php require APPROOT . '/views/layouts/footer.php'; ?>
