<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>
<!-- Page content (single <main> lives in navbar layout) -->
<div class="user-page-shell pb-12 pt-2">
    <?php if (hasRole('Company')): ?>
    <div class="max-w-[1128px] mx-auto px-4 mb-4">
        <div class="bg-indigo-50 border border-indigo-200 text-indigo-800 px-4 py-3 rounded-lg flex items-center gap-3">
            <span class="material-symbols-outlined text-indigo-600">person</span>
            <div>
                <p class="font-bold text-sm">Personal Feed</p>
                <p class="text-xs text-indigo-700">You are interacting as your personal profile. <a href="<?php echo URLROOT; ?>/company/dashboard" class="underline font-semibold ml-1 hover:text-indigo-900">Switch to Company</a></p>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="max-w-[1128px] mx-auto grid grid-cols-1 md:grid-cols-12 gap-6 px-4">
        <!-- Left Sidebar: Profile Summary -->
        <aside class="md:col-span-3 flex flex-col space-y-2 self-start sticky top-20 z-10">
            <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden w-full border border-slate-200 dark:border-slate-800 shadow-[0px_2px_8px_rgba(0,0,0,0.06)]">
                <!-- Cover: filled via data-user-cover when available -->
                <div class="h-16 bg-gradient-to-br from-[#0A66C2] via-[#004182] to-[#001b3d] relative overflow-hidden">
                    <img data-user-cover="true" alt="" class="absolute inset-0 w-full h-full object-cover opacity-50 mix-blend-overlay pointer-events-none hidden" width="400" height="64" decoding="async" />
                </div>
                <!-- Profile Pic & Info -->
                <div class="px-4 pb-4 -mt-10 flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-full border-4 border-white dark:border-slate-900 overflow-hidden bg-slate-100 shadow-md relative z-10 ring-1 ring-slate-200/80">
                        <img data-user-pic="true" alt="" class="w-full h-full object-cover" src="" />
                    </div>
                    <div class="mt-3 min-h-[3.5rem]">
                        <h1 data-user-name="full" class="font-title-md text-sm font-bold text-slate-900 dark:text-white">Welcome</h1>
                        <p data-user-headline class="font-manrope text-[11px] leading-relaxed text-slate-500 mt-1 max-w-[180px] mx-auto">Your headline appears here</p>
                    </div>
                </div>
                <!-- Stats Section -->
                <div class="border-t border-slate-50 dark:border-slate-800 py-3">
                    <a href="<?php echo URLROOT; ?>/user/network" class="block px-4 flex justify-between items-center hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer py-1.5 transition-all group rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0A66C2]/40" title="Open My Network">
                        <span class="font-manrope text-[11px] font-semibold text-slate-500 group-hover:text-slate-700">Connections</span>
                        <span id="user-connections-count" class="font-manrope text-[11px] font-bold text-[#0A66C2]">0</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/user/network" class="block px-4 flex justify-between items-center hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer py-1.5 transition-all group rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0A66C2]/40" title="See pending connection requests">
                        <span class="font-manrope text-[11px] font-semibold text-slate-500 group-hover:text-slate-700">Pending invites</span>
                        <span id="user-pending-count" class="font-manrope text-[11px] font-bold text-[#0A66C2]">0</span>
                    </a>
                </div>
                <!-- Action Section -->
                <div class="border-t border-slate-50 dark:border-slate-800 p-3 bg-slate-50/30">
                    <a class="flex items-center gap-2 font-manrope text-[11px] font-bold text-[#0A66C2] hover:text-[#004182] transition-colors" href="<?php echo URLROOT; ?>/user/profile">
                        <span class="material-symbols-outlined text-[16px]">account_circle</span>
                        View your profile
                    </a>
                </div>
            </div>
            <!-- Identity Sidebar Part 2 -->
            <div class="bg-white dark:bg-slate-900 rounded-lg overflow-hidden w-full border border-slate-200 dark:border-slate-800 shadow-[0px_4px_12px_rgba(0,0,0,0.05)]">
                <div class="p-3">
                    <h3 class="font-manrope text-xs font-semibold text-slate-900 mb-3">Recent</h3>
                    <div class="space-y-3">
                        <?php if(!empty($data['recents'])): foreach($data['recents'] as $rec): ?>
                        <div class="flex items-center text-slate-600 hover:bg-slate-50 p-1 rounded cursor-pointer transition-all" onclick="openBigPostView(<?php echo (int)($rec['post_id'] ?? 1); ?>)">
                            <span class="material-symbols-outlined text-[18px] mr-2"><?php echo htmlspecialchars($rec['icon']); ?></span>
                            <span class="font-manrope text-xs font-semibold truncate"><?php echo htmlspecialchars($rec['title']); ?></span>
                        </div>
                        <?php endforeach; else: ?>
                        <div class="flex items-center text-slate-600 hover:bg-slate-50 p-1 rounded cursor-pointer transition-all" onclick="openBigPostView(1)">
                            <span class="material-symbols-outlined text-[18px] mr-2">tag</span>
                            <span class="font-manrope text-xs font-semibold">Tech Community</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="border-t border-slate-100 p-3 text-center">
                    <button class="text-slate-500 font-semibold text-xs hover:text-[#0A66C2]" onclick="window.location.href='<?php echo URLROOT; ?>/user/network'">Discover more</button>
                </div>
            </div>
        </aside>

        <!-- Center: Feed -->
        <div class="md:col-span-6 flex flex-col space-y-4">
            <!-- Composer -->
            <div class="bg-white rounded-lg border border-slate-200 shadow-[0px_4px_12px_rgba(0,0,0,0.05)] p-4">
                <div class="flex items-center space-x-3 mb-4">
                    <img data-user-pic="true" alt="" class="w-12 h-12 rounded-full object-cover bg-slate-200 ring-1 ring-slate-200/80" src="" decoding="async" />
                    <button id="open-post-composer" class="flex-1 text-left bg-slate-50 border border-slate-200 rounded-full px-4 py-3 text-slate-500 font-medium hover:bg-slate-100 transition-colors">
                        Start a post
                    </button>
                </div>
                <div class="flex justify-between items-center px-2">
                    <button id="open-post-media" class="flex items-center space-x-2 text-slate-500 hover:bg-slate-100 p-2 rounded transition-all">
                        <span class="material-symbols-outlined text-blue-500">image</span>
                        <span class="font-manrope text-sm font-semibold">Media</span>
                    </button>
                    <button id="open-post-event" class="flex items-center space-x-2 text-slate-500 hover:bg-slate-100 p-2 rounded transition-all">
                        <span class="material-symbols-outlined text-orange-400">calendar_month</span>
                        <span class="font-manrope text-sm font-semibold">Event</span>
                    </button>
                    <button id="open-post-article" class="flex items-center space-x-2 text-slate-500 hover:bg-slate-100 p-2 rounded transition-all">
                        <span class="material-symbols-outlined text-green-500">article</span>
                        <span class="font-manrope text-sm font-semibold">Write article</span>
                    </button>
                </div>
            </div>
            <div class="flex items-center space-x-2 flex-wrap gap-y-1" id="feed-sort-bar">
                <hr class="flex-1 border-slate-200 min-w-[4rem]"/>
                <div class="relative inline-flex items-center gap-1 text-xs text-slate-500">
                    <span>Sort by:</span>
                    <button type="button" id="feed-sort-trigger" class="inline-flex items-center gap-0.5 font-bold text-slate-900 rounded-lg px-2 py-1 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0A66C2]/40" aria-expanded="false" aria-haspopup="listbox">
                        <span id="feed-sort-label">Recent</span>
                        <span class="material-symbols-outlined text-[16px] text-slate-600">arrow_drop_down</span>
                    </button>
                    <ul id="feed-sort-menu" class="hidden absolute right-0 top-full mt-1 z-40 min-w-[10rem] rounded-lg border border-slate-200 bg-white py-1 shadow-lg" role="listbox">
                        <li role="option"><button type="button" class="feed-sort-opt w-full text-left px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50" data-sort="recent">Most recent</button></li>
                        <li role="option"><button type="button" class="feed-sort-opt w-full text-left px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50" data-sort="top">Top (engagement)</button></li>
                    </ul>
                </div>
            </div>
            <!-- Feed Posts -->
            <div id="feed-container" class="space-y-4">
                <!-- Dynamic posts will be loaded here -->
            </div>
        </div>

        <!-- Right Sidebar: Trending & Suggestions -->
        <aside class="md:col-span-3 flex flex-col space-y-3 self-start sticky top-20 z-10">
            <!-- Trending Section -->
            <div class="bg-white dark:bg-slate-900 rounded-lg overflow-hidden w-full border border-slate-200 dark:border-slate-800 shadow-[0px_4px_12px_rgba(0,0,0,0.05)]">
                <div class="p-4">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-manrope text-sm font-bold text-slate-900 dark:text-white">Trending Now</h3>
                        <button type="button" id="feed-trending-info" class="p-1 rounded-full text-slate-600 hover:bg-slate-100 hover:text-[#0A66C2] transition-colors" title="How trending works" aria-label="How trending topics are ranked">
                            <span class="material-symbols-outlined text-[18px]">info</span>
                        </button>
                    </div>
                    <ul class="space-y-4">
                        <?php if(!empty($data['trending'])): foreach($data['trending'] as $trend): ?>
                        <li class="cursor-pointer group" onclick="openBigPostView(<?php echo (int)($trend['post_id'] ?? 1); ?>)"><div class="flex items-start space-x-1.5"><span class="material-symbols-outlined text-[18px] mt-0.5 text-blue-500 shrink-0">trending_up</span><div class="flex flex-col min-w-0"><span class="font-manrope text-sm font-semibold text-slate-800 group-hover:underline decoration-[#0A66C2] truncate block"><?php echo htmlspecialchars($trend['title']); ?></span><span class="text-xs text-slate-400 block"><?php echo htmlspecialchars($trend['time']); ?> - <?php echo htmlspecialchars($trend['readers']); ?></span></div></div></li>
                        <?php endforeach; else: ?>
                        <li class="cursor-pointer group" onclick="openBigPostView(1)"><div class="flex items-start space-x-1"><span class="material-symbols-outlined text-[18px] mt-0.5 text-slate-400">trending_up</span><div class="flex flex-col"><span class="font-manrope text-sm font-semibold text-slate-700 group-hover:underline decoration-[#0A66C2]">Global Enterprise Trends</span><span class="text-xs text-slate-400">Just now - 12,040 readers</span></div></div></li>
                        <?php endif; ?>
                    </ul>
                    <button class="mt-4 text-slate-500 font-semibold text-sm hover:bg-slate-50 w-full text-left py-1 rounded transition-colors" onclick="window.location.href='<?php echo URLROOT; ?>/user/network'">Show more <span class="material-symbols-outlined text-[14px]">expand_more</span></button>
                </div>
            </div>
            <!-- Suggested Connections -->
            <div class="bg-white rounded-lg border border-slate-200 shadow-[0px_4px_12px_rgba(0,0,0,0.05)] p-4">
                <h3 class="font-manrope text-sm font-bold text-slate-900 mb-4">Add to your feed</h3>
                <div id="suggestions-container" class="space-y-4">
                    <!-- Dynamic suggestions will be loaded here -->
                </div>
                <button class="mt-4 text-slate-500 font-semibold text-sm hover:bg-slate-50 w-full text-left py-1 rounded flex items-center transition-colors" onclick="window.location.href='<?php echo URLROOT; ?>/user/network'">View all recommendations <span class="material-symbols-outlined text-[14px] ml-1">arrow_forward</span></button>
            </div>
            <!-- Footer Links -->
            <footer class="pn-feed-sidebar-footer flex flex-wrap justify-center gap-x-4 gap-y-2">
                <a class="text-[11px] text-slate-500 hover:text-[#0A66C2] hover:underline" href="<?php echo URLROOT; ?>/user/network">About</a>
                <a class="text-[11px] text-slate-500 hover:text-[#0A66C2] hover:underline" href="<?php echo URLROOT; ?>/user/settings">Accessibility</a>
                <a class="text-[11px] text-slate-500 hover:text-[#0A66C2] hover:underline" href="<?php echo URLROOT; ?>/user/messaging">Help Center</a>
                <a class="text-[11px] text-slate-500 hover:text-[#0A66C2] hover:underline" href="<?php echo URLROOT; ?>/user/settings">Privacy & Terms</a>
                <a class="text-[11px] text-slate-500 hover:text-[#0A66C2] hover:underline" href="<?php echo URLROOT; ?>/user/jobs">Ad Choices</a>
                <a class="text-[11px] text-slate-500 hover:text-[#0A66C2] hover:underline" href="<?php echo URLROOT; ?>/user/jobs">Advertising</a>
                <div class="w-full flex justify-center items-center mt-2 space-x-1">
                    <span class="text-[12px] font-black text-[#0A66C2]">ProNetwork</span>
                    <span class="text-[11px] text-slate-500">ProNetwork Corporation 2026</span>
                </div>
            </footer>
        </aside>
    </div>

    <!-- Post Composer Modal -->
    <div id="post-composer-modal" class="fixed inset-0 z-[120] hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-[640px] rounded-xl bg-white shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                <h3 class="font-manrope text-lg font-bold text-slate-900">Create post</h3>
                <button id="close-post-composer" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="feed-post-form" data-custom-validation="true" novalidate class="p-4 space-y-3">
                <textarea id="feed-post-content" name="content" rows="5" required class="w-full border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A66C2]" placeholder="What do you want to talk about?"></textarea>
                <div id="feed-post-error" class="hidden text-xs text-red-600 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/50 rounded-lg p-3 items-center gap-2 font-medium">
                    <span class="material-symbols-outlined text-[18px] text-red-500">error</span>
                    <span id="feed-post-error-text">Post content or media is required.</span>
                </div>
                <div id="post-media-section" class="hidden space-y-2 rounded-lg border border-slate-200 p-3">
                    <label class="text-sm font-semibold text-slate-700" for="feed-post-media">Upload media</label>
                    <input id="feed-post-media" name="media" type="file" accept="image/*,video/*" class="block w-full text-sm"/>
                    <p class="text-xs text-slate-500">Allowed: image/video up to 10 MB.</p>
                </div>
                <div id="post-event-section" class="hidden grid grid-cols-1 md:grid-cols-2 gap-2 rounded-lg border border-slate-200 p-3">
                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="feed-event-title">Event title</label>
                        <input id="feed-event-title" name="event_title" type="text" class="mt-1 w-full border border-slate-200 rounded p-2 text-sm" placeholder="Design meetup"/>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="feed-event-date">Event date</label>
                        <input id="feed-event-date" name="event_date" type="date" class="mt-1 w-full border border-slate-200 rounded p-2 text-sm"/>
                    </div>
                </div>
                <div id="post-article-section" class="hidden grid grid-cols-1 gap-2 rounded-lg border border-slate-200 p-3">
                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="feed-article-title">Article title</label>
                        <input id="feed-article-title" name="article_title" type="text" class="mt-1 w-full border border-slate-200 rounded p-2 text-sm" placeholder="How I improved design systems"/>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="feed-article-url">Article URL</label>
                        <input id="feed-article-url" name="article_url" type="url" class="mt-1 w-full border border-slate-200 rounded p-2 text-sm" placeholder="https://example.com/article"/>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2 rounded-full bg-[#0A66C2] text-white font-semibold hover:bg-[#004182]">Post</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reactions List Modal -->
    <div id="reactions-list-modal" class="fixed inset-0 z-[130] hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-[480px] rounded-xl bg-white shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[80vh]">
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                <div class="flex items-center space-x-2">
                    <span class="material-symbols-outlined text-blue-500 text-[20px]">thumb_up</span>
                    <h3 class="font-manrope text-base font-bold text-slate-900">Reactions</h3>
                </div>
                <button type="button" id="reactions-modal-close" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500" aria-label="Close reactions">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div id="reactions-modal-list" class="flex-1 overflow-y-auto p-2 space-y-1 min-h-[3rem]">
                <!-- Dynamic reactors loaded here -->
            </div>
        </div>
    </div>

    <!-- Premium LinkedIn-Style Big Post View Modal -->
    <div id="big-post-view-modal" class="hidden fixed inset-0 z-[100] bg-black/80 backdrop-blur-sm flex items-center justify-center p-2 md:p-6 overflow-hidden animate-fade-in">
        <div class="w-full max-w-[1180px] h-[90vh] max-h-[820px] rounded-xl bg-white dark:bg-slate-900 shadow-2xl border border-slate-700 overflow-hidden flex flex-col md:flex-row animate-scale-up relative">
            <!-- Left Side: Dark Theme Image Display Container -->
            <div id="big-post-modal-left" class="flex-1 bg-black flex items-center justify-center overflow-hidden relative min-h-[240px] md:min-h-0">
                <!-- Media rendered dynamically here -->
            </div>
            <!-- Right Side: White/Light Discussion Feed Container -->
            <div id="big-post-modal-right" class="w-full md:w-[420px] lg:w-[460px] shrink-0 bg-white dark:bg-slate-900 flex flex-col h-full overflow-hidden border-l border-slate-200 dark:border-slate-800">
                <!-- Header, Content, Comments rendered dynamically here -->
            </div>
        </div>
    </div>

</div>
<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
