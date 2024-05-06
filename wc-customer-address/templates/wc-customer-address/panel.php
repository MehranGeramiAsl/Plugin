<?php
// Show Message
if (!empty($message)) {
    ?>
    <div class="woocommerce-message" role="alert">
        <?php echo $message; ?>
    </div>
    <?php
}

// Edit Form
if (!empty($_GET['ID'])) {
    ?>
    <h3>ویرایش آدرس</h3>
    <form class="woocommerce-EditAccountForm edit-account"
          action="<?php echo \WC_Customer_Address\WooCommerce::get_page_url(); ?>" method="post">
        <?php wp_nonce_field('wc_customer_address_nonce', 'wc_customer_address_nonce'); ?>
        <input type="hidden" value="<?php echo $item['id']; ?>" name="ID">

        <?php
        if ($option['limit_iran_country'] == "2") {
            ?>

            <!-- Country -->
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="balanceChange">
                    کشور
                    <span class="required">*</span>
                </label>
                <select name="country">
                    <?php
                    foreach ($country_choices as $loop_country_key => $loop_country_val) {
                        ?>
                        <option value="<?php echo $loop_country_key; ?>" <?php echo($default_country == $loop_country_key ? 'selected' : ''); ?>>
                            <?php
                            echo $loop_country_val;
                            ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
                <span id="country-loading-ajax" style="display:none;margin-top: 15px;">لطفا کمی صبر کنید ...</span>
            </p>
            <div class="clear"></div>
            <?php
        }
        ?>

        <!-- State -->
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="balanceChange">
                استان
                <span class="required">*</span>
            </label>
            <select name="state">
                <?php
                foreach ($states['states'] as $loop_state_key => $loop_state_val) {
                    ?>
                    <option value="<?php echo $loop_state_key; ?>" <?php echo($item['state'] == $loop_state_key ? 'selected' : ''); ?>>
                        <?php
                        echo $loop_state_val;
                        ?>
                    </option>
                    <?php
                }
                ?>
            </select>
            <span id="state-loading-ajax" style="display:none;margin-top: 15px;">لطفا کمی صبر کنید ...</span>
        </p>
        <div class="clear"></div>

        <!-- City -->
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="balanceChange">
                شهر
                <span class="required">*</span>
            </label>

            <span class="woocommerce-input-wrapper">
                <select name="city">
                <?php
                foreach ($cities as $loop_city_item) {
                    ?>
                    <option value="<?php echo $loop_city_item[0]; ?>" <?php echo($default_city == $loop_city_item[0] ? 'selected' : ''); ?>>
                        <?php
                        echo $loop_city_item[0];
                        ?>
                    </option>
                    <?php
                }
                ?>
                </select>
            </span>
        </p>
        <div class="clear"></div>

        <!-- Address -->
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="balanceChange">
                آدرس
                <span class="required">*</span>
            </label>

            <span class="woocommerce-input-wrapper">
                <input type="text"
                       class="input-text"
                       name="address"
                       id="address"
                       placeholder=""
                       required
                       value="<?php echo $item['address']; ?>">
            </span>
        </p>
        <div class="clear"></div>

        <!-- phone -->
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="balanceChange">
                تلفن تماس
                <span class="required">*</span>
            </label>

            <span class="woocommerce-input-wrapper">
                <input type="text"
                       class="input-text"
                       name="phone"
                       id="phone"
                       placeholder=""
                       required
                       style="text-align: left; direction: ltr;"
                       value="<?php echo $item['phone']; ?>">
            </span>
        </p>
        <div class="clear"></div>

        <!-- zipcode -->
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="balanceChange">
                کد پستی
            </label>

            <span class="woocommerce-input-wrapper">
                <input type="text"
                       class="input-text"
                       name="zipcode"
                       id="zipcode"
                       style="text-align: left; direction: ltr;"
                       placeholder=""
                       value="<?php echo $item['zipcode']; ?>">
            </span>
        </p>
        <div class="clear"></div>

        <!-- Default -->
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="balanceChange">
                پیش فرض
            </label>

            <select name="default">
                <?php
                foreach ($default_choices as $loop_default_key => $loop_default_val) {
                    ?>
                    <option value="<?php echo $loop_default_key; ?>" <?php echo($item['defaultRaw'] == $loop_default_key ? 'selected' : ''); ?>>
                        <?php
                        echo $loop_default_val;
                        ?>
                    </option>
                    <?php
                }
                ?>
            </select>
        </p>
        <div class="clear"></div>

        <!-- Submit -->
        <p>
            <button type="submit" class="woocommerce-Button button" value="ویرایش آدرس">
                ویرایش آدرس
            </button>
        </p>
    </form>
    <?php
}

// Show User Addresses
if (!empty($getCustomerAddress) and !isset($_GET['ID']) and !isset($_GET['redirect'])) {
    ?>

    <h3>آدرس های من</h3>
    <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
        <thead>
        <tr>
            <th class="woocommerce-orders-table__header"><span class="nobr">تاریخ ایجاد</span></th>
            <th class="woocommerce-orders-table__header"><span class="nobr">کشور</span></th>
            <th class="woocommerce-orders-table__header"><span class="nobr">استان</span></th>
            <th class="woocommerce-orders-table__header"><span class="nobr">شهر</span></th>
            <th class="woocommerce-orders-table__header"><span class="nobr">آدرس</span></th>
            <th class="woocommerce-orders-table__header"><span class="nobr">تلفن</span></th>
            <th class="woocommerce-orders-table__header"><span class="nobr">کدپستی</span></th>
            <th class="woocommerce-orders-table__header"><span class="nobr">پیش فرض</span></th>
            <th class="woocommerce-orders-table__header"><span class="nobr"></span></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($getCustomerAddress as $item) {
            ?>
            <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">

                <td class="woocommerce-orders-table__cell" data-title="تاریخ ایجاد">
                    <?php
                    echo \WP_Hinza\ParsiDate::jdate("Y-m-d", $item['date'], 'eng');
                    ?>
                </td>

                <td class="woocommerce-orders-table__cell" data-title="کشور">
                    <?php
                    echo $item['country_name'];
                    ?>
                </td>

                <td class="woocommerce-orders-table__cell" data-title="استان">
                    <?php
                    echo $item['state_name'];
                    ?>
                </td>

                <td class="woocommerce-orders-table__cell" data-title="شهر">
                    <?php
                    echo $item['city'];
                    ?>
                </td>

                <td class="woocommerce-orders-table__cell" data-title="آدرس">
                    <?php
                    echo $item['address'];
                    ?>
                </td>

                <td class="woocommerce-orders-table__cell" data-title="تلفن">
                    <?php
                    echo(empty($item['phone']) ? '_' : $item['phone']);
                    ?>
                </td>

                <td class="woocommerce-orders-table__cell" data-title="کدپستی">
                    <?php
                    echo(empty($item['zipcode']) ? '_' : $item['zipcode']);
                    ?>
                </td>

                <td class="woocommerce-orders-table__cell" data-title="پیش فرض">
                    <?php
                    echo($item['default'] === true ? 'آری' : '_');
                    ?>
                </td>

                <td class="woocommerce-orders-table__cell" data-title="عملیات">
                    <a href="<?php echo \WC_Customer_Address\WooCommerce::get_page_url(['ID' => $item['id']]); ?>">ویرایش</a>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <?php
}

// Add Form
if (!isset($_GET['ID']) and $allowedAddAddress === true) {
    ?>
    <h3>افزودن آدرس</h3>
    <form class="woocommerce-EditAccountForm edit-account"
          action="<?php echo \WC_Customer_Address\WooCommerce::get_page_url(); ?>" method="post">
        <?php wp_nonce_field('wc_customer_address_nonce', 'wc_customer_address_nonce'); ?>
        <input type="hidden" value="0" name="ID">
        <input type="hidden" name="redirect"
               value="<?php echo(!empty($_GET['redirect']) ? urldecode($_GET['redirect']) : ''); ?>">

        <?php
        if ($option['limit_iran_country'] == "2") {
            ?>

            <!-- Country -->
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="balanceChange">
                    کشور
                    <span class="required">*</span>
                </label>
                <select name="country">
                    <?php
                    foreach ($country_choices as $loop_country_key => $loop_country_val) {
                        ?>
                        <option value="<?php echo $loop_country_key; ?>" <?php echo($default_country == $loop_country_key ? 'selected' : ''); ?>>
                            <?php
                            echo $loop_country_val;
                            ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
                <span id="country-loading-ajax" style="display:none;margin-top: 15px;">لطفا کمی صبر کنید ...</span>
            </p>
            <div class="clear"></div>
            <?php
        }
        ?>

        <!-- State -->
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="balanceChange">
                استان
                <span class="required">*</span>
            </label>
            <select name="state">
                <?php
                foreach ($states['states'] as $loop_state_key => $loop_state_val) {
                    ?>
                    <option value="<?php echo $loop_state_key; ?>" <?php echo($default_state == $loop_state_key ? 'selected' : ''); ?>>
                        <?php
                        echo $loop_state_val;
                        ?>
                    </option>
                    <?php
                }
                ?>
            </select>
            <span id="state-loading-ajax" style="display:none;margin-top: 15px;">لطفا کمی صبر کنید ...</span>
        </p>
        <div class="clear"></div>

        <!-- City -->
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="balanceChange">
                شهر
                <span class="required">*</span>
            </label>

            <span class="woocommerce-input-wrapper">
                <select name="city">
                <?php
                foreach ($cities as $loop_city_item) {
                    ?>
                    <option value="<?php echo $loop_city_item[0]; ?>" <?php echo($default_city == $loop_city_item[0] ? 'selected' : ''); ?>>
                        <?php
                        echo $loop_city_item[0];
                        ?>
                    </option>
                    <?php
                }
                ?>
            </select>
            </span>
        </p>
        <div class="clear"></div>

        <!-- Address -->
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="balanceChange">
                آدرس
                <span class="required">*</span>
            </label>

            <span class="woocommerce-input-wrapper">
                <input type="text"
                       class="input-text"
                       name="address"
                       id="address"
                       placeholder=""
                       required
                       value="">
            </span>
        </p>
        <div class="clear"></div>

        <!-- phone -->
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="balanceChange">
                تلفن تماس
                <span class="required">*</span>
            </label>

            <span class="woocommerce-input-wrapper">
                <input type="text"
                       class="input-text"
                       name="phone"
                       id="phone"
                       placeholder=""
                       required
                       style="text-align: left; direction: ltr;"
                       value="">
            </span>
        </p>
        <div class="clear"></div>

        <!-- zipcode -->
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="balanceChange">
                کد پستی
            </label>

            <span class="woocommerce-input-wrapper">
                <input type="text"
                       class="input-text"
                       name="zipcode"
                       id="zipcode"
                       style="text-align: left; direction: ltr;"
                       placeholder=""
                       value="">
            </span>
        </p>
        <div class="clear"></div>

        <!-- Default -->
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="balanceChange">
                پیش فرض
            </label>

            <select name="default">
                <?php
                foreach ($default_choices as $loop_default_key => $loop_default_val) {
                    ?>
                    <option value="<?php echo $loop_default_key; ?>">
                        <?php
                        echo $loop_default_val;
                        ?>
                    </option>
                    <?php
                }
                ?>
            </select>
        </p>
        <div class="clear"></div>

        <!-- Submit -->
        <p>
            <button type="submit" class="woocommerce-Button button" value="افزودن آدرس">
                افزودن آدرس
            </button>
        </p>
    </form>
    <?php
}

// Set jQuery Script
if ($option['limit_iran_country'] == "2") {
    ?>
    <script>
        jQuery(document).ready(function ($) {
            $(document).on('change', 'select[name=country]', function (e) {

                var states_select = $('select[name=state]');
                states_select.empty();

                var target = $(this);
                var state = target.val();

                if (!state) {
                    return;
                }

                var loadingCountry = $("#country-loading-ajax");
                loadingCountry.show();

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {
                        action: 'wc_customer_address_get_states_list',
                        country: state
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function (json) {
                        if (!json) {
                            return;
                        }
                        for (i = 0; i < json.length; i++) {
                            var state_item = '<option value="' + json[i]['value'] + '">' + json[i]['label'] + '</option>';
                            states_select.append(state_item);
                        }
                    },
                    complete: function (params) {
                        loadingCountry.hide();
                    }
                });
            });
        });
    </script>
    <?php
} else {
    ?>
    <script>
        jQuery(document).ready(function ($) {
            $(document).on('change', 'select[name=state]', function (e) {

                var cities_select = $('select[name=city]');
                cities_select.empty();

                var target = $(this);
                var state = target.val();

                if (!state) {
                    return;
                }

                var loadingState = $("#state-loading-ajax");
                loadingState.show();

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {
                        action: 'wc_customer_address_get_city_list',
                        state: state
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function (json) {
                        if (!json) {
                            return;
                        }
                        for (i = 0; i < json.length; i++) {
                            var state_item = '<option value="' + json[i]['value'] + '">' + json[i]['label'] + '</option>';
                            cities_select.append(state_item);
                        }
                    },
                    complete: function (params) {
                        loadingState.hide();
                    }
                });
            });
        });
    </script>
    <?php
}
