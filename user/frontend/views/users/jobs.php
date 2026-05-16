<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>

<div class="user-page-shell pt-2 pb-12 font-['Manrope']" id="jobs-page">
    <div class="jobs-page-grid max-w-[1128px] mx-auto grid grid-cols-1 md:grid-cols-12 gap-6 px-4">

        <aside class="jobs-sidebar-col md:col-span-4 lg:col-span-3 space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden sticky top-20">
                <div class="p-4 border-b border-slate-100 bg-gradient-to-br from-slate-50 to-white">
                    <h2 class="font-title-md text-slate-900">Job search</h2>
                    <p class="text-xs text-slate-500 mt-1">Track applications, saves, and alerts</p>
                </div>
                <nav class="flex flex-col py-1" id="jobs-sidebar-nav" aria-label="Jobs navigation">
                    <button type="button" data-jobs-panel="browse" class="jobs-nav-item is-active flex items-center gap-3 px-5 py-3 text-left transition-colors border-l-4 border-[#0A66C2] bg-blue-50/60 group">
                        <span class="material-symbols-outlined text-[#0A66C2]">search</span>
                        <span class="flex-1 font-bold text-slate-800">Browse jobs</span>
                    </button>
                    <button type="button" data-jobs-panel="saved" class="jobs-nav-item flex items-center gap-3 px-5 py-3 text-left hover:bg-slate-50 transition-colors border-l-4 border-transparent group">
                        <span class="material-symbols-outlined text-slate-500 group-hover:text-[#0A66C2]">bookmark</span>
                        <span class="flex-1 font-bold text-slate-700 group-hover:text-slate-900">Saved</span>
                        <span id="jobs-saved-count" class="text-xs font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full min-w-[1.5rem] text-center">0</span>
                    </button>
                    <button type="button" data-jobs-panel="applied" class="jobs-nav-item flex items-center gap-3 px-5 py-3 text-left hover:bg-slate-50 transition-colors border-l-4 border-transparent group">
                        <span class="material-symbols-outlined text-slate-500 group-hover:text-[#0A66C2]">assignment_turned_in</span>
                        <span class="flex-1 font-bold text-slate-700 group-hover:text-slate-900">My applications</span>
                        <span id="jobs-applied-count" class="text-xs font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full min-w-[1.5rem] text-center">0</span>
                    </button>
                    <button type="button" data-jobs-panel="alerts" class="jobs-nav-item flex items-center gap-3 px-5 py-3 text-left hover:bg-slate-50 transition-colors border-l-4 border-transparent group">
                        <span class="material-symbols-outlined text-slate-500 group-hover:text-[#0A66C2]">notifications</span>
                        <span class="flex-1 font-bold text-slate-700 group-hover:text-slate-900">Job alerts</span>
                        <span id="jobs-alerts-count" class="text-xs font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full min-w-[1.5rem] text-center">0</span>
                    </button>
                    <button type="button" data-jobs-panel="salary" class="jobs-nav-item flex items-center gap-3 px-5 py-3 text-left hover:bg-slate-50 transition-colors border-l-4 border-transparent group">
                        <span class="material-symbols-outlined text-slate-500 group-hover:text-[#0A66C2]">payments</span>
                        <span class="flex-1 font-bold text-slate-700 group-hover:text-slate-900">With salary</span>
                    </button>
                </nav>
                <div class="border-t border-slate-100 p-4 bg-slate-50/50 space-y-2">
                    <a href="<?php echo URLROOT; ?>/company" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-[#0A66C2] to-blue-600 text-white font-bold py-2.5 rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all text-sm">
                        <span class="material-symbols-outlined text-lg">domain</span>
                        Company hub
                    </a>
                    <a href="<?php echo URLROOT; ?>/user/settings" class="w-full flex items-center justify-center gap-2 border-2 border-slate-200 text-slate-700 font-bold py-2 rounded-full hover:bg-white hover:border-[#0A66C2] hover:text-[#0A66C2] transition-all text-sm">
                        <span class="material-symbols-outlined text-lg">tune</span>
                        Job preferences
                    </a>
                </div>
            </div>

            <div id="jobs-alerts-card" class="hidden bg-white rounded-xl border border-slate-200 shadow-sm p-4 sticky top-20">
                <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500 text-lg">notifications_active</span>
                    Your alerts
                </h3>
                <div id="jobs-alerts-list" class="space-y-2 text-sm text-slate-600 mb-3"></div>
                <button type="button" id="jobs-add-alert-btn" class="w-full text-sm font-bold text-[#0A66C2] hover:bg-blue-50 py-2 rounded-lg transition-colors">
                    + Create alert
                </button>
            </div>
        </aside>

        <section class="jobs-list-col md:col-span-8 lg:col-span-4 flex flex-col gap-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col jobs-list-panel sticky top-20">
                <div class="p-4 border-b border-slate-100 space-y-3 bg-white z-10">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h1 id="jobs-list-title" class="text-lg font-black text-slate-800">Recommended for you</h1>
                            <p id="jobs-list-subtitle" class="text-xs text-slate-500 mt-0.5">Based on your profile and activity</p>
                        </div>
                        <span id="jobs-result-count" class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full shrink-0">—</span>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">search</span>
                        <input type="search" id="jobs-search-input" placeholder="Search title, company, location…" autocomplete="off"
                            class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#0A66C2]/25 focus:border-[#0A66C2] transition-all" />
                        <button type="button" id="jobs-search-clear" class="hidden absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full hover:bg-slate-200 text-slate-500 flex items-center justify-center" aria-label="Clear search">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                    <div id="jobs-type-filters" class="flex flex-wrap gap-2" role="group" aria-label="Job type filters">
                        <button type="button" data-job-type="" class="jobs-filter-chip is-active">All</button>
                        <button type="button" data-job-type="Full-time" class="jobs-filter-chip">Full-time</button>
                        <button type="button" data-job-type="Part-time" class="jobs-filter-chip">Part-time</button>
                        <button type="button" data-job-type="Contract" class="jobs-filter-chip">Contract</button>
                        <button type="button" data-job-type="Internship" class="jobs-filter-chip">Internship</button>
                    </div>
                </div>
                <div id="jobs-container" class="flex flex-col overflow-y-auto flex-1 bg-slate-50/80 min-h-[280px] max-h-[calc(100vh-220px)]">
                    <div class="flex flex-col items-center justify-center py-16 gap-3">
                        <div class="animate-spin rounded-full h-10 w-10 border-2 border-slate-200 border-t-[#0A66C2]"></div>
                        <p class="text-sm text-slate-500">Loading opportunities…</p>
                    </div>
                </div>
            </div>
        </section>

        <aside class="jobs-detail-col hidden lg:block lg:col-span-5 min-w-0">
            <div id="job-detail-container" class="jobs-detail-panel bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col items-center justify-center sticky top-20 text-center p-8 min-h-[320px] max-h-[calc(100vh-100px)]">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl flex items-center justify-center mb-5 shadow-inner">
                    <span class="material-symbols-outlined text-4xl text-[#0A66C2]/60">work</span>
                </div>
                <h2 class="text-lg font-bold text-slate-800 mb-2">Select a job</h2>
                <p class="text-slate-500 text-sm max-w-xs leading-relaxed">Pick a listing to read the full description, see the company, and apply in one click.</p>
            </div>
        </aside>
    </div>
</div>

<div id="jobs-mobile-backdrop" class="jobs-mobile-backdrop hidden fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm" aria-hidden="true"></div>
<div id="jobs-mobile-detail" class="jobs-mobile-drawer hidden fixed inset-x-0 bottom-0 z-50 max-h-[92vh] flex flex-col rounded-t-2xl bg-white shadow-2xl transform translate-y-full transition-transform duration-300" role="dialog" aria-modal="true" aria-labelledby="jobs-mobile-detail-title">
    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 shrink-0">
        <h2 id="jobs-mobile-detail-title" class="font-bold text-slate-800 text-sm">Job details</h2>
        <button type="button" id="jobs-mobile-close" class="w-10 h-10 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-600" aria-label="Close">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <div id="jobs-mobile-detail-body" class="flex-1 overflow-y-auto"></div>
</div>

<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
