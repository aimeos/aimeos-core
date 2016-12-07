<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	 * Returns the localized name of the type
	 *
	 * @return string|null Localized name of the type
	 */
	public function getTypeName();

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
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setTypeId( $typeid );
}
