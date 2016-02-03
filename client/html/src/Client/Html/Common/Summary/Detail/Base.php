<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Common\Summary\Detail;


/**
 * Default implementation of checkout detail summary HTML client.
 *
 * @package Client
 * @subpackage Html
 */
abstract class Base
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
{
	/**
	 * Returns a list of tax rates and values for the given basket.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket containing the products, services, etc.
	 * @return array Associative list of tax rates as key and corresponding amounts as value
	 */
	protected function getTaxRates( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		$taxrates = array();

		foreach( $basket->getProducts() as $product )
		{
			$price = $product->getSumPrice();
			$taxrate = $price->getTaxrate();

			if( isset( $taxrates[$taxrate] ) ) {
				$taxrates[$taxrate]->addItem( $price );
			} else {
				$taxrates[$taxrate] = $price->setQuantity( 1 ); // sum is already calculated
			}
		}

		try
		{
			$price = clone $basket->getService( 'delivery' )->getPrice();
			$taxrate = $price->getTaxrate();

			if( isset( $taxrates[$taxrate] ) ) {
				$taxrates[$taxrate]->addItem( $price, $price->getQuantity() );
			} else {
				$taxrates[$taxrate] = $price->setQuantity( 1 ); // only single price
			}
		}
		catch( \Exception $e ) { ; } // if delivery service isn't available

		try
		{
			$price = clone $basket->getService( 'payment' )->getPrice();
			$taxrate = $price->getTaxrate();

			if( isset( $taxrates[$taxrate] ) ) {
				$taxrates[$taxrate]->addItem( $price, $price->getQuantity() );
			} else {
				$taxrates[$taxrate] = $price->setQuantity( 1 ); // only single price
			}
		}
		catch( \Exception $e ) { ; } // if payment service isn't available

		return $taxrates;
	}
}