<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Discourages the use of various native PHP functions and suggests alternatives.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.11.0
 */
class WordPress_Sniffs_PHP_DiscouragedPHPFunctionsSniff extends WordPress_AbstractFunctionRestrictionsSniff {

	/**
	 * Groups of functions to discourage.
	 *
	 * Example: groups => array(
	 *  'lambda' => array(
	 *      'type'      => 'error' | 'warning',
	 *      'message'   => 'Use anonymous functions instead please!',
	 *      'functions' => array( 'file_get_contents', 'create_function' ),
	 *  )
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(
			'create_function' => array(
				'type'      => 'warning',
				'message'   => '%s() is discouraged, please use anonymous functions instead.',
				'functions' => array(
					'create_function',
				),
			),

			'serialize' => array(
				'type'      => 'warning',
				'message'   => '%s() found. Serialized data has known vulnerability problems with Object Injection. JSON is generally a better approach for serializing data. See https://www.owasp.org/index.php/PHP_Object_Injection',
				'functions' => array(
					'serialize',
					'unserialize',
				),
			),

			'urlencode' => array(
				'type'      => 'warning',
				'message'   => '%s() should only be used when dealing with legacy applications rawurlencode() should now be used instead. See http://php.net/manual/en/function.rawurlencode.php and http://www.faqs.org/rfcs/rfc3986.html',
				'functions' => array(
					'urlencode',
				),
			),

			'runtime_configuration' => array(
				'type'      => 'warning',
				'message'   => '%s() found. Changing configuration at runtime is rarely necessary.',
				'functions' => array(
					'error_reporting',
					'ini_alter',
					'ini_restore',
					'ini_set',
					'apache_setenv',
					'putenv',
					'set_include_path',
					'restore_include_path',
					// This alias was DEPRECATED in PHP 5.3.0, and REMOVED as of PHP 7.0.0.
					'magic_quotes_runtime',
					// Warning This function was DEPRECATED in PHP 5.3.0, and REMOVED as of PHP 7.0.0.
					'set_magic_quotes_runtime',
					// Warning This function was removed from most SAPIs in PHP 5.3.0, and was removed from PHP-FPM in PHP 7.0.0.
					'dl',
				),
			),

			'system_calls' => array(
				'type'      => 'warning',
				'message'   => '%s() found. PHP system calls are often disabled by server admins.',
				'functions' => array(
					'exec',
					'passthru',
					'proc_open',
					'shell_exec',
					'system',
					'popen',
				),
			),

			'obfuscation' => array(
				'type'      => 'warning',
				'message'   => '%s() can be used to obfuscate code which is strongly discouraged. Please verify that the function is used for benign reasons.',
				'functions' => array(
					'base64_decode',
					'base64_encode',
					'convert_uudecode',
					'convert_uuencode',
					'str_rot13',
				),
			),

		);
	} // end getGroups()

} // End class.
