<?php require APPROOT . '/views/layouts/header.php'; ?>
<?php require APPROOT . '/views/layouts/navbar.php'; ?>
<!-- View Content -->
<main class="pt-16 pb-12">
    <div class="max-w-[1128px] mx-auto grid grid-cols-1 md:grid-cols-12 gap-6 px-4">
        <!-- Left Column: Primary Content -->
        <div class="md:col-span-8 flex flex-col gap-3">
            <!-- Profile Header Card -->
            <section class="bg-white rounded-lg overflow-hidden border border-gray-200 shadow-[0px_4px_12px_rgba(0,0,0,0.05)]">
                <div class="h-48 relative bg-primary-container group/banner">
                    <img id="profile-banner-img" alt="Cover photo" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBIW8R5FVBgDPzRX8HrkFwKoNp5BnCe9RL7hA1WAlwtptmFRXeh-bvkFbzxlv-sdE0D44lZUFjEmP1zaeKgsdEGDiybVwUltAKo2RYVkHaGj237brNgHvlWA9n3_jLmKJFTA93wRf-6B63wHIiOowC0FJUyB2HSh0-ykZNRaV5OuplP5Gv57-Jc8PUBzrOivCO1hLlvEw82IjNVbZXrHikdCOeuhtXDkSZ0X8k225jVV-7O9MDc8oCqQzHoYKChD1K36t3qTuzsv_8h"/>
                    <div id="trigger-banner-upload" class="absolute top-4 right-4 bg-black/40 hover:bg-black/60 text-white p-2 rounded-full cursor-pointer hidden group-hover/banner:flex items-center justify-center transition-all">
                        <span class="material-symbols-outlined text-[20px]">add_a_photo</span>
                    </div>
                    <input type="file" id="banner-upload-input" class="hidden" accept="image/*">
                    <div class="absolute -bottom-16 left-6 ring-4 ring-white rounded-full bg-white group/pic cursor-pointer">
                        <img data-user-pic="true" alt="Profile picture" class="w-40 h-40 rounded-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAK6BovwzEq7r9exnmOKhCW56RrHK2v36V_ytBdRxHvuWpgxSp5Sob-LSKSprvDdK1tqz5hnHyQeXqz2061VF46r_FUrbf1NUXbZEvIPjCKRrSy9c5uoDRsJ153B-wLMVDCjv4DL_g4JuxmdwV6oCr3ziI4Ef5VifZHmvty3eF02L_LMvZcmcSO7Tsr3O2JI82NAyd6NYIzjdmpbY9wieVJ6N0_hNUnjr21gl9zZuTQuVQXqDb-TwDzjypem8FcmlK5NMHxD6lXtiX6"/>
                        <div id="trigger-pic-upload" class="absolute inset-0 bg-black/30 rounded-full hidden group-hover/pic:flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-3xl">add_a_photo</span>
                        </div>
                        <input type="file" id="profile-pic-input" class="hidden" accept="image/*">
                    </div>
                </div>
                <div class="pt-20 px-6 pb-6">
                    <div class="flex justify-between items-start">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <h1 data-user-name="full" class="font-display-md text-display-md text-on-surface text-2xl font-bold">Alex Sterling</h1>
                                <span id="open-profile-edit" class="material-symbols-outlined text-on-surface-variant cursor-pointer hover:bg-gray-100 p-1 rounded-full transition-colors text-[20px]" data-icon="edit">edit</span>
                            </div>
                            <p data-user-headline class="font-body-lg text-body-lg text-on-surface-variant mt-1">Senior Product Designer | UX Strategist | Tech Speaker</p>
                            <p class="font-caption text-caption text-secondary mt-2 flex items-center gap-1">
                                <span data-user-location>San Francisco, CA</span> • <span class="text-primary-container font-semibold cursor-pointer text-[#0A66C2]">Contact info</span>
                            </p>
                            <p class="font-label-md text-label-md text-[#0A66C2] mt-2 font-semibold">500+ connections</p>
                        </div>
                        <div class="hidden md:flex flex-col gap-2 text-right">
                            <div class="flex items-center gap-2 cursor-pointer">
                                <span class="material-symbols-outlined text-on-surface-variant" data-icon="corporate_fare">corporate_fare</span>
                                <span class="font-label-md text-label-md text-on-surface font-semibold hover:text-[#0A66C2]">Quantum Systems Inc.</span>
                            </div>
                            <div class="flex items-center gap-2 cursor-pointer">
                                <span class="material-symbols-outlined text-on-surface-variant" data-icon="school">school</span>
                                <span class="font-label-md text-label-md text-on-surface font-semibold hover:text-[#0A66C2]">Stanford University</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <button class="bg-[#0A66C2] text-white px-4 py-1.5 rounded-full font-label-lg text-label-lg hover:bg-[#004182] transition-colors">Open to work</button>
                        <button class="border border-[#0A66C2] text-[#0A66C2] px-4 py-1.5 rounded-full font-label-lg text-label-lg hover:bg-blue-50 transition-colors">Add profile section</button>
                        <button class="border border-outline text-outline px-4 py-1.5 rounded-full font-label-lg text-label-lg hover:bg-gray-50 transition-colors">More</button>
                    </div>
                </div>
            </section>

            <!-- About Section -->
            <section class="bg-white rounded-lg p-6 border border-gray-200 shadow-[0px_4px_12px_rgba(0,0,0,0.05)]">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-title-lg text-xl font-bold text-on-surface">About</h2>
                    <span class="material-symbols-outlined text-on-surface-variant cursor-pointer" data-icon="edit">edit</span>
                </div>
                <p data-user-bio class="font-body-md text-body-md text-on-surface-variant leading-relaxed text-gray-600">
                    Passionate Product Designer with over 10 years of experience in creating intuitive digital experiences that bridge the gap between user needs and business objectives. I specialize in design systems, high-fidelity prototyping, and cross-functional leadership in agile environments. 
                    <br/><br/>
                    Currently leading the core design team at Quantum Systems, where we're redefining how enterprise data is visualized. I'm a firm believer in design thinking and mentoring the next generation of creative talent.
                </p>
            </section>

            <!-- Experience Section -->
            <section class="bg-white rounded-lg p-6 border border-gray-200 shadow-[0px_4px_12px_rgba(0,0,0,0.05)]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="font-title-lg text-xl font-bold text-on-surface">Experience</h2>
                    <div class="flex gap-4">
                        <span class="material-symbols-outlined text-on-surface-variant cursor-pointer" data-icon="add">add</span>
                        <span class="material-symbols-outlined text-on-surface-variant cursor-pointer" data-icon="edit">edit</span>
                    </div>
                </div>
                <div id="experience-container" class="flex flex-col gap-8">
                    <!-- Job 1 -->
                    <div class="flex gap-4">
                        <div class="w-12 h-12 flex-shrink-0 bg-gray-100 rounded p-2">
                            <span class="material-symbols-outlined text-4xl text-gray-600" data-icon="rocket_launch">rocket_launch</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <h3 class="font-title-md text-lg font-semibold text-on-surface">Senior Product Designer</h3>
                            <p class="font-body-md text-on-surface">Quantum Systems Inc. • Full-time</p>
                            <p class="text-xs text-gray-500">Jan 2021 - Present • 3 yrs 4 mos</p>
                            <p class="text-xs text-gray-500">San Francisco, California</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Education Section -->
            <section class="bg-white rounded-lg p-6 border border-gray-200 shadow-[0px_4px_12px_rgba(0,0,0,0.05)]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="font-title-lg text-xl font-bold text-on-surface">Education</h2>
                    <div class="flex gap-4">
                        <span class="material-symbols-outlined text-on-surface-variant cursor-pointer" data-icon="add">add</span>
                        <span class="material-symbols-outlined text-on-surface-variant cursor-pointer" data-icon="edit">edit</span>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-12 h-12 flex-shrink-0 bg-gray-100 rounded p-2">
                        <span class="material-symbols-outlined text-4xl text-gray-600" data-icon="school">school</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <h3 class="font-title-md text-lg font-semibold text-on-surface">Stanford University</h3>
                        <p class="font-body-md text-on-surface">Master of Science - MS, Human Computer Interaction</p>
                        <p class="text-xs text-gray-500">2014 - 2016</p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Right Column: Sidebar -->
        <aside class="md:col-span-4 flex flex-col gap-3">
            <!-- Profile Completion Strength -->
            <section class="bg-white rounded-lg p-4 border border-gray-200 shadow-[0px_4px_12px_rgba(0,0,0,0.05)]">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-on-surface">Profile Strength</h3>
                    <span class="text-sm text-gray-500">Expert</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                    <div class="bg-[#0A66C2] h-2 rounded-full w-[90%]"></div>
                </div>
                <p class="text-xs text-gray-500 mt-4">
                    Your profile is among the top 5% in your network. Keep it updated to stay visible to recruiters.
                </p>
            </section>
            <!-- Contact Info Card -->
            <section class="bg-white rounded-lg p-4 border border-gray-200 shadow-[0px_4px_12px_rgba(0,0,0,0.05)]">
                <h3 class="font-bold text-on-surface mb-4">Contact Information</h3>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-gray-500" data-icon="mail">mail</span>
                        <div>
                            <p class="text-xs font-bold text-on-surface">Email</p>
                            <p data-user-email class="text-sm text-[#0A66C2] font-semibold">alex.sterling@design.com</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Connections Section -->
            <section class="bg-white rounded-lg p-4 border border-gray-200 shadow-[0px_4px_12px_rgba(0,0,0,0.05)]">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-on-surface">Connections</h3>
                    <span id="profile-connections-count" class="text-xs text-gray-500">0 connections</span>
                </div>
                <div id="connections-list-profile" class="flex flex-col">
                    <!-- Connections will be loaded here -->
                </div>
                <button class="w-full mt-4 py-1 text-slate-500 text-xs font-semibold hover:bg-slate-50 rounded border border-slate-200 transition-colors">Show all connections</button>
            </section>
        </aside>
    </div>
    </div>

    <!-- Modals Section -->

    <!-- Edit Profile Modal -->
    <div id="profile-edit-modal" class="fixed inset-0 bg-black/50 z-[100] hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="flex items-center justify-between p-4 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900">Edit intro</h3>
                <button id="close-profile-edit" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Full name*</label>
                    <input type="text" id="edit-full-name" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Headline*</label>
                    <input type="text" id="edit-headline" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Location*</label>
                    <input type="text" id="edit-location" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">About (Bio)*</label>
                    <textarea id="edit-bio" rows="4" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all"></textarea>
                </div>
                <div id="profile-edit-error" class="hidden text-red-600 text-sm font-medium">Please fill all required fields.</div>
            </div>
            <div class="flex justify-end gap-3 p-4 border-t border-gray-100">
                <button id="cancel-profile-edit" class="px-5 py-2 text-gray-600 font-semibold hover:bg-gray-100 rounded-full transition-colors">Cancel</button>
                <button id="save-profile-edit" class="px-5 py-2 bg-[#0A66C2] text-white font-semibold hover:bg-[#004182] rounded-full transition-colors">Save</button>
            </div>
        </div>
    </div>

    <!-- Add Experience Modal -->
    <div id="experience-modal" class="fixed inset-0 bg-black/50 z-[100] hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="flex items-center justify-between p-4 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900">Add experience</h3>
                <button id="close-experience" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Title*</label>
                    <input type="text" id="exp-title" placeholder="Ex: Manager" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Company name*</label>
                    <input type="text" id="exp-company" placeholder="Ex: Microsoft" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700">Start date*</label>
                        <input type="date" id="exp-start" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700">End date</label>
                        <input type="date" id="exp-end" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="exp-current" class="w-4 h-4 text-blue-600">
                    <label for="exp-current" class="text-sm text-gray-600">I am currently working in this role</label>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Description</label>
                    <textarea id="exp-description" rows="4" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 p-4 border-t border-gray-100">
                <button id="save-experience" class="px-5 py-2 bg-[#0A66C2] text-white font-semibold hover:bg-[#004182] rounded-full transition-colors">Save</button>
            </div>
        </div>
    </div>

    <!-- Add Education Modal -->
    <div id="education-modal" class="fixed inset-0 bg-black/50 z-[100] hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="flex items-center justify-between p-4 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900">Add education</h3>
                <button id="close-education" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">School/University*</label>
                    <input type="text" id="edu-school" placeholder="Ex: Stanford University" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Degree</label>
                    <input type="text" id="edu-degree" placeholder="Ex: Bachelor's" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Field of study</label>
                    <input type="text" id="edu-field" placeholder="Ex: Business" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700">Start Year</label>
                        <input type="number" id="edu-start" placeholder="YYYY" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700">End Year (or expected)</label>
                        <input type="number" id="edu-end" placeholder="YYYY" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 p-4 border-t border-gray-100">
                <button id="save-education" class="px-5 py-2 bg-[#0A66C2] text-white font-semibold hover:bg-[#004182] rounded-full transition-colors">Save</button>
            </div>
        </div>
    </div>

</main>
<?php require APPROOT . '/views/layouts/footer.php'; ?>

