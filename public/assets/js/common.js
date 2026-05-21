/**
 * ProNetwork — Common Logic
 * assets/js/common.js
 * Handles populating user data on all pages.
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
    initApp();
    initGlobalSearch();
});

async function initApp() {
    hydrateUserState();
    try {
        const res = await fetch(`${URLROOT}/user/me`);
        const data = await res.json();
        if (data.success) {
            const u = data.user;
            setUserState(u);
            populateUserData(u);
        }
    } catch(e) {
        console.error('Failed to init app:', e);
    }
}

function setUserState(user) {
    localStorage.setItem('pn_user', JSON.stringify(user));
}

function getUserState() {
    const user = localStorage.getItem('pn_user');
    return user ? JSON.parse(user) : null;
}

function hydrateUserState() {
    const u = getUserState();
    if (u) populateUserData(u);
}

function pnAssetUrl(path) {
    return `${URLROOT}/${String(path).replace(/^\/+/, '')}`;
}

function pnUiAvatarUrl(name, options = {}) {
    const bg = options.background || '0A66C2';
    const color = options.color || 'fff';
    const size = options.size || 128;
    const label = encodeURIComponent(String(name || 'User').trim() || 'User');
    return `https://ui-avatars.com/api/?name=${label}&background=${bg}&color=${color}&size=${size}&bold=true`;
}

function pnProfilePicUrl(user = {}) {
    const pic = user.profile_pic || '';
    if (pic) {
        if (pic.startsWith('http')) return pic;
        const isCompany = user.role === 'Company' || pic.startsWith('logos/');
        const folder = isCompany ? 'companies' : 'profiles';
        return `${URLROOT}/uploads/${folder}/${pic.replace(/^\/+/, '')}`;
    }
    return pnUiAvatarUrl(user.full_name);
}

function pnUserProfileUrl(userId) {
    return `${URLROOT}/user/profile?id=${encodeURIComponent(userId)}`;
}

function pnAvatarImg(user = {}, className = '', extraAttrs = '') {
    const src = pnProfilePicUrl(user);
    const fallback = pnUiAvatarUrl(user.full_name);
    const alt = escapeHtml(user.full_name || 'User');
    return `<img src="${src}" alt="${alt}" class="${className}" loading="lazy" onerror="this.onerror=null;this.src='${fallback}'" ${extraAttrs}>`;
}

function pnCoverImageUrl(user = {}) {
    const cover = user.cover_image || '';
    if (cover) {
        return cover.startsWith('http') ? cover : `${URLROOT}/uploads/covers/${cover}`;
    }
    return pnAssetUrl('uploads/covers/1778246066_IMG-20240915-WA0002.jpg');
}

function pnCompanyLogoFile(companyName = '') {
    const name = String(companyName).toLowerCase();
    if (name.includes('amazon')) return 'logos/amazon-com-inc-logo.jpeg';
    if (name.includes('apple')) return 'logos/apple-inc-logo.jpeg';
    if (name.includes('armani')) return 'logos/armani-logo.jpeg';
    if (name.includes('flipkart')) return 'logos/flipkart-logo.jpeg';
    if (name.includes('google')) return 'logos/google-llc-logo.jpeg';
    if (name.includes('infosys')) return 'logos/infosys-limited-logo.jpeg';
    if (name.includes('microsoft')) return 'logos/microsoft-corporation-logo.jpeg';
    if (name.includes('tata')) return 'logos/tata-consultancy-services-logo.jpeg';
    if (name.includes('tesla')) return 'logos/tesla-inc-logo.jpeg';
    if (name.includes('green')) return 'logos/greengrid.png';
    if (name.includes('cloud')) return 'logos/cloudscale.png';
    return 'logos/nexa.png';
}

function pnCompanyLogoUrl(company = {}) {
    const logo = (company.logo || company.logo_path || '').trim();
    if (logo.startsWith('http')) {
        return logo;
    }
    if (logo) {
        return `${URLROOT}/uploads/companies/${logo.replace(/^\/+/, '')}`;
    }
    return pnUiAvatarUrl(company.company_name || company.name || 'Company', { background: '6366f1' });
}

function pnCompanyLogoImg(company = {}, className = '') {
    const src = pnCompanyLogoUrl(company);
    const fallback = pnUiAvatarUrl(company.company_name || company.name || 'Company', { background: '6366f1' });
    const alt = escapeHtml(company.company_name || company.name || 'Company');
    return `<img src="${src}" alt="${alt}" class="${className}" loading="lazy" onerror="this.onerror=null;this.src='${fallback}'">`;
}

function pnCompanyBannerFile(companyName = '') {
    const name = String(companyName).toLowerCase();
    if (name.includes('amazon')) return 'banners/amazon-com-inc-banner.jpeg';
    if (name.includes('apple')) return 'banners/apple-inc-banner.jpeg';
    if (name.includes('armani')) return 'banners/armani-banner.jpeg';
    if (name.includes('cloud')) return 'banners/cloudscale-systems-banner.jpeg';
    if (name.includes('flipkart')) return 'banners/flipkart-banner.jpeg';
    if (name.includes('google')) return 'banners/google-llc-banner.jpeg';
    if (name.includes('green')) return 'banners/greengrid-labs-banner.jpeg';
    if (name.includes('infosys')) return 'banners/infosys-limited-banner.jpeg';
    if (name.includes('microsoft')) return 'banners/microsoft-corporation-banner.jpeg';
    if (name.includes('tata')) return 'banners/tata-consultancy-services-banner.jpeg';
    if (name.includes('tesla')) return 'banners/tesla-inc-banner.jpeg';
    return 'banners/nexa-analytics-banner.jpeg';
}

function pnCompanyBannerUrl(company = {}) {
    const banner = company.banner || company.banner_path || '';
    if (banner && !banner.startsWith('http')) {
        return `${URLROOT}/uploads/companies/${banner}`;
    }
    return `${URLROOT}/uploads/companies/${pnCompanyBannerFile(company.company_name || company.name || '')}`;
}

function populateUserData(u) {
    if (!u) return;

    document.querySelectorAll('[data-user-name="full"]').forEach(el => { el.textContent = u.full_name || ''; });
    document.querySelectorAll('[data-user-headline]').forEach(el => { el.textContent = u.headline || 'Add a headline'; });
    document.querySelectorAll('[data-user-location]').forEach(el => { el.textContent = u.location || 'Location not set'; });
    document.querySelectorAll('[data-user-industry]').forEach(el => { el.textContent = u.industry || 'Industry not set'; });
    document.querySelectorAll('[data-user-bio]').forEach(el => {
        if (el.tagName === 'TEXTAREA') el.value = u.bio || '';
        else el.textContent = u.bio || 'No bio yet.';
    });
    document.querySelectorAll('[data-user-email]').forEach(el => { el.textContent = u.email || ''; });

    const parts = [u.full_name, u.location, u.industry].filter(Boolean);
    document.querySelectorAll('[data-user-summary]').forEach(el => {
        el.textContent = parts.length ? parts.join(' · ') : 'Complete your profile to stand out.';
    });

    const picUrl = pnProfilePicUrl(u);
    document.querySelectorAll('img[data-user-pic="true"]').forEach(img => {
        img.src = picUrl;
        img.alt = u.full_name ? `${u.full_name} profile photo` : 'Profile photo';
    });
    document.querySelectorAll('[data-user-pic]').forEach(el => {
        if (el.tagName === 'IMG') {
            el.src = picUrl;
            el.alt = u.full_name ? `${u.full_name} profile photo` : 'Profile photo';
        }
    });

    document.querySelectorAll('img[data-user-cover="true"]').forEach(img => {
        img.src = pnCoverImageUrl(u);
        img.classList.remove('hidden');
    });
}

function escapeHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

/** Strip leading/trailing whitespace per line (incl. NBSP) from post/comment copy. */
function normalizePostContent(text) {
  if (text == null) return '';
  return String(text)
    .replace(/\r\n?/g, '\n')
    .replace(/^\uFEFF/, '')
    .split('\n')
    .map((line) =>
      line
        .replace(/^[\s\u00A0\u1680\u2000-\u200A\u202F\u205F\u3000\uFEFF]+/, '')
        .replace(/[\s\u00A0\u1680\u2000-\u200A\u202F\u205F\u3000\uFEFF]+$/, '')
    )
    .join('\n')
    .trim();
}

