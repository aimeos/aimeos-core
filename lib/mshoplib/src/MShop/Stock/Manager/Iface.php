<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Stock
 */


namespace Aimeos\MShop\Stock\Manager;


/**
 * Generic interface for stock managers
 * @package MShop
 * @subpackage Stock
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Decreases the stock level for the given product codes/quantity pairs and type
	 *
	 * @param array $pairs Associative list of product codes as keys and quantities as values
	 * @param string $type Unique code of the stock type
	 * @return \Aimeos\MShop\Stock\Manager\Iface Manager object for chaining method calls
	 */
	public function decrease( iterable $pairs, string $type = 'default' ) : \Aimeos\MShop\Stock\Manager\Iface;

	/**
	 * Increases the stock level for the given product codes/quantity pairs and type
	 *
	 * @param array $pairs Associative list of product codes as keys and quantities as values
	 * @param string $type Unique code of the type
	 * @return \Aimeos\MShop\Stock\Manager\Iface Manager object for chaining method calls
	 */
	public function increase( iterable $pairs, string $type = 'default' ) : \Aimeos\MShop\Stock\Manager\Iface;
}
