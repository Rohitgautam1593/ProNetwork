    </div> <!-- Close flex-1 overflow-y-auto -->
</div> <!-- Close flex-1 flex flex-col -->
</div> <!-- Close flex min-h-screen -->

<!-- Global Toast -->
<div id="toast-container" class="fixed bottom-6 right-6 z-[200]"></div>

<!-- Admin Entity Detail Modal (Polished Premium UI) -->
<div id="adminEntityModal" class="fixed inset-0 z-[170] hidden overflow-y-auto" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeAdminEntityModal()"></div>
    
    <!-- Outer wrapper ensuring absolute minimum top/bottom spacing for vertical overflow flexibility -->
    <div class="min-h-screen px-4 py-8 flex items-center justify-center">
        <!-- Scaled Container Frame -->
        <div class="relative w-full max-w-3xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col border border-white/80"
             id="admin-entity-card">
            
            <!-- ── Dynamic Colored Banner Hero ── -->
            <div id="admin-entity-hero" class="relative flex items-start sm:items-center gap-4 px-4 sm:px-6 py-5 flex-shrink-0 transition-all duration-300" style="background: linear-gradient(135deg, #0f172a 0%, #334155 100%);">
                <!-- Decorative texture grid -->
                <div class="absolute inset-0 opacity-10"
                     style="background-image:radial-gradient(circle,#fff 1.5px,transparent 1.5px);background-size:18px 18px;"></div>
                
                <!-- Avatar Frame -->
                <div id="admin-entity-avatar"
                     class="relative w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex-shrink-0 flex items-center justify-center
                            overflow-hidden shadow-inner ring-4 ring-white/25 bg-white/20 text-white font-black text-xl">
                    <span class="material-symbols-outlined text-[28px]">database</span>
                </div>
                
                <!-- Metadata Headers -->
                <div class="relative min-w-0 flex-1">
                    <div id="admin-entity-type"
                         class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md
                                text-[10px] font-black uppercase tracking-widest
                                bg-white/20 text-white backdrop-blur-md border border-white/30 mb-1.5 shadow-sm">Record</div>
                    <h3 id="admin-entity-title"
                        class="admin-layout-modal-title text-lg font-black text-white font-manrope leading-tight truncate">Loading...</h3>
                    <p id="admin-entity-subtitle" class="admin-layout-modal-subtitle text-xs text-white/80 mt-0.5 font-medium truncate"></p>
                </div>
                
                <!-- Close Trigger -->
                <button onclick="closeAdminEntityModal()"
                        class="relative flex-shrink-0 w-8 h-8 flex items-center justify-center
                               rounded-full bg-white/10 hover:bg-white/25 text-white transition-all duration-150">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <!-- ── Loader Screen ── -->
            <div id="admin-entity-loading" class="p-12 text-center flex-1 flex flex-col items-center justify-center">
                <div class="inline-block w-8 h-8 border-2 border-slate-900 border-t-transparent rounded-full animate-spin"></div>
                <p class="mt-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Fetching secure block...</p>
            </div>

            <!-- ── Content Stack Body ── -->
            <div id="admin-entity-body" class="hidden flex-1 max-h-[calc(100vh-12rem)] overflow-y-auto p-0 bg-white custom-scrollbar">
                <div class="flex flex-col md:flex-row gap-0 divide-y md:divide-y-0 md:divide-x divide-slate-100">
                    
                    <!-- Left Section: Main Data Stack -->
                    <div class="flex-1 p-5 sm:p-6 space-y-5 min-w-0 flex flex-col justify-between">
                        <div class="space-y-5">
                            <div id="admin-entity-summary-strip" class="grid grid-cols-1 sm:grid-cols-3 gap-2 hidden"></div>
                            
                            <!-- Media Container Preview -->
                            <div id="admin-entity-image-wrap" class="hidden">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">image</span> Attached Media
                                </p>
                                <div class="relative bg-slate-50/80 border border-slate-100 rounded-2xl p-2 flex items-center justify-center overflow-hidden group">
                                    <img id="admin-entity-image" src="" alt="Post Attachment" 
                                         class="w-full max-h-64 object-contain rounded-xl">
                                    <a id="admin-entity-image-link" href="#" target="_blank" class="absolute bottom-3 right-3 px-3 py-1.5 bg-slate-900/80 hover:bg-slate-900 text-white text-[10px] font-bold rounded-lg backdrop-blur-sm transition-all shadow-md flex items-center gap-1 opacity-0 group-hover:opacity-100">
                                        <span class="material-symbols-outlined text-[12px]">open_in_new</span> Full Size
                                    </a>
                                </div>
                            </div>

                            <!-- Core Description/Overview -->
                            <div>
                                <p id="admin-entity-content-label"
                                   class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">notes</span> Overview
                                </p>
                                <div id="admin-entity-content"
                                     class="text-xs text-slate-700 leading-relaxed whitespace-pre-wrap
                                            bg-slate-50/75 border border-slate-100 rounded-2xl p-4
                                            font-normal admin-layout-modal-copy overflow-wrap-anywhere"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Section: Details Meta Keys Panel -->
                    <div class="w-full md:w-64 flex-shrink-0 bg-slate-50/30 p-5 sm:p-6 space-y-4 flex flex-col justify-between">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">info</span> Meta Info
                            </p>
                            <dl id="admin-entity-meta" class="space-y-2.5"></dl>
                        </div>

                        <div class="pt-4 border-t border-slate-100 mt-6 space-y-2.5">
                            <a id="admin-entity-manage" href="#" 
                               class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-bold transition-all shadow-sm">
                                <span class="material-symbols-outlined text-[14px]">open_in_new</span> Management Page
                            </a>
                            <button id="admin-entity-delete-btn" type="button"
                               class="hidden w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 text-[11px] font-bold transition-all shadow-sm border border-red-100">
                                <span class="material-symbols-outlined text-[14px]">delete_forever</span> <span id="admin-entity-delete-text">Delete Record</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ── Error Feedback Screen ── -->
            <div id="admin-entity-error" class="hidden p-12 text-center flex-1 flex flex-col items-center justify-center">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-red-50 text-red-600 flex items-center justify-center mb-3">
                    <span class="material-symbols-outlined">error</span>
                </div>
                <p class="text-sm font-bold text-slate-900">Could not retrieve secure metadata.</p>
                <p id="admin-entity-error-message" class="mt-1 text-xs text-slate-500"></p>
            </div>
        </div>
    </div>
