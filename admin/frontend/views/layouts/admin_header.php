<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo SITENAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-manrope { font-family: 'Manrope', sans-serif; }
    </style>
    <!-- Shared values used by admin JavaScript. -->
    <script>
        const URLROOT = '<?php echo URLROOT; ?>';
        const CURRENT_USER_ID = <?php echo isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0; ?>;
    </script>
</head>
<body class="admin-body">
<div id="admin-sidebar-overlay" class="admin-sidebar-overlay fixed inset-0 z-[80] bg-slate-950/45 backdrop-blur-sm opacity-0 pointer-events-none lg:hidden transition-opacity"></div>
<div class="admin-shell flex h-screen w-full overflow-hidden">
