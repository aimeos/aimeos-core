<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Typeid;


/**
 * Common interface for items having types.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Returns the type of the item.
	 *
	 * @return string|null Type of the item
	 */
	public function getType();

	/**
	 * Returns the type ID of the item.
	 *
	 * @return integer|null Type ID of the item
	 */
	public function getTypeId();

	/**
	 * Sets the new type ID of the item.
	 *
	 * @param integer $typeid type ID of the item
	 * @return void
	 */
	public function setTypeId( $typeid );
}
