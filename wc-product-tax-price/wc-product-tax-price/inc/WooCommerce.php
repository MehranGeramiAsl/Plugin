<?php

namespace WC_Product_Tax_Price;

class WooCommerce
{

    public static $taxRegularPriceMeta = '_regular_price_tax';

    public static $taxSalePriceMeta = '_sale_price_tax';

    public static $taxPriceMeta = '_price_tax';

    public function __construct()
    {
        $option = Option::get();
        if ($option['is_active'] == "1") {

            // Add Field in Admin WooCommerce
            // @see https://rudrastyh.com/woocommerce/product-data-metabox.html
            // @see https://rudrastyh.com/woocommerce/add-custom-fields-to-product-variations.html
            add_action('woocommerce_product_options_general_product_data', [$this, 'general_product_data_tab']);
            add_action('woocommerce_process_product_meta', [$this, 'woocommerce_process_product_meta']);
            add_action('woocommerce_variation_options_pricing', [$this, 'woocommerce_variation_options_tab'], 10, 3);
            add_action('woocommerce_save_product_variation', [$this, 'woocommerce_save_product_variation'], 10, 2);

            // Show Custom Price in Front Site
            # https://stackoverflow.com/questions/45806249/change-product-prices-via-a-hook-in-woocommerce-3
            # https://wordpress.stackexchange.com/questions/308252/woocommerce-get-price-filter-hook-not-working-for-product-variation-price
            # https://www.robbertvermeulen.com/woocommerce-price-based-on-user-role/
            # https://developer.woocommerce.com/2015/09/14/caching-and-dynamic-pricing-upcoming-changes-to-the-get_variation_prices-method/
            $this->setup_product_price_filter();

            // Setup Custom Cart Price
            // @see Line 680 wp-content/plugins/woocommerce/includes/class-wc-cart-totals.php
            add_action('woocommerce_before_calculate_totals', [$this, 'woocommerce_before_calculate_totals'], 1100, 1);
            add_filter('woocommerce_calculate_item_totals_taxes', [$this, 'woocommerce_calculate_item_totals_taxes'], 1100, 3);
        }
    }

    /* @Method */
    public function setup_product_price_filter()
    {
        add_filter('woocommerce_product_get_regular_price', array($this, 'product_regular_price'), 12, 2);
        add_filter('woocommerce_product_variation_get_regular_price', array($this, 'product_regular_price'), 12, 2);
        add_filter('woocommerce_product_get_sale_price', array($this, 'product_sale_price'), 12, 2);
        add_filter('woocommerce_product_variation_get_sale_price', array($this, 'product_sale_price'), 12, 2);
        add_filter('woocommerce_product_get_price', array($this, 'product_price'), 12, 2);
        add_filter('woocommerce_product_variation_get_price', array($this, 'product_price'), 12, 2);
        add_filter('woocommerce_variation_prices_price', array($this, 'woocommerce_variation_prices_price'), 12, 3);
        add_filter('woocommerce_get_variation_prices_hash', array($this, 'variation_prices_hash'), 99, 1);
    }

    /* @Method */
    public function disable_product_price_filter()
    {
        remove_filter('woocommerce_product_get_regular_price', array($this, 'product_regular_price'), 12);
        remove_filter('woocommerce_product_variation_get_regular_price', array($this, 'product_regular_price'), 12);
        remove_filter('woocommerce_product_get_sale_price', array($this, 'product_sale_price'), 12);
        remove_filter('woocommerce_product_variation_get_sale_price', array($this, 'product_sale_price'), 12);
        remove_filter('woocommerce_product_get_price', array($this, 'product_price'), 12);
        remove_filter('woocommerce_product_variation_get_price', array($this, 'product_price'), 12);
        remove_filter('woocommerce_variation_prices_price', array($this, 'woocommerce_variation_prices_price'), 12);
        remove_filter('woocommerce_get_variation_prices_hash', array($this, 'variation_prices_hash'), 99);
    }

    /* @Method */
    public static function get_product_regular_price_with_tax($product_id)
    {
        return get_post_meta($product_id, self::$taxRegularPriceMeta, true);
    }

    /* @Method */
    public static function get_product_sale_price_with_tax($product_id)
    {
        return get_post_meta($product_id, self::$taxSalePriceMeta, true);
    }

