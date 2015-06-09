<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Price
 */


/**
 * Abstract class for all price managers with basic methods.
 *
 * @package MShop
 * @subpackage Price
 */
abstract class MShop_Price_Manager_Abstract
	extends MShop_Common_Manager_ListRef_Abstract
{
	/**
	 * Returns the price item with the lowest price for the given quantity.
	 *
	 * @param array $priceItems List of price items implementing MShop_Price_Item_Interface
	 * @param integer $quantity Number of products
	 * @param string|null $currencyId Three letter ISO currency code or null for all
	 * @throws MShop_Price_Exception if no price item is available
	 */
	public function getLowestPrice( array $priceItems, $quantity, $currencyId = null )
	{
		$priceList = array();

		foreach( $priceItems as $priceItem )
		{
			$iface = 'MShop_Price_Item_Interface';
			if( ( $priceItem instanceof $iface ) === false ) {
				throw new MShop_Price_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
			}

			if( $currencyId !== null && $currencyId !== $priceItem->getCurrencyId() ) {
				continue;
			}

			$priceList[ $priceItem->getQuantity() ] = $priceItem;
		}

		ksort( $priceList );

		if( ( $price = reset( $priceList ) ) === false ) {
			throw new MShop_Price_Exception( sprintf( 'Price item not available' ) );
		}

		if( $price->getQuantity() > $quantity )
		{
			$msg = sprintf( 'Price for the given quantity "%1$d" not available', $quantity );
			throw new MShop_Price_Exception( $msg );
		}

		foreach( $priceList as $qty => $priceItem )
		{
			if( $qty <= $quantity && $qty > $price->getQuantity() ) {
				$price = $priceItem;
			}
		}

		return clone $price;
	}
}
