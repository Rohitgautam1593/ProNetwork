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
                Welcome back to your hiring console.
            </h1>
            <p class="text-base font-body-lg text-on-surface-variant max-w-md">
                Access your candidate pipeline, adjust job listings, and respond to talent directly from your company dashboard.
            </p>
        </div>

        <!-- Form column -->
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl border border-outline-variant/30 ambient-shadow p-8">
                <h2 class="text-2xl font-display-md text-on-surface mb-2">Sign in</h2>
                <p class="text-sm font-body-md text-on-surface-variant mb-6 border-b border-outline-variant/20 pb-4">Stay updated on your professional world.</p>

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

                <form action="<?php echo URLROOT; ?>/company/login" method="POST" class="space-y-4" <?php echo !empty($data['successMessage']) ? 'style="opacity:0.45;pointer-events:none;"' : ''; ?>>
                    <div>
                        <label class="block font-label-md text-on-surface mb-1" for="email">Work email</label>
                        <input id="email" type="email" name="email" required placeholder="Email address"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                               class="w-full h-10 px-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm font-body-md bg-white">
                    </div>
                    <div>
                        <label class="block font-label-md text-on-surface mb-1" for="password">Password</label>
                        <div class="relative w-full">
                            <input id="password" type="password" name="password" required placeholder="Password"
                                   class="w-full h-10 pl-3 pr-10 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm font-body-md bg-white">
                            <button type="button" data-toggle-password="password" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 text-secondary hover:text-primary transition-colors flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <div></div>
                            <a class="font-label-md text-primary hover:underline text-xs" data-action="forgot-password" href="#">Forgot password?</a>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-primary text-white font-label-lg px-6 py-3 rounded-full hover:bg-[#004182] transition-colors shadow-sm flex items-center justify-center gap-2">
                            Sign in
                        </button>
                    </div>

                    <p class="text-center font-body-md text-xs text-secondary mt-4">
                        New to ProNetwork Enterprise? <a href="<?php echo URLROOT; ?>/company/register" class="font-semibold text-primary hover:underline">Create an account</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
