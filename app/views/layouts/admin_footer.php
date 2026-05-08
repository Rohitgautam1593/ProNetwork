    </div> <!-- Close flex-1 overflow-y-auto -->
</div> <!-- Close flex-1 flex flex-col -->
</div> <!-- Close flex min-h-screen -->

<!-- Global Toast -->
<div id="toast-container" class="fixed bottom-6 right-6 z-[200]"></div>

<script src="<?php echo URLROOT; ?>/assets/js/forms.js"></script>
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
</script>
</body>
</html>
