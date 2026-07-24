<?php require USERROOT . "/frontend/views/layouts/header.php"; ?>
<?php require USERROOT . "/frontend/views/layouts/navbar.php"; ?>

<div class="user-page-shell pt-6 pb-12">
  <div class="max-w-[1128px] mx-auto px-4 flex flex-col md:flex-row gap-6">
    <?php require USERROOT . "/frontend/views/pages/sidebar.php"; ?>

    <div class="flex-1 min-w-0">
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-6 mb-6 gap-4">
          <div>
            <span class="text-xs font-bold uppercase tracking-wider text-[#0A66C2]">About ProNetwork</span>
            <h1 class="text-3xl font-black text-slate-900 mt-1"><?php echo $data['title']; ?></h1>
          </div>
          <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 rounded-full text-xs font-semibold text-slate-600">
            <span class="material-symbols-outlined text-[14px]">hub</span>
            Professional networking platform
          </span>
        </div>

        <div class="prose prose-slate max-w-none text-slate-600 text-sm leading-relaxed space-y-6">
          <section class="space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              What We Build
            </h2>
            <p>
              ProNetwork helps people create professional profiles, discover jobs, follow companies, build connections, and keep conversations organized in one focused workspace.
            </p>
          </section>

          <section class="space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              Who It Serves
            </h2>
            <p>
              The platform supports students, professionals, companies, and administrators. Each role has a dedicated flow for profiles, jobs, hiring activity, messaging, and moderation.
            </p>
          </section>

          <section class="space-y-3">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#0A66C2] rounded-full"></span>
              Platform Values
            </h2>
            <ul class="list-disc pl-5 space-y-1">
              <li>Clear professional identity for every user.</li>
              <li>Useful job and company discovery.</li>
              <li>Safer communities through reporting and admin review.</li>
              <li>Simple, responsive tools that work across devices.</li>
            </ul>
          </section>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require USERROOT . "/frontend/views/layouts/footer.php"; ?>
