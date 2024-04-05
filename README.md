# Khalti WooCommerce Payment Gateway Plugin

## Description

This plugin integrates the Khalti payment gateway with WooCommerce, allowing customers to make payments using Khalti's e-Payment Checkout platform. 

## Installation

1. Download the plugin ZIP file from the [GitHub repository](https://github.com/mahendrajungthapa/khalti).
2. Log in to your WordPress admin panel.
3. Navigate to Plugins > Add New.
4. Click on the "Upload Plugin" button and select the downloaded ZIP file.
5. Activate the plugin once it's installed.

## Configuration

1. Go to WooCommerce > Settings > Payments.
2. Enable the "Khalti Payment Gateway" option.
3. Configure the settings:
   - Title: Title of the payment method displayed to customers during checkout.
   - Description: Description of the payment method displayed to customers during checkout.
   - Test mode: Enable this option for testing with sandbox environment.
   - Live Secret Key: Your live secret key provided by Khalti.
   - Sandbox Secret Key: Your sandbox secret key provided by Khalti.

## Usage

- Customers will see the Khalti payment option during checkout if enabled.
- Once selected, customers will be redirected to the Khalti payment portal to complete the transaction.
- After successful payment, customers will be redirected back to your website.

## Development

This plugin utilizes the Khalti API to initiate payment requests. You can customize the plugin code according to your specific requirements and preferences. 

## Support

For any issues or inquiries, please contact [Khalti Support](https://khalti.com/contact-us/).

## License

This plugin is licensed under the [MIT License](LICENSE).

