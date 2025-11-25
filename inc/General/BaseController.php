<?php
/**
* @package  Sanasana
 * 
 * 
 * ACTIVATION HOOKS
 */

 namespace SanasanaInit\General;

 class BaseController
 {
     protected $plugin_path;
     protected $plugin_url;
     protected $plugin_name;
     protected $site_logo_url;
     protected $version;
 
     public function __construct()
     {
         $this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
         $this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
         $this->plugin_name = plugin_basename(dirname(__FILE__, 3));
         $this->site_logo_url = get_site_icon_url();
         $this->version = '1.0.0';
     }
 
     public function get_site_logo_url()
     {
         return $this->site_logo_url;
     }
	 	 
     public function get_current_lang() {
        $current_url = $_SERVER['REQUEST_URI'];
        $lang_param = strpos($current_url, '/en/') !== false ? 'en' : 'es';
        return $lang_param;
     }

     public function get_affiliation_url($plan_crm_id = null) {
        $base_url = get_option('sanasana_frontend_base_url', '');
        $affiliation_path = get_option('sanasana_affiliation_path', ''); 
        $plan_id_parameter = get_option('sanasana_plan_id_parameter', '');
        $lang_param = $this->get_current_lang();
        $plan_url = $base_url . $affiliation_path;
        
        $query_params = [];
        if ($plan_crm_id) {
            $query_params[$plan_id_parameter] = $plan_crm_id;
        }
        if ($lang_param) {
            $query_params['lang'] = $lang_param;
        }
        if ($query_params && count($query_params) > 0) {
            $query_string = http_build_query($query_params);
            $plan_url .= '?' . $query_string;
        }
        return $plan_url;
    }

    public function get_post_from_current_url($post_type)
    {
        global $wp;
        $current_url = home_url($wp->request);
        $slug = basename($current_url);
        $post = get_page_by_path($slug, OBJECT, $post_type);
        return $post;
    }

    public function get_login_url() {
        $base_url = get_option('sanasana_frontend_base_url', '');
        $login_path = get_option('sanasana_login_path', '');
        $lang_param = $this->get_current_lang();
        $login_url = $base_url . $login_path;
        $query_params = [];
        if ($lang_param) {
            $query_params['lang'] = $lang_param;
        }
        if ($query_params && count($query_params) > 0) {
            $query_string = http_build_query($query_params);
            $login_url .= '?' . $query_string;
        }
        return $login_url;
    } 
	 
 }