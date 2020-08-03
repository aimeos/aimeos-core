<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Time;


/**
 * Common interface for items having types.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Returns the date/time, the availability of the item will start
	 *
	 * @return string|null ISO date in "YYYY-MM-DD hh:mm:ss" format or null for no date
	 */
	public function getDateStart() : ?string;


	/**
	 * Sets the date/time, the availability of the item will start
	 *
	 * @return string|null $date ISO date in "YYYY-MM-DD hh:mm:ss" format or null for no date
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setDateStart( ?string $date ) : \Aimeos\MShop\Common\Item\Iface;


	/**
	 * Returns the date/time, the availability of the item will end
	 *
	 * @return string|null ISO date in "YYYY-MM-DD hh:mm:ss" format or null for no date
	 */
	public function getDateEnd() : ?string;


	/**
	 * Sets the date/time, the availability of the item will end
	 *
	 * @return string|null $date ISO date in "YYYY-MM-DD hh:mm:ss" format or null for no date
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setDateEnd( ?string $date ) : \Aimeos\MShop\Common\Item\Iface;
}
