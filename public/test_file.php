<?php
header('Content-Type: text/plain');
echo "cwd: " . getcwd() . "\n";
echo "assets/js/forms.js exists: " . (file_exists('assets/js/forms.js') ? 'YES' : 'NO') . "\n";
echo "assets/js/common.js exists: " . (file_exists('assets/js/common.js') ? 'YES' : 'NO') . "\n";
echo "index.php exists: " . (file_exists('index.php') ? 'YES' : 'NO') . "\n";
// Let's list files in assets/js
if (is_dir('assets/js')) {
    echo "Files in assets/js:\n";
    print_r(scandir('assets/js'));
} else {
    echo "assets/js is not a directory!\n";
}
// Let's check files in assets
if (is_dir('assets')) {
    echo "Files in assets:\n";
    print_r(scandir('assets'));
} else {
    echo "assets is not a directory!\n";
}