function initGlobalSearch() {
    const input = document.getElementById('global-search-input');
    const results = document.getElementById('global-search-results');
    const form = document.getElementById('global-search-form');
    if (!input || !results || !form) return;

    let timer = null;
    let lastQuery = '';

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const term = input.value.trim();
        if (term.length >= 2) runSearch(term);
    });

    input.addEventListener('input', () => {
        const term = input.value.trim();
        clearTimeout(timer);

        if (term.length < 2) {
            hideSearchResults();
            return;
        }

        results.classList.remove('hidden');
        results.innerHTML = '<div class="px-4 py-3 text-sm text-slate-500">Searching...</div>';
        timer = setTimeout(() => runSearch(term), 220);
    });

    input.addEventListener('focus', () => {
        if (results.innerHTML.trim()) results.classList.remove('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#global-search')) hideSearchResults();
    });

    async function runSearch(term) {
        if (term === lastQuery && !results.classList.contains('hidden')) return;
        lastQuery = term;

        try {
            const response = await fetch(`${URLROOT}/search?q=${encodeURIComponent(term)}`);
            const data = await response.json();
            if (!data.success) {
                renderSearchMessage(data.message || 'Search failed.');
                return;
            }
            renderSearchResults(data.results, term);
        } catch (error) {
            renderSearchMessage('Unable to search right now.');
        }
    }

    function hideSearchResults() {
        results.classList.add('hidden');
    }

    function renderSearchMessage(message) {
        results.classList.remove('hidden');
        results.innerHTML = `<div class="px-4 py-3 text-sm text-slate-500">${escapeHtml(message)}</div>`;
    }

    function renderSearchResults(data, term) {
        const people = data.people || [];
        const jobs = data.jobs || [];
        const companies = data.companies || [];
        const posts = data.posts || [];
        const total = people.length + jobs.length + companies.length + posts.length;

        results.classList.remove('hidden');
        if (!total) {
            renderSearchMessage(`No results for "${term}"`);
            return;
        }

        results.innerHTML = [
            renderSection('People', people.map(person => ({
                icon: pnProfilePicUrl(person),
                fallback: 'person',
                title: person.full_name,
                subtitle: [person.headline || 'Professional', person.location || ''].filter(Boolean).join(' · '),
                href: `${URLROOT}/user/profile?id=${person.user_id}`
            }))),
            renderSection('Jobs', jobs.map(job => ({
                icon: pnCompanyLogoUrl(job),
                fallback: 'work',
                title: job.title,
                subtitle: [job.company_name, job.location || job.job_type || ''].filter(Boolean).join(' · '),
                href: `${URLROOT}/user/jobs`
            }))),
            renderSection('Companies', companies.map(company => ({
                icon: pnCompanyLogoUrl(company),
                fallback: 'business',
                title: company.company_name,
                subtitle: company.industry || 'Company',
                href: `${URLROOT}/company/show/${company.company_id}`
            }))),
            renderSection('Posts', posts.map(post => ({
                icon: '',
                fallback: 'article',
                title: post.content.length > 70 ? post.content.slice(0, 70) + '...' : post.content,
                subtitle: `Post by ${post.full_name}`,
                href: `${URLROOT}/user/feed`
            })))
        ].join('');
    }

    function renderSection(label, items) {
        if (!items.length) return '';
        return `
            <div class="py-1">
                <div class="px-4 py-2 text-[11px] uppercase tracking-wide font-bold text-slate-500">${escapeHtml(label)}</div>
                ${items.map(renderItem).join('')}
            </div>
        `;
    }

    function renderItem(item) {
        const avatar = item.icon
            ? `<img src="${escapeHtml(item.icon)}" alt="" class="w-9 h-9 rounded object-cover bg-slate-100">`
            : `<div class="w-9 h-9 rounded bg-[#eef3f8] flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-slate-500 text-[20px]">${escapeHtml(item.fallback)}</span></div>`;

        return `
            <a href="${escapeHtml(item.href)}" class="flex gap-3 px-4 py-2 hover:bg-slate-50 transition-colors">
                ${avatar}
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-slate-900 truncate">${escapeHtml(item.title || '')}</span>
                    <span class="block text-xs text-slate-500 truncate">${escapeHtml(item.subtitle || '')}</span>
                </span>
            </a>
        `;
    }

    function imageUrl(folder, value) {
        return value.startsWith('http') ? value : `${URLROOT}/uploads/${folder}/${value}`;
    }
}

