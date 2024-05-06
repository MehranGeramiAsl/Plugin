<div class="woocommerce-billing-fields wc-customer-address-checkout">

    <a href="<?php echo \WC_Customer_Address\WooCommerce::get_page_url(['redirect' => urlencode(wc_get_checkout_url())]) ?>"
       class="button">
        افزودن آدرس جدید
    </a>

    <?php
    foreach ($getCustomerAddress as $item) {
        ?>
        <div
                class="wc-customer-address__box"
                data-id="<?php echo $item['id']; ?>"
                data-country="<?php echo esc_attr($item['country']); ?>"
                data-state="<?php echo esc_attr($item['state']); ?>"
                data-city="<?php echo esc_attr($item['city']); ?>"
                data-address="<?php echo esc_attr($item['address']); ?>"
                data-phone="<?php echo esc_attr($item['phone']); ?>"
                data-zipcode="<?php echo esc_attr($item['zipcode']); ?>">
            <p>
                استان،شهر:
                <span><?php echo $item['state_name'] . ' / ' . $item['city']; ?></span>
            </p>
            <p>
                آدرس:
                <span><?php echo $item['address']; ?><?php echo(!empty($item['zipcode']) ? ', ' . $item['zipcode'] : ''); ?></span>
            </p>
            <p>
                تلفن:
                <span><?php echo $item['phone']; ?></span>
            </p>
        </div>
        <?php
    }
    ?>
</div>
<style>
    .wc-customer-address-checkout {
        margin-bottom: 25px;
    }

    .wc-customer-address__box {
        border: 1px solid #e3e3e3;
        border-radius: 15px;
        padding: 15px 15px 0 0;
        margin: 15px auto;
        cursor: pointer;
    }

    .wc-customer-address__box.selected {
        border: 1px solid #975b12;
        background: rgb(251 195 28 / 23%);
    }
</style>
<script>
    jQuery(document).ready(function ($) {

        jQuery(document).on("click", ".wc-customer-address__box", function (e) {
            e.preventDefault();
            var _this = $(this);
            jQuery('.wc-customer-address__box').each(function () {
                jQuery(this).removeClass('selected');
            });
            _this.addClass("selected");


            jQuery("#billing_address_1").val(_this.attr('data-address'));
            jQuery("#billing_postcode").val(_this.attr('data-zipcode'));
            jQuery("#billing_phone").val(_this.attr('data-phone'));
            jQuery("#billing_city").val(_this.attr('data-city'));
            window.wc_change_city = _this.attr('data-city');
            let currentCountry = jQuery("#billing_country").val();
            jQuery("#billing_country").val(_this.attr('data-country')).trigger('change.select2');
            if (currentCountry !== _this.attr('data-country')) {

                jQuery("#billing_state").empty();
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {
                        action: 'wc_customer_address_get_states_list',
                        country: _this.attr('data-country')
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function (json) {
                        if (!json) {
                            return;
                        }
                        for (i = 0; i < json.length; i++) {
                            var state_item = '<option value="' + json[i]['value'] + '">' + json[i]['label'] + '</option>';
                            jQuery("#billing_state").append(state_item);
                        }
                        jQuery("#billing_state").val(_this.attr('data-state')).trigger('change.select2');
                    }
                });
            } else {
                jQuery("#billing_state").val(_this.attr('data-state')).trigger('change.select2');
            }
        });
    });
</script>
