<?php
/**
 * Shared route helpers for top nav and admin sidebar active states.
 */

function pn_current_route(): string
{
    return isset($_GET['url']) ? trim((string) $_GET['url'], '/') : '';
}

/** True when $route equals $prefix or is a child path ($prefix/...). */
function pn_route_matches(string $prefix): bool
{
    $route = pn_current_route();
    $prefix = trim($prefix, '/');
    if ($prefix === '') {
        return $route === '';
    }
    if ($route === $prefix) {
        return true;
    }
    return strpos($route, $prefix . '/') === 0;
}

function pn_nav_item_cls(bool $active): string
{
    return $active
        ? 'is-active text-[#0A66C2] bg-blue-50/90 shadow-sm ring-1 ring-[#0A66C2]/20 rounded-xl'
        : 'text-slate-500 hover:text-slate-900';
}

function pn_nav_icon_cls(bool $active): string
{
    return $active
        ? 'is-active text-[#0A66C2] bg-blue-50/90 rounded-lg p-1 ring-1 ring-[#0A66C2]/20'
        : 'text-slate-400 hover:text-[#0A66C2]';
}

function pn_nav_home_active(): bool
{
    $route = pn_current_route();
    if ($route === 'user/feed') {
        return true;
    }
    if (strpos($route, 'post/show') === 0) {
        return true;
    }
    return false;
}

function pn_nav_admin_active(): bool
{
    return pn_route_matches('admin');
}

function pn_nav_company_active(): bool
{
    $route = pn_current_route();
    $publicCompanyRoutes = [
        'company',
        'company/index',
        'company/login',
        'company/register',
    ];
    if (in_array($route, $publicCompanyRoutes, true)) {
        return false;
    }
    return pn_route_matches('company');
}

function pn_admin_section(): string
{
    $route = pn_current_route();
    if ($route === 'admin' || strpos($route, 'admin/') !== 0) {
        return '';
    }
    $parts = explode('/', $route, 3);
    return $parts[1] ?? 'dashboard';
}

function pn_admin_nav_active(string $section): bool
{
    return pn_admin_section() === $section;
}
