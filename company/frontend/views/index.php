<?php require USERROOT . '/frontend/views/layouts/header.php'; ?>
<?php require USERROOT . '/frontend/views/layouts/navbar.php'; ?>

<main class="bg-[#f3f2ef] min-h-screen pt-6 pb-12 font-['Manrope']">
    <div class="max-w-[1128px] mx-auto px-4">
        
        <!-- Premium Hero Section -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8 relative">
            <!-- Dynamic Gradient Background -->
            <div class="h-40 md:h-48 relative overflow-hidden bg-slate-900">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-900 via-indigo-800 to-[#0A66C2] opacity-90"></div>
                <!-- Abstract floating shapes for texture -->
                <div class="absolute -top-24 -left-24 w-64 h-64 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-pulse"></div>
                <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-pulse" style="animation-delay: 2s;"></div>
                
                <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4">
                    <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight mb-3 drop-shadow-sm">
                        Discover Top Workplaces
                    </h1>
                    <p class="text-blue-100 font-medium text-lg max-w-2xl text-shadow-sm">
                        Explore industry leaders, find your next career opportunity, and build your professional network.
                    </p>
                </div>
            </div>
            
            <div class="p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 relative bg-white">
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-slate-800">Is your company on ProNetwork?</h2>
                    <p class="text-slate-500 text-sm mt-1">Create an official page to attract talent and showcase your brand.</p>
                </div>
                <div class="shrink-0 flex gap-3 w-full md:w-auto">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo URLROOT; ?>/company/create" class="w-full md:w-auto text-center bg-gradient-to-r from-[#0A66C2] to-indigo-600 text-white font-bold px-8 py-3 rounded-full hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">add_business</span>
                            Create Workspace
                        </a>
                    <?php else: ?>
                        <a href="<?php echo URLROOT; ?>/auth/login" class="w-full md:w-auto text-center bg-gradient-to-r from-[#0A66C2] to-indigo-600 text-white font-bold px-8 py-3 rounded-full hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                            Sign in to Create Page
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
            <!-- Main Content: Companies Grid -->
            <div class="xl:col-span-8 space-y-6">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-2xl font-bold text-slate-800">Explore Companies</h2>
                    <span class="text-sm font-semibold text-slate-500 bg-slate-200 px-3 py-1 rounded-full">
                        <?php echo count($data['companies']); ?> available
                    </span>
                </div>
                
                <?php if (!empty($data['companies'])): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($data['companies'] as $comp): ?>
                            <?php 
                                $logo = pn_company_logo_url($comp);
                                $banner = pn_company_banner_url($comp);
                            ?>
                            <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden group flex flex-col h-full">
                                <!-- Company Banner -->
                                <div class="h-24 bg-slate-100 relative overflow-hidden cursor-pointer" onclick="window.location.href='<?php echo URLROOT; ?>/company/show/<?php echo $comp['company_id']; ?>'">
                                    <img src="<?php echo htmlspecialchars($banner); ?>" alt="" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                </div>
                                
                                <!-- Card Content -->
                                <div class="px-5 pb-5 flex-1 flex flex-col relative">
                                    <!-- Overlapping Logo -->
                                    <a href="<?php echo URLROOT; ?>/company/show/<?php echo $comp['company_id']; ?>" class="block -mt-10 mb-3 relative z-10 w-max">
                                        <div class="w-20 h-20 rounded-xl overflow-hidden bg-white border-4 border-white shadow-md flex items-center justify-center">
                                            <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($comp['company_name']); ?>" class="w-full h-full object-cover">
                                        </div>
                                    </a>
                                    
                                    <div class="flex-1">
                                        <a href="<?php echo URLROOT; ?>/company/show/<?php echo $comp['company_id']; ?>" class="block group-hover:text-[#0A66C2] transition-colors">
                                            <h3 class="text-lg font-bold text-slate-900 truncate"><?php echo htmlspecialchars($comp['company_name']); ?></h3>
                                        </a>
                                        <p class="text-sm font-medium text-[#0A66C2] truncate mt-0.5"><?php echo htmlspecialchars($comp['industry'] ?? 'Company'); ?></p>
                                        
                                        <p class="text-xs text-slate-500 font-semibold mt-2 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[16px] text-slate-400">group</span>
                                            <?php echo number_format($comp['followers']); ?> followers
                                        </p>
                                        
                                        <p class="text-sm text-slate-600 mt-3 line-clamp-2 leading-relaxed">
                                            <?php echo htmlspecialchars($comp['description'] ?? ''); ?>
                                        </p>
                                    </div>
                                    
                                    <!-- Footer Action -->
                                    <div class="pt-5 mt-auto border-t border-slate-100">
                                        <a href="<?php echo URLROOT; ?>/company/show/<?php echo $comp['company_id']; ?>" class="block w-full text-center text-[#0A66C2] font-bold py-2 rounded-lg border border-[#0A66C2] hover:bg-blue-50 hover:shadow-sm transition-all">
                                            View Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center shadow-sm">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-4xl text-slate-400">corporate_fare</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800">No companies found</h3>
                        <p class="text-slate-500 mt-2">The directory is currently empty. Be the first to create an employer workspace.</p>
                        <?php if (isLoggedIn()): ?>
                            <a href="<?php echo URLROOT; ?>/company/create" class="inline-block mt-6 bg-[#0A66C2] text-white font-bold px-6 py-2 rounded-full hover:bg-[#004182] transition-colors">Create Page</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Sidebar -->
            <div class="xl:col-span-4 space-y-6">
                <!-- Trending Pages Widget -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sticky top-20">
                    <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-orange-500">trending_up</span>
                        Trending Pages
                    </h2>
                    
                    <?php if (!empty($data['companies'])): ?>
                        <div class="space-y-4">
                            <?php 
                            // Sort by followers for "trending" effect, just a visual enhancement
                            $trending = $data['companies'];
                            usort($trending, function($a, $b) {
                                return $b['followers'] <=> $a['followers'];
                            });
                            $trending = array_slice($trending, 0, 5);
                            
                            foreach ($trending as $comp):
                                $logo = pn_company_logo_url($comp);
                            ?>
                                <a href="<?php echo URLROOT; ?>/company/show/<?php echo $comp['company_id']; ?>" class="flex gap-3 items-center group p-2 -mx-2 rounded-lg hover:bg-slate-50 transition-colors">
                                    <div class="w-12 h-12 rounded-lg overflow-hidden border border-slate-200 bg-white shrink-0 shadow-sm">
                                        <img src="<?php echo htmlspecialchars($logo); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-bold text-slate-800 text-sm truncate group-hover:text-[#0A66C2] transition-colors"><?php echo htmlspecialchars($comp['company_name']); ?></h3>
                                        <span class="text-xs font-semibold text-slate-500 block truncate mt-0.5"><?php echo number_format($comp['followers']); ?> followers</span>
                                    </div>
                                    <div class="shrink-0 text-slate-400 group-hover:text-[#0A66C2]">
                                        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-slate-500 text-center py-4">No trending data available.</p>
                    <?php endif; ?>
                </div>

                <!-- Ad/Promo Widget -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl border border-slate-700 shadow-lg p-6 text-center text-white relative overflow-hidden group cursor-pointer" onclick="window.location.href='<?php echo URLROOT; ?>/user/jobs'">
                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-5 transition-opacity"></div>
                    <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/20">
                        <span class="material-symbols-outlined text-3xl text-blue-300">work</span>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Accelerate your career</h3>
                    <p class="text-slate-300 text-sm mb-6 leading-relaxed">Discover companies hiring now and stay updated with industry trends.</p>
                    <span class="inline-block bg-blue-500 text-white font-bold px-6 py-2 rounded-full shadow-md group-hover:bg-blue-400 transition-colors w-full text-sm">
                        Explore Jobs
                    </span>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require USERROOT . '/frontend/views/layouts/footer.php'; ?>
