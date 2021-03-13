<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MShop
 * @subpackage Rule
 */


namespace Aimeos\MShop\Rule\Item;


/**
 * Generic interface for rules created and saved by rule managers.
 *
 * @package MShop
 * @subpackage Rule
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Config\Iface,
		\Aimeos\MShop\Common\Item\Position\Iface, \Aimeos\MShop\Common\Item\Status\Iface,
		\Aimeos\MShop\Common\Item\Time\Iface, \Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Returns the name of the rule item.
	 *
	 * @return string Label of the rule item
	 */
	public function getLabel() : string;

	/**
	 * Sets the new label of the rule item.
	 *
	 * @param string $label New label of the rule item
	 * @return \Aimeos\MShop\Rule\Item\Iface Rule item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Rule\Item\Iface;

	/**
	 * Returns the provider of the rule.
	 *
	 * @return string Rule provider which is the short rule class name
	 */
	public function getProvider() : string;

	/**
	 * Sets the new provider of the rule item which is the short name of the rule class name.
	 *
	 * @param string $provider Rule provider, esp. short rule class name
	 * @return \Aimeos\MShop\Rule\Item\Iface Rule item for chaining method calls
	 */
	public function setProvider( string $provider ) : \Aimeos\MShop\Rule\Item\Iface;
}
