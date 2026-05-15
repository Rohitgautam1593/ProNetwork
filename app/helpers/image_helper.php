<?php
/**
 * Shared image fallbacks for local demo assets.
 */

function pn_upload_url(string $folder, string $fileName): string {
    return URLROOT . '/uploads/' . trim($folder, '/') . '/' . ltrim($fileName, '/');
}

function pn_company_logo_file(?string $companyName = null): string {
    $name = strtolower((string) $companyName);
    if (strpos($name, 'amazon') !== false) return 'logos/amazon-com-inc-logo.jpeg';
    if (strpos($name, 'apple') !== false) return 'logos/apple-inc-logo.jpeg';
    if (strpos($name, 'armani') !== false) return 'logos/armani-logo.jpeg';
    if (strpos($name, 'flipkart') !== false) return 'logos/flipkart-logo.jpeg';
    if (strpos($name, 'google') !== false) return 'logos/google-llc-logo.jpeg';
    if (strpos($name, 'infosys') !== false) return 'logos/infosys-limited-logo.jpeg';
    if (strpos($name, 'microsoft') !== false) return 'logos/microsoft-corporation-logo.jpeg';
    if (strpos($name, 'tata') !== false) return 'logos/tata-consultancy-services-logo.jpeg';
    if (strpos($name, 'tesla') !== false) return 'logos/tesla-inc-logo.jpeg';
    if (strpos($name, 'green') !== false) {
        return 'logos/greengrid.png';
    }
    if (strpos($name, 'cloud') !== false) {
        return 'logos/cloudscale.png';
    }
    return 'logos/nexa.png';
}

function pn_company_logo_url(array $company): string {
    $logo = trim((string) ($company['logo'] ?? $company['logo_path'] ?? ''));
    if ($logo !== '') {
        if (!preg_match('/^https?:\/\//i', $logo)) {
            $path = PROJECTROOT . '/public/uploads/companies/' . ltrim($logo, '/');
            if (is_file($path)) {
                return pn_upload_url('companies', $logo);
            }
        }
    }

    return pn_upload_url('companies', pn_company_logo_file($company['company_name'] ?? $company['name'] ?? null));
}

function pn_company_banner_file(?string $companyName = null): string {
    $name = strtolower((string) $companyName);
    if (strpos($name, 'amazon') !== false) return 'banners/amazon-com-inc-banner.jpeg';
    if (strpos($name, 'apple') !== false) return 'banners/apple-inc-banner.jpeg';
    if (strpos($name, 'armani') !== false) return 'banners/armani-banner.jpeg';
    if (strpos($name, 'cloud') !== false) return 'banners/cloudscale-systems-banner.jpeg';
    if (strpos($name, 'flipkart') !== false) return 'banners/flipkart-banner.jpeg';
    if (strpos($name, 'google') !== false) return 'banners/google-llc-banner.jpeg';
    if (strpos($name, 'green') !== false) return 'banners/greengrid-labs-banner.jpeg';
    if (strpos($name, 'infosys') !== false) return 'banners/infosys-limited-banner.jpeg';
    if (strpos($name, 'microsoft') !== false) return 'banners/microsoft-corporation-banner.jpeg';
    if (strpos($name, 'tata') !== false) return 'banners/tata-consultancy-services-banner.jpeg';
    if (strpos($name, 'tesla') !== false) return 'banners/tesla-inc-banner.jpeg';
    return 'banners/nexa-analytics-banner.jpeg';
}

function pn_company_banner_url(array $company): string {
    $banner = trim((string) ($company['banner'] ?? $company['banner_path'] ?? ''));
    if ($banner !== '') {
        if (!preg_match('/^https?:\/\//i', $banner)) {
            $path = PROJECTROOT . '/public/uploads/companies/' . ltrim($banner, '/');
            if (is_file($path)) {
                return pn_upload_url('companies', $banner);
            }
        }
    }

    return pn_upload_url('companies', pn_company_banner_file($company['company_name'] ?? $company['name'] ?? null));
}

function pn_ui_avatar_url(?string $name, string $background = '0A66C2'): string {
    $label = rawurlencode(trim((string) $name) !== '' ? trim((string) $name) : 'User');
    return 'https://ui-avatars.com/api/?name=' . $label . '&background=' . $background . '&color=fff&size=128&bold=true';
}

function pn_profile_pic_url(?array $user = null): string {
    $pic = trim((string) ($user['profile_pic'] ?? ''));
    if ($pic !== '') {
        if (preg_match('/^https?:\/\//i', $pic)) {
            return $pic;
        }
        return pn_upload_url('profiles', $pic);
    }

    return pn_ui_avatar_url($user['full_name'] ?? null);
}

function pn_cover_image_url(?array $user = null): string {
    $cover = trim((string) ($user['cover_image'] ?? ''));
    if ($cover !== '') {
        if (preg_match('/^https?:\/\//i', $cover)) {
            return $cover;
        }
        return pn_upload_url('covers', $cover);
    }

    return pn_upload_url('covers', '1778246066_IMG-20240915-WA0002.jpg');
}
