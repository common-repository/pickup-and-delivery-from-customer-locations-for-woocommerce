<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://powerfulwp.com
 * @since      1.0.0
 *
 * @package    Pdfclw
 * @subpackage Pdfclw/includes
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Pdfclw
 * @subpackage Pdfclw/includes
 * @author     powerfulwp <apowerfulwp@gmail.com>
 */
class Pdfclw {
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Pdfclw_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if ( defined( 'PDFCLW_VERSION' ) ) {
            $this->version = PDFCLW_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'pdfclw';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Pdfclw_Loader. Orchestrates the hooks of the plugin.
     * - Pdfclw_I18n. Defines internationalization functionality.
     * - Pdfclw_Admin. Defines all hooks for the admin area.
     * - Pdfclw_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        /**
         * The function file.
         * core plugin.
         */
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php';
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pdfclw-loader.php';
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pdfclw-admin.php';
        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-pdfclw-public.php';
        /**
         * The file responsible for the order
         */
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pdfclw-order.php';
        $this->loader = new Pdfclw_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Pdfclw_I18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {
        // $plugin_i18n = new Pdfclw_I18n();
        // $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new Pdfclw_Admin($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        /**
         * Add menu
         */
        $this->loader->add_action(
            'admin_menu',
            $plugin_admin,
            'pdfclw_admin_menu',
            99
        );
        /**
         * Settings
         */
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'pdfclw_add_pickup_metaboxes' );
        $this->loader->add_action(
            'woocommerce_process_shop_order_meta',
            $plugin_admin,
            'pdfclw_save_pickup_meta',
            1,
            2
        );
        // Add the custom columns to the pdfclw_pickup post type.
        $this->loader->add_filter( 'manage_pdfclw_pickup_posts_columns', $plugin_admin, 'set_custom_edit_pickup_columns' );
        // Add the data to the custom columns for the pdfclw_pickup post type.
        $this->loader->add_action(
            'manage_pdfclw_pickup_posts_custom_column',
            $plugin_admin,
            'custom_pickup_column',
            10,
            2
        );
        $this->loader->add_action( 'woocommerce_admin_order_data_after_billing_address', $plugin_admin, 'get_order_pickup_location_data_column' );
        /**
         * Order columns
         */
        if ( pdfclw_is_hpos_enabled() ) {
            $this->loader->add_action(
                'woocommerce_shop_order_list_table_custom_column',
                $plugin_admin,
                'orders_list_columns',
                20,
                2
            );
            $this->loader->add_filter(
                'woocommerce_shop_order_list_table_columns',
                $plugin_admin,
                'orders_list_columns_order',
                20
            );
        } else {
            $this->loader->add_action(
                'manage_shop_order_posts_custom_column',
                $plugin_admin,
                'orders_list_columns',
                20,
                2
            );
            $this->loader->add_filter(
                'manage_edit-shop_order_columns',
                $plugin_admin,
                'orders_list_columns_order',
                20
            );
        }
        /**
         * Settings
         */
        $this->loader->add_action( 'admin_init', $plugin_admin, 'settings_init' );
        /**
         * New order.
         */
        $plugin_order = new Pdfclw_Order();
        $this->loader->add_action(
            'woocommerce_new_order',
            $plugin_order,
            'new_order',
            2,
            2
        );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new Pdfclw_Public($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        // Checkout.
        $this->loader->add_action(
            'woocommerce_after_checkout_billing_form',
            $plugin_public,
            'checkout_pickup_form',
            10,
            1
        );
        $this->loader->add_action( 'woocommerce_checkout_update_order_meta', $plugin_public, 'update_checkout_fields' );
        $this->loader->add_action(
            'woocommerce_after_checkout_validation',
            $plugin_public,
            'pickup_checkout_validation',
            10,
            2
        );
        // Show pickp address on pages and emails.
        $this->loader->add_action(
            'woocommerce_email_after_order_table',
            $plugin_public,
            'pickup_on_emails',
            90,
            4
        );
        $this->loader->add_action(
            'woocommerce_thankyou',
            $plugin_public,
            'pickup_on_thankyou',
            90,
            1
        );
        $this->loader->add_action(
            'woocommerce_order_details_after_order_table',
            $plugin_public,
            'pickup_on_details_after_order_table',
            90,
            4
        );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Pdfclw_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
