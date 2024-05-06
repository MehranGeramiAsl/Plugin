<?php
defined('ABSPATH') || exit;
global $custom_style_notices;
?>

<form action="" method="POST">
    <?php if($custom_style_notices):?>
    <div class="notice notice-<?php echo $custom_style_notices['type'];?>">
        <p><?php echo $custom_style_notices['massege'];?></p>
    </div>
    <?php endif;?>
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row">
                <label for="custom-style">استایل سفارشی</label>
            </th>
            <td>
            <textarea  class="large-text code" name="custom-style" id="custom-style" cols="30" rows="10" placeholder="استایل سفارشی شما..."></textarea>
            
            </td>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <th scope="row">
                <label for="custom-style">اسکریپت سفارشی</label>
            </th>
            <td>
                <textarea  class="large-text code" name="custom-script" id="custom-script" cols="30" rows="10" placeholder="اسکریپت سفارشی شما..."></textarea>
            </td>
            
        </tr>
    </tbody>
</table>
<p class="submit">
<button class="button button-primary">ذخیره تغییرات</button>
</p>



</form>