<?php
/**
 * SMTP + CC settings for approval notification emails.
 * Prefer environment variables; fall back to existing project SMTP (same as ticket mail).
 */
if (!defined('APPROVAL_MAIL_CONFIG_LOADED')) {
    define('APPROVAL_MAIL_CONFIG_LOADED', true);

    if (!function_exists('approval_mail_env')) {
        function approval_mail_env($key, $default = '')
        {
            $v = getenv($key);
            if ($v === false || $v === null || $v === '') {
                return $default;
            }
            return $v;
        }
    }

    if (!function_exists('approval_mail_smtp')) {
        function approval_mail_smtp()
        {
            return array(
                'host' => approval_mail_env('MAHA_SMTP_HOST', 'mail.kwickfoods.in'),
                'username' => approval_mail_env('MAHA_SMTP_USER', 'info@kwickfoods.in'),
                'password' => approval_mail_env('MAHA_SMTP_PASS', ''),
                'port' => (int) approval_mail_env('MAHA_SMTP_PORT', '587'),
                'secure' => approval_mail_env('MAHA_SMTP_SECURE', 'tls'),
                'from_email' => approval_mail_env('MAHA_SMTP_FROM', 'info@kwickfoods.in'),
                'from_name' => approval_mail_env('MAHA_SMTP_FROM_NAME', 'Maha Chai'),
            );
        }
    }

    if (!function_exists('approval_mail_fixed_cc')) {
        /** Always CC on every approval/rejection mail. */
        function approval_mail_fixed_cc()
        {
            return array(
                'coo@mahachai.in' => 'COO',
                'pradeep.kulkarni@mahachai.in' => 'Pradeep Kulkarni',
                'rajatdh07@gmail.com' => 'Rajat Dhanwalkar',
            );
        }
    }

    if (!function_exists('approval_mail_logo_url')) {
        function approval_mail_logo_url()
        {
            return approval_mail_env('MAHA_MAIL_LOGO', 'https://mahachai.in/logo.png');
        }
    }
}
