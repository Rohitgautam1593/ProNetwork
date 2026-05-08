/**
 * ProNetwork — Network Logic
 * assets/js/network.js
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('suggestions-grid')) {
        initNetwork();
    }
});

async function initNetwork() {
    await fetchSuggestions();
    await fetchInvitations();
}

async function fetchSuggestions() {
    const grid = document.getElementById('suggestions-grid');
    if (!grid) return;
    
    try {
        const response = await fetch(`${URLROOT}/network/suggestions`);
        const data = await response.json();
        
        if (data.success) {
            grid.innerHTML = '';
            if (data.suggestions.length === 0) {
                grid.innerHTML = '<p class="col-span-full text-center text-slate-500 py-8">No new suggestions at this time.</p>';
                return;
            }
            
            data.suggestions.forEach(user => {
                const picUrl = user.profile_pic ? (user.profile_pic.startsWith('http') ? user.profile_pic : `${URLROOT}/uploads/profiles/` + user.profile_pic) : '';
                const picHtml = picUrl ? `<img src="${picUrl}" class="w-20 h-20 rounded-full border-4 border-white -mt-10 object-cover relative z-10" alt="${escapeHtml(user.full_name)}">` : `<div class="w-20 h-20 rounded-full border-4 border-white -mt-10 bg-slate-200 flex items-center justify-center relative z-10"><span class="material-symbols-outlined text-3xl text-slate-400">person</span></div>`;
                
                const card = document.createElement('div');
                card.className = 'border border-slate-200 rounded-lg overflow-hidden flex flex-col items-center text-center relative group hover:shadow-md transition-shadow bg-white';
                card.innerHTML = `
<div class="h-16 w-full bg-gradient-to-r from-blue-400 to-indigo-600"></div>
<button class="absolute top-2 right-2 w-7 h-7 bg-black/20 text-white rounded-full flex items-center justify-center hover:bg-black/40 transition-colors">
<span class="material-symbols-outlined text-lg">close</span>
</button>
${picHtml}
<div class="p-4 pt-2 flex flex-col flex-1 w-full">
<h3 class="font-semibold text-slate-900 line-clamp-1 hover:underline cursor-pointer">${escapeHtml(user.full_name)}</h3>
<p class="text-xs text-slate-500 line-clamp-2 min-h-[32px] mt-1">${escapeHtml(user.headline || 'Professional')}</p>
<div class="mt-auto pt-4">
<button class="connect-btn w-full border-2 border-[#0A66C2] text-[#0A66C2] font-semibold py-1 rounded-full hover:bg-blue-50 transition-colors" data-user-id="${user.user_id}">Connect</button>
</div>
</div>`;
                
                card.querySelector('.connect-btn').addEventListener('click', async (e) => {
                    const userId = e.target.getAttribute('data-user-id');
                    const res = await fetch(`${URLROOT}/network/send_request`, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({user_id: userId})
                    });
                    const d = await res.json();
                    if (d.success) {
                        e.target.textContent = 'Pending';
                        e.target.disabled = true;
                        e.target.classList.remove('text-[#0A66C2]', 'border-[#0A66C2]');
                        e.target.classList.add('text-slate-400', 'border-slate-300');
                    }
                });
                
                grid.appendChild(card);
            });
        }
    } catch(err) {
        console.error(err);
    }
}

async function fetchInvitations() {
    const invContainer = document.getElementById('invitations-container');
    if (!invContainer) return;
    
    try {
        const response = await fetch(`${URLROOT}/network/pending`);
        const data = await response.json();
        
        if (data.success) {
            invContainer.innerHTML = '';
            if (data.requests.length === 0) {
                invContainer.innerHTML = '<p class="text-center text-slate-500 py-4">No pending invitations.</p>';
                return;
            }
            
                data.requests.forEach(req => {
                    const picUrl = req.profile_pic ? (req.profile_pic.startsWith('http') ? req.profile_pic : `${URLROOT}/uploads/profiles/` + req.profile_pic) : '';
                const picHtml = picUrl ? `<img src="${picUrl}" class="w-14 h-14 rounded-full object-cover">` : `<div class="w-14 h-14 rounded-full bg-slate-200 flex items-center justify-center"><span class="material-symbols-outlined text-slate-400">person</span></div>`;
                
                const item = document.createElement('div');
                item.className = 'p-4 flex items-start gap-4 border-b border-slate-100 last:border-0';
                item.innerHTML = `
${picHtml}
<div class="flex-1">
<div class="flex items-center justify-between">
<div>
<h3 class="font-semibold text-slate-900 hover:underline cursor-pointer">${escapeHtml(req.full_name)}</h3>
<p class="text-sm text-slate-500 leading-tight">${escapeHtml(req.headline || 'Professional')}</p>
</div>
<div class="flex items-center gap-2">
<button class="accept-btn bg-[#0A66C2] text-white font-semibold px-4 py-1.5 rounded-full hover:bg-[#004182] transition-colors" data-user-id="${req.user_id}">Accept</button>
<button class="ignore-btn text-slate-500 font-semibold px-4 py-1.5 rounded-full hover:bg-slate-100 transition-colors">Ignore</button>
</div>
</div>
</div>`;
                
                item.querySelector('.accept-btn').addEventListener('click', async (e) => {
                    const userId = e.target.getAttribute('data-user-id');
                    const res = await fetch(`${URLROOT}/network/accept`, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({user_id: userId})
                    });
                    const d = await res.json();
                    if (d.success) {
                        item.remove();
                        if (invContainer.children.length === 0) {
                            invContainer.innerHTML = '<p class="text-center text-slate-500 py-4">No pending invitations.</p>';
                        }
                    }
                });
                
                invContainer.appendChild(item);
            });
        }
    } catch(err) {
        console.error(err);
    }
}

function escapeHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}
