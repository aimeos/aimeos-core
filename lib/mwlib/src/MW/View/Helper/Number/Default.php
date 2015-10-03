<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for formatting numbers.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Number_Default
	extends MW_View_Helper_Base
	implements MW_View_Helper_Iface
{
	private $dsep;
	private $tsep;


	/**
	 * Initializes the Number view helper.
	 *
	 * @param MW_View_Iface $view View instance with registered view helpers
	 * @param string $decimalSeparator Character for the decimal point
	 * @param string $thousandsSeperator Character separating groups of thousands
	 */
	public function __construct( $view, $decimalSeparator = '.', $thousandsSeperator = '' )
	{
		parent::__construct( $view );

		$this->dsep = $decimalSeparator;
		$this->tsep = $thousandsSeperator;
	}


	/**
	 * Returns the formatted number.
	 *
	 * @param int|float|decimal $number Number to format
	 * @param integer $decimals Number of decimals behind the decimal point
	 * @return string Formatted number
	 */
	public function transform( $number, $decimals = 2 )
	{
		return number_format( $number, $decimals, $this->dsep, $this->tsep );
	}
}