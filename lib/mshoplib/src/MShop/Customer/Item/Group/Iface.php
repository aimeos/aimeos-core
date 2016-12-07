<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Customer\Item\Group;


/**
 * Interface for customer group objects
 *
 * @package MShop
 * @subpackage Customer
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface
{
	/**
	 * Returns the code of the customer group
	 *
	 * @return string Code of the customer group
	 */
	public function getCode();

	/**
	 * Sets the new code of the customer group
	 *
	 * @param string $value Code of the customer group
	 * @return \Aimeos\MShop\Customer\Item\Group\Iface Customer group item for chaining method calls
	 */
	public function setCode( $value );

	/**
	 * Returns the label of the customer group
	 *
	 * @return string Label of the customer group
	 */
	public function getLabel();

	/**
	 * Sets the new label of the customer group
	 *
	 * @param string $value Label of the customer group
	 * @return \Aimeos\MShop\Customer\Item\Group\Iface Customer group item for chaining method calls
	 */
	public function setLabel( $value );
}
