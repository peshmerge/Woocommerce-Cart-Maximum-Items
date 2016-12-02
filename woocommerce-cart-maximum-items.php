<?php
/*
 * Plugin Name:              Woocommerce Cart Maximum Items
 * Plugin URI:               https://github.com/peshmerge/Woocommerce-Cart-Maximum-Items
 * Description:              Specify the maximum amount of items in the Woocommerce shopping cart.
 * Version:                  1.0
 * Author:                   Peshmerge Morad
 * License: 			     GPLv3
 * License URI: 		     http://www.gnu.org/licenses/gpl-3.0.txt
 * Author URI:               https://baran-it.com
 * Text Domain: 		     baran-it
 */

/**
 * Load bootstrap style
 */
function load_bootstrap_css_js()
{
    wp_register_style('prefix_bootstrap', plugin_dir_url(__FILE__) . '/style/css/bootstrap.min.css');
    wp_enqueue_style('prefix_bootstrap');

    wp_register_style('prefix_bootstrap', plugin_dir_url(__FILE__) . '/style/css/bootstrap-theme.min.css');
    wp_enqueue_style('prefix_bootstrap');

    wp_register_script('prefix_bootstrap', plugin_dir_url(__FILE__) . '/style/js/bootstrap.min.js');
    wp_enqueue_script('prefix_bootstrap');
}

/**
 * The main function
 */
function show_woocommerce_cart_maximum_items_options()
{
    //Enqueue style and script
    load_bootstrap_css_js();

    //Plugin version
    $woocommerce_cart_maximum_items_version = '1.0';
    //Check the sent values from the form
    if (isset($_POST['woo_cart_max'])) {
        //Update the options in the database
        update_option('woo_cart_max_amount', (intval)($_POST['woo_cart_max_amount'] != '')
            ? $_POST['woo_cart_max_amount'] : 0);
        update_option('woo_cart_max_amount_message', (string)($_POST['woo_cart_max_amount_message'] != '')
            ? $_POST['woo_cart_max_amount_message'] : 'You are not allowed to add more products');

        echo '<div id="message" class="updated fade">';
        echo '<p><strong>Options Saved!</strong></p></div>';
    }
    //Get the amoount from the database
    $amount = get_option('woo_cart_max_amount');
    if ($amount)
        $woo_cart_max_amount = $amount;
    else
        $woo_cart_max_amount = 0;
    //Get the message from the database
    $message = get_option('woo_cart_max_amount_message');
    if ($message)
        $woo_cart_max_amount_message = $message;
    else
        $woo_cart_max_amount_message = '';
    ?>

    <fieldset class="options">
        <p>1. Enter the maximum number of allowed products/items in for Woocommerce Cart. Enter 0 for unlimited
            amount!</p>
        <p>2. Enter a customized error message that will be displayed when the user try to add more elements than it
            allowed!</p>
    </fieldset>
    <div class="" style="background-color: white;">
        <form method="post" class="form-horizontal" role="form" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
            <input type="hidden" name="woo_cart_max" id="woo_cart_max" value="true"/>
            <h3><label for="title"> Woocommerce Cart Maximum Items settings </label></h3>
            <div class="form-group col-md-8">
                <div class="col-xs-10">
                    <label for="woo_cart_max_amount" class="col-xs-3 col-form-label">Maximum items: </label>
                    <input type="number" name="woo_cart_max_amount" class="form-control col-xs-3" min="0" max="200"
                           value="<?php echo $amount ?>">
                </div>
            </div>
            <div class="form-group col-md-8">
                <div class="col-xs-10">
                    <label for="woo_cart_max_amount_message" class="col-xs-3 col-form-label">Custom Error
                        message:</label>
                    <textarea name="woo_cart_max_amount_message" class="form-control" placeholder="Your message here"
                              rows="4" cols="50" maxlength="250"><?php echo (string)$message ?></textarea>
                </div>
            </div>
            <div class="form-group col-md-8">
                <input type="submit" class="button-primary" name="woo_cart_max" value="Update Options"/>
            </div>
        </form>
    </div>
    <?php
}


/**
 *
 */
function woocommerce_cart_maximum_items_options()
{
    echo '<div class="wrap"><h2>Specify the maximum amount of items in the Woocommerce shopping cart.</h2>';
    echo '<div id="poststuff"><div id="post-body">';
    show_woocommerce_cart_maximum_items_options();
    echo '</div></div>';
    echo '</div>';
}

/**
 * Add option page
 */
function woocommerce_cart_maximum_items_options_options_page()
{
    add_options_page('Woocommerce Cart Maximum Items', 'Woocommerce Cart Maximum Items', 'manage_options', __FILE__, 'woocommerce_cart_maximum_items_options');
}

/**
 * @param        $passed
 * @param        $product_id
 * @param        $quantity
 * @param string $variation_id
 * @param string $variations
 *
 * @return bool
 */
function validation_function($passed, $product_id, $quantity, $variation_id = '', $variations = '')
{
    $amount = (intval)(get_option('woo_cart_max_amount'));
    $message = get_option('woo_cart_max_amount_message');
    if ($amount > 0) {
        if (WC()->cart->get_cart_contents_count() >= intval($amount)) {
            $passed = false;
            wc_add_notice(__(htmlspecialchars($message), 'baran-it'), 'error');
        }
        return $passed;
    }
}

//Add the filter
add_action('woocommerce_add_to_cart_validation', 'validation_function', 10, 5);


//Add the option page to the admin menu
add_action('admin_menu', 'woocommerce_cart_maximum_items_options_options_page');
