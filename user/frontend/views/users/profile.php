<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>

<!-- View Content -->
<div id="profile-page" class="bg-[#f3f2ef] min-h-screen pt-6 pb-12 font-['Manrope']">
    <div class="max-w-[1128px] mx-auto grid grid-cols-1 md:grid-cols-12 gap-6 px-4">
        <!-- Left Column: Primary Content -->
        <div class="md:col-span-8 flex flex-col gap-4">

            <!-- Profile Header Card -->
            <section class="bg-white rounded-xl overflow-hidden border border-slate-200 shadow-sm relative">
                <div class="h-48 relative bg-slate-100 group/banner">
                    <img id="profile-banner-img" data-user-cover="true" alt="Cover photo" class="w-full h-full object-cover transition-transform duration-700 group-hover/banner:scale-105" src="<?php echo pn_cover_image_url(); ?>"/>
                    <div id="trigger-banner-upload" class="absolute top-4 right-4 bg-black/50 hover:bg-black/70 backdrop-blur-sm text-white p-2.5 rounded-full cursor-pointer hidden group-hover/banner:flex items-center justify-center transition-all shadow-lg hover:scale-105">
                        <span class="material-symbols-outlined text-[20px]">add_a_photo</span>
                    </div>
                    <input type="file" id="banner-upload-input" class="hidden" accept="image/*">
                </div>

                <div class="pt-20 px-6 pb-6 relative">
                    <!-- Profile Picture Zone -->
                    <div class="absolute -top-20 left-6">
                        <div class="relative group/pic cursor-pointer">
                            <img data-user-pic="true" id="main-profile-img" alt="Profile picture" class="w-36 h-36 rounded-full object-cover ring-4 ring-white shadow-md bg-white transition-transform duration-300 group-hover/pic:scale-[1.02]" src="<?php echo pn_profile_pic_url(); ?>"/>
                            <div class="absolute inset-0 bg-black/40 rounded-full hidden group-hover/pic:flex items-center justify-center backdrop-blur-[2px] transition-all" onclick="togglePicMenu(event)">
                                <span class="material-symbols-outlined text-white text-3xl drop-shadow-md">touch_app</span>
                            </div>

                            <!-- Pic Options Dropdown -->
                            <div id="pic-options-menu" class="hidden absolute top-full mt-2 left-0 bg-white rounded-xl shadow-xl border border-slate-100 w-48 overflow-hidden z-50">
                                <button onclick="viewProfilePic()" class="w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-3 transition-colors border-b border-slate-50">
                                    <span class="material-symbols-outlined text-slate-400 text-[18px]">visibility</span> View picture
                                </button>
                                <button onclick="document.getElementById('profile-pic-input').click(); closePicMenu();" class="w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-3 transition-colors">
                                    <span class="material-symbols-outlined text-slate-400 text-[18px]">add_a_photo</span> Upload new
                                </button>
                            </div>
                            <input type="file" id="profile-pic-input" class="hidden" accept="image/*">
                        </div>
                    </div>

                    <div class="flex justify-between items-start mt-2">
                        <div class="flex flex-col flex-1">
                            <div class="flex items-center gap-3">
                                <h1 data-user-name="full" class="text-2xl font-black text-slate-900 tracking-tight">Loading...</h1>
                                <button id="open-profile-edit" class="w-8 h-8 rounded-full hover:bg-blue-50 flex items-center justify-center text-slate-400 hover:text-[#0A66C2] transition-colors">
                                    <span class="material-symbols-outlined text-[18px]" data-icon="edit">edit</span>
                                </button>
                            </div>
                            <p data-user-headline class="text-base text-slate-700 mt-1 font-medium">Loading headline...</p>
                            <p class="text-sm text-slate-500 mt-2 flex items-center gap-1.5 font-medium">
                                <span data-user-location>Location</span>
                                <span>&bull;</span>
                                <button id="open-contact-info" class="text-[#0A66C2] font-bold hover:underline">Contact info</button>
                            </p>
                            <p id="profile-connections-link" class="text-sm text-[#0A66C2] mt-2 font-bold flex items-center gap-1 cursor-pointer hover:underline">
                                <span class="material-symbols-outlined text-[16px]">group</span>
                                <span id="profile-header-connections-count">0 connections</span>
                            </p>
                        </div>
                        <div class="hidden md:flex flex-col gap-3 text-right shrink-0 ml-4">
                            <!-- Populated dynamically via JS from latest experience/education -->
                            <div id="header-latest-exp" class="flex items-center gap-2 justify-end cursor-pointer group/link">
                                <img src="<?php echo URLROOT; ?>/assets/images/default_company.png" class="w-8 h-8 rounded object-cover shadow-sm hidden" id="header-exp-img">
                                <span class="text-sm text-slate-800 font-bold group-hover/link:text-[#0A66C2] transition-colors" id="header-exp-text"></span>
                            </div>
                            <div id="header-latest-edu" class="flex items-center gap-2 justify-end cursor-pointer group/link">
                                <div class="w-8 h-8 bg-slate-100 rounded flex items-center justify-center shadow-sm hidden" id="header-edu-icon"><span class="material-symbols-outlined text-slate-500 text-[16px]">school</span></div>
                                <span class="text-sm text-slate-800 font-bold group-hover/link:text-[#0A66C2] transition-colors" id="header-edu-text"></span>
                            </div>
                        </div>
                    </div>
                    <div id="profile-action-row" class="mt-6 flex flex-wrap gap-2.5">
                        <button id="profile-primary-action" class="bg-gradient-to-r from-[#0A66C2] to-blue-600 text-white px-5 py-1.5 rounded-full text-sm font-bold shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">Open to work</button>
                        <button id="profile-secondary-action" class="border border-[#0A66C2] text-[#0A66C2] px-5 py-1.5 rounded-full text-sm font-bold hover:bg-blue-50 hover:-translate-y-0.5 transition-all shadow-sm">Add profile section</button>
                        <div class="relative">
                            <button id="profile-more-action" class="border border-slate-300 text-slate-600 px-5 py-1.5 rounded-full text-sm font-bold hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm">More</button>
                            <div id="profile-more-menu" class="hidden absolute left-0 mt-2 bg-white rounded-xl shadow-xl border border-slate-100 w-56 overflow-hidden z-50"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- About Section -->
            <section class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm relative group">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-black text-slate-900">About</h2>
                    <button data-own-profile-action="true" class="w-8 h-8 rounded-full hover:bg-blue-50 flex items-center justify-center text-slate-400 hover:text-[#0A66C2] transition-colors opacity-0 group-hover:opacity-100" onclick="document.getElementById('open-profile-edit').click()">
                        <span class="material-symbols-outlined text-[20px]" data-icon="edit">edit</span>
                    </button>
                </div>
                <p data-user-bio class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap"></p>
            </section>

            <!-- Experience Section -->
            <section class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm relative group">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-black text-slate-900">Experience</h2>
                    <div class="flex gap-2">
                        <button id="open-experience" class="w-9 h-9 rounded-full hover:bg-blue-50 flex items-center justify-center text-slate-500 hover:text-[#0A66C2] transition-colors">
                            <span class="material-symbols-outlined text-[24px]" data-icon="add">add</span>
                        </button>
                    </div>
                </div>
                <div id="experience-container" class="flex flex-col gap-6 relative">
                    <!-- Dynamic Experience -->
                    <div class="flex justify-center py-6"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#0A66C2]"></div></div>
                </div>
            </section>

            <!-- Education Section -->
            <section class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm relative group">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-black text-slate-900">Education</h2>
                    <div class="flex gap-2">
                        <button id="open-education" class="w-9 h-9 rounded-full hover:bg-blue-50 flex items-center justify-center text-slate-500 hover:text-[#0A66C2] transition-colors">
                            <span class="material-symbols-outlined text-[24px]" data-icon="add">add</span>
                        </button>
                    </div>
                </div>
                <div id="education-container" class="flex flex-col gap-6 relative">
                    <!-- Dynamic Education -->
                    <div class="flex justify-center py-6"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#0A66C2]"></div></div>
                </div>
            </section>
        </div>

        <!-- Right Column: Sidebar -->
        <aside class="md:col-span-4 flex flex-col gap-4">
            <section class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm sticky top-20">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-black text-slate-900">Profile Strength</h3>
                    <span id="profile-strength-label" class="text-xs font-bold text-slate-500">Loading</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 mt-3 overflow-hidden">
                    <div id="profile-strength-bar" class="bg-gradient-to-r from-[#0A66C2] to-blue-400 h-2 rounded-full w-[0%] transition-all"></div>
                </div>
                <p id="profile-strength-copy" class="text-xs text-slate-500 mt-4 font-medium leading-relaxed">
                    Loading profile strength...
                </p>
                <div class="mt-6 pt-4 border-t border-slate-100 flex justify-between items-center">
                    <h3 class="text-sm font-black text-slate-900">Connections</h3>
                    <span id="profile-connections-count" class="text-xs font-bold text-[#0A66C2]">0</span>
                </div>
                <div id="connections-list-profile" class="flex flex-col mt-4 gap-3">
                    <!-- Connections -->
                </div>
                <button id="show-all-connections" class="w-full mt-5 py-2 text-slate-600 hover:text-slate-900 text-sm font-bold hover:bg-slate-50 rounded-lg border border-slate-200 transition-colors shadow-sm">Show all connections</button>
            </section>
        </aside>
    </div>
