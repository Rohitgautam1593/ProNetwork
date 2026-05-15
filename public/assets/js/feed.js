/**
 * ProNetwork — Feed Logic
 * assets/js/feed.js
 * Handles fetching and rendering the dynamic post feed.
 */
'use strict';

window.FEED_SORT_MODE = window.FEED_SORT_MODE || 'recent';

document.addEventListener('DOMContentLoaded', () => {
    const feedList = document.getElementById('feed-container');
    if (feedList) {
        initFeed();
        initSuggestions();
        initFeedStats();
        initFeedCommentsAndShare();
        initFeedSortBar();
        initTrendingInfoBtn();
        initReactionsModalUi();
        initBigPostModalDismiss();
    }
});

async function initFeedStats() {
    try {
        const [connRes, pendRes] = await Promise.all([
            fetch(`${URLROOT}/network/connections`),
            fetch(`${URLROOT}/network/pending`)
        ]);
        const connData = await connRes.json();
        const pendData = await pendRes.json();
        if (connData.success) {
            const countEl = document.getElementById('user-connections-count');
            if (countEl) countEl.textContent = connData.connections.length;
        }
        const pendEl = document.getElementById('user-pending-count');
        if (pendEl && pendData.success && Array.isArray(pendData.requests)) {
            pendEl.textContent = pendData.requests.length;
        }
    } catch (e) {}
}

async function initSuggestions() {
    const sugCont = document.getElementById('suggestions-container');
    if (!sugCont) return;
    try {
        const [companyRes, peopleRes] = await Promise.all([
            fetch(`${URLROOT}/company/suggestions`),
            fetch(`${URLROOT}/network/suggestions`)
        ]);
        const companyData = await companyRes.json();
        const peopleData = await peopleRes.json();
        sugCont.innerHTML = '';

        if (companyData.success && companyData.companies?.length) {
            companyData.companies.slice(0, 3).forEach(company => {
                const logo = pnCompanyLogoUrl(company);
                sugCont.innerHTML += `
<div class="flex items-start justify-between gap-3">
  <div class="flex items-center space-x-3 min-w-0">
    <img src="${logo}" class="w-10 h-10 rounded-lg object-contain bg-white border border-slate-100">
    <div class="min-w-0">
      <h4 class="text-sm font-bold text-slate-900 truncate">${escapeHtml(company.company_name)}</h4>
      <p class="text-[11px] text-slate-500 leading-tight truncate">${escapeHtml(company.industry || 'Company page')}</p>
      <button class="company-follow-suggestion mt-1 border border-slate-500 rounded-full px-3 py-1 text-slate-600 text-[11px] font-bold hover:bg-slate-50 flex items-center space-x-1" data-company-id="${company.company_id}">
        <span class="material-symbols-outlined text-[14px]">add</span><span>Follow</span>
      </button>
    </div>
  </div>
</div>`;
            });
        }

        if (peopleData.success && peopleData.suggestions?.length) {
            peopleData.suggestions.slice(0, Math.max(0, 3 - (companyData.companies?.length || 0))).forEach(u => {
                const picUrl = pnProfilePicUrl(u);
                const picHtml = `<img src="${picUrl}" alt="" class="w-10 h-10 rounded-full object-cover">`;
                const uid = escapeHtml(String(u.user_id));
                const profileUrl = `${URLROOT}/user/profile?id=${uid}`;
                sugCont.innerHTML += `
<div class="flex items-start justify-between gap-2">
  <div class="flex items-center space-x-3 min-w-0 flex-1">
    <a href="${profileUrl}" class="shrink-0 rounded-full ring-1 ring-slate-200 hover:ring-[#0A66C2]/40">${picHtml}</a>
    <div class="min-w-0">
      <a href="${profileUrl}" class="block min-w-0"><h4 class="text-sm font-bold text-slate-900 truncate hover:text-[#0A66C2] hover:underline">${escapeHtml(u.full_name)}</h4></a>
      <p class="text-[11px] text-slate-500 leading-tight truncate">${escapeHtml(u.headline || 'Professional')}</p>
      <button type="button" class="person-connect-suggestion mt-1 border border-slate-500 rounded-full px-3 py-1 text-slate-600 text-[11px] font-bold hover:bg-slate-50 flex items-center space-x-1" data-user-id="${uid}">
        <span class="material-symbols-outlined text-[14px]">person_add</span><span>Connect</span>
      </button>
    </div>
  </div>
</div>`;
            });
        }
        if (!sugCont.innerHTML.trim()) {
            sugCont.innerHTML = '<p class="text-xs text-slate-500">No suggestions right now.</p>';
        }
        sugCont.querySelectorAll('.company-follow-suggestion').forEach(btn => {
            btn.addEventListener('click', async ev => {
                ev.preventDefault();
                ev.stopPropagation();
                const id = btn.getAttribute('data-company-id');
                btn.disabled = true;
                const res = await fetch(`${URLROOT}/company/follow/${id}`, { method: 'POST' });
                const data = await res.json();
                if (data.success) {
                    btn.innerHTML = '<span class="material-symbols-outlined text-[14px]">check</span><span>Following</span>';
                    setTimeout(() => initFeed(), 250);
                } else {
                    btn.disabled = false;
                    feedToast(data.message || 'Could not follow company.', 'error');
                }
            });
        });
        sugCont.querySelectorAll('.person-connect-suggestion').forEach(btn => {
            btn.addEventListener('click', async ev => {
                ev.preventDefault();
                ev.stopPropagation();
                const id = btn.getAttribute('data-user-id');
                if (!id) return;
                btn.disabled = true;
                try {
                    const res = await fetch(`${URLROOT}/network/send_request`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ user_id: Number(id) })
                    });
                    const data = await res.json();
                    if (data.success) {
                        btn.innerHTML = '<span class="material-symbols-outlined text-[14px]">send</span><span>Sent</span>';
                        feedToast('Connection request sent.', 'success');
                        initFeedStats();
                    } else {
                        btn.disabled = false;
                        feedToast(data.message || 'Could not send request.', 'error');
                    }
                } catch (e) {
                    btn.disabled = false;
                    feedToast('Server error.', 'error');
                }
            });
        });
    } catch(e){}
}

async function initFeed() {
    const feedList = document.getElementById('feed-container');
    if (!feedList) return;

    if (window.IS_SINGLE_POST && window.SINGLE_POST_DATA) {
        feedList.innerHTML = '';
        renderPostCard(window.SINGLE_POST_DATA, false, 0);
        setTimeout(() => {
            const cbtn = feedList.querySelector('.comment-toggle');
            if (cbtn) cbtn.click();
        }, 100);
        return;
    }

    try {
        const response = await fetch(`${URLROOT}/post`);
        const result = await response.json();

        if (result.success) {
            window.FEED_POSTS_RAW = Array.isArray(result.posts) ? result.posts.slice() : [];
            applyFeedSortAndRender();
        }
    } catch (err) {
        console.error('Failed to load feed:', err);
    }
}

