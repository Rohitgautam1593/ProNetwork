<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>

<!-- Auth Layout Wrapper -->
<main class="min-h-screen flex flex-col md:flex-row max-w-[1440px] mx-auto bg-white overflow-hidden shadow-xl">
<!-- Brand / Marketing Section (Left) -->
<section class="hidden md:flex flex-1 relative bg-primary items-center justify-center p-xl overflow-hidden">
<!-- Decorative Background Element -->
<div class="absolute inset-0 z-0 opacity-20">
<img alt="Professionals collaborating" class="w-full h-full object-cover" data-alt="Modern office setting with professionals collaborating" src="<?php echo pn_cover_image_url(); ?>"/>
</div>
<div class="absolute inset-0 bg-gradient-to-br from-primary via-primary/90 to-on-primary-fixed-variant z-10"></div>
<div class="relative z-20 max-w-md text-on-primary text-center">
    <div class="mb-lg flex flex-col items-center justify-center gap-3">
        <span class="pn-brand-mark" style="width: 3.25rem; height: 3.25rem; border-radius: 16px; font-size: 1.75rem; box-shadow: 0 12px 28px rgba(10, 102, 194, 0.35);">P</span>
        <span class="font-display-lg text-[48px] tracking-tight font-black">ProNetwork</span>
    </div>
    <h1 class="font-display-md text-display-md mb-md">Welcome to your professional community</h1>
    <p class="font-body-lg text-body-lg text-on-primary/80 mb-xl">
                        Connect with industry leaders, discover your next career move, and grow your professional identity.
                    </p>
    <div class="grid grid-cols-2 gap-md">
    <div class="p-md rounded-lg bg-white/10 backdrop-blur-md border border-white/20">
    <span class="material-symbols-outlined text-4xl mb-sm" data-icon="work">work</span>
    <p class="font-label-lg text-label-lg">1.2M+ Jobs</p>
    </div>
    <div class="p-md rounded-lg bg-white/10 backdrop-blur-md border border-white/20">
    <span class="material-symbols-outlined text-4xl mb-sm" data-icon="groups">groups</span>
    <p class="font-label-lg text-label-lg">Expert Groups</p>
    </div>
    </div>
    </div>
    </section>
    <!-- Form Section (Right) -->
    <section class="flex-1 flex flex-col justify-center items-center p-md md:p-xl bg-surface-container-lowest overflow-y-auto custom-scrollbar">
    <!-- Mobile Brand Logo -->
    <div class="md:hidden mb-lg flex justify-center">
      <a href="<?php echo URLROOT; ?>" class="pn-brand flex items-center gap-2 flex-shrink-0 rounded-xl px-1" aria-label="ProNetwork home">
        <span class="pn-brand-mark">P</span>
        <span class="text-xl font-black text-slate-950 tracking-tight">ProNetwork</span>
      </a>
    </div>
<div class="w-full max-w-[400px] space-y-lg">
<!-- Toggle Header -->
<div class="text-center">
<h2 class="font-display-md text-display-md text-on-background mb-xs">Join ProNetwork</h2>
<p class="font-body-md text-on-surface-variant">Stay updated on your professional world</p>
</div>
<!-- Forms Container -->
<div class="bg-white rounded-xl p-0" id="auth-container">
<!-- Tabs Logic -->
<div class="flex border-b border-outline-variant mb-lg">
<button id="tab-signin" onclick="switchTab('signin')" class="flex-1 py-md font-title-md text-primary border-b-2 border-primary">Login</button>
<button id="tab-signup" onclick="switchTab('signup')" class="flex-1 py-md font-title-md text-on-surface-variant hover:text-primary transition-colors">Register</button>
</div>