    /* @Method */
    public static function get_product_price_with_tax($product_id)
    {
        return get_post_meta($product_id, self::$taxPriceMeta, true);
    }

    /* @Hook */
    public function general_product_data_tab()
    {
        global $post;

        echo '<div class="options_group show_if_simple">';

        // Regular Price
        woocommerce_wp_text_input([
            'label' => 'قیمت عادی با ارزش افزوده (' . get_woocommerce_currency_symbol() . ')',
            'placeholder' => '',
            'class' => '',
            'style' => '',
            'wrapper_class' => '',
            'value' => self::get_product_regular_price_with_tax($post->ID),
            'id' => 'simple' . self::$taxRegularPriceMeta,
            'name' => 'simple' . self::$taxRegularPriceMeta,
            'type' => 'text',
            'desc_tip' => true,
            'description' => 'لطفا قیمت عادی محصول را با ارزش افزوده وارد نمایید؟',
            'data_type' => '',
            'custom_attributes' => ''
        ]);

        // Sale Price
        woocommerce_wp_text_input([
            'label' => 'قیمت فروش ویژه با ارزش افزوده (' . get_woocommerce_currency_symbol() . ')',
            'placeholder' => '',
            'class' => '',
            'style' => '',
            'wrapper_class' => '',
            'value' => self::get_product_sale_price_with_tax($post->ID),
            'id' => 'simple' . self::$taxSalePriceMeta,
            'name' => 'simple' . self::$taxSalePriceMeta,
            'type' => 'text',
            'desc_tip' => true,
            'description' => 'لطفا قیمت فروش ویژه محصول را با ارزش افزوده وارد نمایید؟',
            'data_type' => '',
            'custom_attributes' => ''
        ]);

        echo '</div>';
    }

    /* @Hook */
    public function woocommerce_process_product_meta($product_id)
    {
        // Save Regular Price
        if (isset($_POST['simple' . self::$taxRegularPriceMeta])) {

            $val = trim($_POST['simple' . self::$taxRegularPriceMeta]);
            update_post_meta($product_id, self::$taxRegularPriceMeta, $val);
        }

        // Save Sale Price
        if (isset($_POST['simple' . self::$taxSalePriceMeta])) {

            $regular_price = trim($_POST['simple' . self::$taxRegularPriceMeta]);
            $sale_price = trim($_POST['simple' . self::$taxSalePriceMeta]);
            if (empty($sale_price) || (float)$sale_price < 1) {

                update_post_meta($product_id, self::$taxSalePriceMeta, '');
                update_post_meta($product_id, self::$taxPriceMeta, trim($regular_price));
            } else {

                $price = $regular_price;
                if (is_numeric($sale_price) and (float)$sale_price < (float)$regular_price) {
                    $price = $sale_price;
                }

                update_post_meta($product_id, self::$taxSalePriceMeta, (float)$sale_price);
                update_post_meta($product_id, self::$taxPriceMeta, $price);
            }
        }
    }

    /* @Hook */
    public function woocommerce_variation_options_tab($loop, $variation_data, $variation)
    {

        // Regular Price
        woocommerce_wp_text_input([
            'label' => 'قیمت عادی با ارزش افزوده (' . get_woocommerce_currency_symbol() . ')',
            'placeholder' => '',
            'class' => '',
            'style' => '',
            'wrapper_class' => 'form-row',
            'value' => self::get_product_regular_price_with_tax($variation->ID),
            'id' => self::$taxRegularPriceMeta . '[' . $loop . ']',
            'name' => self::$taxRegularPriceMeta . '[' . $loop . ']',
            'type' => 'text',
            'desc_tip' => true,
            'description' => 'لطفا قیمت عادی محصول را با ارزش افزوده وارد نمایید؟',
            'data_type' => '',
            'custom_attributes' => ''
        ]);

        // Sale Price
        woocommerce_wp_text_input([
            'label' => 'قیمت فروش ویژه با ارزش افزوده (' . get_woocommerce_currency_symbol() . ')',
            'placeholder' => '',
            'class' => '',
            'style' => '',
            'wrapper_class' => 'form-row',
            'value' => self::get_product_sale_price_with_tax($variation->ID),
            'id' => self::$taxSalePriceMeta . '[' . $loop . ']',
            'name' => self::$taxSalePriceMeta . '[' . $loop . ']',
            'type' => 'text',
            'desc_tip' => true,
            'description' => 'لطفا قیمت فروش ویژه محصول را با ارزش افزوده وارد نمایید؟',
            'data_type' => '',
            'custom_attributes' => ''
        ]);
    }

