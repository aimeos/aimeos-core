<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Number;


/**
 * View helper class for formatting numbers.
 *
 * @package MW
 * @subpackage View
 */
class Locale
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Number\Iface
{
	private $formatter;


	/**
	 * Initializes the Number view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param string|null $locale Language locale like "en" or "en_UK" or null for default
	 * @param string|null $pattern ICU pattern to format the number or null for default formatting
	 */
	public function __construct( \Aimeos\MW\View\Iface $view, string $locale = null, string $pattern = null )
	{
		parent::__construct( $view );

		if( $pattern !== null ) {
			$this->formatter = new \NumberFormatter( $locale ?: 'en', \NumberFormatter::PATTERN_DECIMAL, $pattern );
		} else {
			$this->formatter = new \NumberFormatter( $locale ?: 'en', \NumberFormatter::DECIMAL );
		}
	}


	/**
	 * Returns the formatted number.
	 *
	 * @param int|double|string $number Number to format
	 * @param int|null $decimals Number of decimals behind the decimal point or null for default value
	 * @return string Formatted number
	 */
	public function transform( $number, int $decimals = null ) : string
	{
		$this->formatter->setAttribute( \NumberFormatter::FRACTION_DIGITS, $decimals !== null ? (int) $decimals : 2 );
		return $this->formatter->format( (double) $number );
	}
}
