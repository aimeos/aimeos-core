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
			$price = $product->getPrice();
			$taxrate = $price->getTaxrate();

			if( isset( $taxrates[$taxrate] ) ) {
				$taxrates[$taxrate] += ( $price->getValue() + $price->getCosts() ) * $product->getQuantity();
			} else {
				$taxrates[$taxrate] = ( $price->getValue() + $price->getCosts() ) * $product->getQuantity();
			}
		}

		try
		{
			$price = $basket->getService( 'delivery' )->getPrice();
			$taxrate = $price->getTaxrate();

			if( isset( $taxrates[$taxrate] ) ) {
				$taxrates[$taxrate] += $price->getValue() + $price->getCosts();
			} else {
				$taxrates[$taxrate] = $price->getValue() + $price->getCosts();
			}
		}
		catch( \Exception $e ) { ; } // if delivery service isn't available

		try
		{
			$price = $basket->getService( 'payment' )->getPrice();
			$taxrate = $price->getTaxrate();

			if( isset( $taxrates[$taxrate] ) ) {
				$taxrates[$taxrate] += $price->getValue() + $price->getCosts();
			} else {
				$taxrates[$taxrate] = $price->getValue() + $price->getCosts();
			}
		}
		catch( \Exception $e ) { ; } // if payment service isn't available

		return $taxrates;
	}
}