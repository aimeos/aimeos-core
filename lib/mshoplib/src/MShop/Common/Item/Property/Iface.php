<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2018
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
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Typeid\Iface,
	\Aimeos\MShop\Common\Item\Parentid\Iface
{
	/**
	 * Returns the language id of the property item
	 *
	 * @return string Language ID of the property item
	 */
	public function getLanguageId();

	/**
	 * Sets the Language Id of the property item
	 *
	 * @param string $id New Language ID of the property item
	 * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
	 */
	public function setLanguageId( $id );

	/**
	 * Returns the value of the property item.
	 *
	 * @return string Value of the property item
	 */
	public function getValue();

	/**
	 * Sets the new value of the property item.
	 *
	 * @param string $value Value of the property item
	 * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
	 */
	public function setValue( $value );

}