</div>

<style>
/* ── Dynamic Layout Binding Overrides ── */
#admin-entity-card {
    animation: customModalFade 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes customModalFade {
    from { opacity: 0; transform: translateY(12px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

/* Ensure Meta Items rendering maps tightly */
#admin-entity-meta div {
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 12px;
    padding: 8px 12px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
#admin-entity-meta dt {
    font-size: 9px;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
#admin-entity-meta dd {
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    margin-top: 2px;
    word-break: normal;
    overflow-wrap: anywhere;
}
</style>

<!-- Load shared scripts before admin page scripts. -->
<script src="<?php echo URLROOT; ?>/assets/js/common.js?v=<?php echo time(); ?>"></script>
<script src="<?php echo URLROOT; ?>/assets/js/forms.js?v=<?php echo time(); ?>"></script>
<script>
    // Ensure data-user-pic works on admin panel too
    async function initAdminUserPic() {
        try {
            const res = await fetch(`${URLROOT}/user/me`);
            const data = await res.json();
            if (data.success && data.user.profile_pic) {
                const picUrl = data.user.profile_pic.startsWith('http') ? data.user.profile_pic : `${URLROOT}/uploads/profiles/` + data.user.profile_pic;
                document.querySelectorAll('img[data-user-pic="true"]').forEach(img => img.src = picUrl);
            }
        } catch(e) {}
    }
    initAdminUserPic();

    // Admin Notification Dropdown Toggle
    document.addEventListener('DOMContentLoaded', () => {
        const notifBtn = document.getElementById('admin-notif-btn');
        const notifDropdown = document.getElementById('admin-notif-dropdown');
        const menuBtn = document.getElementById('admin-menu-btn');
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('admin-sidebar-overlay');

        const closeAdminMenu = () => {
            if (!sidebar || !overlay || !menuBtn) return;
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            document.body.classList.remove('admin-menu-open');
            menuBtn.setAttribute('aria-expanded', 'false');
        };

        const openAdminMenu = () => {
            if (!sidebar || !overlay || !menuBtn) return;
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            document.body.classList.add('admin-menu-open');
            menuBtn.setAttribute('aria-expanded', 'true');
        };

        if (menuBtn && sidebar && overlay) {
            menuBtn.addEventListener('click', () => {
                if (sidebar.classList.contains('-translate-x-full')) {
                    openAdminMenu();
                } else {
                    closeAdminMenu();
                }
            });
            overlay.addEventListener('click', closeAdminMenu);
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) closeAdminMenu();
            });
        }

        if (notifBtn && notifDropdown) {
            notifBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                notifDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                    notifDropdown.classList.add('hidden');
                }
            });
        }

        // Start Admin Stats polling loop
        startAdminStatsPolling();
    });

    function startAdminStatsPolling() {
        const hasAdminBadges = document.getElementById('admin-pending-users-badge') ||
                               document.getElementById('admin-unread-reports-badge') ||
                               document.getElementById('admin-total-notif-badge');
                               
        if (!hasAdminBadges) return;

        const updateAdminBadge = (id, count) => {
            const el = document.getElementById(id);
            if (!el) return;
            if (count > 0) {
                el.textContent = String(count);
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        };

        const poll = async () => {
            try {
                const res = await fetch(`${URLROOT}/admin/stats_api`);
                const data = await res.json();
                
                const pendingUsers = parseInt(data.pending_users ?? 0);
                const unreadReports = parseInt(data.unread_reports ?? 0);
                const totalNotif = parseInt(data.total_admin_notifications ?? 0);

                // Update sidebar badges
                updateAdminBadge('admin-pending-users-badge', pendingUsers);
                updateAdminBadge('admin-unread-reports-badge', unreadReports);
                updateAdminBadge('admin-total-notif-badge', totalNotif);

                // Update header notification dropdown elements
                const countLabel = document.getElementById('admin-dropdown-count-label');
                if (countLabel) {
                    countLabel.textContent = `${totalNotif} Action Item${totalNotif !== 1 ? 's' : ''}`;
                }

                const usersItem = document.getElementById('admin-dropdown-users-item');
                const usersDesc = document.getElementById('admin-dropdown-users-desc');
                if (usersItem) {
                    if (pendingUsers > 0) {
                        usersItem.classList.remove('hidden');
                        if (usersDesc) usersDesc.textContent = `${pendingUsers} account${pendingUsers !== 1 ? 's' : ''} awaiting approval`;
                    } else {
                        usersItem.classList.add('hidden');
                    }
                }

                const reportsItem = document.getElementById('admin-dropdown-reports-item');
                const reportsDesc = document.getElementById('admin-dropdown-reports-desc');
                if (reportsItem) {
                    if (unreadReports > 0) {
                        reportsItem.classList.remove('hidden');
                        if (reportsDesc) reportsDesc.textContent = `${unreadReports} content flag${unreadReports !== 1 ? 's' : ''} to review`;
                    } else {
                        reportsItem.classList.add('hidden');
                    }
                }

                const emptyState = document.getElementById('admin-dropdown-empty-state');
                if (emptyState) {
                    if (totalNotif > 0) {
                        emptyState.classList.add('hidden');
                    } else {
                        emptyState.classList.remove('hidden');
                    }
                }
            } catch (e) {
                // Silently ignore network errors
            }
        };

        poll();
        setInterval(poll, 10000); // Poll every 10 seconds
    }
</script>
</body>
</html>
