<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
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
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Number\Iface
{
	private $dsep;
	private $tsep;
	private $decimals;


	/**
	 * Initializes the Number view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param string $decimalSeparator Character for the decimal point
	 * @param string $thousandsSeperator Character separating groups of thousands
	 */
	public function __construct( $view, $decimalSeparator = '.', $thousandsSeperator = '', $decimals = 2 )
	{
		parent::__construct( $view );

		$this->dsep = $decimalSeparator;
		$this->tsep = $thousandsSeperator;
		$this->decimals = $decimals;
	}


	/**
	 * Returns the formatted number.
	 *
	 * @param int|float|decimal $number Number to format
	 * @param integer|null $decimals Number of decimals behind the decimal point or null for default value
	 * @return string Formatted number
	 */
	public function transform( $number, $decimals = null )
	{
		if( $decimals === null ) {
			$decimals = $this->decimals;
		}

		return number_format( $number, $decimals, $this->dsep, $this->tsep );
	}
}