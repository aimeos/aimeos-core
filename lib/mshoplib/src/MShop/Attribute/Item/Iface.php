<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Attribute
 */


namespace Aimeos\MShop\Attribute\Item;


/**
 * Generic interface for all attribute items.
 *
 * @package MShop
 * @subpackage Attribute
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Domain\Iface,
		\Aimeos\MShop\Common\Item\ListsRef\Iface, \Aimeos\MShop\Common\Item\Position\Iface,
		\Aimeos\MShop\Common\Item\PropertyRef\Iface, \Aimeos\MShop\Common\Item\Status\Iface,
		\Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Returns the unique key of the attribute item
	 *
	 * @return string Unique key consisting of domain/type/code
	 */
	public function getKey() : string;

	/**
	 * Returns the code of the attribute item.
	 *
	 * @return string Returns the code of the attribute item
	 */
	public function getCode() : string;

	/**
	 * Sets the code for the attribute item.
	 *
	 * @param string $code Code of the attribute item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Attribute\Item\Iface;

	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel() : string;

	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Attribute\Item\Iface;

}