</div>

<!-- Modals -->
<!-- Contact Info Modal -->
<div id="contact-info-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4 transition-opacity duration-200 opacity-0">
    <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl scale-95 transition-transform duration-200">
        <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-xl font-black text-slate-900">Contact info</h3>
            <button id="close-contact-info" class="w-9 h-9 hover:bg-slate-100 rounded-full flex items-center justify-center text-slate-500 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div id="contact-info-content" class="p-6 space-y-4"></div>
    </div>
</div>

<!-- Add Profile Section Modal -->
<div id="section-picker-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4 transition-opacity duration-200 opacity-0">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl scale-95 transition-transform duration-200">
        <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="text-xl font-black text-slate-900">Add profile section</h3>
            <button id="close-section-picker" class="w-9 h-9 hover:bg-slate-100 rounded-full flex items-center justify-center text-slate-500 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="p-3">
            <button data-section-action="intro" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-left">
                <span class="material-symbols-outlined text-[#0A66C2]">badge</span>
                <span><span class="block text-sm font-bold text-slate-900">Intro and about</span><span class="block text-xs text-slate-500">Name, headline, location, bio, phone, and website</span></span>
            </button>
            <button data-section-action="experience" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-left">
                <span class="material-symbols-outlined text-[#0A66C2]">work</span>
                <span><span class="block text-sm font-bold text-slate-900">Experience</span><span class="block text-xs text-slate-500">Add a role with company and dates</span></span>
            </button>
            <button data-section-action="education" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-left">
                <span class="material-symbols-outlined text-[#0A66C2]">school</span>
                <span><span class="block text-sm font-bold text-slate-900">Education</span><span class="block text-xs text-slate-500">Add a school, degree, and years</span></span>
            </button>
        </div>
    </div>