    /* @Hook */
    public function woocommerce_save_product_variation($variation_id, $loop)
    {

        // Save Regular Price
        if (isset($_POST[self::$taxRegularPriceMeta][$loop])) {

            $val = trim($_POST[self::$taxRegularPriceMeta][$loop]);
            update_post_meta($variation_id, self::$taxRegularPriceMeta, $val);
        }

        // Save Sale Price
        if (isset($_POST[self::$taxSalePriceMeta][$loop])) {

            $regular_price = trim($_POST[self::$taxRegularPriceMeta][$loop]);
            $sale_price = trim($_POST[self::$taxSalePriceMeta][$loop]);
            if (empty($sale_price) || (float)$sale_price < 1) {

                update_post_meta($variation_id, self::$taxSalePriceMeta, '');
                update_post_meta($variation_id, self::$taxPriceMeta, trim($regular_price));

            } else {

                $price = $regular_price;
                if (is_numeric($sale_price) and (float)$sale_price < (float)$regular_price) {
                    $price = $sale_price;
                }

                update_post_meta($variation_id, self::$taxSalePriceMeta, (float)$sale_price);
                update_post_meta($variation_id, self::$taxPriceMeta, $price);
            }
        }
    }

    /* @Method */
    public static function get_customer_type()
    {
        $typeId = \WC_Customer_Type\WooCommerce::get_session_customer_type_checkout();
        return apply_filters('wp_hinza_get_customer_type_at_type', $typeId);
    }

    /**
     * Custom Price Hook
     */

    /* @Hook */
    public function product_regular_price($regular_price, $product)
    {
        // Check Is Admin
        if (is_admin()) {
            return $regular_price;
        }

        // Check Customer Type
        $customerTypeId = self::get_customer_type();

        // Check Price For Customer Type
        $option = Option::get();
        if ($option['price_customer_type_' . $customerTypeId] == "1") {
            return $regular_price;
        }

        // Get Product ID
        $product_id = $product->get_id();

        // Get Product Tax Price
        $regular_price_with_tax = self::get_product_regular_price_with_tax($product_id);
        if (!empty($regular_price_with_tax)) {
            return $regular_price_with_tax;
        }

        // Return
        return $regular_price;
    }

    /* @Hook */
    public function product_sale_price($sale_price, $product)
    {
        // Check Is Admin
        if (is_admin()) {
            return $sale_price;
        }

        // Check Customer Type
        $customerTypeId = self::get_customer_type();

        // Check Price For Customer Type
        $option = Option::get();
        if ($option['price_customer_type_' . $customerTypeId] == "1") {
            return $sale_price;
        }

        // Get Product ID
        $product_id = $product->get_id();

        // Get Product Tax Price
        $sale_price_with_tax = self::get_product_sale_price_with_tax($product_id);

        // Return
        return ($sale_price_with_tax < 1 ? '' : $sale_price);
    }

    /* @Hook */
    public function product_price($price, $product)
    {
        // Check Is Admin
        if (is_admin()) {
            return $price;
        }

        // Check Customer Type
        $customerTypeId = self::get_customer_type();

        // Check Price For Customer Type
        $option = Option::get();
        if ($option['price_customer_type_' . $customerTypeId] == "1") {
            return $price;
        }

        // Get Product ID
        $product_id = $product->get_id();

        // Get Product Tax Price
        $price_with_tax = self::get_product_price_with_tax($product_id);
        if (empty($price_with_tax)) {
            return $price;
        }

        // Return
        return $price_with_tax;
    }

    /* @Hook */
    public function variation_prices_hash($hash)
    {
        // Check Is Admin
        if (is_admin()) {
            return $hash;
        }

        // Check Customer Type
        $customerTypeId = self::get_customer_type();

        // Check Price For Customer Type
        $option = Option::get();
        if ($option['price_customer_type_' . $customerTypeId] == "1") {
            return $hash;
        }

        // Setup Hash
        $hash[] = 'price_' . get_current_user_id() . '_' . time();

        // Return
        return $hash;
    }

