<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the PluginMenuSlug sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.3.0
 */
class WordPress_Tests_VIP_PluginMenuSlugUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			3  => 1,
			5  => 1,
			9  => 2,
			14 => ( PHP_VERSION_ID < 50300 ) ? 1 : 0, // PHPCS on PHP 5.2 does not recognize T_NS_SEPARATOR.
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
