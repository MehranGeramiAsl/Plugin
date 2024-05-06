<h4 style="margin-top: 0;">اطلاعات تحویل گیرنده</h4>
<?php

woocommerce_form_field(
    'receiver-full-name',
    [
        'type' => 'text',
        'class' => ['woocommerce-form-row woocommerce-form-row--wide form-row-wide'],
        'input_class' => [],
        'label' => 'نام و نام خانوادگی',
        'required' => $isRequireField,
        'default' => ''
    ],
    $checkout->get_value('receiver-full-name')
);

woocommerce_form_field(
    'receiver-phone',
    [
        'type' => 'text',
        'class' => ['woocommerce-form-row woocommerce-form-row--wide form-row-wide'],
        'input_class' => [],
        'label' => 'شماره همراه',
        'required' => $isRequireField,
        'default' => ''
    ],
    $checkout->get_value('receiver-phone')
);