    /* @Hook */
    public function woocommerce_variation_prices_price($variation_get_price_edit, $variation, $product)
    {
        // Check Is Admin
        if (is_admin()) {
            return $variation_get_price_edit;
        }

        // Check Customer Type
        $customerTypeId = self::get_customer_type();

        // Check Price For Customer Type
        $option = Option::get();
        if ($option['price_customer_type_' . $customerTypeId] == "1") {
            return $variation_get_price_edit;
        }

        // Check Number Children
        $variable_ids = $product->get_children();
        if (empty($variable_ids)) {
            return $variation_get_price_edit;
        }

        // Get Variation Prices
        $getVariationPrice = $this->getVariableDefaultPrices($product->get_id(), $variable_ids);

        // Check No Sku in variation list
        if ($getVariationPrice['has_sku'] == "no") {
            return $variation_get_price_edit;
        }

        // Get Min and Max From Current
        $current_price_range = $this->calculateRangPrice($getVariationPrice['children']);

        // Check Type is Min Or Max
        $type = null;
        if ($variation_get_price_edit == $current_price_range['min_variation_price']) {
            $type = 'min';
        }
        if ($variation_get_price_edit == $current_price_range['max_variation_price']) {
            $type = 'max';
        }

        // Check Not Type
        if (is_null($type)) {
            return $variation_get_price_edit;
        }

        // Check Children From API
        $newVariablePrice = $this->getVariablePrices($product->get_id(), $variable_ids);
        $new_list_calculate = $this->calculateRangPrice($newVariablePrice['children']);

        // Return Data
        return ($type == "min" ? $new_list_calculate['min_variation_price'] : $new_list_calculate['max_variation_price']);
    }

    /* @Method */
    public function getVariablePrices($product_id, $children_ids)
    {
        // Setup Cache Key
        $cacheKey = 'productTaxVariationPrice_variation_' . $product_id;

        // Check in non-persist cache
        if (isset($GLOBALS[$cacheKey])) {
            return $GLOBALS[$cacheKey];
        }

        // Create init Variable
        $children = [];
        $has_sku = 'yes';

        foreach ($children_ids as $child) {
            $children[$child]['regular_price'] = get_post_meta($child, self::$taxRegularPriceMeta, true);
            $children[$child]['sale_price'] = get_post_meta($child, self::$taxSalePriceMeta, true);
            $children[$child]['price'] = get_post_meta($child, self::$taxPriceMeta, true);
        }

        return $GLOBALS[$cacheKey] = compact(
            "has_sku",
            "children"
        );
    }

    /* @Method */
    public function getVariableDefaultPrices($product_id, $children_ids)
    {
        // Setup Cache Key
        $cacheKey = 'productTaxVariationDefaultPrice_variation_' . $product_id;

        // Check in non-persist cache
        if (isset($GLOBALS[$cacheKey])) {
            return $GLOBALS[$cacheKey];
        }

        // Create init Variable
        $children = [];
        $has_sku = 'yes';

        foreach ($children_ids as $child) {
            $children[$child]['regular_price'] = get_post_meta($child, '_regular_price', true);
            $children[$child]['sale_price'] = get_post_meta($child, '_sale_price', true);
            $children[$child]['price'] = get_post_meta($child, '_price', true);
        }

        return $GLOBALS[$cacheKey] = compact(
            "has_sku",
            "children"
        );
    }

    /* @Method */
    public function calculateRangPrice($children = array()): array
    {

        $min_variation_price = $min_variation_regular_price = $min_variation_sale_price = $max_variation_price = $max_variation_regular_price = $max_variation_sale_price = '';

        foreach ($children as $child) {
            $child_regular_price = $child['regular_price'];
            $child_sale_price = $child['sale_price'];
            $child_price = $child['price'];

            if ($child_price == '' && $child_regular_price == '')
                continue;

            // Regular prices
            if ($child_regular_price != '') {
                if (!is_numeric($min_variation_regular_price) || $child_regular_price < $min_variation_regular_price)
                    $min_variation_regular_price = $child_regular_price;

                if (!is_numeric($max_variation_regular_price) || $child_regular_price > $max_variation_regular_price)
                    $max_variation_regular_price = $child_regular_price;
            }

            // Sale prices
            if ($child_sale_price != '') {
                if ($child_price == $child_sale_price) {
                    if (!is_numeric($min_variation_sale_price) || $child_sale_price < $min_variation_sale_price)
                        $min_variation_sale_price = $child_sale_price;

                    if (!is_numeric($max_variation_sale_price) || $child_sale_price > $max_variation_sale_price)
                        $max_variation_sale_price = $child_sale_price;
                }
            }

            // Actual prices
            if ($child_price != '') {
                if ($child_price > $max_variation_price)
                    $max_variation_price = $child_price;

                if ($min_variation_price == '' || $child_price < $min_variation_price)
                    $min_variation_price = $child_price;
            }
        }

        return compact(
            "min_variation_price",
            "min_variation_regular_price",
            "min_variation_sale_price",
            "max_variation_price",
            "max_variation_regular_price",
            "max_variation_sale_price"
        );
    }

