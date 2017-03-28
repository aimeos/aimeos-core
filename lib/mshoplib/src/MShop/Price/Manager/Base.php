<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Price
 */


namespace Aimeos\MShop\Price\Manager;


/**
 * Abstract class for all price managers with basic methods.
 *
 * @package MShop
 * @subpackage Price
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\ListRef\Base
{
	/**
	 * Returns the price item with the lowest price for the given quantity.
	 *
	 * @param array $priceItems List of price items implementing \Aimeos\MShop\Price\Item\Iface
	 * @param integer $quantity Number of products
	 * @param string|null $currencyId Three letter ISO currency code or null for all
	 * @return \Aimeos\MShop\Price\Iface Price item with the lowest price
	 * @throws \Aimeos\MShop\Price\Exception if no price item is available
	 */
	public function getLowestPrice( array $priceItems, $quantity, $currencyId = null )
	{
		$priceList = $this->getPriceList( $priceItems, $currencyId );

		if( ( $price = reset( $priceList ) ) === false ) {
			throw new \Aimeos\MShop\Price\Exception( sprintf( 'Price item not available' ) );
		}

		if( $price->getQuantity() > $quantity )
		{
			$msg = sprintf( 'Price for the given quantity "%1$d" not available', $quantity );
			throw new \Aimeos\MShop\Price\Exception( $msg );
		}

		foreach( $priceList as $qty => $priceItem )
		{
			if( $qty <= $quantity && $qty > $price->getQuantity() ) {
				$price = $priceItem;
			}
		}

		return $price;
	}


	/**
	 * Returns the price items sorted by quantity
	 *
	 * @param array $priceItems List of price items implementing \Aimeos\MShop\Price\Item\Iface
	 * @param string|null $currencyId Three letter ISO currency code or null for all
	 * @return array Associative list of quantity as keys and price item as value
	 * @throws \Aimeos\MShop\Price\Exception If an object is no price item
	 */
	protected function getPriceList( array $priceItems, $currencyId )
	{
		$list = [];

		foreach( $priceItems as $priceItem )
		{
			$iface = '\\Aimeos\\MShop\\Price\\Item\\Iface';

			if( ( $priceItem instanceof $iface ) === false ) {
				throw new \Aimeos\MShop\Price\Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
			}

			if( $currencyId !== null && $currencyId !== $priceItem->getCurrencyId() ) {
				continue;
			}

			$qty = $priceItem->getQuantity();

			if( !isset( $list[$qty] ) || $list[$qty]->getValue() > $priceItem->getValue() ) {
				$list[$qty] = $priceItem;
			}
		}

		ksort( $list );

		return $list;
	}
}
