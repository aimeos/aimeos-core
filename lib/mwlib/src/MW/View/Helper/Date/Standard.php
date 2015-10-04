<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for formatting dates.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Date_Standard
	extends MW_View_Helper_Base
	implements MW_View_Helper_Iface
{
	private $format;


	/**
	 * Initializes the Date view helper.
	 *
	 * @param MW_View_Iface $view View instance with registered view helpers
	 * @param string $format New date format
	 * @see http://php.net/manual/en/datetime.createfromformat.php
	 */
	public function __construct( $view, $format = '' )
	{
		parent::__construct( $view );

		$this->format = $format;
	}


	/**
	 * Returns the formatted date.
	 *
	 * @param string $date ISO date and time
	 * @return string Formatted date
	 */
	public function transform( $date )
	{
		return DateTime::createFromFormat( 'Y-m-d H:i:s', $date )->format( $this->format );
	}
}