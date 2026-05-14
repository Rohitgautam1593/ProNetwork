<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProNetwork — Administrative Control Suite Gateway</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        portal: {
                            bg: '#0A0E1A',
                            card: 'rgba(16, 23, 42, 0.65)',
                            input: 'rgba(15, 23, 42, 0.8)',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0A0E1A;
            overflow-x: hidden;
        }
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-20px) scale(1.05); }
        }
        @keyframes floatReverse {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(20px) scale(0.95); }
        }
        .animate-float-slow {
            animation: floatSlow 12s ease-in-out infinite;
        }
        .animate-float-reverse {
            animation: floatReverse 14s ease-in-out infinite;
        }
        ::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative px-4 selection:bg-cyan-500 selection:text-white">

    <div class="absolute top-1/4 left-1/4 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-cyan-500/20 rounded-full blur-[120px] pointer-events-none animate-float-slow"></div>
    <div class="absolute bottom-1/4 right-1/4 translate-x-1/2 translate-y-1/2 w-[28rem] h-[28rem] bg-indigo-600/20 rounded-full blur-[140px] pointer-events-none animate-float-reverse"></div>
    <div class="absolute top-1/2 right-1/3 w-64 h-64 bg-blue-500/10 rounded-full blur-[90px] pointer-events-none animate-pulse duration-1000"></div>

    <div class="w-full max-w-md z-10">
        <div class="mb-6 flex justify-center">
            <a href="<?php echo URLROOT; ?>/" class="text-xs font-semibold text-slate-500 hover:text-cyan-400 transition-colors flex items-center gap-1.5 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Public Feed
            </a>
        </div>

        <div class="backdrop-blur-xl bg-portal-card border border-slate-800/80 rounded-3xl p-8 md:p-10 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.7)] relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-cyan-500/50 to-transparent"></div>

            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 shadow-lg shadow-cyan-500/30 mb-4 text-white font-extrabold text-xl tracking-wider">
                    PN
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">Admin Gateway</h1>
                <p class="text-xs text-slate-400 mt-1.5 font-normal tracking-wide">Enter root verification parameters to proceed</p>
            </div>

            <?php if (!empty($data['error'])): ?>
                <div class="mb-6 p-3.5 bg-rose-500/10 border border-rose-500/20 rounded-xl text-xs text-rose-400 flex items-start gap-2.5 animate-in fade-in zoom-in duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 shrink-0 text-rose-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium leading-relaxed"><?php echo htmlspecialchars($data['error']); ?></span>
                </div>
            <?php endif; ?>

            <form id="admin-login-form" action="<?php echo URLROOT; ?>/admin/login" method="POST" class="space-y-5" onsubmit="return validateAdminSubmit(event)">

                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Administrative Email</label>
                    <div class="relative group">
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            placeholder="command@pronetwork.com"
                            class="w-full px-4 py-3 bg-portal-input border border-slate-800 rounded-xl text-sm text-white placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-all duration-200 block"
                            autocomplete="username" required />
                    </div>
                    <p id="email-error" class="text-[11px] text-rose-400 font-medium hidden animate-in fade-in duration-150"></p>
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Access Secret</label>
                    <div class="relative group">
                        <input type="password" id="password" name="password"
                            placeholder="••••••••••••"
                            class="w-full px-4 py-3 bg-portal-input border border-slate-800 rounded-xl text-sm text-white placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-all duration-200 block"
                            autocomplete="current-password" required />
                    </div>
                    <p id="password-error" class="text-[11px] text-rose-400 font-medium hidden animate-in fade-in duration-150"></p>
                </div>

                <div class="pt-2">
                    <button type="submit" id="submit-btn"
                        class="w-full py-3.5 px-4 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white text-sm font-bold tracking-wide rounded-xl shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/30 active:scale-[0.99] transition-all duration-200 flex items-center justify-center gap-2 group relative">

                        <span id="btn-text">Authenticate Session</span>

                        <svg id="btn-arrow" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>

                        <div id="btn-spinner" class="hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin absolute right-4"></div>
                    </button>
                </div>

                <div class="pt-4 text-center border-t border-slate-800/60 mt-6">
                    <p class="text-[11px] text-slate-500 tracking-tight">
                        Unregistered access vectors strictly logged. Enterprise firewall monitoring Active.
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        function validateAdminSubmit(e) {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const emailError = document.getElementById('email-error');
            const passwordError = document.getElementById('password-error');
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnArrow = document.getElementById('btn-arrow');
            const btnSpinner = document.getElementById('btn-spinner');

            emailError.classList.add('hidden');
            passwordError.classList.add('hidden');
            emailInput.classList.remove('border-rose-500/60');
            passwordInput.classList.remove('border-rose-500/60');

            const emailVal = emailInput.value.trim();
            const passwordVal = passwordInput.value.trim();
            let isValid = true;

            if (!emailVal) {
                emailError.textContent = 'Email parameter strictly required.';
                emailError.classList.remove('hidden');
                emailInput.classList.add('border-rose-500/60');
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
                emailError.textContent = 'Invalid structural format string.';
                emailError.classList.remove('hidden');
                emailInput.classList.add('border-rose-500/60');
                isValid = false;
            }

            if (!passwordVal) {
                passwordError.textContent = 'Secret authorization token required.';
                passwordError.classList.remove('hidden');
                passwordInput.classList.add('border-rose-500/60');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                return false;
            }

            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-85', 'cursor-not-allowed');
            btnText.textContent = 'Verifying Clearance...';
            if (btnArrow) btnArrow.classList.add('hidden');
            btnSpinner.classList.remove('hidden');

            return true;
        }

        document.getElementById('email').addEventListener('input', function() {
            document.getElementById('email-error').classList.add('hidden');
            this.classList.remove('border-rose-500/60');
        });

        document.getElementById('password').addEventListener('input', function() {
            document.getElementById('password-error').classList.add('hidden');
            this.classList.remove('border-rose-500/60');
        });
    </script>
</body>
</html>
