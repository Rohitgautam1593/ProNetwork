<?php require APPROOT . '/views/layouts/header.php'; ?>
<?php require APPROOT . '/views/layouts/navbar.php'; ?>
<!-- View Content -->
<main class="pt-16 pb-12">
    <div class="max-w-[1128px] mx-auto grid grid-cols-1 md:grid-cols-12 gap-6 px-4">
        <!-- Left Sidebar Navigation -->
        <aside class="md:col-span-3">
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden sticky top-20">
                <div class="flex flex-col">
                    <a class="flex items-center gap-3 p-4 hover:bg-slate-50 transition-colors" href="#">
                        <span class="material-symbols-outlined text-slate-600">bookmark</span>
                        <span class="font-title-md text-title-md">My Jobs</span>
                    </a>
                    <a class="flex items-center gap-3 p-4 hover:bg-slate-50 transition-colors" href="#">
                        <span class="material-symbols-outlined text-slate-600">notifications</span>
                        <span class="font-title-md text-title-md">Job Alerts</span>
                    </a>
                    <a class="flex items-center gap-3 p-4 hover:bg-slate-50 transition-colors" href="#">
                        <span class="material-symbols-outlined text-slate-600">payments</span>
                        <span class="font-title-md text-title-md">Salary</span>
                    </a>
                    <a class="flex items-center gap-3 p-4 hover:bg-slate-50 transition-colors" href="#">
                        <span class="material-symbols-outlined text-slate-600">assignment</span>
                        <span class="font-title-md text-title-md">Skill Assessments</span>
                    </a>
                </div>
                <div class="border-t border-slate-100 p-4">
                    <button class="w-full flex items-center justify-center gap-2 border border-[#0A66C2] text-[#0A66C2] font-semibold py-2 rounded-full hover:bg-blue-50 transition-all">
                        <span class="material-symbols-outlined text-lg">edit_square</span>
                        Post a free job
                    </button>
                </div>
            </div>
        </aside>

        <!-- Center Content: Searchable Job List -->
        <section class="md:col-span-5 flex flex-col gap-4">
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-4">
                <h1 class="text-xl font-bold mb-4">Recommended for you</h1>
                <div id="jobs-container" class="flex flex-col gap-1">
                    <div class="flex justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#0A66C2]"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Right Sidebar: Job Detail Preview -->
        <aside class="hidden lg:block lg:col-span-4">
            <!-- Dynamic Job Detail will be loaded here -->
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-8 text-center text-slate-500 sticky top-20">
                Select a job to view details
            </div>
        </aside>
    </div>
</main>
<?php require APPROOT . '/views/layouts/footer.php'; ?>
