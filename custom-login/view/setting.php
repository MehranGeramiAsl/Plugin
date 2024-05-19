<?php
global $title;
$col_color = isset($custom_login_options['column_color']) ? $custom_login_options['column_color'] : '';
$background = isset($custom_login_options['background']) ? $custom_login_options['background'] : '';
$css = isset($custom_login_options['css']) ? $custom_login_options['css'] : '';
?>
<h1><?php echo $title; ?></h1>
<form action="" method="POST">
    <table class="form-table">
    <tbody>
    <tr>
            <th scope="row">
        
                <label for="col-color"> رنگ ستون</label>
            </th>
            <td>
                <input type="text" name="column_color" id="col-color" value="<?php echo esc_attr($col_color);?>">
            </td>
        </tr>
        <th scope="row">
        
                <label for="background"> رنگ ستون</label>
            </th>
            <td>
                <input type="url" name="background" id="background" value="<?php echo esc_url($background);?>">
                <button type="button" id="background-selector" class="button button-secondary">انتخاب تصویر پس زمینه</button>
                <img src="<?php echo esc_url($background); ?>" alt="" style="width: 160px; height: auto;" id="background-preview">
            </td>
        </tr>
        <tr>
            <th scope="row">
        
                <label for="css_code"> استایل سفارشی </label>
            </th>
            <td>
                <textarea name="css_code" id="css_code" cols="30" rows="10"><?php echo esc_textarea($css);?></textarea>
            </td>
        </tr>
    </tbody>

>
<p class="submit">
<button class="button button-primary">ذخیره تغییرات</button>
</p>



</form>