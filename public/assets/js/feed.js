/**
 * ProNetwork — Feed Logic
 * assets/js/feed.js
 * Handles fetching and rendering the dynamic post feed.
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const feedList = document.getElementById('feed-container');
    if (feedList) {
        initFeed();
        initSuggestions();
        initFeedStats();
        initFeedCommentsAndShare();
    }
});

async function initFeedStats() {
    try {
        const res = await fetch(`${URLROOT}/network/connections`);
        const data = await res.json();
        if (data.success) {
            const countEl = document.getElementById('user-connections-count');
            if (countEl) countEl.textContent = data.connections.length;
        }
        
        const viewsEl = document.getElementById('user-views-count');
        if (viewsEl) viewsEl.textContent = '—';
    } catch(e) {}
}

async function initSuggestions() {
    const sugCont = document.getElementById('suggestions-container');
    if (!sugCont) return;
    try {
        const res = await fetch(`${URLROOT}/network/suggestions`);
        const data = await res.json();
        if (data.success) {
            sugCont.innerHTML = '';
            if (data.suggestions.length === 0) {
                sugCont.innerHTML = '<p class="text-xs text-slate-500">No suggestions right now.</p>';
                return;
            }
            data.suggestions.slice(0,3).forEach(u => {
                const picUrl = u.profile_pic ? (u.profile_pic.startsWith('http') ? u.profile_pic : `${URLROOT}/uploads/profiles/` + u.profile_pic) : '';
                const picHtml = picUrl ? `<img src="${picUrl}" class="w-10 h-10 rounded-full object-cover">` : `<div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center"><span class="material-symbols-outlined text-slate-400 text-xl">person</span></div>`;
                sugCont.innerHTML += `
<div class="flex items-start justify-between"><div class="flex items-center space-x-3">${picHtml}<div><h4 class="text-sm font-bold text-slate-900">${escapeHtml(u.full_name)}</h4><p class="text-[11px] text-slate-500 leading-tight">${escapeHtml(u.headline||'Professional')}</p><button class="mt-1 border border-slate-500 rounded-full px-3 py-1 text-slate-600 text-[11px] font-bold hover:bg-slate-50 flex items-center space-x-1"><span class="material-symbols-outlined text-[14px]">add</span><span>Follow</span></button></div></div></div>
`;
            });
        }
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
            // Clear static placeholders if any
            feedList.innerHTML = '';
            
            if (result.posts.length === 0) {
                feedList.innerHTML = '<div class="pn-feed-empty bg-white p-10 text-center rounded-xl border border-slate-200 shadow-sm"><p class="text-slate-600 font-medium">No posts yet. Be the first to share something!</p></div>';
            } else {
                result.posts.forEach((post, i) => {
                    renderPostCard(post, false, i);
                });
                scrollToPostFromHash();
            }
        }
    } catch (err) {
        console.error('Failed to load feed:', err);
    }
}

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
                    <div class="w-12 h-12 rounded-full border border-slate-100 overflow-hidden bg-slate-50">
                        ${post.profile_pic ? 
                            `<img src="${post.profile_pic.startsWith('http') ? post.profile_pic : `${URLROOT}/uploads/profiles/` + post.profile_pic}" alt="${escapeHtml(post.full_name)}" class="w-full h-full object-cover">` : 
                            `<div class="w-full h-full flex items-center justify-center bg-slate-200"><span class="material-symbols-outlined text-slate-400 text-3xl">person</span></div>`
                        }
                    </div>
                    <div>
                        <h4 class="font-bold text-[14px] text-slate-900 dark:text-white hover:text-[#0A66C2] transition-colors cursor-pointer">${escapeHtml(post.full_name)}</h4>
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
                        ${post.user_id == (window.CURRENT_USER_ID || 0) ? `
                        <div class="h-[1px] bg-slate-100 mx-2 my-1"></div>
                        <button type="button" class="delete-own-post-btn w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors" data-post-id="${post.post_id}">
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                            Delete post
                        </button>
                        ` : ''}
                    </div>
                </div>
            </div>
            
            <div class="post-body-content text-[14px] text-slate-800 dark:text-slate-200 leading-normal whitespace-pre-wrap">
                ${escapeHtml(post.content)}
            </div>

            ${post.post_image ? `
                <div class="mt-3 -mx-4 overflow-hidden rounded-lg">
                    <img src="${URLROOT}/uploads/posts/${post.post_image}" alt="" class="pn-feed-post-media w-full max-h-[500px] object-cover border-y border-slate-50 dark:border-slate-800">
                </div>
            ` : ''}

            <div class="mt-4 flex items-center justify-between text-[12px] text-slate-500 border-b border-slate-50 dark:border-slate-800 pb-2 px-1">
                <div class="flex items-center gap-1.5">
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
            }
        } catch (e) {}
    });

    if (prepend) {
        feedList.prepend(card);
    } else {
        feedList.appendChild(card);
    }
}

function scrollToPostFromHash() {
    const m = /^#post-(\d+)$/.exec(window.location.hash || '');
    if (!m) return;
    requestAnimationFrame(() => {
        const el = document.getElementById('post-' + m[1]);
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
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
    const pic = c.profile_pic
        ? (c.profile_pic.startsWith('http') ? c.profile_pic : `${URLROOT}/uploads/profiles/${c.profile_pic}`)
        : '';
    const avatar = pic
        ? `<img src="${escapeHtml(pic)}" alt="" class="w-9 h-9 rounded-full object-cover bg-slate-100 shrink-0">`
        : `<div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-slate-500 text-[20px]">person</span></div>`;
    return `
        <div class="pn-comment-item flex gap-2 items-start" data-comment-id="${c.comment_id}">
            ${avatar}
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-baseline gap-x-2 gap-y-0">
                    <span class="font-semibold text-slate-900 dark:text-white text-[13px]">${escapeHtml(c.full_name || 'Member')}</span>
                    <span class="text-[11px] text-slate-400">${when}</span>
                </div>
                <p class="text-[13px] text-slate-700 dark:text-slate-200 mt-0.5 whitespace-pre-wrap break-words">${escapeHtml(c.content)}</p>
            </div>
        </div>`;
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

// Event Delegation for Post Menus
document.addEventListener('click', (e) => {
    const toggle = e.target.closest('.post-menu-toggle');
    const reportBtn = e.target.closest('.report-post-btn');
    const deleteBtn = e.target.closest('.delete-own-post-btn');
    const menu = e.target.closest('.post-menu');

    if (reportBtn) {
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
