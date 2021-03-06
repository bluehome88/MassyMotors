<?php
/**
 * Module Name: Monitor
 * Module Description: Receive immediate notifications if your site goes down, 24/7.
 * Jumpstart Description: Receive immediate notifications if your site goes down, 24/7.
 * Sort Order: 28
 * Recommendation Order: 10
 * First Introduced: 2.6
 * Requires Connection: Yes
 * Auto Activate: No
 * Module Tags: Recommended
 * Feature: Security, Jumpstart
 * Additional Search Queries: monitor, uptime, downtime, monitoring
 */

class Jetpack_Monitor {

	public $module = 'monitor';

	function __construct() {
		add_action( 'jetpack_modules_loaded', array( $this, 'jetpack_modules_loaded' ) );
		add_action( 'jetpack_activate_module_monitor', array( $this, 'activate_module' ) );
	}

	public function activate_module() {
		if ( Jetpack::is_user_connected() ) {
			self::update_option_receive_jetpack_monitor_notification( true );
		}
	}

	public function jetpack_modules_loaded() {
		Jetpack::enable_module_configurable( $this->module );
		Jetpack::module_configuration_load( $this->module, array( $this, 'jetpack_configuration_load' ) );
		Jetpack::module_configuration_screen( $this->module, array( $this, 'jetpack_configuration_screen' ) );
	}

	public function jetpack_configuration_load() {
		if ( Jetpack::is_user_connected() && ! self::is_active() ) {
			Jetpack::deactivate_module( $this->module );
			Jetpack::state( 'message', 'module_deactivated' );
			wp_safe_redirect( Jetpack::admin_url( 'page=jetpack' ) );
			die();
		}
		if ( ! empty( $_POST['action'] ) && $_POST['action'] == 'monitor-save' ) {
			check_admin_referer( 'monitor-settings' );
			$this->update_option_receive_jetpack_monitor_notification( isset( $_POST['receive_jetpack_monitor_notification'] ) );
			Jetpack::state( 'message', 'module_configured' );
			wp_safe_redirect( Jetpack::module_configuration_url( $this->module ) );
			exit;
		}
	}

	public function jetpack_configuration_screen() {
		?>
		<p><?php esc_html_e( 'Nobody likes downtime, and that\'s why Jetpack Monitor is on the job, keeping tabs on your site by checking it every five minutes. As soon as any downtime is detected, you will receive an email notification alerting you to the issue. That way you can act quickly, to get your site back online again!', 'jetpack' ); ?>
		<p><?php esc_html_e( 'We???ll also let you know as soon as your site is up and running, so you can keep an eye on total downtime.', 'jetpack'); ?></p>
		<div class="narrow">
		<?php if ( Jetpack::is_user_connected() && current_user_can( 'manage_options' ) ) : ?>
			<?php $user_email = Jetpack::get_connected_user_email(); ?>
			<form method="post" id="monitor-settings">
				<input type="hidden" name="action" value="monitor-save" />
				<?php wp_nonce_field( 'monitor-settings' ); ?>

				<table id="menu" class="form-table">
						<tr>
						<th scope="row">
							<?php _e( 'Notifications', 'jetpack' ); ?>
						</th>
						<td>
							<label for="receive_jetpack_monitor_notification">
									<input type="checkbox" name="receive_jetpack_monitor_notification" id="receive_jetpack_monitor_notification" value="receive_jetpack_monitor_notification"<?php checked( self::user_receives_notifications() ); ?> />
								<span><?php _e( 'Receive Monitor Email Notifications.' , 'jetpack'); ?></span>
							</label>
							<p class="description"><?php printf( __( 'Emails will be sent to %s (<a href="%s">Edit</a>)', 'jetpack' ), $user_email, 'https://wordpress.com/settings/account/'); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		<?php else : ?>
			<p><?php _e( 'This profile is not currently linked to a WordPress.com Profile.', 'jetpack' ); ?></p>
		<?php endif; ?>
		</div>
		<?php
	}

