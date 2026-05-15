<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>

<div class="user-page-shell pt-2 pb-12">
    <div class="max-w-[1128px] mx-auto grid grid-cols-1 md:grid-cols-[240px_1fr] gap-6 items-start px-4 md:px-0">
        <aside class="md:sticky md:top-20 flex flex-col gap-2 bg-white rounded-lg border border-gray-200 shadow-[0px_4px_12px_rgba(0,0,0,0.05)] overflow-hidden divide-y divide-gray-100">
            <div class="p-4">
                <div class="flex items-center gap-3 mb-1">
                    <img alt="Profile picture" data-user-pic="true" class="w-9 h-9 rounded-full object-cover bg-white" src="<?php echo pn_profile_pic_url(); ?>"/>
                    <div>
                        <h1 class="font-title-md text-lg font-bold text-[#0A66C2]">Settings</h1>
                        <p data-user-name="full" class="text-gray-500 text-xs font-body-md truncate max-w-[160px]">Loading...</p>
                    </div>
                </div>
            </div>
            <nav id="settings-tabs" class="flex flex-col">
                <button type="button" data-settings-tab="account" class="settings-tab is-active text-left bg-blue-50 text-[#0A66C2] font-bold border-l-4 border-[#0A66C2] px-4 py-3 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-xl">person</span>
                    <span class="font-label-lg">Account preferences</span>
                </button>
                <button type="button" data-settings-tab="security" class="settings-tab text-left text-gray-600 hover:bg-gray-50 border-l-4 border-transparent px-4 py-3 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-xl">lock</span>
                    <span class="font-label-lg">Sign in &amp; security</span>
                </button>
                <button type="button" data-settings-tab="visibility" class="settings-tab text-left text-gray-600 hover:bg-gray-50 border-l-4 border-transparent px-4 py-3 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-xl">visibility</span>
                    <span class="font-label-lg">Visibility</span>
                </button>
                <button type="button" data-settings-tab="privacy" class="settings-tab text-left text-gray-600 hover:bg-gray-50 border-l-4 border-transparent px-4 py-3 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-xl">database</span>
                    <span class="font-label-lg">Data privacy</span>
                </button>
                <button type="button" data-settings-tab="preferences" class="settings-tab text-left text-gray-600 hover:bg-gray-50 border-l-4 border-transparent px-4 py-3 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-xl">tune</span>
                    <span class="font-label-lg">General preferences</span>
                </button>
            </nav>
            <div class="p-4">
                <button id="settings-export-shortcut" class="w-full py-2 px-4 rounded-full border border-[#0A66C2] text-[#0A66C2] font-semibold text-sm hover:bg-blue-50 transition-colors">
                    Download my data
                </button>
            </div>
        </aside>

        <section class="flex flex-col gap-6">
            <div class="bg-white rounded-lg p-6 shadow-[0px_4px_12px_rgba(0,0,0,0.05)] border border-gray-100">
                <h2 id="settings-page-title" class="font-display-md text-2xl mb-1">Account preferences</h2>
                <p id="settings-page-copy" class="text-gray-500 font-body-md">Manage profile basics, contact details, and account information.</p>
            </div>

            <div data-settings-panel="account" class="settings-panel flex flex-col gap-6">
                <div class="bg-white rounded-lg shadow-[0px_4px_12px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="font-title-lg text-lg text-[#0A66C2]">Profile information</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Name, headline, location, and industry</span>
                                <span id="settings-profile-summary" class="settings-row-copy">Loading...</span>
                            </div>
                            <button type="button" data-settings-action="edit-profile" class="settings-link">Edit</button>
                        </div>
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Contact details</span>
                                <span id="settings-contact-summary" class="settings-row-copy">Loading...</span>
                            </div>
                            <button type="button" data-settings-action="edit-contact" class="settings-link">Edit</button>
                        </div>
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Profile page</span>
                                <span class="settings-row-copy">Open your public profile and review how others see you.</span>
                            </div>
                            <button type="button" data-settings-action="open-profile" class="settings-link">Open</button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-[0px_4px_12px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="font-title-lg text-lg text-[#0A66C2]">Optional profile context</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Personal demographics</span>
                                <span id="settings-demographics-summary" class="settings-row-copy">Not added</span>
                            </div>
                            <button type="button" data-settings-action="edit-demographics" class="settings-link">Edit</button>
                        </div>
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Verifications note</span>
                                <span id="settings-verification-summary" class="settings-row-copy">Not added</span>
                            </div>
                            <button type="button" data-settings-action="edit-verification" class="settings-link">Edit</button>
                        </div>
                    </div>
                </div>
            </div>

            <div data-settings-panel="security" class="settings-panel hidden flex flex-col gap-6">
                <div class="bg-white rounded-lg shadow-[0px_4px_12px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="font-title-lg text-lg text-[#0A66C2]">Sign in &amp; security</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Email address</span>
                                <span id="settings-email-summary" class="settings-row-copy">Loading...</span>
                            </div>
                            <button type="button" data-settings-action="change-email" class="settings-link">Change</button>
                        </div>
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Password</span>
                                <span class="settings-row-copy">Use your current password to set a new password.</span>
                            </div>
                            <button type="button" data-settings-action="change-password" class="settings-link">Change</button>
                        </div>
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Current session</span>
                                <span id="settings-session-summary" class="settings-row-copy">Active in this browser</span>
                            </div>
                            <button type="button" data-settings-action="logout" class="settings-link text-red-600 hover:bg-red-50">Sign out</button>
                        </div>
                    </div>
                </div>
            </div>

            <div data-settings-panel="visibility" class="settings-panel hidden flex flex-col gap-6">
                <div class="bg-white rounded-lg shadow-[0px_4px_12px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="font-title-lg text-lg text-[#0A66C2]">Visibility</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Profile visibility</span>
                                <span id="settings-profile-visibility-summary" class="settings-row-copy">Loading...</span>
                            </div>
                            <button type="button" data-settings-action="profile-visibility" class="settings-link">Edit</button>
                        </div>
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Show email on contact card</span>
                                <span id="settings-show-email-summary" class="settings-row-copy">Loading...</span>
                            </div>
                            <button type="button" data-settings-action="show-email" class="settings-link">Edit</button>
                        </div>
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Show phone on contact card</span>
                                <span id="settings-show-phone-summary" class="settings-row-copy">Loading...</span>
                            </div>
                            <button type="button" data-settings-action="show-phone" class="settings-link">Edit</button>
                        </div>
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Who can message you</span>
                                <span id="settings-allow-messages-summary" class="settings-row-copy">Loading...</span>
                            </div>
                            <button type="button" data-settings-action="allow-messages" class="settings-link">Edit</button>
                        </div>
                    </div>
                </div>
            </div>

            <div data-settings-panel="privacy" class="settings-panel hidden flex flex-col gap-6">
                <div class="bg-white rounded-lg shadow-[0px_4px_12px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="font-title-lg text-lg text-[#0A66C2]">Data privacy</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Personalized recommendations</span>
                                <span id="settings-personalization-summary" class="settings-row-copy">Loading...</span>
                            </div>
                            <button type="button" data-settings-action="data-personalization" class="settings-link">Edit</button>
                        </div>
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Search visibility</span>
                                <span id="settings-search-summary" class="settings-row-copy">Loading...</span>
                            </div>
                            <button type="button" data-settings-action="search-visibility" class="settings-link">Edit</button>
                        </div>
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Download your data</span>
                                <span class="settings-row-copy">Export your profile, settings, experience, education, connections, and posts.</span>
                            </div>
                            <button type="button" data-settings-action="export-data" class="settings-link">Download</button>
                        </div>
                    </div>
                </div>
            </div>

            <div data-settings-panel="preferences" class="settings-panel hidden flex flex-col gap-6">
                <div class="bg-white rounded-lg shadow-[0px_4px_12px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="font-title-lg text-lg text-[#0A66C2]">General preferences</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Theme</span>
                                <span id="settings-theme-summary" class="settings-row-copy">Loading...</span>
                            </div>
                            <button type="button" data-settings-action="theme" class="settings-link">Edit</button>
                        </div>
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Language</span>
                                <span id="settings-language-summary" class="settings-row-copy">Loading...</span>
                            </div>
                            <button type="button" data-settings-action="language" class="settings-link">Edit</button>
                        </div>
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Content language</span>
                                <span id="settings-content-language-summary" class="settings-row-copy">Loading...</span>
                            </div>
                            <button type="button" data-settings-action="content-language" class="settings-link">Edit</button>
                        </div>
                        <div class="settings-row">
                            <div>
                                <span class="font-title-md text-on-surface">Autoplay videos</span>
                                <span id="settings-autoplay-summary" class="settings-row-copy">Loading...</span>
                            </div>
                            <button type="button" data-settings-action="autoplay-videos" class="settings-link">Edit</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
