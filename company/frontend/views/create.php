<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>

<main class="bg-surface-container-low min-h-screen pt-4 pb-12">
    <div class="max-w-[1128px] mx-auto px-4 grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <div class="lg:col-span-8">
            <div class="bg-white rounded-xl border border-outline-variant/30 ambient-shadow overflow-hidden">
                <div class="p-6 border-b border-outline-variant/20">
                    <h1 class="font-display-md text-2xl text-on-surface">Let's create your Company Page</h1>
                    <p class="font-body-md text-on-surface-variant mt-1">Connect with clients, employees, and the ProNetwork community.</p>
                </div>
                
                <form action="<?php echo URLROOT; ?>/company/create" method="POST" class="p-6 space-y-6">
                    
                    <div>
                        <label class="block font-label-lg text-on-surface mb-2" for="name">Company Name <span class="text-error">*</span></label>
                        <input type="text" id="name" name="name" required placeholder="Add your organization's name" 
                               class="w-full h-10 px-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm font-body-md bg-white">
                        <p class="text-xs text-secondary mt-1">This is how your organization will appear on ProNetwork.</p>
                    </div>

                    <div>
                        <label class="block font-label-lg text-on-surface mb-2" for="website">Website <span class="font-body-md text-secondary font-normal">(optional)</span></label>
                        <input type="url" id="website" name="website" placeholder="Begin with http://, https:// or www." 
                               class="w-full h-10 px-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm font-body-md bg-white">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-label-lg text-on-surface mb-2" for="industry">Industry <span class="text-error">*</span></label>
                            <select id="industry" name="industry" required class="w-full h-10 px-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm font-body-md bg-white">
                                <option value="" disabled selected>Select an industry</option>
                                <option value="Information Technology">Information Technology</option>
                                <option value="Financial Services">Financial Services</option>
                                <option value="Healthcare">Healthcare</option>
                                <option value="Education">Education</option>
                                <option value="Marketing & Advertising">Marketing & Advertising</option>
                                <option value="Design & Architecture">Design & Architecture</option>
                                <option value="Retail & E-commerce">Retail & E-commerce</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-label-lg text-on-surface mb-2" for="size">Company Size <span class="text-error">*</span></label>
                            <select id="size" name="size" required class="w-full h-10 px-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm font-body-md bg-white">
                                <option value="" disabled selected>Select company size</option>
                                <option value="1-10 employees">1-10 employees</option>
                                <option value="11-50 employees">11-50 employees</option>
                                <option value="51-200 employees">51-200 employees</option>
                                <option value="201-500 employees">201-500 employees</option>
                                <option value="501-1000 employees">501-1000 employees</option>
                                <option value="1001+ employees">1001+ employees</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-label-lg text-on-surface mb-2" for="description">Tagline / Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Briefly describe what your organization does" 
                                  class="w-full p-3 rounded-md border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-sm font-body-md bg-white resize-none"></textarea>
                    </div>

                    <div class="border-t border-outline-variant/20 pt-6">
                        <label class="flex items-start gap-3">
                            <input type="checkbox" required class="mt-1 border-outline-variant text-primary focus:ring-primary rounded">
                            <span class="font-body-md text-sm text-secondary">
                                I verify that I am an authorized representative of this organization and have the right to act on its behalf in the creation and management of this page. The organization and I agree to the additional terms for Pages.
                            </span>
                        </label>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="bg-primary text-white font-label-lg px-8 py-2.5 rounded-full hover:bg-[#004182] transition-colors shadow-sm">
                            Create page
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-4">
            <!-- Page Preview Panel -->
            <div class="bg-white rounded-xl border border-outline-variant/30 ambient-shadow overflow-hidden sticky top-20">
                <div class="bg-surface-container p-4 border-b border-outline-variant/20">
                    <h3 class="font-label-lg text-on-surface">Page Preview</h3>
                </div>
                <div class="p-6">
                    <div class="border border-outline-variant/30 rounded-lg overflow-hidden shadow-sm">
                        <div class="h-16 bg-surface-container"></div>
                        <div class="px-4 pb-4">
                            <div class="w-12 h-12 bg-white rounded border border-outline-variant/30 -mt-6 flex items-center justify-center overflow-hidden">
                                <span class="material-symbols-outlined text-outline-variant text-2xl">domain</span>
                            </div>
                            <h4 id="preview-name" class="font-title-md text-on-surface mt-2 truncate">Company Name</h4>
                            <p id="preview-tagline" class="font-body-md text-sm text-on-surface-variant truncate mt-0.5">Tagline</p>
                            <p class="font-body-md text-xs text-secondary mt-1">1 follower</p>
                            <button class="mt-3 w-full bg-primary-fixed text-primary font-label-md py-1 rounded-full border border-primary-container pointer-events-none">+ Follow</button>
                        </div>
                    </div>
                    <p class="text-center font-body-md text-xs text-secondary mt-4">
                        This is a preview of how your page will look to other members.
                    </p>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const previewName = document.getElementById('preview-name');
        
        const descInput = document.getElementById('description');
        const previewTagline = document.getElementById('preview-tagline');

        nameInput.addEventListener('input', function() {
            previewName.textContent = this.value || 'Company Name';
        });

        descInput.addEventListener('input', function() {
            previewTagline.textContent = this.value || 'Tagline';
        });
    });
</script>

<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
