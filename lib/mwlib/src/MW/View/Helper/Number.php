<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 * @version $Id$
 */


/**
 * View helper class for formatting numbers.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Number
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	private $_dsep;
	private $_tsep;


	/**
	 * Initializes the Number view helper.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 * @param string $decimalSeparator Character for the decimal point
	 * @param string $thousandsSeperator Character separating groups of thousands
	 */
	public function __construct( $view, $decimalSeparator = '.', $thousandsSeperator = '' )
	{
		parent::__construct( $view );

		$this->_dsep = $decimalSeparator;
		$this->_tsep = $thousandsSeperator;
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
		return number_format( $number, $decimals, $this->_dsep, $this->_tsep );
	}
}