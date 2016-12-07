<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Parentid;


/**
 * Interface for items with parent/child relationship
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Returns the parent ID this item belongs to
	 *
	 * @return string Parent ID of the item
	 */
	public function getParentId();


	/**
	 * Sets the new parent ID this item belongs to
	 *
	 * @param string $parentid New parent ID of the item
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setParentId( $parentid );
}
