<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Flag calling in_array(), array_search() and array_keys() without true as the third parameter.
 *
 * @link    https://vip.wordpress.com/documentation/code-review-what-we-look-for/#using-in_array-without-strict-parameter
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.9.0
 * @since   0.10.0 This sniff not only checks for `in_array()`, but also `array_search()` and `array_keys()`.
 *                 The sniff no longer needlessly extends the WordPress_Sniffs_Arrays_ArrayAssignmentRestrictionsSniff
 *                 which it didn't use.
 * @since   0.11.0 Refactored to extend the new WordPress_AbstractFunctionParameterSniff.
 */
class WordPress_Sniffs_PHP_StrictInArraySniff extends WordPress_AbstractFunctionParameterSniff {

	/**
	 * The group name for this group of functions.
	 *
	 * @since 0.11.0
	 *
	 * @var string
	 */
	protected $group_name = 'strict';

	/**
	 * List of array functions to which a $strict parameter can be passed.
	 *
	 * The $strict parameter is the third and last parameter for each of these functions.
	 *
	 * The array_keys() function only requires the $strict parameter when the optional
	 * second parameter $search has been set.
	 *
	 * @link http://php.net/in-array
	 * @link http://php.net/array-search
	 * @link http://php.net/array-keys
	 *
	 * @since 0.10.0
	 * @since 0.11.0 Renamed from $array_functions to $target_functions.
	 *
	 * @var array <string function_name> => <bool always needed ?>
	 */
	protected $target_functions = array(
		'in_array'     => true,
		'array_search' => true,
		'array_keys'   => false,
	);

	/**
	 * Process the parameters of a matched function.
	 *
	 * @since 0.11.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param array  $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched.
	 * @param array  $parameters      Array with information about the parameters.
	 *
	 * @return void
	 */
	public function process_parameters( $stackPtr, $group_name, $matched_content, $parameters ) {
		// Check if the strict check is actually needed.
		if ( false === $this->target_functions[ $matched_content ] ) {
			if ( count( $parameters ) === 1 ) {
				return;
			}
		}

		// We're only interested in the third parameter.
		if ( false === isset( $parameters[3] ) || 'true' !== $parameters[3]['raw'] ) {
			$this->phpcsFile->addWarning(
				'Not using strict comparison for %s; supply true for third argument.',
				( isset( $parameters[3]['start'] ) ? $parameters[3]['start'] : $parameters[1]['start'] ),
				'MissingTrueStrict',
				array( $matched_content )
			);
			return;
		}
	}

	/**
	 * Process the function if no parameters were found.
	 *
	 * @since 0.11.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param array  $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched.
	 *
	 * @return void
	 */
	public function process_no_parameters( $stackPtr, $group_name, $matched_content ) {
		$this->phpcsFile->addError( 'Missing arguments to %s.', $stackPtr, 'MissingArguments', array( $matched_content ) );
	}

} // End class.