	public function is_active() {
		Jetpack::load_xml_rpc_client();
		$xml = new Jetpack_IXR_Client( array(
			'user_id' => get_current_user_id()
		) );
		$xml->query( 'jetpack.monitor.isActive' );
		if ( $xml->isError() ) {
			wp_die( sprintf( '%s: %s', $xml->getErrorCode(), $xml->getErrorMessage() ) );
		}
		return $xml->getResponse();
	}

	public function update_option_receive_jetpack_monitor_notification( $value ) {
		Jetpack::load_xml_rpc_client();
		$xml = new Jetpack_IXR_Client( array(
			'user_id' => get_current_user_id()
		) );
		$xml->query( 'jetpack.monitor.setNotifications', (bool) $value );

		if ( $xml->isError() ) {
			wp_die( sprintf( '%s: %s', $xml->getErrorCode(), $xml->getErrorMessage() ) );
		}

		// To be used only in Jetpack_Core_Json_Api_Endpoints::get_remote_value.
		update_option( 'monitor_receive_notifications', (bool) $value );

		return true;
	}

	/**
	 * Checks the status of notifications for current Jetpack site user.
	 *
	 * @since 2.8
	 * @since 4.1.0 New parameter $die_on_error.
	 *
	 * @param bool $die_on_error Whether to issue a wp_die when an error occurs or return a WP_Error object.
	 *
	 * @return boolean|WP_Error
	 */
	static function user_receives_notifications( $die_on_error = true ) {
		Jetpack::load_xml_rpc_client();
		$xml = new Jetpack_IXR_Client( array(
			'user_id' => get_current_user_id()
		) );
		$xml->query( 'jetpack.monitor.isUserInNotifications' );

		if ( $xml->isError() ) {
			if ( $die_on_error ) {
				wp_die( sprintf( '%s: %s', $xml->getErrorCode(), $xml->getErrorMessage() ) );
			} else {
				return new WP_Error( $xml->getErrorCode(), $xml->getErrorMessage(), array( 'status' => 400 ) );
			}
		}
		return $xml->getResponse();
	}

	public function activate_monitor() {
		Jetpack::load_xml_rpc_client();
		$xml = new Jetpack_IXR_Client( array(
			'user_id' => get_current_user_id()
		) );

		$xml->query( 'jetpack.monitor.activate' );

		if ( $xml->isError() ) {
			wp_die( sprintf( '%s: %s', $xml->getErrorCode(), $xml->getErrorMessage() ) );
		}
		return true;
	}

	public function deactivate_monitor() {
		Jetpack::load_xml_rpc_client();
		$xml = new Jetpack_IXR_Client( array(
			'user_id' => get_current_user_id()
		) );

		$xml->query( 'jetpack.monitor.deactivate' );

		if ( $xml->isError() ) {
			wp_die( sprintf( '%s: %s', $xml->getErrorCode(), $xml->getErrorMessage() ) );
		}
		return true;
	}

	/*
	 * Returns date of the last downtime.
	 *
	 * @since 4.0.0
	 * @return date in YYYY-MM-DD HH:mm:ss format
	 */
	public function monitor_get_last_downtime() {
//		if ( $last_down = get_transient( 'monitor_last_downtime' ) ) {
//			return $last_down;
//		}

		Jetpack::load_xml_rpc_client();
		$xml = new Jetpack_IXR_Client( array(
			'user_id' => get_current_user_id()
		) );

		$xml->query( 'jetpack.monitor.getLastDowntime' );

		if ( $xml->isError() ) {
			return new WP_Error( 'monitor-downtime', $xml->getErrorMessage() );
		}

		set_transient( 'monitor_last_downtime', $xml->getResponse(), 10 * MINUTE_IN_SECONDS );

		return $xml->getResponse();
	}

}

new Jetpack_Monitor;