</div>

<!-- Fullscreen Image Viewer Modal -->
<div id="image-viewer-modal" class="fixed inset-0 bg-black/95 z-[9999] hidden items-center justify-center transition-opacity duration-300 opacity-0">
    <button onclick="closeImageViewer()" class="absolute top-6 right-6 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
        <span class="material-symbols-outlined text-3xl">close</span>
    </button>
    <img id="fullscreen-img" src="" class="max-w-[90vw] max-h-[90vh] object-contain rounded-lg shadow-2xl scale-95 transition-transform duration-300">
</div>

<!-- Edit Profile Modal -->
<div id="profile-edit-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4 transition-opacity duration-200 opacity-0">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl scale-95 transition-transform duration-200" id="profile-edit-content">
        <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-xl font-black text-slate-900">Edit intro</h3>
            <button id="close-profile-edit" class="w-9 h-9 hover:bg-slate-100 rounded-full flex items-center justify-center text-slate-500 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="p-6 space-y-5">
            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Full name*</label>
                <input type="text" id="edit-full-name" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all">
            </div>
            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Headline*</label>
                <input type="text" id="edit-headline" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all">
            </div>
            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Location*</label>
                <input type="text" id="edit-location" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all">
            </div>
            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Industry</label>
                <input type="text" id="edit-industry" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-sm font-bold text-slate-700">Phone</label>
                    <input type="tel" id="edit-phone" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-bold text-slate-700">Website</label>
                    <input type="url" id="edit-website" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all">
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">About (Bio)</label>
                <textarea id="edit-bio" rows="5" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all custom-scrollbar"></textarea>
            </div>
            <div id="profile-edit-error" class="hidden text-red-600 text-sm font-bold bg-red-50 p-3 rounded-lg border border-red-100">Please fill all required fields.</div>
        </div>
        <div class="flex justify-end gap-3 p-5 border-t border-slate-100 sticky bottom-0 bg-white">
            <button id="save-profile-edit" class="px-6 py-2 bg-[#0A66C2] text-white font-bold text-sm rounded-full shadow-sm hover:shadow-md hover:bg-[#004182] transition-all flex items-center gap-2">
                Save
            </button>
        </div>
    </div>
