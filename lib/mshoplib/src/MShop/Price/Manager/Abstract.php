<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Price
 * @version $Id$
 */


/**
 * Abstract class for all price managers with basic methods.
 *
 * @package MShop
 * @subpackage Price
 */
abstract class MShop_Price_Manager_Abstract
	extends MShop_Common_Manager_Abstract
{
	/**
	 * Returns the price item with the lowest price for the given quantity.
	 *
	 * @param array $priceItems List of price items implementing MShop_Price_Item_Interface
	 * @param integer $quantity Number of products
	 * @throws MShop_Price_Exception if no price item is available
	 */
	public function getLowestPrice( array $priceItems, $quantity )
	{
		$priceList = array();

		foreach( $priceItems as $priceItem )
		{
			$iface = 'MShop_Price_Item_Interface';
			if( ( $priceItem instanceof $iface ) === false ) {
				throw new MShop_Price_Exception( sprintf( 'Item doesn\'t implement "%1$s"', $iface ) );
			}

			$priceList[ $priceItem->getQuantity() ] = $priceItem;
		}

		ksort( $priceList );

		if( ( $price = reset( $priceList ) ) === false ) {
			throw new MShop_Price_Exception( 'No price item available' );
		}

		if( $price->getQuantity() > $quantity )
		{
			$msg = sprintf( 'No price for the given quantity available', $quantity );
			throw new MShop_Price_Exception( $msg );
		}

		foreach( $priceList as $qty => $priceItem )
		{
			if( $qty <= $quantity && $qty > $price->getQuantity() ) {
				$price = $priceItem;
			}
		}

		return $price;
	}
}
