

console.log(jQuery);
window.addEventListener('load', function() {

let submi_btn = document.getElementById('wp-submit');
let user_field = document.getElementById('user_login');
let user_pass = document.getElementById('user_pass');

submi_btn.disabled = true;

let pass_valid = false , user_valid = false;



user_field.addEventListener('input', function(){

user_valid = this.value.length >= login_js_data.username_min_lenght;
submi_btn.disabled = !(user_valid && pass_valid);

});

user_pass.addEventListener('input', function(){

    pass_valid = this.value.length >= login_js_data.password_min_lenght;
    submi_btn.disabled = !(user_valid && pass_valid);
    });

});