</div>

<!-- Add Experience Modal -->
<div id="experience-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4 transition-opacity duration-200 opacity-0">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl scale-95 transition-transform duration-200" id="experience-content">
        <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 id="experience-modal-title" class="text-xl font-black text-slate-900">Add experience</h3>
            <button id="close-experience" class="w-9 h-9 hover:bg-slate-100 rounded-full flex items-center justify-center text-slate-500 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="p-6 space-y-5">
            <input type="hidden" id="exp-id">
            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Title*</label>
                <input type="text" id="exp-title" placeholder="Ex: Senior Software Engineer" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all">
            </div>
            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Company name*</label>
                <input type="text" id="exp-company" placeholder="Ex: Google" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-sm font-bold text-slate-700">Start date*</label>
                    <input type="date" id="exp-start" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all text-slate-600">
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-bold text-slate-700">End date</label>
                    <input type="date" id="exp-end" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all text-slate-600">
                </div>
            </div>
            <div class="flex items-center gap-2.5 bg-slate-50 p-3 rounded-lg border border-slate-200">
                <input type="checkbox" id="exp-current" class="w-4 h-4 text-[#0A66C2] rounded border-slate-300 focus:ring-[#0A66C2]">
                <label for="exp-current" class="text-sm font-bold text-slate-700 cursor-pointer">I am currently working in this role</label>
            </div>
            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Description</label>
                <textarea id="exp-description" rows="4" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all custom-scrollbar"></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-3 p-5 border-t border-slate-100 sticky bottom-0 bg-white">
            <button id="delete-experience" class="hidden mr-auto px-5 py-2 border border-red-200 text-red-600 font-bold text-sm rounded-full hover:bg-red-50 transition-all">
                Delete
            </button>
            <button id="save-experience" class="px-6 py-2 bg-[#0A66C2] text-white font-bold text-sm rounded-full shadow-sm hover:shadow-md hover:bg-[#004182] transition-all flex items-center gap-2">
                Save Experience
            </button>
        </div>
    </div>
</div>

<!-- Add Education Modal -->
<div id="education-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4 transition-opacity duration-200 opacity-0">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl scale-95 transition-transform duration-200" id="education-content">
        <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 id="education-modal-title" class="text-xl font-black text-slate-900">Add education</h3>
            <button id="close-education" class="w-9 h-9 hover:bg-slate-100 rounded-full flex items-center justify-center text-slate-500 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="p-6 space-y-5">
            <input type="hidden" id="edu-id">
            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">School/University*</label>
                <input type="text" id="edu-school" placeholder="Ex: MIT" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all">
            </div>
            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Degree</label>
                <input type="text" id="edu-degree" placeholder="Ex: Bachelor of Science" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all">
            </div>
            <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Field of study</label>
                <input type="text" id="edu-field" placeholder="Ex: Computer Science" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-sm font-bold text-slate-700">Start Year</label>
                    <input type="number" id="edu-start" placeholder="YYYY" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all text-slate-600">
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-bold text-slate-700">End Year (or expected)</label>
                    <input type="number" id="edu-end" placeholder="YYYY" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#0A66C2] outline-none transition-all text-slate-600">
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3 p-5 border-t border-slate-100 sticky bottom-0 bg-white">
            <button id="delete-education" class="hidden mr-auto px-5 py-2 border border-red-200 text-red-600 font-bold text-sm rounded-full hover:bg-red-50 transition-all">
                Delete
            </button>
            <button id="save-education" class="px-6 py-2 bg-[#0A66C2] text-white font-bold text-sm rounded-full shadow-sm hover:shadow-md hover:bg-[#004182] transition-all flex items-center gap-2">
                Save Education
            </button>
        </div>
    </div>
</div>

<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
