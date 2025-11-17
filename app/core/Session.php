<?php
// Session management class

class Session {
    /**
     * Start session if not started
     */
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set session value
     */
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Get session value
     */
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if session key exists
     */
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove session value
     */
    public static function remove($key) {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy session
     */
    public static function destroy() {
        self::start();
        session_destroy();
    }

    /**
     * Set user session
     */
    public static function setUser($user) {
        self::set('user_id', $user['MaNguoiDung'] ?? $user['id'] ?? null);
        self::set('user_name', $user['TenDangNhap'] ?? $user['username'] ?? null);
        self::set('user_role', $user['VaiTro'] ?? $user['role'] ?? null);
        self::set('user', $user);
    }

    /**
     * Get current user
     */
    public static function getUser() {
        return self::get('user');
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return self::has('user_id');
    }

    /**
     * Logout user
     */
    public static function logout() {
        self::remove('user_id');
        self::remove('user_name');
        self::remove('user_role');
        self::remove('user');
    }
}

