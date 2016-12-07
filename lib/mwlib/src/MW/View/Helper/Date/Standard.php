<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Date;


/**
 * View helper class for formatting dates.
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Date\Iface
{
	private $format;


	/**
	 * Initializes the Date view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
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
		return \DateTime::createFromFormat( 'Y-m-d H:i:s', $date )->format( $this->format );
	}
}