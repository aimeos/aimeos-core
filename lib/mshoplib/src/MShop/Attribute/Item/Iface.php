<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Attribute
 */


/**
 * Generic interface for all attribute items.
 *
 * @package MShop
 * @subpackage Attribute
 */
interface MShop_Attribute_Item_Iface
	extends MShop_Common_Item_ListRef_Iface, MShop_Common_Item_Position_Iface, MShop_Common_Item_Typeid_Iface
{
	/**
	 * Returns the domain of the attribute item.
	 *
	 * @return string Returns the domain for this item e.g. text, media, price...
	 */
	public function getDomain();

	/**
	 * Set the name of the domain for this attribute item.
	 *
	 * @param string $domain Name of the domain e.g. text, media, price...
	 * @return void
	 */
	public function setDomain( $domain );

	/**
	 * Returns a unique code of the attribute item.
	 *
	 * @return string Returns the code of the attribute item
	 */
	public function getCode();

	/**
	 * Sets a unique code for the attribute item.
	 *
	 * @param string $code Code of the attribute item
	 * @return void
	 */
	public function setCode( $code );

	/**
	 * Returns the status (enabled/disabled) of the attribute item.
	 *
	 * @return integer Returns the status
	 */
	public function getStatus();

	/**
	 * Sets the new status of the attribute item.
	 *
	 * @param integer $status Status of attribute item
	 * @return void
	 */
	public function setStatus( $status );

	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel();

	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 * @return void
	 */
	public function setLabel( $label );

}
