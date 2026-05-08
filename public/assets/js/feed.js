/**
 * ProNetwork — Feed Logic
 * assets/js/feed.js
 * Handles fetching and rendering the dynamic post feed.
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
    // Only run if we are on the feed page
    const feedList = document.getElementById('feed-container');
    if (feedList) {
        initFeed();
        initSuggestions();
        initFeedStats();
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
        
        // Dummy profile views for now as there's no DB table for it
        const viewsEl = document.getElementById('user-views-count');
        if (viewsEl) viewsEl.textContent = Math.floor(Math.random() * 100) + 20;
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

    try {
        const response = await fetch(`${URLROOT}/post`);
        const result = await response.json();

        if (result.success) {
            // Clear static placeholders if any
            feedList.innerHTML = '';
            
            if (result.posts.length === 0) {
                feedList.innerHTML = '<div class="bg-white p-8 text-center rounded-lg border border-slate-200"><p class="text-slate-500">No posts yet. Be the first to share something!</p></div>';
            } else {
                result.posts.forEach(post => {
                    renderPostCard(post);
                });
            }
        }
    } catch (err) {
        console.error('Failed to load feed:', err);
    }
}

/**
 * Renders a single post card and adds it to the feed.
 * @param {Object} post The post data from API
 * @param {Boolean} prepend Whether to add to top or bottom
 */
function renderPostCard(post, prepend = false) {
    const feedList = document.getElementById('feed-container');
    if (!feedList) return;

    const card = document.createElement('article');
    card.className = 'bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-[0px_2px_8px_rgba(0,0,0,0.04)] overflow-hidden mb-4 transition-all hover:shadow-[0px_4px_16px_rgba(0,0,0,0.08)]';
    
    // Time formatting
    const postDate = new Date(post.created_at);
    const timeAgo = formatTimeAgo(postDate);

    card.innerHTML = `
        <div class="p-4">
            <!-- Author Info -->
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full border border-slate-100 overflow-hidden bg-slate-50">
                        ${post.profile_pic ? 
                            `<img src="${post.profile_pic.startsWith('http') ? post.profile_pic : `${URLROOT}/uploads/profiles/` + post.profile_pic}" alt="${post.full_name}" class="w-full h-full object-cover">` : 
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
                <button class="w-8 h-8 flex items-center justify-center hover:bg-slate-50 rounded-full transition-colors text-slate-400">
                    <span class="material-symbols-outlined">more_horiz</span>
                </button>
            </div>
            
            <!-- Post Content -->
            <div class="text-[14px] text-slate-800 dark:text-slate-200 leading-normal whitespace-pre-wrap">
                ${escapeHtml(post.content)}
            </div>

            ${post.post_image ? `
                <div class="mt-3 -mx-4">
                    <img src="${URLROOT}/uploads/posts/${post.post_image}" class="w-full max-h-[500px] object-cover border-y border-slate-50 dark:border-slate-800">
                </div>
            ` : ''}

            <!-- Post Stats -->
            <div class="mt-4 flex items-center justify-between text-[12px] text-slate-500 border-b border-slate-50 dark:border-slate-800 pb-2 px-1">
                <div class="flex items-center gap-1.5">
                    <div class="flex -space-x-1">
                        <div class="w-4 h-4 rounded-full bg-blue-500 flex items-center justify-center border border-white"><span class="material-symbols-outlined text-white text-[10px] fill-current">thumb_up</span></div>
                        <div class="w-4 h-4 rounded-full bg-red-500 flex items-center justify-center border border-white"><span class="material-symbols-outlined text-white text-[10px] fill-current">favorite</span></div>
                    </div>
                    <span class="like-count font-medium">${post.reaction_count || 0}</span>
                </div>
                <div class="flex gap-2">
                    <span>0 comments</span> • <span>0 shares</span>
                </div>
            </div>

            <!-- Post Actions -->
            <div class="flex mt-1 -mx-2 pt-1">
                <button class="like-btn flex-1 flex items-center justify-center gap-2 py-2 text-slate-600 dark:text-slate-400 font-bold text-[13px] hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-all active:scale-95" data-post-id="${post.post_id}">
                    <span class="material-symbols-outlined text-[20px]">thumb_up</span>
                    <span>Like</span>
                </button>
                <button class="flex-1 flex items-center justify-center gap-2 py-2 text-slate-600 dark:text-slate-400 font-bold text-[13px] hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-all">
                    <span class="material-symbols-outlined text-[20px]">chat</span>
                    <span>Comment</span>
                </button>
                <button class="flex-1 flex items-center justify-center gap-2 py-2 text-slate-600 dark:text-slate-400 font-bold text-[13px] hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-all">
                    <span class="material-symbols-outlined text-[20px]">share</span>
                    <span>Share</span>
                </button>
            </div>
        </div>
    `;

    // Add Like Event Listener
    const likeBtn = card.querySelector('.like-btn');
    likeBtn.addEventListener('click', async () => {
        try {
            const res = await fetch(`${URLROOT}/post/react/${post.post_id}`, { method: 'POST' });
            const data = await res.json();
            if (data.success) {
                const countEl = card.querySelector('.like-count');
                countEl.textContent = data.count;
                likeBtn.classList.toggle('text-[#0A66C2]');
            }
        } catch(e) {}
    });

    if (prepend) {
        feedList.prepend(card);
    } else {
        feedList.appendChild(card);
    }
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