function sortFeedPosts(posts, mode) {
    const arr = (posts || []).slice();
    if (mode === 'top') {
        arr.sort((a, b) => {
            const sb = (Number(b.reaction_count) || 0) + (Number(b.comment_count) || 0);
            const sa = (Number(a.reaction_count) || 0) + (Number(a.comment_count) || 0);
            if (sb !== sa) return sb - sa;
            return (new Date(b.created_at).getTime()) - (new Date(a.created_at).getTime());
        });
    } else {
        arr.sort((a, b) => (new Date(b.created_at).getTime()) - (new Date(a.created_at).getTime()));
    }
    return arr;
}

function applyFeedSortAndRender() {
    const feedList = document.getElementById('feed-container');
    if (!feedList || window.IS_SINGLE_POST) return;
    const raw = window.FEED_POSTS_RAW || [];
    window.FEED_POSTS_CACHE = sortFeedPosts(raw, window.FEED_SORT_MODE || 'recent');
    feedList.innerHTML = '';

    if (window.FEED_POSTS_CACHE.length === 0) {
        feedList.innerHTML = '<div class="pn-feed-empty bg-white p-10 text-center rounded-xl border border-slate-200 shadow-sm"><p class="text-slate-600 font-medium">No posts yet. Be the first to share something!</p></div>';
    } else {
        window.FEED_POSTS_CACHE.forEach((post, i) => {
            renderPostCard(post, false, i);
        });
        scrollToPostFromHash();
    }
}

function initFeedSortBar() {
    const trigger = document.getElementById('feed-sort-trigger');
    const menu = document.getElementById('feed-sort-menu');
    const label = document.getElementById('feed-sort-label');
    if (!trigger || !menu || !label) return;

    function closeMenu() {
        menu.classList.add('hidden');
        trigger.setAttribute('aria-expanded', 'false');
    }

    function openMenu() {
        menu.classList.remove('hidden');
        trigger.setAttribute('aria-expanded', 'true');
    }

    trigger.addEventListener('click', ev => {
        ev.stopPropagation();
        if (menu.classList.contains('hidden')) openMenu();
        else closeMenu();
    });

    menu.querySelectorAll('.feed-sort-opt').forEach(btn => {
        btn.addEventListener('click', ev => {
            ev.stopPropagation();
            const mode = btn.getAttribute('data-sort') || 'recent';
            window.FEED_SORT_MODE = mode;
            label.textContent = mode === 'top' ? 'Top' : 'Recent';
            closeMenu();
            applyFeedSortAndRender();
        });
    });

    document.addEventListener('click', () => closeMenu());
    menu.addEventListener('click', ev => ev.stopPropagation());

    label.textContent = (window.FEED_SORT_MODE || 'recent') === 'top' ? 'Top' : 'Recent';
}

function initTrendingInfoBtn() {
    const btn = document.getElementById('feed-trending-info');
    if (!btn) return;
    btn.addEventListener('click', () => {
        feedToast('Trending topics are ranked from recent post engagement (reactions and comments).', 'info');
    });
}

