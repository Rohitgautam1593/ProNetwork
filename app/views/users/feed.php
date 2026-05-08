<?php require APPROOT . '/views/layouts/header.php'; ?>
<?php require APPROOT . '/views/layouts/navbar.php'; ?>
<!-- View Content -->
<main class="pt-16">
    <div class="max-w-[1128px] mx-auto grid grid-cols-1 md:grid-cols-12 gap-6 px-4">
        <!-- Left Sidebar: Profile Summary -->
        <aside class="md:col-span-3 flex flex-col space-y-2">
            <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden w-full sticky top-20 border border-slate-200 dark:border-slate-800 shadow-[0px_2px_8px_rgba(0,0,0,0.06)]">
                <!-- Premium Banner -->
                <div class="h-16 bg-gradient-to-br from-[#0A66C2] via-[#004182] to-[#001b3d] relative">
                    <img alt="Profile Banner" class="w-full h-full object-cover opacity-40 mix-blend-overlay" src="https://images.unsplash.com/photo-1557683316-973673baf926?auto=format&fit=crop&q=80&w=400"/>
                </div>
                <!-- Profile Pic & Info -->
                <div class="px-4 pb-4 -mt-10 flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-full border-4 border-white dark:border-slate-900 overflow-hidden bg-white shadow-md relative z-10">
                        <img data-user-pic="true" alt="User Profile Picture" class="w-full h-full object-cover" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"/>
                    </div>
                    <div class="mt-3">
                        <h1 data-user-name="full" class="font-title-md text-sm font-bold text-slate-900 dark:text-white">Alex Sterling</h1>
                        <p data-user-headline="true" class="font-manrope text-[11px] leading-relaxed text-slate-500 mt-1 max-w-[180px] mx-auto">Industry Leader | UX Strategist | Tech Speaker</p>
                    </div>
                </div>
                <!-- Stats Section -->
                <div class="border-t border-slate-50 dark:border-slate-800 py-3">
                    <div class="px-4 flex justify-between items-center hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer py-1.5 transition-all group">
                        <span class="font-manrope text-[11px] font-semibold text-slate-500 group-hover:text-slate-700">Connections</span>
                        <span id="user-connections-count" class="font-manrope text-[11px] font-bold text-[#0A66C2]">0</span>
                    </div>
                    <div class="px-4 flex justify-between items-center hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer py-1.5 transition-all group">
                        <span class="font-manrope text-[11px] font-semibold text-slate-500 group-hover:text-slate-700">Profile viewers</span>
                        <span id="user-views-count" class="font-manrope text-[11px] font-bold text-[#0A66C2]">0</span>
                    </div>
                </div>
                <!-- Action Section -->
                <div class="border-t border-slate-50 dark:border-slate-800 p-3 bg-slate-50/30">
                    <a class="flex items-center gap-2 font-manrope text-[11px] font-bold text-[#0A66C2] hover:text-[#004182] transition-colors" href="/ProNetwork/public/user/profile">
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
                        <div class="flex items-center text-slate-600 hover:bg-slate-50 p-1 rounded cursor-pointer transition-all">
                            <span class="material-symbols-outlined text-[18px] mr-2">group</span>
                            <span class="font-manrope text-xs font-semibold">UX Design Patterns</span>
                        </div>
                        <div class="flex items-center text-slate-600 hover:bg-slate-50 p-1 rounded cursor-pointer transition-all">
                            <span class="material-symbols-outlined text-[18px] mr-2">event</span>
                            <span class="font-manrope text-xs font-semibold">Global Tech Summit</span>
                        </div>
                        <div class="flex items-center text-slate-600 hover:bg-slate-50 p-1 rounded cursor-pointer transition-all">
                            <span class="material-symbols-outlined text-[18px] mr-2">tag</span>
                            <span class="font-manrope text-xs font-semibold">Leadership2024</span>
                        </div>
                    </div>
                </div>
                <div class="border-t border-slate-100 p-3 text-center">
                    <button class="text-slate-500 font-semibold text-xs hover:text-[#0A66C2]">Discover more</button>
                </div>
            </div>
        </aside>

        <!-- Center: Feed -->
        <div class="md:col-span-6 flex flex-col space-y-4">
            <!-- Composer -->
            <div class="bg-white rounded-lg border border-slate-200 shadow-[0px_4px_12px_rgba(0,0,0,0.05)] p-4">
                <div class="flex items-center space-x-3 mb-4">
                    <img data-user-pic="true" alt="User Profile Avatar" class="w-12 h-12 rounded-full object-cover bg-slate-200" data-alt="professional portrait of a confident executive smiling subtly in soft studio lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDgryhBzlTXQtDdGshJHtrMN5fqUZ61CqU953deGlbInT6ahGMDM8cb3rZpzF34tC0fiC_p6BJM1bBAAmr-BhqMww150y35yJAkqXzyOB3QmyGSKmQSsi8___S5CwpVXXsnRAaNmd5rSk7KNOX030Ef_9y12-f3aNaPQJ2wHbHSX02iBdLQv7iuwU_h52TZfxJfU7Ten1oS6r5ryp8FccYOa8h1mzcPdx0GWxSxmoA74gd9spgDJNG6p-OzcVlDy7CIWHvfO9udCaGS"/>
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
            <div class="flex items-center space-x-2">
                <hr class="flex-1 border-slate-200"/>
                <span class="text-xs text-slate-500">Sort by: <span class="font-bold text-slate-900 cursor-pointer">Top <span class="material-symbols-outlined text-[14px]">arrow_drop_down</span></span></span>
            </div>
            <!-- Feed Posts -->
            <div id="feed-container" class="space-y-4">
                <!-- Dynamic posts will be loaded here -->
            </div>
        </div>

        <!-- Right Sidebar: Trending & Suggestions -->
        <aside class="md:col-span-3 flex flex-col space-y-3">
            <!-- Trending Section -->
            <div class="bg-white dark:bg-slate-900 rounded-lg overflow-hidden w-full border border-slate-200 dark:border-slate-800 shadow-[0px_4px_12px_rgba(0,0,0,0.05)] sticky top-20">
                <div class="p-4">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-manrope text-sm font-bold text-slate-900 dark:text-white">Trending Now</h3>
                        <span class="material-symbols-outlined text-[18px] text-slate-600">info</span>
                    </div>
                    <ul class="space-y-4">
                        <li class="cursor-pointer group"><div class="flex items-start space-x-1"><span class="material-symbols-outlined text-[18px] mt-0.5 text-slate-400">trending_up</span><div class="flex flex-col"><span class="font-manrope text-sm font-semibold text-slate-700 group-hover:underline decoration-[#0A66C2]">Remote Work Trends 2024</span><span class="text-xs text-slate-400">2d ago - 15,432 readers</span></div></div></li>
                        <li class="cursor-pointer group"><div class="flex items-start space-x-1"><span class="material-symbols-outlined text-[18px] mt-0.5 text-slate-400">lightbulb</span><div class="flex flex-col"><span class="font-manrope text-sm font-semibold text-slate-700 group-hover:underline decoration-[#0A66C2]">Networking Tips for Introverts</span><span class="text-xs text-slate-400">1d ago - 8,102 readers</span></div></div></li>
                        <li class="cursor-pointer group"><div class="flex items-start space-x-1"><span class="material-symbols-outlined text-[18px] mt-0.5 text-slate-400">notifications_active</span><div class="flex flex-col"><span class="font-manrope text-sm font-semibold text-slate-700 group-hover:underline decoration-[#0A66C2]">Job Alerts: Fintech Growth</span><span class="text-xs text-slate-400">5h ago - 3,291 readers</span></div></div></li>
                    </ul>
                    <button class="mt-4 text-slate-500 font-semibold text-sm hover:bg-slate-50 w-full text-left py-1 rounded transition-colors">Show more <span class="material-symbols-outlined text-[14px]">expand_more</span></button>
                </div>
            </div>
            <!-- Suggested Connections -->
            <div class="bg-white rounded-lg border border-slate-200 shadow-[0px_4px_12px_rgba(0,0,0,0.05)] p-4">
                <h3 class="font-manrope text-sm font-bold text-slate-900 mb-4">Add to your feed</h3>
                <div id="suggestions-container" class="space-y-4">
                    <!-- Dynamic suggestions will be loaded here -->
                </div>
                <button class="mt-4 text-slate-500 font-semibold text-sm hover:bg-slate-50 w-full text-left py-1 rounded flex items-center transition-colors">View all recommendations <span class="material-symbols-outlined text-[14px] ml-1">arrow_forward</span></button>
            </div>
            <!-- Footer Links -->
            <footer class="p-4 flex flex-wrap justify-center gap-x-4 gap-y-2">
                <a class="text-[11px] text-slate-500 hover:text-[#0A66C2] hover:underline" href="#">About</a>
                <a class="text-[11px] text-slate-500 hover:text-[#0A66C2] hover:underline" href="#">Accessibility</a>
                <a class="text-[11px] text-slate-500 hover:text-[#0A66C2] hover:underline" href="#">Help Center</a>
                <a class="text-[11px] text-slate-500 hover:text-[#0A66C2] hover:underline" href="#">Privacy & Terms</a>
                <a class="text-[11px] text-slate-500 hover:text-[#0A66C2] hover:underline" href="#">Ad Choices</a>
                <a class="text-[11px] text-slate-500 hover:text-[#0A66C2] hover:underline" href="#">Advertising</a>
                <div class="w-full flex justify-center items-center mt-2 space-x-1">
                    <span class="text-[12px] font-black text-[#0A66C2]">ProNetwork</span>
                    <span class="text-[11px] text-slate-500">ProNetwork Corporation 2024</span>
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
                <div id="feed-post-error" class="hidden text-xs text-red-600"></div>
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

</main>
<?php require APPROOT . '/views/layouts/footer.php'; ?>
