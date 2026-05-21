<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>

<div class="min-h-[80vh] flex items-center justify-center bg-surface py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl border border-outline-variant/30 shadow-xl relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-primary"></div>
        
        <div class="text-center">
            <span class="material-symbols-outlined text-primary text-5xl mb-3">lock_reset</span>
            <h2 class="font-display-md text-3xl text-on-surface">Re-establish Security Secret</h2>
            <p class="font-body-md text-secondary mt-2">Update credentials for account: <strong class="text-on-surface"><?php echo htmlspecialchars($data['email']); ?></strong></p>
        </div>

        <form id="reset-password-form" class="mt-8 space-y-6">
            <input type="hidden" id="reset-email" value="<?php echo htmlspecialchars($data['email']); ?>">
            <input type="hidden" id="reset-otp" value="<?php echo htmlspecialchars($data['otp']); ?>">
            
            <div class="space-y-4">
                <div>
                    <label class="block font-label-md text-on-surface mb-1" for="new-password">New Security Password</label>
                    <div class="relative w-full">
                        <input id="new-password" type="password" required placeholder="Minimum 6 characters"
                               class="w-full h-10 pl-3 pr-10 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm font-body-md bg-white">
                        <button type="button" data-toggle-password="new-password" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 text-secondary hover:text-primary transition-colors flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block font-label-md text-on-surface mb-1" for="confirm-password">Confirm New Password</label>
                    <div class="relative w-full">
                        <input id="confirm-password" type="password" required placeholder="Verify credentials match"
                               class="w-full h-10 pl-3 pr-10 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm font-body-md bg-white">
                        <button type="button" data-toggle-password="confirm-password" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 text-secondary hover:text-primary transition-colors flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" id="reset-btn"
                        class="w-full h-10 bg-primary hover:bg-[#004182] text-white font-title-medium rounded-full shadow-md transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <span>Commit Access Secret</span>
                    <div id="reset-spinner" class="hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Toggle password visibility
        document.querySelectorAll('[data-toggle-password]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const inputId = btn.getAttribute('data-toggle-password');
                const input = document.getElementById(inputId);
                if (input) {
                    const icon = btn.querySelector('.material-symbols-outlined');
                    if (input.type === 'password') {
                        input.type = 'text';
                        if (icon) icon.textContent = 'visibility_off';
                    } else {
                        input.type = 'password';
                        if (icon) icon.textContent = 'visibility';
                    }
                }
            });
        });

        // Form handler
        const form = document.getElementById('reset-password-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('reset-email').value.trim();
            const otp = document.getElementById('reset-otp').value.trim();
            const password = document.getElementById('new-password').value.trim();
            const confirmPassword = document.getElementById('confirm-password').value.trim();
            const btn = document.getElementById('reset-btn');
            const spinner = document.getElementById('reset-spinner');

            if (password.length < 6) {
                showToast('Password must be a minimum of 6 characters.', 'error');
                return;
            }

            if (password !== confirmPassword) {
                showToast('Passwords do not match.', 'error');
                return;
            }

            btn.disabled = true;
            spinner.classList.remove('hidden');

            try {
                const response = await fetch('<?php echo URLROOT; ?>/auth/reset_password', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, otp, password })
                });
                const result = await response.json();

                if (result.success) {
                    showToast('Clearance credentials updated successfully! Re-routing...', 'success');
                    setTimeout(() => {
                        window.location.href = '<?php echo URLROOT; ?>/auth/login';
                    }, 2000);
                } else {
                    showToast(result.message || 'Reset failed.', 'error');
                }
            } catch (err) {
                showToast('A connection error occurred.', 'error');
            } finally {
                btn.disabled = false;
                spinner.classList.add('hidden');
            }
        });
    });
</script>

<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
