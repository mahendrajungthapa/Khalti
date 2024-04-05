<?php
/**
 * Plugin Name: Khalti WooCommerce Payment Gateway
 * Description: Integrates Khalti payment gateway with WooCommerce.
 * Version: 1.0
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_filter('woocommerce_payment_gateways', 'add_to_woo_khalti_payment_gateway');

function add_to_woo_khalti_payment_gateway($gateways) {
    $gateways[] = 'WC_Khalti_Gateway';
    return $gateways;
}

add_action('plugins_loaded', 'init_khalti_payment_gateway');

function init_khalti_payment_gateway() {
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

    class WC_Khalti_Gateway extends WC_Payment_Gateway {
        public function __construct() {
            $this->id = 'khalti';
            $this->icon = ''; // URL of the icon to be displayed
            $this->has_fields = true;
            $this->method_title = 'Khalti Payment Gateway';
            $this->method_description = 'Allows payments with Khalti e-Payment Checkout platform.';

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->enabled = $this->get_option('enabled');
            $this->testmode = 'yes' === $this->get_option('testmode');
            $this->live_secret_key = $this->get_option('live_secret_key');
            $this->sandbox_secret_key = $this->get_option('sandbox_secret_key');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'khalti'),
                    'label' => __('Enable Khalti Payment Gateway', 'khalti'),
                    'type' => 'checkbox',
                    'default' => 'no'
                ),
                'title' => array(
                    'title' => __('Title', 'khalti'),
                    'type' => 'text',
                    'default' => __('Khalti', 'khalti')
                ),
                'description' => array(
                    'title' => __('Description', 'khalti'),
                    'type' => 'textarea',
                    'default' => ''
                ),
                'testmode' => array(
                    'title' => __('Test mode', 'khalti'),
                    'label' => __('Enable Test Mode', 'khalti'),
                    'type' => 'checkbox',
                    'default' => 'yes'
                ),
                'live_secret_key' => array(
                    'title' => __('Live Secret Key', 'khalti'),
                    'type' => 'text'
                ),
                'sandbox_secret_key' => array(
                    'title' => __('Sandbox Secret Key', 'khalti'),
                    'type' => 'text'
                )
            );
        }

        public function process_payment($order_id) {
            $order = wc_get_order($order_id);

            // Your code to initiate a payment request to Khalti and handle the response

            // Assuming the payment initiation is successful, and you have the payment URL
            $payment_url = 'https://payment-url-from-khalti'; // Example payment URL from Khalti

            return array(
                'result' => 'success',
                'redirect' => $payment_url
            );
        }
    }
}