<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
interface Iface extends \Aimeos\MW\View\Helper\Iface
{
	/**
	 * Returns the formatted number.
	 *
	 * @param int|float|decimal $number Number to format
	 * @param integer|null $decimals Number of decimals behind the decimal point or null for default value
	 * @return string Formatted number
	 */
	public function transform( $number, $decimals = null );
}