<?php /* Company module header */ ?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title><?php echo SITENAME; ?></title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/style.css?v=<?php echo time(); ?>">
  <script src="<?php echo URLROOT; ?>/assets/js/common.js?v=<?php echo time(); ?>"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          "colors": {
            "on-primary-fixed-variant": "#00468a",
            "secondary": "#5d5f5f",
            "primary-fixed": "#d6e3ff",
            "tertiary-fixed-dim": "#ffb68e",
            "surface-container-lowest": "#ffffff",
            "tertiary-container": "#a94b00",
            "on-tertiary-fixed": "#331200",
            "surface-container": "#ecedf6",
            "surface-container-high": "#e6e8f0",
            "on-surface": "#191c21",
            "surface-bright": "#f9f9ff",
            "primary-container": "#0a66c2",
            "on-primary": "#ffffff",
            "on-error-container": "#93000a",
            "surface-variant": "#e1e2ea",
            "secondary-fixed": "#e2e2e2",
            "surface-dim": "#d8dae2",
            "primary-fixed-dim": "#a8c8ff",
            "on-secondary-fixed": "#1a1c1c",
            "on-secondary-container": "#616363",
            "on-tertiary": "#ffffff",
            "surface-container-low": "#f2f3fb",
            "error": "#ba1a1a",
            "inverse-on-surface": "#eff0f9",
            "tertiary": "#833900",
            "on-primary-fixed": "#001b3d",
            "error-container": "#ffdad6",
            "surface-tint": "#005eb5",
            "secondary-container": "#dfe0e0",
            "outline": "#727783",
            "primary": "#004e99",
            "surface": "#f9f9ff",
            "on-tertiary-fixed-variant": "#773300",
            "on-tertiary-container": "#ffe0d1",
            "inverse-primary": "#a8c8ff",
            "outline-variant": "#c1c6d4",
            "tertiary-fixed": "#ffdbca",
            "on-secondary-fixed-variant": "#454747",
            "surface-container-highest": "#e1e2ea",
            "on-error": "#ffffff",
            "background": "#f9f9ff",
            "on-primary-container": "#dbe6ff",
            "on-background": "#191c21",
            "inverse-surface": "#2e3037",
            "on-secondary": "#ffffff",
            "secondary-fixed-dim": "#c6c6c7",
            "on-surface-variant": "#414752"
          },
          "borderRadius": {
            "DEFAULT": "0.25rem",
            "lg": "0.5rem",
            "xl": "0.75rem",
            "full": "9999px"
          },
          "spacing": {
            "md": "16px",
            "margin": "auto",
            "max_width": "1128px",
            "xl": "32px",
            "sm": "8px",
            "lg": "24px",
            "gutter": "24px",
            "unit": "4px",
            "xs": "4px"
          },
          "fontFamily": {
            "title-md": ["Manrope"],
            "title-lg": ["Manrope"],
            "caption": ["Inter"],
            "display-lg": ["Manrope"],
            "label-lg": ["Inter"],
            "body-lg": ["Inter"],
            "display-md": ["Manrope"],
            "label-md": ["Inter"],
            "body-md": ["Inter"]
          },
          "fontSize": {
            "title-md": ["16px", { "lineHeight": "24px", "fontWeight": "600" }],
            "title-lg": ["20px", { "lineHeight": "28px", "fontWeight": "600" }],
            "caption": ["11px", { "lineHeight": "14px", "fontWeight": "400" }],
            "display-lg": ["32px", { "lineHeight": "40px", "fontWeight": "700" }],
            "label-lg": ["14px", { "lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "500" }],
            "body-lg": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
            "display-md": ["24px", { "lineHeight": "32px", "fontWeight": "600" }],
            "label-md": ["12px", { "lineHeight": "16px", "letterSpacing": "0.02em", "fontWeight": "500" }],
            "body-md": ["14px", { "lineHeight": "20px", "fontWeight": "400" }]
          }
        },
      },
    }
  </script>
  <style>
    .ambient-shadow {
      box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.05);
    }
  </style>
    <script>
        const URLROOT = '<?php echo URLROOT; ?>';
        const CURRENT_USER_ID = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; ?>;
    </script>
</head>
<body class="bg-background font-body-md text-on-background">