    /**
     * Cart Hooks
     */

    /* @Hook */
    public function woocommerce_before_calculate_totals($cart)
    {
        // Disable For Admin
        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }

        // Avoiding hook repetition (when using price calculations for example | optional)
        if (did_action('woocommerce_before_calculate_totals') >= 2)
            return;

        // Check Customer Type
        $customerTypeId = self::get_customer_type();

        // Check Price For Customer Type
        $option = Option::get();
        if ($option['price_customer_type_' . $customerTypeId] == "1") {
            return;
        }

        // Check Type Two Price For This Customer Type
        if ($option['calculate_tax_customer_type_' . $customerTypeId] == "2") {
            return;
        }

        // Remove All Filter Price
        $this->disable_product_price_filter();

        // Set Default Price
        foreach ($cart->get_cart() as $hash => $cart_item) {

            // get the data of the cart item
            $product_id = $cart_item['product_id'];
            $variation_id = $cart_item['variation_id'];
            $id = (!empty($variation_id) ? $variation_id : $product_id);
            $quantity = $cart_item['quantity'];
            $product = $cart_item['data'];

            // Get Default Price
            $default_price = get_post_meta($id, '_price', true);
            $with_tax_price = self::get_product_price_with_tax($id);
            $taxable = (float)$with_tax_price - (float)$default_price;
            if ($taxable > 0) {

                // @see https://rudrastyh.com/woocommerce/change-product-prices-in-cart.html
                // $cart_item['data']->set_tax_class('zero-rate');
                $cart_item['data']->set_price((float)$default_price);

                // Setup Line Tax
                $cart_item['line_tax'] = $taxable * $quantity;
                $cart_item['line_subtotal_tax'] = $taxable * $quantity;
                foreach (['subtotal', 'total'] as $k) {

                    if (isset($cart_item['line_tax_data'][$k])) {
                        $first_key = key($cart_item['line_tax_data'][$k]);
                        $cart_item['line_tax_data'][$k][$first_key] = $taxable * $quantity;
                    }
                }
            }
        }
    }

    /* @Hook */
    public function woocommerce_calculate_item_totals_taxes($tax_array, $cart_item, $cart)
    {
        // Check Count
        if (count($tax_array) > 1) {
            return $tax_array;
        }

        // Disable For Admin
        if (is_admin() && !defined('DOING_AJAX')) {
            return $tax_array;
        }

        // Check Customer Type
        $customerTypeId = WooCommerce::get_customer_type();

        // Check Price For Customer Type
        $option = Option::get();
        if ($option['price_customer_type_' . $customerTypeId] == "1") {
            return $tax_array;
        }

        // Check Type Two Price For This Customer Type
        if ($option['calculate_tax_customer_type_' . $customerTypeId] == "2") {
            return $tax_array;
        }

        // Remove All Filter Price
        $this->disable_product_price_filter();

        // Get Cart Item Array
        $cart_item = $cart_item->object;

        // get the data of the cart item
        $product_id = $cart_item['product_id'];
        $variation_id = $cart_item['variation_id'];
        $id = (!empty($variation_id) ? $variation_id : $product_id);
        $quantity = $cart_item['quantity'];
        $product = $cart_item['data'];

        // Get Default Price
        $default_price = get_post_meta($id, '_price', true);
        $with_tax_price = WooCommerce::get_product_price_with_tax($id);
        $taxable = (float)$with_tax_price - (float)$default_price;
        if ($taxable > 0) {
            $first_key = key($tax_array);
            $tax_array[$first_key] = $taxable * $quantity;
        }

        return $tax_array;
    }
}

new WooCommerce();