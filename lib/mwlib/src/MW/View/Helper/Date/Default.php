<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for formatting dates.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Date_Default
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	private $_format;


	/**
	 * Initializes the Date view helper.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 * @param string $format New date format
	 * @see http://php.net/manual/en/datetime.createfromformat.php
	 */
	public function __construct( $view, $format = '' )
	{
		parent::__construct( $view );

		$this->_format = $format;
	}


	/**
	 * Returns the formatted date.
	 *
	 * @param string $date ISO date and time
	 * @return string Formatted date
	 */
	public function transform( $date )
	{
		return DateTime::createFromFormat( 'Y-m-d H:i:s', $date )->format( $this->_format );
	}
}