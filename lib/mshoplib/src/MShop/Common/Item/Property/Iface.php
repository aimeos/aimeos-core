<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Property;


/**
 * Common property item interface
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\TypeRef\Iface,
	\Aimeos\MShop\Common\Item\Parentid\Iface
{
	/**
	 * Returns the unique key of the property item
	 *
	 * @return string Unique key consisting of type/language/value
	 */
	public function getKey() : string;

	/**
	 * Returns the language id of the property item
	 *
	 * @return string|null Language ID of the property item
	 */
	public function getLanguageId() : ?string;

	/**
	 * Sets the Language Id of the property item
	 *
	 * @param string|null $id New Language ID of the property item
	 * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
	 */
	public function setLanguageId( ?string $id ) : \Aimeos\MShop\Common\Item\Property\Iface;

	/**
	 * Returns the value of the property item.
	 *
	 * @return string Value of the property item
	 */
	public function getValue() : string;

	/**
	 * Sets the new value of the property item.
	 *
	 * @param string $value Value of the property item
	 * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
	 */
	public function setValue( ?string $value ) : \Aimeos\MShop\Common\Item\Property\Iface;

}
