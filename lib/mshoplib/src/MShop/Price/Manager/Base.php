<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
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
	use \Aimeos\MShop\Common\Manager\ListRef\Traits;
	use \Aimeos\MShop\Common\Manager\PropertyRef\Traits;


	/**
	 * Returns the price item with the lowest price for the given quantity.
	 *
	 * @param \Aimeos\Map $priceItems List of price items implementing \Aimeos\MShop\Price\Item\Iface
	 * @param int $quantity Number of products
	 * @param string|null $currencyId Three letter ISO currency code or null for all
	 * @return \Aimeos\MShop\Price\Item\Iface Price item with the lowest price
	 * @throws \Aimeos\MShop\Price\Exception if no price item is available
	 */
	public function getLowestPrice( \Aimeos\Map $priceItems, int $quantity, string $currencyId = null ) : \Aimeos\MShop\Price\Item\Iface
	{
		$priceList = $this->getPriceList( $priceItems, $currencyId );

		if( ( $price = $priceList->first() ) === null )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Price item not available' );
			throw new \Aimeos\MShop\Price\Exception( $msg );
		}

		if( $price->getQuantity() > $quantity )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Price for the given quantity "%1$d" not available' );
			throw new \Aimeos\MShop\Price\Exception( sprintf( $msg, $quantity ) );
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
	 * @param \Aimeos\Map $priceItems List of price items implementing \Aimeos\MShop\Price\Item\Iface
	 * @param string|null $currencyId Three letter ISO currency code or null for all
	 * @return \Aimeos\Map Associative list of quantity as keys and price item as value
	 * @throws \Aimeos\MShop\Price\Exception If an object is no price item
	 */
	protected function getPriceList( \Aimeos\Map $priceItems, string $currencyId = null ) : \Aimeos\Map
	{
		$list = map();

		foreach( $priceItems as $priceItem )
		{
			self::checkClass( \Aimeos\MShop\Price\Item\Iface::class, $priceItem );

			if( $currencyId !== null && $currencyId !== $priceItem->getCurrencyId() ) {
				continue;
			}

			$qty = $priceItem->getQuantity();

			if( !isset( $list[$qty] ) || $list[$qty]->getValue() > $priceItem->getValue() ) {
				$list[$qty] = $priceItem;
			}
		}

		return $list->ksort();
	}
}
