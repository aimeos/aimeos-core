<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 * @package MShop
 * @subpackage Type
 */


namespace Aimeos\MShop\Type\Item;


/**
 * Generic interface for type items
 *
 * @package MShop
 * @subpackage Type
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Domain\Iface,
		\Aimeos\MShop\Common\Item\Position\Iface, \Aimeos\MShop\Common\Item\Status\Iface
{
	/**
	 * Returns the code of the type item
	 *
	 * @return string Code of the type item
	 */
	public function getCode() : string;

	/**
	 * Sets the code of the type item
	 *
	 * @param string $code New code of the type item
	 * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Common\Item\Iface;

	/**
	 * Returns the translated name for the type item
	 *
	 * @return string Translated name of the type item
	 */
	public function getName() : string;

	/**
	 * Returns the label of the type item
	 *
	 * @return string Label of the type item
	 */
	public function getLabel() : string;

	/**
	 * Sets the label of the type item
	 *
	 * @param string $label New label of the type item
	 * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Type\Item\Iface;

	/**
	 * Returns the translations of the type item label
	 *
	 * @return array Translations of the type item label
	 */
	public function getI18n() : array;

	/**
	 * Sets the translations of the type item label
	 *
	 * @param array $value New translations of the type item label
	 * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
	 */
	public function setI18n( array $value ) : \Aimeos\MShop\Type\Item\Iface;
}
