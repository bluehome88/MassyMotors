<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Ensures filenames do not contain underscores.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#naming-conventions
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.1.0
 * @since   0.11.0 - This sniff will now also check for all lowercase file names.
 *                 - This sniff will now also verify that files containing a class start with `class-`.
 *                 - This sniff will now also verify that files in `wp-includes` containing
 *                   template tags end in `-template`. Based on @subpackage file DocBlock tag.
 *                 - This sniff will now allow for underscores in file names for certain theme
 *                   specific exceptions if the `$is_theme` property is set to `true`.
 * @since   0.12.0 - Now extends the `WordPress_Sniff` class.
 *
 * @uses    WordPress_Sniff::$custom_test_class_whitelist
 */
class WordPress_Sniffs_Files_FileNameSniff extends WordPress_Sniff {

	/**
	 * Regex for the theme specific exceptions.
	 *
	 * N.B. This regex currently does not allow for mimetype sublevel only file names,
	 * such as `plain.php`.
	 *
	 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
	 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#custom-taxonomies
	 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#custom-post-types
	 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#embeds
	 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#attachment
	 * @link https://developer.wordpress.org/themes/template-files-section/partial-and-miscellaneous-template-files/#content-slug-php
	 * @link https://wphierarchy.com/
	 * @link https://en.wikipedia.org/wiki/Media_type#Naming
	 *
	 * @since 0.11.0
	 *
	 * @var string
	 */
	const THEME_EXCEPTIONS_REGEX = '`
		^                    # Anchor to the beginning of the string.
		(?:
							 # Template prefixes which can have exceptions.
			(?:archive|category|content|embed|page|single|tag|taxonomy)
			-[^\.]+          # These need to be followed by a dash and some chars.
		|
			(?:application|audio|example|image|message|model|multipart|text|video) #Top-level mime-types
			(?:_[^\.]+)?     # Optionally followed by an underscore and a sub-type.
		)\.(?:php|inc)$      # End in .php (or .inc for the test files) and anchor to the end of the string.
	`Dx';

	/**
	 * Whether the codebase being sniffed is a theme.
	 *
	 * If true, it will allow for certain typical theme specific exceptions to the filename rules.
	 *
	 * @since 0.11.0
	 *
	 * @var bool
	 */
	public $is_theme = false;

	/**
	 * Whether to apply strict class file name rules.
	 *
	 * If true, it demands that classes are prefixed with `class-` and that the rest of the
	 * file name reflects the class name.
	 *
	 * @since 0.11.0
	 *
	 * @var bool
	 */
	public $strict_class_file_names = true;

	/**
	 * Historical exceptions in WP core to the class name rule.
	 *
	 * @since 0.11.0
	 *
	 * @var array
	 */
	private $class_exceptions = array(
		'class.wp-dependencies.php' => true,
		'class.wp-scripts.php'      => true,
		'class.wp-styles.php'       => true,
	);

	/**
	 * Unit test version of the historical exceptions in WP core.
	 *
	 * @since 0.11.0
	 *
	 * @var array
	 */
	private $unittest_class_exceptions = array(
		'class.wp-dependencies.inc' => true,
		'class.wp-scripts.inc'      => true,
		'class.wp-styles.inc'       => true,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		if ( defined( 'PHP_CODESNIFFER_IN_TESTS' ) ) {
			$this->class_exceptions = array_merge( $this->class_exceptions, $this->unittest_class_exceptions );
		}

		return array( T_OPEN_TAG );
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {

		// Usage of `strip_quotes` is to ensure `stdin_path` passed by IDEs does not include quotes.
		$file = $this->strip_quotes( $this->phpcsFile->getFileName() );
		if ( 'STDIN' === $file ) {
			return;
		}

		$fileName = basename( $file );
		$expected = strtolower( str_replace( '_', '-', $fileName ) );

		/*
		 * Generic check for lowercase hyphenated file names.
		 */
		if ( $fileName !== $expected && ( false === $this->is_theme || 1 !== preg_match( self::THEME_EXCEPTIONS_REGEX, $fileName ) ) ) {
			$this->phpcsFile->addError(
				'Filenames should be all lowercase with hyphens as word separators. Expected %s, but found %s.',
				0,
				'NotHyphenatedLowercase',
				array( $expected, $fileName )
			);
		}
		unset( $expected );

		/*
		 * Check files containing a class for the "class-" prefix and that the rest of
		 * the file name reflects the class name.
		 */
		if ( true === $this->strict_class_file_names ) {
			$has_class = $this->phpcsFile->findNext( T_CLASS, $stackPtr );
			if ( false !== $has_class && false === $this->is_test_class( $has_class ) ) {
				$class_name = $this->phpcsFile->getDeclarationName( $has_class );
				$expected   = 'class-' . strtolower( str_replace( '_', '-', $class_name ) );

				if ( substr( $fileName, 0, -4 ) !== $expected && ! isset( $this->class_exceptions[ $fileName ] ) ) {
					$this->phpcsFile->addError(
						'Class file names should be based on the class name with "class-" prepended. Expected %s, but found %s.',
						0,
						'InvalidClassFileName',
						array(
							$expected . '.php',
							$fileName,
						)
					);
				}
				unset( $expected );
			}
		}

		/*
		 * Check non-class files in "wp-includes" with a "@subpackage Template" tag for a "-template" suffix.
		 */
		if ( false !== strpos( $file, DIRECTORY_SEPARATOR . 'wp-includes' . DIRECTORY_SEPARATOR ) ) {
			$subpackage_tag = $this->phpcsFile->findNext( T_DOC_COMMENT_TAG, $stackPtr, null, false, '@subpackage' );
			if ( false !== $subpackage_tag ) {
				$subpackage = $this->phpcsFile->findNext( T_DOC_COMMENT_STRING, $subpackage_tag );
				if ( false !== $subpackage ) {
					$fileName_end = substr( $fileName, -13 );
					$has_class    = $this->phpcsFile->findNext( T_CLASS, $stackPtr );

					if ( ( 'Template' === trim( $this->tokens[ $subpackage ]['content'] )
						&& $this->tokens[ $subpackage_tag ]['line'] === $this->tokens[ $subpackage ]['line'] )
						&& ( ( ! defined( 'PHP_CODESNIFFER_IN_TESTS' ) && '-template.php' !== $fileName_end )
						|| ( defined( 'PHP_CODESNIFFER_IN_TESTS' ) && '-template.inc' !== $fileName_end ) )
						&& false === $has_class
					) {
						$this->phpcsFile->addError(
							'Files containing template tags should have "-template" appended to the end of the file name. Expected %s, but found %s.',
							0,
							'InvalidTemplateTagFileName',
							array(
								substr( $fileName, 0, -4 ) . '-template.php',
								$fileName,
							)
						);
					}
				}
			}
		}

		// Only run this sniff once per file, no need to run it again.
		return ( $this->phpcsFile->numTokens + 1 );

	} // End process_token().

} // End class.