/**
 * Custom Professional Modal
 * Replaces standard alert/confirm/prompt
 * @param {Object} options { title, message, type, isPrompt, placeholder, confirmText, cancelText, isDanger }
 * @returns {Promise} Resolves with value (true/false or input string)
 */
window.pnModal = function(options = {}) {
    return new Promise((resolve) => {
        const {
            title = 'Notification',
            message = '',
            type = 'info', // info, warning, success, flag
            isPrompt = false,
            placeholder = 'Enter details...',
            confirmText = 'Confirm',
            cancelText = 'Cancel',
            isDanger = false,
            defaultValue = ''
        } = options;

        // Create elements
        const backdrop = document.createElement('div');
        backdrop.className = 'pn-modal-backdrop';
        
        const iconMap = {
            info: 'info',
            warning: 'warning',
            success: 'check_circle',
            flag: 'flag'
        };

        const safeTitle = escapeHtml(title);
        const safeMessage = escapeHtml(message);
        const safePlaceholder = escapeHtml(placeholder);
        const safeDefaultValue = escapeHtml(defaultValue);
        const safeConfirmText = escapeHtml(confirmText);
        const safeCancelText = escapeHtml(cancelText);

        backdrop.innerHTML = `
            <div class="pn-modal-container">
                <div class="pn-modal-header">
                    <div class="pn-modal-icon ${type}">
                        <span class="material-symbols-outlined">${iconMap[type] || 'info'}</span>
                    </div>
                    <h3 class="pn-modal-title">${safeTitle}</h3>
                </div>
                <div class="pn-modal-body">
                    <p class="pn-modal-message">${safeMessage}</p>
                    ${isPrompt ? `<textarea class="pn-modal-input" placeholder="${safePlaceholder}" rows="3">${safeDefaultValue}</textarea>` : ''}
                </div>
                <div class="pn-modal-footer">
                    ${safeCancelText ? `<button class="pn-modal-btn pn-modal-btn-secondary" id="pn-modal-cancel">${safeCancelText}</button>` : ''}
                    <button class="pn-modal-btn ${isDanger ? 'pn-modal-btn-danger' : 'pn-modal-btn-primary'}" id="pn-modal-confirm">${safeConfirmText}</button>
                </div>
            </div>
        `;

        document.body.appendChild(backdrop);

        // Animation
        setTimeout(() => backdrop.classList.add('active'), 10);
        if (isPrompt) {
            setTimeout(() => {
                const input = backdrop.querySelector('.pn-modal-input');
                if (input) {
                    input.focus();
                    input.setSelectionRange(input.value.length, input.value.length);
                }
            }, 100);
        }

        const close = (val) => {
            backdrop.classList.remove('active');
            setTimeout(() => {
                backdrop.remove();
                resolve(val);
            }, 300);
        };

        backdrop.querySelector('#pn-modal-confirm').onclick = () => {
            if (isPrompt) {
                const input = backdrop.querySelector('.pn-modal-input').value;
                close(input);
            } else {
                close(true);
            }
        };

        const cancelBtn = backdrop.querySelector('#pn-modal-cancel');
        if (cancelBtn) cancelBtn.onclick = () => close(isPrompt ? null : false);
        
        // Close on backdrop click
        backdrop.onclick = (e) => {
            if (e.target === backdrop) close(isPrompt ? null : false);
        };
    });
};

// Override standard native blocking dialogs globally with premium ProNetwork UI Modals
window.alert = function(message) {
    return window.pnModal({
        title: 'ProNetwork Alert',
        message: String(message),
        type: 'info',
        confirmText: 'OK',
        cancelText: ''
    });
};

window.confirm = function(message) {
    return window.pnModal({
        title: 'Confirmation Required',
        message: String(message),
        type: 'warning',
        confirmText: 'Yes, Confirm',
        cancelText: 'Cancel'
    });
};

window.prompt = function(message, defaultValue = '') {
    return window.pnModal({
        title: 'Input Required',
        message: String(message),
        type: 'info',
        isPrompt: true,
        defaultValue: String(defaultValue),
        confirmText: 'Submit',
        cancelText: 'Cancel'
    });
};
