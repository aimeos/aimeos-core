<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Group\Item;


/**
 * Interface for group objects
 *
 * @package MShop
 * @subpackage Customer
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface
{
	/**
	 * Returns the code of the group
	 *
	 * @return string Code of the group
	 */
	public function getCode() : string;

	/**
	 * Sets the new code of the group
	 *
	 * @param string $value Code of the group
	 * @return \Aimeos\MShop\Group\Item\Iface Customer group item for chaining method calls
	 */
	public function setCode( string $value ) : \Aimeos\MShop\Group\Item\Iface;

	/**
	 * Returns the label of the group
	 *
	 * @return string Label of the group
	 */
	public function getLabel() : string;

	/**
	 * Sets the new label of the group
	 *
	 * @param string $value Label of the group
	 * @return \Aimeos\MShop\Group\Item\Iface Customer group item for chaining method calls
	 */
	public function setLabel( string $value ) : \Aimeos\MShop\Group\Item\Iface;
}
