<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
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
	extends \Aimeos\MShop\Common\Manager\Base
{
	use \Aimeos\MShop\Common\Manager\ListsRef\Traits;
	use \Aimeos\MShop\Common\Manager\PropertyRef\Traits;


	/**
	 * Returns the price item with the lowest price for the given quantity.
	 *
	 * @param \Aimeos\Map $priceItems List of price items implementing \Aimeos\MShop\Price\Item\Iface
	 * @param float $quantity Number of products
	 * @param string|null $currencyId Three letter ISO currency code or null for all
	 * @param string|null $siteId Site ID of the prices which should be used
	 * @return \Aimeos\MShop\Price\Item\Iface Price item with the lowest price
	 * @throws \Aimeos\MShop\Price\Exception if no price item is available
	 */
	public function getLowestPrice( \Aimeos\Map $priceItems, float $quantity, string $currencyId = null,
		string $siteId = null ) : \Aimeos\MShop\Price\Item\Iface
	{
		$priceList = $this->getPriceList( $priceItems, $currencyId, $siteId );

		if( ( $price = $priceList->first() ) === null )
		{
			$msg = $this->context()->translate( 'mshop', 'Price item not available' );
			throw new \Aimeos\MShop\Price\Exception( $msg );
		}

		if( $price->getQuantity() > $quantity )
		{
			$msg = $this->context()->translate( 'mshop', 'Price for the given quantity "%1$s" not available' );
			throw new \Aimeos\MShop\Price\Exception( sprintf( $msg, $quantity ) );
		}

		return $this->call( 'calcLowestPrice', $priceList, $quantity );
	}


	/**
	 * Returns the lowest price for the given quantity
	 *
	 * @param \Aimeos\Map $priceList Associative list of quantity as keys and price item as value
	 * @param float $quantity Number of products
	 * @return \Aimeos\MShop\Price\Item\Iface Price item with the lowest price
	 */
	protected function calcLowestPrice( \Aimeos\Map $priceList, float $quantity ) : \Aimeos\MShop\Price\Item\Iface
	{
		$price = $priceList->first();

		foreach( $priceList as $qty => $priceItem )
		{
			// add $priceItem->getValue() < $price->getValue() to use lowest price regardless of quantity
			if( $quantity >= $qty && $price->getQuantity() < $qty ) {
				$price = $priceItem;
			}
		}

		return $price;
	}


	/**
	 * Returns the price items sorted by quantity
	 *
	 * @param \Aimeos\Map $priceItems List of price items implementing \Aimeos\MShop\Price\Item\Iface
	 * @param string|null $currencyId Three letter ISO currency code or null for all
	 * @param string|null $siteId Site ID of the prices which should be used
	 * @return \Aimeos\Map Associative list of quantity as keys and price item as value
	 * @throws \Aimeos\MShop\Price\Exception If an object is no price item
	 */
	protected function getPriceList( \Aimeos\Map $priceItems, ?string $currencyId, ?string $siteId ) : \Aimeos\Map
	{
		$list = map();
		$siteIds = $this->context()->locale()->getSitePath();
		$priceItems->implements( \Aimeos\MShop\Price\Item\Iface::class, true );

		foreach( $priceItems as $priceItem )
		{
			if( $currencyId && $currencyId !== $priceItem->getCurrencyId() ) {
				continue;
			}

			if( $siteId )
			{
				if( in_array( $siteId, $siteIds ) ) // if product is inherited, inherit price too
				{
					if( !in_array( $priceItem->getSiteId(), $siteIds ) ) {
						continue;
					}
				}
				elseif( $priceItem->getSiteId() !== $siteId ) // Use price from specific site originally passed as parameter
				{
					continue;
				}
			}

			$qty = (string) $priceItem->getQuantity();

			if( !isset( $list[$qty] ) || $list[$qty]->getValue() > $priceItem->getValue() ) {
				$list[$qty] = $priceItem;
			}
		}

		return $list->ksort();
	}
}
