<?php
/**
 * Plugin Name: Auto Login Link
 * Plugin URI: https://84g.us
 * Description: Auto Login Link
 * Author: Bagus Pribadi Setiawan
 * Author URI: https://84g.us
 * Version: 1.0.0
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: auto-login-link
 * Domain Path: /language
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 */

class Auto_Login_Link {

    private static $instance;

    const LANGUAGE_DOMAIN = 'auto-login-link';
    const PLUGIN_SLUG = 'auto-login-link';

    public static function get_instance() {
        if (NULL === self::$instance) {
            self::$instance = new self();
        }
		return (self::$instance);
    }

    private function __construct() {
        $this->autoload();

        add_action('plugins_loaded', [$this, 'init']);
        add_action('plugins_loaded', [$this, 'load_textdomain']);

        add_action('wp_loaded', function() {
            if (!is_admin()) {
                $this->check_auto_login();
            }
        });

        register_activation_hook(__FILE__, [$this, 'activate']);
    }

    private function autoload($path = NULL) {
        $class_paths = [];

        if (is_null($path)) {
            $class_paths[] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes';
        } else {
            $class_paths[] = $path;
        }

        if (count($class_paths)) {
            foreach ($class_paths as $class_path) {
                if (file_exists($class_path) && is_dir($class_path)) {
                    foreach (scandir($class_path) as $file) {
                        $path = $class_path . DIRECTORY_SEPARATOR . $file;
                        if(!is_dir($path)) {
                            require_once($path);
                        } else if ($file != '.' && $file != '..') {
                            $this->autoload($path);
                        }
                    }
                }
            }
        }
    }

    public function init() {
        add_action('admin_menu', [$this, 'plugin_menu']);
    }

    public function plugin_menu() {
        $title = __('Auto Login Link', self::LANGUAGE_DOMAIN);

        add_menu_page($title, $title, 'manage_options', self::PLUGIN_SLUG, [new Auto_Login_Link_Admin, 'index'], 'dashicons-admin-network');
    }

    public static function exec_template($template, $data = NULL, $return_output = FALSE) {
		$templ = plugin_dir_path(__FILE__) . 'templates/' . $template . '.php';

		if ($return_output)
			ob_start();

		if (NULL !== $data) {
			extract($data);
        }

		include($templ);

		if ($return_output) {
			$ret = ob_get_clean();
			return ($ret);
		}
	}

    public function load_textdomain() {
        $path = str_ireplace(WP_PLUGIN_DIR, '', dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;
        load_plugin_textdomain(self::LANGUAGE_DOMAIN, FALSE, $path);
    }

    public function activate() {
        // create table
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "auto_login_link (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            url varchar(255) NOT NULL,
            user_id integer NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta($sql);
    }

    public static function redirect($url) {
        echo '<script>window.location.href="' . $url . '"</script>';
    }

    public static function set_notice($type, $message) {
        @session_start();
        $_SESSION['_notice_type'] = $type;
        $_SESSION['_notice_message'] = $message;
    }

    public static function check_notice($print = FALSE) {
        @session_start();
        if (isset($_SESSION['_notice_type'])) {
            if (!$print) {
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-' . $_SESSION['_notice_type'] . '"><p>' . $_SESSION['_notice_message'] . '</p></div>';
                });
            } else {
                echo '<div class="notice notice-' . $_SESSION['_notice_type'] . '"><p>' . $_SESSION['_notice_message'] . '</p></div>';
            }
            unset($_SESSION['_notice_type']);
            unset($_SESSION['_notice_message']);
        }
    }

    public function check_auto_login() {
        if (!defined('DOING_CRON') && !defined('DOING_AJAX')) {
            $url = trim($_SERVER['REQUEST_URI'], '/');

            $auto_login_link_model = new Auto_Login_Link_Model();
            $auto_login_link = $auto_login_link_model->get([
                'single' => TRUE,
                'url' => $url
            ]);

            if (is_object($auto_login_link) && $auto_login_link->user_id > 0) {
                wp_destroy_current_session();
                wp_set_current_user($auto_login_link->user_id);
                wp_set_auth_cookie($auto_login_link->user_id);
                wp_redirect(site_url());
                exit();
            }
        }
    }

}

Auto_Login_Link::get_instance();
