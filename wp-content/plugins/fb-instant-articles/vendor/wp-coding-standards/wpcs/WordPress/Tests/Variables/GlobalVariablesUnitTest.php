<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the GlobalVariables sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.3.0
 */
class WordPress_Tests_Variables_GlobalVariablesUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			3   => 1,
			6   => 1,
			16  => 1,
			17  => 1,
			18  => 1,
			25  => 1,
			35  => 1,
			36  => 1,
			54  => 1,
			95  => 1,
			128 => 1,
			133 => 1,
		);

	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array();

	}

} // End class.