function closeReactionsModal() {
    const modal = document.getElementById('reactions-list-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function initReactionsModalUi() {
    const modal = document.getElementById('reactions-list-modal');
    const closeBtn = document.getElementById('reactions-modal-close');
    if (closeBtn) closeBtn.addEventListener('click', () => closeReactionsModal());
    if (modal) {
        modal.addEventListener('click', ev => {
            if (ev.target === modal) closeReactionsModal();
        });
    }
}

function initBigPostModalDismiss() {
    const modal = document.getElementById('big-post-view-modal');
    if (!modal) return;
    modal.addEventListener('click', ev => {
        if (ev.target === modal) {
            modal.classList.add('hidden');
        }
    });
    document.addEventListener('keydown', ev => {
        if (ev.key !== 'Escape') return;
        closeReactionsModal();
        if (!modal.classList.contains('hidden')) {
            modal.classList.add('hidden');
        }
    });
}

function pnPostMediaMarkup(post) {
    if (!post.post_image) return '';
    const fn = String(post.post_image).replace(/^.*[\\/]/, '');
    const ext = (fn.split('.').pop() || '').toLowerCase();
    const url = `${URLROOT}/uploads/posts/${encodeURIComponent(fn)}`;
    if (['mp4', 'webm', 'ogv'].includes(ext)) {
        return `
                    <div class="mt-3 -mx-4 overflow-hidden rounded-lg">
                        <video src="${url}" class="pn-feed-post-media w-full max-h-[500px] object-contain bg-black border-y border-slate-50 dark:border-slate-800 group-hover/pb:opacity-95 transition-opacity" controls playsinline preload="metadata"></video>
                    </div>`;
    }
    return `
                    <div class="mt-3 -mx-4 overflow-hidden rounded-lg">
                        <img src="${url}" alt="" class="pn-feed-post-media w-full max-h-[500px] object-cover border-y border-slate-50 dark:border-slate-800 group-hover/pb:opacity-95 transition-opacity" decoding="async">
                    </div>`;
}

function pnBigPostLeftMediaMarkup(postObj) {
    const isCompany = postObj.is_company_activity;
    if (isCompany) {
        const banner = pnCompanyBannerUrl(postObj);
        return `<img src="${banner}" class="w-full h-full object-contain select-none animate-fade-in">`;
    }
    if (postObj.post_image) {
        const fn = String(postObj.post_image).replace(/^.*[\\/]/, '');
        const ext = (fn.split('.').pop() || '').toLowerCase();
        const url = `${URLROOT}/uploads/posts/${encodeURIComponent(fn)}`;
        if (['mp4', 'webm', 'ogv'].includes(ext)) {
            return `<video src="${url}" class="w-full h-full max-h-full object-contain select-none animate-fade-in bg-black" controls playsinline preload="metadata"></video>`;
        }
        return `<img src="${url}" class="w-full h-full object-contain select-none animate-fade-in">`;
    }
    return '';
}

window.pnPrependFeedPost = function (post) {
    if (!post || window.IS_SINGLE_POST) return;
    if (!window.FEED_POSTS_RAW) window.FEED_POSTS_RAW = [];
    window.FEED_POSTS_RAW.unshift(post);
    applyFeedSortAndRender();
};

/**
 * Ripple + motion helpers for feed actions
 */
function addRipple(btn, clientX, clientY) {
    if (!btn) return;
    const rect = btn.getBoundingClientRect();
    const x = (clientX != null ? clientX : rect.left + rect.width / 2) - rect.left;
    const y = (clientY != null ? clientY : rect.top + rect.height / 2) - rect.top;
    const span = document.createElement('span');
    span.className = 'pn-feed-ripple';
    span.style.left = `${x}px`;
    span.style.top = `${y}px`;
    btn.appendChild(span);
    setTimeout(() => span.remove(), 620);
}

function triggerActionBump(btn) {
    if (!btn) return;
    btn.classList.remove('pn-feed-action--bump');
    void btn.offsetWidth;
    btn.classList.add('pn-feed-action--bump');
    btn.addEventListener('animationend', () => btn.classList.remove('pn-feed-action--bump'), { once: true });
}

function triggerIconPop(btn) {
    const icon = btn.querySelector('.pn-feed-action__icon');
    if (!icon) return;
    icon.classList.remove('pn-feed-action--pop');
    void icon.offsetWidth;
    icon.classList.add('pn-feed-action--pop');
    icon.addEventListener('animationend', () => icon.classList.remove('pn-feed-action--pop'), { once: true });
}

function triggerShareWiggle(btn) {
    if (!btn) return;
    btn.classList.remove('pn-feed-action--share-wiggle');
    void btn.offsetWidth;
    btn.classList.add('pn-feed-action--share-wiggle');
    btn.addEventListener('animationend', () => btn.classList.remove('pn-feed-action--share-wiggle'), { once: true });
}

function bumpLikeCount(el) {
    if (!el) return;
    el.classList.remove('pn-count-bump');
    void el.offsetWidth;
    el.classList.add('pn-count-bump');
    el.addEventListener('animationend', () => el.classList.remove('pn-count-bump'), { once: true });
}

/**
 * Renders a single post card and adds it to the feed.
 * @param {Object} post The post data from API
 * @param {Boolean} prepend Whether to add to top or bottom
 * @param {Number} staggerIndex Staggered entrance order
 */
function renderPostCard(post, prepend = false, staggerIndex = 0) {
    if (post.is_company_activity) {
        renderCompanyActivityCard(post, prepend, staggerIndex);
        return;
    }

    const feedList = document.getElementById('feed-container');
    if (!feedList) return;

    const cc = Number(post.comment_count) || 0;
    const card = document.createElement('article');
    card.id = 'post-' + post.post_id;
    card.className =
        'pn-feed-card bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-[0px_2px_8px_rgba(0,0,0,0.04)] overflow-visible mb-4';
    card.style.setProperty('--pn-stagger', String(staggerIndex));
    
    const postDate = new Date(post.created_at);
    const timeAgo = formatTimeAgo(postDate);

    card.innerHTML = `
        <div class="p-4">
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full border border-slate-100 overflow-hidden bg-slate-50 cursor-pointer shrink-0" onclick="window.location.href='${post.is_company_activity || post.user_role === 'Company' || post.user_role === 'Company Page' ? `${URLROOT}/company/show/${post.company_id || post.user_id}` : `${URLROOT}/user/profile?id=${post.user_id}`}'">
                        <img src="${pnProfilePicUrl(post)}" alt="${escapeHtml(post.full_name)}" class="w-full h-full object-cover">
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-[14px] text-slate-900 dark:text-white hover:underline transition-colors cursor-pointer truncate" onclick="window.location.href='${post.is_company_activity || post.user_role === 'Company' || post.user_role === 'Company Page' ? `${URLROOT}/company/show/${post.company_id || post.user_id}` : `${URLROOT}/user/profile?id=${post.user_id}`}'">${escapeHtml(post.full_name)}</h4>
                        <p class="text-[11px] text-slate-500 line-clamp-1">${escapeHtml(post.user_role || 'Member')}</p>
                        <p class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                            ${timeAgo} • <span class="material-symbols-outlined text-[12px]">public</span>
                        </p>
                    </div>
                </div>
                <div class="relative">
                    <button type="button" class="post-menu-toggle w-9 h-9 flex items-center justify-center hover:bg-slate-100 rounded-full transition-all text-slate-500 active:scale-90">
                        <span class="material-symbols-outlined pointer-events-none text-[22px]">more_horiz</span>
                    </button>
                    <div class="post-menu hidden absolute right-0 top-11 bg-white border border-slate-200 rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] z-50 w-60 py-1.5 overflow-hidden ring-1 ring-black ring-opacity-5 animate-in fade-in zoom-in duration-200">
                        <button type="button" class="report-post-btn w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors" data-post-id="${post.post_id}">
                            <span class="material-symbols-outlined text-[20px] text-slate-400">flag</span>
                            Report this post
                        </button>
                        ${post.user_id == (typeof CURRENT_USER_ID !== 'undefined' ? CURRENT_USER_ID : 0) ? `
                        <div class="h-[1px] bg-slate-100 mx-2 my-1"></div>
                        <button type="button" class="delete-own-post-btn w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors" data-post-id="${post.post_id}">
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                            Delete post
                        </button>
                        ` : ''}
                    </div>
                </div>
            </div>
            
            <div class="clickable-post-body cursor-pointer group/pb transition-all">
                <div class="post-body-content text-[14px] text-slate-800 dark:text-slate-200 leading-normal whitespace-pre-wrap group-hover/pb:text-slate-900 dark:group-hover/pb:text-white">
                    ${escapeHtml(post.content)}
                </div>

                ${pnPostMediaMarkup(post)}
            </div>

            <div class="mt-4 flex items-center justify-between text-[12px] text-slate-500 border-b border-slate-50 dark:border-slate-800 pb-2 px-1">
                <div class="flex items-center gap-1.5 cursor-pointer hover:underline reaction-count-toggle" data-post-id="${post.post_id}">
                    <div class="flex -space-x-1">
                        <div class="w-4 h-4 rounded-full bg-blue-500 flex items-center justify-center border border-white"><span class="material-symbols-outlined text-white text-[10px] fill-current">thumb_up</span></div>
                        <div class="w-4 h-4 rounded-full bg-red-500 flex items-center justify-center border border-white"><span class="material-symbols-outlined text-white text-[10px] fill-current">favorite</span></div>
                    </div>
                    <span class="like-count font-medium">${post.reaction_count || 0}</span>
                </div>
                <div class="flex gap-2">
                    <span class="comment-stat-line">${cc} comment${cc === 1 ? '' : 's'}</span>
                </div>
            </div>

            <div class="pn-feed-actions flex mt-1 -mx-2 pt-1 border-b border-transparent">
                <button type="button" class="like-btn pn-feed-action pn-feed-action--like flex-1 flex items-center justify-center gap-2 py-2.5 text-slate-600 dark:text-slate-400 font-bold text-[13px] hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg" data-post-id="${post.post_id}">
                    <span class="material-symbols-outlined pn-feed-action__icon like-icon text-[20px]">thumb_up</span>
                    <span class="pn-feed-action__label">Like</span>
                </button>
                <button type="button" class="comment-toggle pn-feed-action pn-feed-action--comment flex-1 flex items-center justify-center gap-2 py-2.5 text-slate-600 dark:text-slate-400 font-bold text-[13px] hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg" data-post-id="${post.post_id}">
                    <span class="material-symbols-outlined pn-feed-action__icon text-[20px]">chat_bubble</span>
                    <span class="pn-feed-action__label">Comment</span>
                </button>
                <button type="button" class="share-post-btn pn-feed-action pn-feed-action--share flex-1 flex items-center justify-center gap-2 py-2.5 text-slate-600 dark:text-slate-400 font-bold text-[13px] hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg" data-post-id="${post.post_id}">
                    <span class="material-symbols-outlined pn-feed-action__icon share-icon text-[20px]">share</span>
                    <span class="pn-feed-action__label">Share</span>
                </button>
            </div>

            <div class="post-comments-wrap border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/40" data-comments-for="${post.post_id}">
                <div data-comments-list class="comments-list px-4 pt-3 space-y-3 max-h-72 overflow-y-auto text-sm min-h-[2.5rem]"></div>
                <div class="px-4 pb-3 pt-2">
                    <form class="post-comment-form flex gap-2 items-center" data-post-id="${post.post_id}">
                        <label class="sr-only" for="comment-input-${post.post_id}">Write a comment</label>
                        <input id="comment-input-${post.post_id}" type="text" name="content" autocomplete="off" class="comment-input flex-1 min-w-0 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-4 py-2 text-sm text-slate-900 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0A66C2]" maxlength="2000" placeholder="Add a comment…" />
                        <button type="submit" class="comment-submit rounded-full bg-[#0A66C2] hover:bg-[#004182] text-white px-4 py-2 text-sm font-bold shrink-0 disabled:opacity-50">Post</button>
                    </form>
                </div>
            </div>
        </div>
    `;

    const likeBtn = card.querySelector('.like-btn');
    if (likeBtn && Number(post.user_has_liked) > 0) likeBtn.classList.add('is-liked');
    likeBtn.addEventListener('click', async ev => {
        addRipple(likeBtn, ev.clientX, ev.clientY);
        try {
            const res = await fetch(`${URLROOT}/post/react/${post.post_id}`, { method: 'POST' });
            const data = await res.json();
            if (data.success) {
                const countEl = card.querySelector('.like-count');
                if (countEl) {
                    countEl.textContent = data.count;
                    bumpLikeCount(countEl);
                }
                likeBtn.classList.toggle('is-liked');
                triggerIconPop(likeBtn);
                triggerActionBump(likeBtn);
                const pid = post.post_id;
                [window.FEED_POSTS_RAW, window.FEED_POSTS_CACHE].forEach(arr => {
                    if (!Array.isArray(arr)) return;
                    const p = arr.find(x => x.post_id == pid);
                    if (p) {
                        p.reaction_count = data.count;
                        p.user_has_liked = likeBtn.classList.contains('is-liked') ? 1 : 0;
                    }
                });
            }
        } catch (e) {}
    });

    const postBodyClickEl = card.querySelector('.clickable-post-body');
    if (postBodyClickEl) {
        postBodyClickEl.addEventListener('click', () => {
            openBigPostView(post);
        });
    }

    if (prepend) {
        feedList.prepend(card);
    } else {
        feedList.appendChild(card);
    }
}

function renderCompanyActivityCard(post, prepend = false, staggerIndex = 0) {
    const feedList = document.getElementById('feed-container');
    if (!feedList) return;

    const card = document.createElement('article');
    card.id = String(post.post_id);
    card.className = 'pn-feed-card bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-[0px_2px_8px_rgba(0,0,0,0.04)] overflow-hidden mb-4';
    card.style.setProperty('--pn-stagger', String(staggerIndex));
    const logo = pnCompanyLogoUrl(post);
    const banner = pnCompanyBannerUrl(post);
    const postDate = new Date(post.created_at);
    const timeAgo = formatTimeAgo(postDate);
    const isJob = post.activity_type === 'job';

    card.innerHTML = `
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <a href="${URLROOT}/company/show/${post.company_id}" class="flex items-center space-x-3 min-w-0">
                    <div class="w-12 h-12 rounded-lg border border-slate-100 overflow-hidden bg-white shrink-0">
                        <img src="${logo}" alt="${escapeHtml(post.company_name)}" class="w-full h-full object-contain">
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-[14px] text-slate-900 hover:text-[#0A66C2] transition-colors truncate">${escapeHtml(post.company_name)}</h4>
                        <p class="text-[11px] text-slate-500 line-clamp-1">${escapeHtml(isJob ? 'Job update from a page you follow' : 'Page you follow')}</p>
                        <p class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">${timeAgo} • <span class="material-symbols-outlined text-[12px]">public</span></p>
                    </div>
                </a>
                <span class="text-[11px] px-2 py-1 rounded-full bg-blue-50 text-[#0A66C2] font-bold">${isJob ? 'Hiring' : 'Update'}</span>
            </div>
            <div class="clickable-post-body cursor-pointer group/pb transition-all">
                <p class="text-[14px] text-slate-800 leading-normal group-hover/pb:text-slate-900">${escapeHtml(post.content)}</p>
                <div class="mt-3 -mx-4">
                    <img src="${banner}" alt="" class="w-full max-h-[260px] object-cover border-y border-slate-50 group-hover/pb:opacity-95 transition-opacity">
                </div>
            </div>
            <div class="mt-4 flex gap-2">
                <a href="${URLROOT}/company/show/${post.company_id}" class="px-4 py-2 rounded-full border border-[#0A66C2] text-[#0A66C2] text-sm font-bold hover:bg-blue-50 transition-colors">View page</a>
                ${isJob ? `<a href="${URLROOT}/user/jobs?id=${post.job_id}" class="px-4 py-2 rounded-full bg-[#0A66C2] text-white text-sm font-bold hover:bg-[#004182] transition-colors">View job</a>` : ''}
            </div>
        </div>
    `;

    const postBodyClickEl = card.querySelector('.clickable-post-body');
    if (postBodyClickEl) {
        postBodyClickEl.addEventListener('click', () => {
            openBigPostView(post);
        });
    }

    if (prepend) feedList.prepend(card);
    else feedList.appendChild(card);
}

function scrollToPostFromHash() {
    const m = /^#post-(\d+)$/.exec(window.location.hash || '');
    if (!m) return;
    requestAnimationFrame(() => {
        const el = document.getElementById('post-' + m[1]);
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
}

function openBigPostView(postObjOrId) {
    if (!postObjOrId) return;

    if (typeof postObjOrId === 'string' || typeof postObjOrId === 'number') {
        const cached = window.FEED_POSTS_CACHE?.find(p => p.post_id == postObjOrId);
        if (cached) {
            renderBigPostOverlay(cached);
        } else {
            const modal = document.getElementById('big-post-view-modal');
            const leftBay = document.getElementById('big-post-modal-left');
            const rightBay = document.getElementById('big-post-modal-right');
            if (modal && leftBay && rightBay) {
                leftBay.innerHTML = '<div class="flex items-center justify-center h-full w-full"><span class="text-white text-xs">Loading media canvas...</span></div>';
                rightBay.innerHTML = '<div class="flex items-center justify-center h-full w-full p-8"><span class="text-slate-400 text-xs">Loading authentic live database thread data...</span></div>';
                modal.classList.remove('hidden');
            }
            fetch(`${URLROOT}/post/detail/${postObjOrId}`)
                .then(r => r.json())
                .then(d => {
                    if (d.success && d.post) {
                        if (!window.FEED_POSTS_CACHE) window.FEED_POSTS_CACHE = [];
                        window.FEED_POSTS_CACHE.push(d.post);
                        renderBigPostOverlay(d.post);
                    } else if (window.FEED_POSTS_CACHE?.[0]) {
                        renderBigPostOverlay(window.FEED_POSTS_CACHE[0]);
                    }
                }).catch(() => {
                    if (window.FEED_POSTS_CACHE?.[0]) renderBigPostOverlay(window.FEED_POSTS_CACHE[0]);
                });
        }
        return;
    }

    renderBigPostOverlay(postObjOrId);
}

function renderBigPostOverlay(postObj) {
    const modal = document.getElementById('big-post-view-modal');
    const leftBay = document.getElementById('big-post-modal-left');
    const rightBay = document.getElementById('big-post-modal-right');
    if (!modal || !leftBay || !rightBay) return;

    const isCompany = postObj.is_company_activity;
    const logo = isCompany ? pnCompanyLogoUrl(postObj) : pnProfilePicUrl(postObj);
    const title = escapeHtml(postObj.company_name || postObj.full_name || 'Member');
    const role = escapeHtml(postObj.user_role || (isCompany ? 'Company Page' : 'Professional'));
    const timeAgo = formatTimeAgo(new Date(postObj.created_at || Date.now()));
    const targetUrl = (isCompany || postObj.user_role === 'Company' || postObj.user_role === 'Company Page') 
        ? `${URLROOT}/company/show/${postObj.company_id || postObj.user_id}` 
        : `${URLROOT}/user/profile?id=${postObj.user_id}`;

    const leftMediaHtml = pnBigPostLeftMediaMarkup(postObj);
    if (leftMediaHtml) {
        leftBay.innerHTML = leftMediaHtml;
    } else {
        leftBay.innerHTML = `
            <div class="p-8 text-center flex flex-col items-center justify-center max-w-lg select-none">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg mb-4">
                    <span class="material-symbols-outlined text-white text-3xl">format_quote</span>
                </div>
                <p class="text-white text-lg font-bold leading-relaxed line-clamp-6 italic">"${escapeHtml(postObj.content || title)}"</p>
                <span class="text-xs text-slate-500 font-semibold mt-4 tracking-widest uppercase">• ProNetwork Feed Update •</span>
            </div>
        `;
    }

    const likeActionsHtml = isCompany
        ? `<a href="${targetUrl}" class="flex-1 flex items-center justify-center gap-1.5 py-2 text-slate-600 dark:text-slate-400 font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[18px]">domain</span>
                    <span>Page</span>
                </a>`
        : `<button type="button" onclick="triggerBigPostLike(${postObj.post_id})" class="flex-1 flex items-center justify-center gap-1.5 py-2 text-slate-600 dark:text-slate-400 font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[18px]">thumb_up</span>
                    <span>Like</span>
                </button>`;
    rightBay.innerHTML = `
        <!-- Sticky Author Header + Close Button -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-800 shrink-0 bg-white dark:bg-slate-900 z-10">
            <div class="flex items-center space-x-3 min-w-0">
                <img src="${logo}" class="w-10 h-10 rounded-full object-cover border border-slate-200 dark:border-slate-800 shrink-0 cursor-pointer" onclick="window.location.href='${targetUrl}'">
                <div class="min-w-0">
                    <h4 class="font-bold text-sm text-slate-900 dark:text-white hover:underline cursor-pointer truncate block leading-tight" onclick="window.location.href='${targetUrl}'">${title}</h4>
                    <p class="text-[11px] text-slate-500 truncate block leading-tight">${role}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5 block leading-tight">${timeAgo} • <span class="material-symbols-outlined text-[10px] align-middle">public</span></p>
                </div>
            </div>
            <div class="flex items-center space-x-1 shrink-0">
                <button onclick="window.location.href='${targetUrl}'" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full text-slate-400 hover:text-[#0A66C2] transition-colors" title="Go to profile">
                    <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                </button>
                <button onclick="document.getElementById('big-post-view-modal').classList.add('hidden')" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" title="Close modal">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
        </div>

        <!-- Scrollable Thread Content & Comments -->
        <div class="flex-1 overflow-y-auto flex flex-col min-h-0">
            <!-- Post Paragraph Content -->
            <div class="p-4 text-sm text-slate-800 dark:text-slate-200 leading-normal whitespace-pre-wrap shrink-0 border-b border-slate-50 dark:border-slate-800/50">
                ${escapeHtml(postObj.content || '')}
            </div>

            <!-- Stats Counts -->
            <div class="px-4 py-2 flex items-center justify-between text-[12px] text-slate-500 border-b border-slate-50 dark:border-slate-800 shrink-0">
                <div class="flex items-center gap-1 font-semibold text-[#0A66C2]">
                    <span class="material-symbols-outlined text-[14px]">thumb_up</span>
                    <span id="big-post-like-count">${postObj.reaction_count || 0}</span>
                </div>
                <div class="font-semibold text-slate-500">
                    <span id="big-post-comment-count">${postObj.comment_count || 0} comments</span>
                </div>
            </div>

            <!-- Interactive Feed Actions Bar -->
            <div class="flex px-2 py-1 border-b border-slate-100 dark:border-slate-800 shrink-0 select-none">
                ${likeActionsHtml}
                <button type="button" onclick="focusBigPostCommentInput()" class="flex-1 flex items-center justify-center gap-1.5 py-2 text-slate-600 dark:text-slate-400 font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[18px]">chat_bubble</span>
                    <span>Comment</span>
                </button>
                <button type="button" onclick="window.location.href='${targetUrl}'" class="flex-1 flex items-center justify-center gap-1.5 py-2 text-slate-600 dark:text-slate-400 font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[18px]">person</span>
                    <span>Author</span>
                </button>
            </div>

            <!-- Comments Output List -->
            <div id="big-post-comments-container" class="flex-1 p-4 space-y-3 overflow-y-auto text-sm min-h-[100px]">
                <div class="flex items-center justify-center py-6 text-slate-400 text-xs">Loading live community thread...</div>
            </div>
        </div>

        <!-- Sticky Comment Input Form -->
        <div class="p-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90 shrink-0">
            <form id="big-post-comment-form" onsubmit="submitBigPostComment(event, ${postObj.post_id})" class="flex gap-2 items-center">
                <input id="big-post-comment-input" type="text" autocomplete="off" class="flex-1 min-w-0 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-4 py-2 text-xs text-slate-900 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0A66C2]" maxlength="2000" placeholder="Add a comment…" required />
                <button type="submit" class="rounded-full bg-[#0A66C2] hover:bg-[#004182] text-white px-4 py-2 text-xs font-bold shrink-0 transition-colors">Post</button>
            </form>
        </div>
    `;

    modal.classList.remove('hidden');

    const rawPid = postObj.post_id;
    const isNumericMemberPost =
        !postObj.is_company_activity &&
        (typeof rawPid === 'number' || (typeof rawPid === 'string' && /^\d+$/.test(String(rawPid))));

    if (!isNumericMemberPost) {
        const c = document.getElementById('big-post-comments-container');
        if (c) {
            c.innerHTML =
                '<div class="text-center py-6 text-slate-400 text-xs">Member comments are not available for this company update. Use Page or Author to continue.</div>';
        }
        const wrap = document.getElementById('big-post-comment-form')?.closest('div.p-3');
        if (wrap) wrap.classList.add('hidden');
        return;
    }

    // Fetch and populate actual comments live!
    if (postObj.post_id) {
        fetch(`${URLROOT}/post/comments/${postObj.post_id}`)
            .then(r => r.json())
            .then(d => {
                const container = document.getElementById('big-post-comments-container');
                if (!container) return;
                if (d.success && d.comments?.length) {
                    container.innerHTML = d.comments.map(renderCommentHtml).join('');
                } else {
                    container.innerHTML = '<div class="text-center py-6 text-slate-400 text-xs">No comments yet. Be the first to start the conversation!</div>';
                }
            })
            .catch(() => {
                const container = document.getElementById('big-post-comments-container');
                if (container) container.innerHTML = '<div class="text-center py-6 text-red-500 text-xs">Failed to load comments</div>';
            });
    }
}

async function triggerBigPostLike(postId) {
    try {
        const res = await fetch(`${URLROOT}/post/react/${postId}`, { method: 'POST' });
        const data = await res.json();
        if (data.success) {
            const likeCntEl = document.getElementById('big-post-like-count');
            if (likeCntEl) likeCntEl.textContent = data.count;
            
            const cardLikeCntEl = document.querySelector(`#post-${postId} .like-count`);
            if (cardLikeCntEl) cardLikeCntEl.textContent = data.count;

            const feedLikeBtn = document.querySelector(`#post-${postId} .like-btn`);
            if (feedLikeBtn) {
                feedLikeBtn.classList.toggle('is-liked');
                triggerIconPop(feedLikeBtn);
            }

            [window.FEED_POSTS_RAW, window.FEED_POSTS_CACHE].forEach(arr => {
                if (!Array.isArray(arr)) return;
                const p = arr.find(x => x.post_id == postId);
                if (p) {
                    p.reaction_count = data.count;
                    if (feedLikeBtn) p.user_has_liked = feedLikeBtn.classList.contains('is-liked') ? 1 : 0;
                }
            });
        }
    } catch(e) {}
}

function focusBigPostCommentInput() {
    const inp = document.getElementById('big-post-comment-input');
    if (inp) inp.focus();
}

async function submitBigPostComment(ev, postId) {
    ev.preventDefault();
    const inp = document.getElementById('big-post-comment-input');
    if (!inp || !inp.value.trim()) return;

    const contentString = inp.value.trim();
    inp.disabled = true;

    try {
        const fd = new FormData();
        fd.append('content', contentString);
        const res = await fetch(`${URLROOT}/post/comments/${postId}`, { method: 'POST', body: fd });
        const data = await res.json();
        
        inp.disabled = false;
        if (data.success) {
            inp.value = '';
            fetch(`${URLROOT}/post/comments/${postId}`)
                .then(r => r.json())
                .then(d => {
                    const container = document.getElementById('big-post-comments-container');
                    if (container && d.success && d.comments?.length) {
                        container.innerHTML = d.comments.map(renderCommentHtml).join('');
                    }
                });

            const countEl = document.getElementById('big-post-comment-count');
            if (countEl) countEl.textContent = `${data.count} comments`;

            const cardEl = document.getElementById('post-' + postId);
            if (cardEl) updateCommentCountLabel(cardEl, data.count);

            const cached = window.FEED_POSTS_CACHE?.find(p => p.post_id == postId);
            if (cached) cached.comment_count = data.count;
        }
    } catch(e) {
        inp.disabled = false;
    }
}

function updateCommentCountLabel(card, count) {
    const n = Number(count) || 0;
    const line = card.querySelector('.comment-stat-line');
    if (!line) return;
    line.textContent = `${n} comment${n === 1 ? '' : 's'}`;
    line.classList.remove('pn-stat-flash');
    void line.offsetWidth;
    line.classList.add('pn-stat-flash');
}

function renderCommentHtml(c) {
    const when = formatTimeAgo(new Date(c.created_at));
    const pic = pnProfilePicUrl(c);
    const targetUrl = c.user_role === 'Company' ? `${URLROOT}/company/show/${c.user_id}` : `${URLROOT}/user/profile?id=${c.user_id}`;
    const avatar = `<img src="${escapeHtml(pic)}" alt="" class="w-9 h-9 rounded-full object-cover bg-slate-100 shrink-0 cursor-pointer" onclick="window.location.href='${targetUrl}'">`;
    const currUid = typeof CURRENT_USER_ID !== 'undefined' ? CURRENT_USER_ID : 0;
    const canDelete = (c.user_id == currUid || c.post_owner_id == currUid);
    const delBtn = canDelete ? `<button type="button" onclick="deleteCommentNode(this, ${c.comment_id}, ${c.post_id})" class="text-slate-400 hover:text-red-600 transition-colors p-0.5 rounded hover:bg-red-50 shrink-0" title="Delete comment"><span class="material-symbols-outlined text-[15px]">delete</span></button>` : '';

    return `
        <div class="pn-comment-item flex gap-2 items-start group/cmt" data-comment-id="${c.comment_id}">
            ${avatar}
            <div class="min-w-0 flex-1">
                <div class="flex items-baseline justify-between gap-x-2">
                    <div class="flex flex-wrap items-baseline gap-x-2 gap-y-0 min-w-0">
                        <span class="font-semibold text-slate-900 dark:text-white text-[13px] cursor-pointer hover:underline truncate" onclick="window.location.href='${targetUrl}'">${escapeHtml(c.full_name || 'Member')}</span>
                        <span class="text-[11px] text-slate-400">${when}</span>
                    </div>
                    ${delBtn}
                </div>
                <p class="text-[13px] text-slate-700 dark:text-slate-200 mt-0.5 whitespace-pre-wrap break-words">${escapeHtml(c.content)}</p>
            </div>
        </div>`;
}

async function deleteCommentNode(btn, commentId, postId) {
    if (!commentId || btn.disabled) return;
    const confirmed = confirm('Delete this comment?');
    if (!confirmed) return;

    btn.disabled = true;
    try {
        const res = await fetch(`${URLROOT}/post/deleteComment/${commentId}`, { method: 'POST' });
        const data = await res.json();
        if (data.success) {
            const item = btn.closest('.pn-comment-item');
            if (item) item.remove();
            const card = document.getElementById('post-' + postId);
            if (card) updateCommentCountLabel(card, data.count);
            feedToast('Comment deleted.', 'success');
        } else {
            feedToast(data.message || 'Failed to delete comment.', 'error');
            btn.disabled = false;
        }
    } catch(e) {
        feedToast('Server error.', 'error');
        btn.disabled = false;
    }
}

async function loadCommentsInto(wrap, postId) {
    const list = wrap.querySelector('[data-comments-list]');
    if (!list) return;
    list.innerHTML = '<p class="text-xs text-slate-500 py-2">Loading comments…</p>';
    try {
        const res = await fetch(`${URLROOT}/post/comments/${postId}`);
        const data = await res.json();
        if (!data.success) {
            list.innerHTML = '<p class="text-xs text-red-600">Could not load comments.</p>';
            return;
        }
        if (!data.comments || data.comments.length === 0) {
            list.innerHTML = '<p class="text-xs text-slate-500 py-2">No comments yet. Be the first to comment.</p>';
            return;
        }
        list.innerHTML = data.comments.map(renderCommentHtml).join('');
    } catch (e) {
        list.innerHTML = '<p class="text-xs text-red-600">Could not load comments.</p>';
    }
}

async function shareFeedPost(postId, shareBtn, clientX, clientY) {
    if (shareBtn) {
        addRipple(shareBtn, clientX, clientY);
        triggerActionBump(shareBtn);
    }
    const card = document.getElementById('post-' + postId);
    const excerpt = (card?.querySelector('.post-body-content')?.textContent || '').trim().slice(0, 200);
    const url = `${URLROOT}/user/feed#post-${postId}`;
    const shareText = excerpt ? excerpt + '…' : 'View this post on ProNetwork';

    if (navigator.share) {
        try {
            await navigator.share({
                title: 'ProNetwork',
                text: shareText,
                url
            });
            feedToast('Thanks for sharing!', 'success');
            if (shareBtn) triggerShareWiggle(shareBtn);
            return;
        } catch (err) {
            if (err && err.name === 'AbortError') return;
        }
    }
    try {
        await navigator.clipboard.writeText(url);
        feedToast('Link copied to clipboard', 'success');
        if (shareBtn) triggerShareWiggle(shareBtn);
    } catch (e) {
        try {
            const ta = document.createElement('textarea');
            ta.value = url;
            ta.setAttribute('readonly', '');
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            feedToast('Link copied to clipboard', 'success');
            if (shareBtn) triggerShareWiggle(shareBtn);
        } catch (e2) {
            window.prompt('Copy this link to share:', url);
        }
    }
}

function initFeedCommentsAndShare() {
    const root = document.getElementById('feed-container');
    if (!root) return;

    root.addEventListener('click', async e => {
        const shareBtn = e.target.closest('.share-post-btn');
        if (shareBtn) {
            e.preventDefault();
            const id = shareBtn.getAttribute('data-post-id');
            if (id) await shareFeedPost(id, shareBtn, e.clientX, e.clientY);
            document.querySelectorAll('.post-menu').forEach(m => m.classList.add('hidden'));
            return;
        }

        const cbtn = e.target.closest('.comment-toggle');
        if (cbtn) {
            e.preventDefault();
            addRipple(cbtn, e.clientX, e.clientY);
            const id = cbtn.getAttribute('data-post-id');
            const card = cbtn.closest('article');
            const wrap = card?.querySelector('.post-comments-wrap');
            if (!id || !wrap) return;

            const willOpen = !wrap.classList.contains('is-open');
            document.querySelectorAll('.post-comments-wrap').forEach(w => {
                if (w !== wrap) {
                    w.classList.remove('is-open');
                    const otherBtn = w.closest('article')?.querySelector('.comment-toggle');
                    if (otherBtn) otherBtn.classList.remove('is-active');
                }
            });

            if (willOpen) {
                wrap.classList.add('is-open');
                cbtn.classList.add('is-active');
                triggerActionBump(cbtn);
                triggerIconPop(cbtn);
                await loadCommentsInto(wrap, id);
            } else {
                wrap.classList.remove('is-open');
                cbtn.classList.remove('is-active');
            }
            document.querySelectorAll('.post-menu').forEach(m => m.classList.add('hidden'));
            return;
        }
    });

    root.addEventListener('submit', async e => {
        const form = e.target.closest('.post-comment-form');
        if (!form) return;
        e.preventDefault();
        const postId = form.getAttribute('data-post-id');
        const input = form.querySelector('.comment-input');
        const btn = form.querySelector('.comment-submit');
        const text = (input?.value || '').trim();
        if (!postId || !text) {
            feedToast('Write a comment first.', 'error');
            return;
        }
        if (btn) btn.disabled = true;
        try {
            const res = await fetch(`${URLROOT}/post/comments/${postId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ content: text })
            });
            const data = await res.json();
            if (data.success && data.comment) {
                input.value = '';
                const card = form.closest('article');
                const wrap = form.closest('.post-comments-wrap');
                const list = wrap?.querySelector('[data-comments-list]');
                if (btn) triggerActionBump(btn);
                if (list) {
                    const emptyMsg = list.querySelector('p.text-slate-500');
                    if (emptyMsg && emptyMsg.textContent.includes('No comments yet')) {
                        list.innerHTML = '';
                    }
                    const div = document.createElement('div');
                    div.innerHTML = renderCommentHtml(data.comment).trim();
                    const node = div.firstElementChild;
                    if (node) list.appendChild(node);
                }
                if (card && typeof data.count === 'number') updateCommentCountLabel(card, data.count);
                feedToast('Comment posted', 'success');
            } else {
                feedToast(data.message || 'Could not post comment.', 'error');
            }
        } catch (err) {
            feedToast('Could not post comment.', 'error');
        } finally {
            if (btn) btn.disabled = false;
        }
    });

    window.addEventListener('hashchange', scrollToPostFromHash);
}

// Event Delegation for Post Menus and Reactions
document.addEventListener('click', (e) => {
    const toggle = e.target.closest('.post-menu-toggle');
    const reportBtn = e.target.closest('.report-post-btn');
    const deleteBtn = e.target.closest('.delete-own-post-btn');
    const menu = e.target.closest('.post-menu');
    const rxnToggle = e.target.closest('.reaction-count-toggle');

    if (rxnToggle) {
        e.preventDefault();
        e.stopPropagation();
        showReactionsModal(rxnToggle.dataset.postId);
        return;
    } else if (reportBtn) {
        e.preventDefault();
        e.stopPropagation();
        reportPost(reportBtn);
    } else if (deleteBtn) {
        e.preventDefault();
        e.stopPropagation();
        deleteOwnPost(deleteBtn);
    } else if (toggle) {
        e.preventDefault();
        e.stopPropagation();
        const dropdown = toggle.nextElementSibling;
        if (dropdown) {
            // Close all other menus
            document.querySelectorAll('.post-menu').forEach(m => {
                if (m !== dropdown) m.classList.add('hidden');
            });
            dropdown.classList.toggle('hidden');
        }
    } else if (!menu) {
        // Clicked outside, close all menus
        document.querySelectorAll('.post-menu').forEach(m => m.classList.add('hidden'));
    }
});

async function showReactionsModal(postId) {
    const modal = document.getElementById('reactions-list-modal');
    const list = document.getElementById('reactions-modal-list');
    if (!modal || !list || !postId || !/^\d+$/.test(String(postId))) return;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    list.innerHTML = '<p class="text-xs text-slate-500 py-4 text-center">Loading reactions…</p>';
    
    try {
        const res = await fetch(`${URLROOT}/post/reactions/${postId}`);
        const data = await res.json();
        if (data.success && data.reactions?.length) {
            list.innerHTML = data.reactions.map(r => {
                const pic = pnProfilePicUrl(r);
                const targetUrl = r.user_role === 'Company' ? `${URLROOT}/company/show/${r.user_id}` : `${URLROOT}/user/profile?id=${r.user_id}`;
                return `
                <div class="flex items-center justify-between p-2 hover:bg-slate-50 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3 cursor-pointer min-w-0 flex-1" onclick="window.location.href='${targetUrl}'">
                        <img src="${escapeHtml(pic)}" class="w-10 h-10 rounded-full object-cover border border-slate-100 bg-slate-50 shrink-0">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-sm font-bold text-slate-900 hover:underline truncate">${escapeHtml(r.full_name)}</h4>
                            <p class="text-xs text-slate-500 truncate">${escapeHtml(r.headline || 'Professional')}</p>
                        </div>
                    </div>
                    <span class="material-symbols-outlined text-blue-500 text-[16px] bg-blue-50 p-1.5 rounded-full ml-2 shrink-0">thumb_up</span>
                </div>`;
            }).join('');
        } else {
            list.innerHTML = '<p class="text-xs text-slate-500 py-4 text-center">No reactions yet.</p>';
        }
    } catch(e) {
        list.innerHTML = '<p class="text-xs text-red-600 py-4 text-center">Could not load reactions.</p>';
    }
}

async function reportPost(reportBtn) {
    const postId = reportBtn.dataset.postId;
    if (!postId || reportBtn.disabled) return;

    const reason = await pnModal({
        title: 'Report Post',
        message: 'Please provide a reason for reporting this post to help our moderation team.',
        type: 'flag',
        isPrompt: true,
        placeholder: 'e.g. Inappropriate content, Spam, Harassment...',
        confirmText: 'Submit Report',
        cancelText: 'Go Back'
    });
    
    if (reason === null) return;

    reportBtn.disabled = true;
    try {
        const res = await fetch(`${URLROOT}/post/report/${postId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ reason: reason.trim() || 'Inappropriate content' })
        });
        const data = await res.json();

        if (data.success) {
            const menu = reportBtn.closest('.post-menu');
            if (menu) menu.classList.add('hidden');
            feedToast('Report submitted! Admin will review it.', 'success');
        } else {
            feedToast(data.message || 'Failed to report.', 'error');
        }
    } catch(e) {
        console.error('Report error:', e);
        feedToast('Server error.', 'error');
    } finally {
        reportBtn.disabled = false;
    }
}

async function deleteOwnPost(deleteBtn) {
    const postId = deleteBtn.dataset.postId;
    if (!postId || deleteBtn.disabled) return;
    const confirmed = await pnModal({
        title: 'Delete Post',
        message: 'Are you sure you want to delete this post? This action cannot be undone.',
        type: 'warning',
        confirmText: 'Delete Forever',
        cancelText: 'Keep Post',
        isDanger: true
    });
    
    if (!confirmed) return;

    deleteBtn.disabled = true;
    try {
        const res = await fetch(`${URLROOT}/post/delete/${postId}`, { method: 'POST' });
        const data = await res.json();
        if (data.success) {
            const card = deleteBtn.closest('article');
            if (card) {
                card.classList.add('pn-feed-card--leaving');
                setTimeout(() => card.remove(), 380);
            }
            feedToast('Post deleted.', 'success');
        } else {
            feedToast(data.message || 'Failed to delete post.', 'error');
        }
    } catch(e) {
        feedToast('Server error.', 'error');
    } finally {
        deleteBtn.disabled = false;
    }
}

// Toast helper for feed
function feedToast(msg, type = 'info') {
    const existing = document.getElementById('feed-toast');
    if (existing) existing.remove();
    const t = document.createElement('div');
    t.id = 'feed-toast';
    const bg = type === 'error' ? 'bg-red-600' : type === 'success' ? 'bg-green-600' : 'bg-blue-600';
    const icon = type === 'error' ? 'error' : type === 'success' ? 'check_circle' : 'info';
    t.className = `fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-xl text-sm font-medium ${bg} text-white ring-1 ring-white/10 pn-toast-motion`;
    t.innerHTML = `<span class="material-symbols-outlined text-[20px]">${icon}</span><span>${escapeHtml(msg)}</span>`;
    document.body.appendChild(t);
    setTimeout(() => {
        t.style.opacity = '0';
        t.style.transform = 'translate3d(12px, 8px, 0) scale(0.96)';
        t.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
        setTimeout(() => t.remove(), 360);
    }, 4200);
}

function formatTimeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    let interval = seconds / 31536000;
    if (interval > 1) return Math.floor(interval) + "y";
    interval = seconds / 2592000;
    if (interval > 1) return Math.floor(interval) + "m";
    interval = seconds / 86400;
    if (interval > 1) return Math.floor(interval) + "d";
    interval = seconds / 3600;
    if (interval > 1) return Math.floor(interval) + "h";
    interval = seconds / 60;
    if (interval > 1) return Math.floor(interval) + "m";
    return "just now";
}

function escapeHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}
