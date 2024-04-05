<?php
/**
 * Plugin Name: Khalti Payment Gateway
 * Plugin URI: http://example.com
 * Description: Allows WooCommerce to accept payments through Khalti.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: http://example.com
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_filter( 'woocommerce_payment_gateways', 'khalti_add_gateway_class' );
function khalti_add_gateway_class( $gateways ) {
    $gateways[] = 'WC_Khalti_Gateway'; 
    return $gateways;
}

add_action( 'plugins_loaded', 'khalti_init_gateway_class' );
function khalti_init_gateway_class() {

    class WC_Khalti_Gateway extends WC_Payment_Gateway {

        public function __construct() {
            $this->id = 'khalti';
            $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
            $this->has_fields = true;
            $this->method_title = 'Khalti Payment Gateway';
            $this->method_description = 'Allows payments with Khalti e-Payment Checkout platform.';

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option( 'title' );
            $this->description = $this->get_option( 'description' );
            $this->enabled = $this->get_option( 'enabled' );
            $this->testmode = 'yes' === $this->get_option( 'testmode' );
            $this->live_secret_key = $this->get_option( 'live_secret_key' );
            $this->sandbox_secret_key = $this->get_option( 'sandbox_secret_key' );

            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            add_action( 'woocommerce_api_khalti_payment_callback', array( $this, 'handle_khalti_payment_callback' ) );
        }

        public function init_form_fields(){
            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Enable/Disable',
                    'label'       => 'Enable Khalti Payment Gateway',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default'     => 'Khalti',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default'     => 'Pay with your Khalti account.',
                ),
                'testmode' => array(
                    'title'       => 'Test mode',
                    'label'       => 'Enable Test Mode',
                    'type'        => 'checkbox',
                    'description' => 'Place the gateway in test mode using test API keys.',
                    'default'     => 'yes',
                    'desc_tip'    => true,
                ),
                'live_secret_key' => array(
                    'title'       => 'Live Secret Key',
                    'type'        => 'text'
                ),
                'sandbox_secret_key' => array(
                    'title'       => 'Sandbox Secret Key',
                    'type'        => 'text'
                ),
            );
        }

        public function process_payment( $order_id ) {
            $order = wc_get_order( $order_id );
            $payment_url = $this->initiate_khalti_payment( $order );

            if ( $payment_url !== false ) {
                return array(
                    'result'   => 'success',
                    'redirect' => $payment_url,
                );
            } else {
                wc_add_notice( 'Failed to initiate payment. Please try again or contact us for assistance.', 'error' );
                return;
            }
        }

        private function initiate_khalti_payment( $order ) {
            $url = $this->testmode ? 'https://a.khalti.com/api/v2/epayment/initiate/' : 'https://khalti.com/api/v2/epayment/initiate/';
            $secret_key = $this->testmode ? $this->sandbox_secret_key : $this->live_secret_key;

            $payload = array(
                'return_url' => add_query_arg( 'wc-api', 'khalti_payment_callback', site_url( 'order-recieved/' ) ),
                'website_url' => get_site_url(),
                'amount' => round( $order->get_total() * 100 ),
                'purchase_order_id' => strval( $order->get_id() ),
                'purchase_order_name' => "Order #" . $order->get_id(),
                'customer_info' => array(
                    'name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                    'email' => $order->get_billing_email(),
                    'phone' => $order->get_billing_phone(),
                ),
            );

            $response = wp_remote_post( $url, array(
                'method'    => 'POST',
                'headers'   => array(
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Key ' . $secret_key,
                ),
                'body'      => json_encode( $payload ),
                'data_format' => 'body',
            ));

            if ( is_wp_error( $response ) ) {
                error_log( 'Khalti Payment Request Error: ' . $response->get_error_message() );
                return false;
            }

            $body = json_decode( wp_remote_retrieve_body( $response ), true );

            if ( isset( $body['payment_url'] ) ) {
                return $body['payment_url'];
            } else {
                error_log( 'Khalti Payment Initiation Failed: ' . print_r( $body, true ) );
                return false;
            }
        }

        public function handle_khalti_payment_callback() {

        }
    }
}