<!-- Login Form -->
<form id="form-signin"  class="space-y-md" method="POST" novalidate>
<div>
<label class="block font-label-lg text-label-lg text-on-surface mb-xs" for="email">Email</label>
<input class="w-full h-[48px] px-md rounded border border-outline-variant bg-white font-body-md form-input-focus" id="email" name="email" placeholder="name@company.com" type="email" autocomplete="email"/>
</div>
<div>
<label class="block font-label-lg text-label-lg text-on-surface mb-xs" for="password">Password</label>
<div class="relative w-full">
<input class="w-full h-[48px] pl-md pr-12 rounded border border-outline-variant bg-white font-body-md form-input-focus" id="password" name="password" placeholder="••••••••" type="password" autocomplete="current-password"/>
<button type="button" data-toggle-password="password" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 text-secondary hover:text-primary transition-colors flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
</div>
<div class="flex items-center justify-between">
<label class="flex items-center space-x-sm cursor-pointer">
<input class="rounded border-outline-variant text-primary focus:ring-primary h-4 w-4" type="checkbox"/>
<span class="font-body-md text-on-surface-variant">Remember me</span>
</label>
<a class="font-label-lg text-primary hover:underline" data-action="forgot-password" href="#">Forgot password?</a>
</div>
<!-- Math Captcha Security Check -->
<div class="space-y-xs">
<label class="block font-label-lg text-label-lg text-on-surface mb-xs" for="signin-captcha">
    Security Check: Solve <span id="signin-captcha-question" class="font-bold text-primary"><?php echo CaptchaHelper::getQuestion(); ?></span>
</label>
<input class="w-full h-[48px] px-md rounded border border-outline-variant bg-white font-body-md form-input-focus" id="signin-captcha" name="captcha" placeholder="Your answer" type="number" required autocomplete="off"/>
</div>
<button class="w-full h-[48px] bg-primary text-on-primary font-title-md rounded-full hover:bg-[#004182] transition-colors shadow-sm" type="submit">
                            Sign In
                        </button>
<!-- Divider -->
<div class="relative my-xl">
<div class="absolute inset-0 flex items-center">
<div class="w-full border-t border-outline-variant"></div>
</div>
<div class="relative flex justify-center text-label-md uppercase">
<span class="bg-white px-md text-on-surface-variant">Or continue with</span>
</div>
</div>
<!-- Social Logins -->
<div class="grid grid-cols-2 gap-md">
<button type="button" data-provider="google" class="flex items-center justify-center space-x-sm h-[48px] border border-outline-variant rounded-full hover:bg-surface-container transition-colors">
<span class="material-symbols-outlined text-[#0A66C2] text-[20px]">account_circle</span>
<span class="font-label-lg">Google</span>
</button>
<button type="button" data-provider="linkedin" class="flex items-center justify-center space-x-sm h-[48px] border border-outline-variant rounded-full hover:bg-surface-container transition-colors">
<span class="material-symbols-outlined text-[#0A66C2]" data-icon="account_circle">account_circle</span>
<span class="font-label-lg">LinkedIn</span>
</button>
</div>
</form>

<!-- Registration Form Fields -->
<form id="form-signup"  class="space-y-md hidden" method="POST" novalidate>
<div>
<label class="block font-label-lg text-label-lg text-on-surface mb-xs" for="fullname">Full Name</label>
<input class="w-full h-[48px] px-md rounded border border-outline-variant bg-white font-body-md form-input-focus" id="fullname" name="fullname" placeholder="John Doe" type="text" autocomplete="name"/>
</div>
<div>
<label class="block font-label-lg text-label-lg text-on-surface mb-xs" for="signup-email">Email</label>
<input class="w-full h-[48px] px-md rounded border border-outline-variant bg-white font-body-md form-input-focus" id="signup-email" name="email" placeholder="name@company.com" type="email" autocomplete="email"/>
</div>
<div>
<label class="block font-label-lg text-label-lg text-on-surface mb-xs" for="signup-password">Password</label>
<div class="relative w-full">
<input class="w-full h-[48px] pl-md pr-12 rounded border border-outline-variant bg-white font-body-md form-input-focus" id="signup-password" name="password" placeholder="••••••••" type="password" autocomplete="new-password"/>
<button type="button" data-toggle-password="signup-password" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 text-secondary hover:text-primary transition-colors flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
<div class="mt-2">
<div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden"><div id="pw-strength-bar" class="h-full rounded-full transition-all duration-300" style="width:0%"></div></div>
<p id="pw-strength-label" class="text-xs mt-1"></p>
</div>
</div>
<div>
<label class="block font-label-lg text-label-lg text-on-surface mb-xs">What is your professional role?</label>
<input id="role-input" name="role" type="hidden" />
<div class="flex flex-wrap gap-sm">
            <button type="button" onclick="selectRole('Professional', this)" class="px-md py-sm rounded-full border border-primary bg-primary-container/10 text-primary font-label-lg">
                                        Professional
                                    </button>
            <button type="button" onclick="selectRole('Student', this)" class="px-md py-sm rounded-full border border-outline-variant text-on-surface-variant font-label-lg hover:border-primary">
                                        Student
                                    </button>
