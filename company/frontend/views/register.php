<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>

<main class="bg-surface-container-low min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 flex flex-col lg:flex-row gap-12 items-center">
        <!-- Brand column -->
        <div class="flex-1 space-y-6">
            <div class="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary-fixed/30 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-primary">
                <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                Employer workspace
            </div>
            <h1 class="text-4xl font-display-md text-on-surface leading-tight">
                Hire with a console built for clarity.
            </h1>
            <p class="text-base font-body-lg text-on-surface-variant max-w-md">
                Post roles, track applicants, and manage your company profile—aligned with how professionals already use ProNetwork.
            </p>
            <ul class="space-y-4 pt-6 text-sm font-body-md text-on-surface-variant">
                <li class="flex gap-3 items-center">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-fixed text-primary">
                        <span class="material-symbols-outlined text-xl">work</span>
                    </span>
                    <span><strong class="text-on-surface font-semibold">Job listings</strong> synced with the member feed</span>
                </li>
                <li class="flex gap-3 items-center">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-fixed text-primary">
                        <span class="material-symbols-outlined text-xl">groups</span>
                    </span>
                    <span><strong class="text-on-surface font-semibold">Applicant tools</strong> to review and adjust pipelines</span>
                </li>
                <li class="flex gap-3 items-center">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-fixed text-primary">
                        <span class="material-symbols-outlined text-xl">verified</span>
                    </span>
                    <span><strong class="text-on-surface font-semibold">Verified employer</strong> presence on your page</span>
                </li>
            </ul>
        </div>

        <!-- Form column -->
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl border border-outline-variant/30 ambient-shadow p-8">
                <h2 class="text-2xl font-display-md text-on-surface mb-2">Create your enterprise hub</h2>
                <p class="text-sm font-body-md text-on-surface-variant mb-6 border-b border-outline-variant/20 pb-4">One account for posting jobs and managing your brand.</p>

                <?php if (!empty($data['error'])): ?>
                    <div class="mb-6 flex gap-3 rounded-xl border border-error/30 bg-error-container p-4 text-sm text-on-error-container" role="alert">
                        <span class="material-symbols-outlined shrink-0 text-error">error</span>
                        <div><?php echo htmlspecialchars($data['error']); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($data['successMessage'])): ?>
                    <div class="mb-6 flex gap-3 rounded-xl border border-[#0d592f]/30 bg-[#e6f4ea] p-4 text-sm font-medium text-[#0d592f]">
                        <span class="material-symbols-outlined shrink-0 animate-spin text-[#0d592f]">progress_activity</span>
                        <div><?php echo htmlspecialchars($data['successMessage']); ?></div>
                    </div>
                    <script>
                        setTimeout(() => { window.location.href = "<?php echo URLROOT; ?>/company/dashboard"; }, 1400);
                    </script>
                <?php endif; ?>

                <form action="<?php echo URLROOT; ?>/company/register" method="POST" class="space-y-4" <?php echo !empty($data['successMessage']) ? 'style="opacity:0.45;pointer-events:none;"' : ''; ?>>
                    <div>
                        <label class="block font-label-md text-on-surface mb-1" for="full_name">Full name <span class="text-error">*</span></label>
                        <input id="full_name" type="text" name="full_name" required placeholder="John Doe"
                               value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                               class="w-full h-10 px-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm font-body-md bg-white">
                    </div>
                    <div>
                        <label class="block font-label-md text-on-surface mb-1" for="email">Work email <span class="text-error">*</span></label>
                        <input id="email" type="email" name="email" required placeholder="talent@yourcompany.com"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                               class="w-full h-10 px-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm font-body-md bg-white">
                    </div>
                    <div>
                        <label class="block font-label-md text-on-surface mb-1" for="password">Password <span class="text-error">*</span></label>
                        <div class="relative w-full">
                            <input id="password" type="password" name="password" required placeholder="Min. 6 characters" minlength="6"
                                   class="w-full h-10 pl-3 pr-10 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm font-body-md bg-white">
                            <button type="button" data-toggle-password="password" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 text-secondary hover:text-primary transition-colors flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-primary text-white font-label-lg px-6 py-3 mt-2 rounded-full hover:bg-[#004182] transition-colors shadow-sm flex items-center justify-center gap-2">
                        Create account
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>

                    <p class="text-center font-body-md text-xs text-secondary mt-4">
                        Already registered? <a href="<?php echo URLROOT; ?>/company/login" class="font-semibold text-primary hover:underline">Sign in</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
