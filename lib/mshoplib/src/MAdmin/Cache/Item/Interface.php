<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MAdmin
 * @subpackage Cache
 */


/**
 * MAdmin cache item Interface.
 *
 * @package MAdmin
 * @subpackage Cache
 */
interface MAdmin_Cache_Item_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the value associated to the key.
	 *
	 * @return string Returns the value of the item
	 */
	public function getValue();

	/**
	 * Sets the new value of the item.
	 *
	 * @param string $value Value of the item
	 */
	public function setValue( $value );

	/**
	 * Returns the expiration time of the item.
	 *
	 * @return string|null Expiration time of the item or null for no expiration
	 */
	public function getTimeExpire();

	/**
	 * Sets the new expiration time of the item.
	 *
	 * @param string|null Expiration time of the item or null for no expiration
	 */
	public function setTimeExpire( $timestamp );

	/**
	 * Returns the tags associated to the item.
	 *
	 * @return array Tags associated to the item
	 */
	public function getTags();

	/**
	 * Sets the new tags associated to the item.
	 *
	 * @param array Tags associated to the item
	 */
	public function setTags( array $tags );
}