</div>
</div>
<label class="flex items-center space-x-sm cursor-pointer">
<input id="terms-check" name="terms" class="rounded border-outline-variant text-primary focus:ring-primary h-4 w-4" type="checkbox" />
<span class="font-body-md text-on-surface-variant text-xs">I agree to the User Agreement, Privacy Policy, and Cookie Policy.</span>
</label>
<!-- Math Captcha Security Check -->
<div class="space-y-xs">
<label class="block font-label-lg text-label-lg text-on-surface mb-xs" for="signup-captcha">
    Security Check: Solve <span id="signup-captcha-question" class="font-bold text-primary"><?php echo CaptchaHelper::getQuestion(); ?></span>
</label>
<input class="w-full h-[48px] px-md rounded border border-outline-variant bg-white font-body-md form-input-focus" id="signup-captcha" name="captcha" placeholder="Your answer" type="number" required autocomplete="off"/>
</div>
<button class="w-full h-[48px] border-2 bg-[#0A66C2] text-white font-title-md rounded-full hover:bg-[#004182] transition-colors" type="submit">
                                Create an account
                            </button>
</form>

</div>

<script>
function switchTab(tab) {
    const signinForm = document.getElementById('form-signin');
    const signupForm = document.getElementById('form-signup');
    const tabSignin = document.getElementById('tab-signin');
    const tabSignup = document.getElementById('tab-signup');

    if (tab === 'signup') {
        signinForm.classList.add('hidden');
        signupForm.classList.remove('hidden');
        tabSignup.classList.add('text-primary', 'border-b-2', 'border-primary');
        tabSignup.classList.remove('text-on-surface-variant');
        tabSignin.classList.remove('text-primary', 'border-b-2', 'border-primary');
        tabSignin.classList.add('text-on-surface-variant');
    } else {
        signupForm.classList.add('hidden');
        signinForm.classList.remove('hidden');
        tabSignin.classList.add('text-primary', 'border-b-2', 'border-primary');
        tabSignin.classList.remove('text-on-surface-variant');
        tabSignup.classList.remove('text-primary', 'border-b-2', 'border-primary');
        tabSignup.classList.add('text-on-surface-variant');
    }
}

// Check URL parameters on load
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (tab === 'signup') {
        switchTab('signup');
    }
});

function selectRole(role, buttonEl) {
    const roleInput = document.getElementById('role-input');
    if (roleInput) {
        roleInput.value = role;
        roleInput.dispatchEvent(new Event('input'));
    }

    const roleButtons = document.querySelectorAll('#form-signup button[type="button"][onclick^="selectRole"]');
    roleButtons.forEach((btn) => {
        btn.classList.remove('border-primary', 'bg-primary-container/10', 'text-primary');
        btn.classList.add('border-outline-variant', 'text-on-surface-variant');
    });

    if (buttonEl) {
        buttonEl.classList.add('border-primary', 'bg-primary-container/10', 'text-primary');
        buttonEl.classList.remove('border-outline-variant', 'text-on-surface-variant');
    }
}
</script>
<!-- Footer Links -->
<footer class="pt-lg flex flex-wrap justify-center gap-md font-caption text-on-surface-variant">
<a class="hover:underline hover:text-primary" href="#">User Agreement</a>
<a class="hover:underline hover:text-primary" href="#">Privacy Policy</a>
<a class="hover:underline hover:text-primary" href="#">Cookie Policy</a>
<a class="hover:underline hover:text-primary" href="#">Brand Policy</a>
</footer>
</div>
</section>
</main>
<!-- Background Decorative Element (Subtle) -->
<div class="fixed bottom-0 right-0 p-lg pointer-events-none opacity-5">
<span class="material-symbols-outlined text-[200px]" data-icon="hub">hub</span>
</div>
<script src="<?php echo URLROOT; ?>/assets/js/forms.js?v=2"></script>
<script src="<?php echo URLROOT; ?>/assets/js/main.js?v=2"></script>

</body>
</html>
