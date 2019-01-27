<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	 * @param string $locale Language locale like "en" or "en_UK"
	 */
	public function __construct( $view, $locale = 'en', $pattern = null )
	{
		parent::__construct( $view );

		if( $pattern !== null ) {
			$this->formatter = new \NumberFormatter( $locale, \NumberFormatter::PATTERN_DECIMAL, $pattern );
		} else {
			$this->formatter = new \NumberFormatter( $locale, \NumberFormatter::DECIMAL );
		}
	}


	/**
	 * Returns the formatted number.
	 *
	 * @param integer|double|string $number Number to format
	 * @param integer|null $decimals Number of decimals behind the decimal point or null for default value
	 * @return string Formatted number
	 */
	public function transform( $number, $decimals = null )
	{
		$this->formatter->setAttribute( \NumberFormatter::FRACTION_DIGITS, (int) $decimals ?: 2 );
		return $this->formatter->format( (double) $number );
	}
}