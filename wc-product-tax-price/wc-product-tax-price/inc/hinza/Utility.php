<?php

namespace WP_Hinza;

if (!class_exists("\WP_Hinza\Utility")) {
    class Utility
    {

        public static function wp_query($arg = [])
        {
            // Create Empty List
            $list = [];

            // Check Return
            $return = ($arg['return'] ?? 'ids');

            // Prepare Params
            $default = [
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => '-1',
                'order' => 'DESC',
                'fields' => ($arg['return'] ?? 'ids'),
                'cache_results' => !($return == "ids"),
                // @see https://10up.github.io/Engineering-Best-Practices/php/#performance
                'no_found_rows' => true,
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
                'suppress_filters' => true
            ];
            $args = wp_parse_args($arg, $default);

            // Get Data
            $query = new \WP_Query($args);

            // Get SQL
            //echo $query->request;
            //exit;

            // Check Return All
            if ($return == "all") {
                return $query->posts;
            }

            // Added To List
            foreach ($query->posts as $ID) {
                $list[] = $ID;
            }
            return $list;
        }

        public static function post_exist($ID, $post_type = false)
        {
            global $wpdb;

            $query = "SELECT count(*) FROM `$wpdb->posts` WHERE `ID` = $ID";
            if (!empty($post_type)) {
                $query .= " AND `post_type` = '$post_type'";
            }

            return ((int)$wpdb->get_var($query) > 0 ? true : false);
        }

        public static function getNumberPostComment($arg = array())
        {
            $default = array(
                'parent' => 0, // Count Only Parent 0
                'status' => 'approve',
                'type' => 'comment',
                'post_id' => 0,
                'number' => false,
                'order' => 'DESC',
                'orderby' => 'comment_ID',
                'hierarchical' => false, //@see https://wordpress.stackexchange.com/questions/265014/wp-comment-query-with-5-top-level-comments-per-page
                'count' => true,
                'update_comment_meta_cache' => false,
                'update_comment_post_cache' => false,
            );
            $args = wp_parse_args($arg, $default);
            $comments_count_query = new \WP_Comment_Query;
            $all = $comments_count_query->query($args);

            return $all;
        }

        /**
         * is_edit_page
         * function to check if the current page is a post edit page
         *
         * @param string $new_edit new|edit
         * @return boolean
         * @author Ohad Raz <admin@bainternet.info>
         *
         * @example global $typenow; (is_edit_page('new') and $typenow =="POST_TYPE")
         */
        public static function is_edit_page($new_edit = null)
        {
            global $pagenow;
            //make sure we are on the backend
            if (!is_admin()) return false;

            if ($new_edit == "edit")
                return in_array($pagenow, array('post.php'));
            elseif ($new_edit == "new") //check for new post page
                return in_array($pagenow, array('post-new.php'));
            else //check for either new or edit
                return in_array($pagenow, array('post.php', 'post-new.php'));
        }

        public static function user_id_exists($user)
        {
            global $wpdb;
            $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = %d", $user));
            if ($count == 1) {
                return true;
            }

            return false;
        }

        public static function getUsers($arg = [])
        {
            $list = [];
            $default = array(
                'fields' => array('id'),
                'orderby' => 'id',
                'order' => 'ASC',
                'count_total' => false
            );
            $args = wp_parse_args($arg, $default);

            $user_query = new \WP_User_Query($args);
            //[Get Request SQL]
            //echo $user_query->request;
            foreach ($user_query->get_results() as $user) {
                $list[] = $user->id;
            }

            return $list;
        }

        public static function admin_notice($text, $model = "info", $close_button = true, $echo = true, $style_extra = 'padding:12px;')
        {
            $text = '
        <div class="notice notice-' . $model . '' . ($close_button === true ? " is-dismissible" : "") . '">
           <div style="' . $style_extra . ' inline">' . $text . '</div>
        </div>
        ';
            if ($echo) {
                echo $text;
            } else {
                return $text;
            }
        }

        public static function inlineAdminNotice($type = 'error', $message = '', $args = [], $priority = 999)
        {
            add_action('admin_notices', function () use ($type, $message, $args) {
                self::admin_notice($message, $type);
                $_SERVER['REQUEST_URI'] = remove_query_arg(array_merge(['_alert_type', '_alert'], $args));
            }, $priority);
        }

        public static function json_exit($array)
        {
            wp_send_json($array);
            exit;
        }

        public static function is_request($type)
        {
            switch ($type) {
                case 'admin':
                    return is_admin();
                case 'ajax':
                    return defined('DOING_AJAX');
                case 'cron':
                    return defined('DOING_CRON');
                case 'frontend':
                    return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON');
            }
        }

        public static function per_number($number)
        {
            return str_replace(
                range(0, 9),
                array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'),
                $number
            );
        }

        public static function eng_number($number)
        {
            return str_replace(
                array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'),
                range(0, 9),
                $number
            );
        }

        public static function isPersianInput($input)
        {
            if (preg_match("/^[آ ا ب پ ت ث ج چ ح خ د ذ ر ز ژ س ش ص ض ط ظ ع غ ف ق ک گ ل م ن و ه ی]/", $input)) {
                return true;
            } else {
                return false;
            }
        }

        public static function isValidMobile($mobile)
        {
            $result = array(
                'success' => true,
                'text' => ''
            );

            //mobile Number character
            if (strlen($mobile) !== 11) {
                $result['text'] = 'شماره همراه 11 کاراکتر می باشد';
                $result['success'] = false;
            }

            //mobile start 09
            if (substr($mobile, 0, 2) !== "09") {
                $result['text'] = 'شماره همراه با 09 شروع می شود';
                $result['success'] = false;
            }

            //mobile numberic
            if (!is_numeric($mobile)) {
                $result['text'] = 'شماره همراه تنها شامل کاراکتر عدد می باشد';
                $result['success'] = false;
            }

            return $result;
        }

        public static function sanitizeMobile($mobile)
        {

            // Convert To English
            $mobile = self::eng_number($mobile);

            // Convert Plus To 00
            $mobile = (int)str_ireplace('+', '00', $mobile);

            // Get Only Numeric
            $mobile = preg_replace('/[^0-9]/', '', $mobile);

            // Check Character Mobile
            $forth_character = substr($mobile, 0, 4);
            if ($forth_character == "0098") {
                $mobile = substr($mobile, 4);
            }

            $twice_character = substr($mobile, 0, 2);
            if ($twice_character == "98") {
                $mobile = substr($mobile, 2);
            }

            $first_character = substr($mobile, 0, 1);
            if ($first_character == "9") {
                $mobile = '0' . $mobile;
            }

            return $mobile;
        }

        public static function getRandomString($length = 5): string
        {
            $stringSpace = 'abcdefghijklmnopqrstuvwxyz';
            $pieces = [];
            $max = mb_strlen($stringSpace, '8bit') - 1;
            for ($i = 0; $i < $length; ++$i) {
                $pieces[] = $stringSpace[random_int(0, $max)];
            }
            return implode('', $pieces);
        }

        public static function roundNumberDown($number, $digits = 10)
        {
            //@see https://stackoverflow.com/questions/41673933/round-to-nearest-and-least-multiple-of-10
            return floor($number / $digits) * $digits;
        }

    }
}


