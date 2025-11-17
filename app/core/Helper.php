<?php
// Helper functions

/**
 * Redirect to a URL
 */
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit;
}

/**
 * Get base URL
 */
function base_url($path = '') {
    return BASE_URL . ltrim($path, '/');
}

/**
 * Get asset URL
 */
function asset_url($path) {
    return BASE_URL . 'assets/' . ltrim($path, '/');
}

/**
 * Get upload URL
 */
function upload_url($filename) {
    return BASE_ASSETS_UPLOADS . $filename;
}

/**
 * Sanitize input
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency
 */
function format_currency($amount) {
    return number_format($amount, 0, ',', '.') . ' đ';
}

/**
 * Format date
 */
function format_date($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user ID
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Flash message
 */
function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

