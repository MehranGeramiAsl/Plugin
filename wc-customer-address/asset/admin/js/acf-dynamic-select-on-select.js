jQuery(document).ready(function ($) {
    if (typeof acf == 'undefined') {
        return;
    }

    // Province
    $(document).on('change', '[data-key="field_65f2ef41c54fa"] .acf-input select', function (e) {
        update_states_on_country_change(e, $);
    });
    $('[data-key="field_65f2ef41c54fa"] .acf-input select').trigger('ready');

    // City
    $(document).on('change', '[data-key="field_65f2ef59c54fb"] .acf-input select', function (e) {
        update_cities_on_state_change(e, $);
    });
    $('[data-key="field_65f2ef59c54fb"] .acf-input select').trigger('ready');
});

function update_states_on_country_change(e, $) {
    if (this.request) {
        // if a recent request has been made abort it
        this.request.abort();
    }

    // get the city select field, and remove all exisiting choices
    var states_select = $('[data-key="field_65f2ef59c54fb"] select');
    states_select.empty();

    // get the target of the event and then get the value of that field
    var target = $(e.target);
    var state = target.val();

    if (!state) {
        // no state selected
        // don't need to do anything else
        return;
    }

    // set and prepare data for ajax
    var data = {
        action: 'wc_customer_address_load_state_choices',
        country: state
    };

    // call the acf function that will fill in other values
    // like post_id and the acf nonce
    data = acf.prepareForAjax(data);

    // make ajax request
    // instead of going through the acf.ajax object to make requests like in <5.7
    // we need to do a lot of the work ourselves, but other than the method that's called
    // this has not changed much
    this.request = $.ajax({
        url: acf.get('ajaxurl'), // acf stored value
        data: data,
        type: 'post',
        dataType: 'json',
        success: function (json) {
            if (!json) {
                return;
            }
            // add the new options to the city field
            for (i = 0; i < json.length; i++) {
                var city_item = '<option value="' + json[i]['value'] + '">' + json[i]['label'] + '</option>';
                states_select.append(city_item);
            }
        }
    });

}

function update_cities_on_state_change(e, $) {
    if (this.request) {
        // if a recent request has been made abort it
        this.request.abort();
    }

    // get the city select field, and remove all exisiting choices
    var cities_select = $('[data-key="field_65f2ef6ac54fc"] select');
    cities_select.empty();

    // get the target of the event and then get the value of that field
    var target = $(e.target);
    var state = target.val();

    if (!state) {
        // no state selected
        // don't need to do anything else
        return;
    }

    // set and prepare data for ajax
    var data = {
        action: 'wc_customer_address_get_city_list',
        state: state
    };

    // call the acf function that will fill in other values
    // like post_id and the acf nonce
    data = acf.prepareForAjax(data);

    // make ajax request
    // instead of going through the acf.ajax object to make requests like in <5.7
    // we need to do a lot of the work ourselves, but other than the method that's called
    // this has not changed much
    this.request = $.ajax({
        url: acf.get('ajaxurl'), // acf stored value
        data: data,
        type: 'post',
        dataType: 'json',
        success: function (json) {
            if (!json) {
                return;
            }
            // add the new options to the city field
            for (i = 0; i < json.length; i++) {
                var city_item = '<option value="' + json[i]['value'] + '">' + json[i]['label'] + '</option>';
                cities_select.append(city_item);
            }
        }
    });

}