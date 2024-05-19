<?php
global $title;
$col_color = isset($custom_login_options['column_color']) ? $custom_login_options['column_color'] : '';
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
    </tbody>

>
<p class="submit">
<button class="button button-primary">ذخیره تغییرات</button>
</p>



</form>