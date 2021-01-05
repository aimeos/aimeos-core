<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider;


/**
 * Free shipping coupon model.
 *
 * @package MShop
 * @subpackage Coupon
 */
class FreeShipping
	extends \Aimeos\MShop\Coupon\Provider\Factory\Base
	implements \Aimeos\MShop\Coupon\Provider\Iface, \Aimeos\MShop\Coupon\Provider\Factory\Iface
{
	private $beConfig = array(
		'freeshipping.productcode' => array(
			'code' => 'freeshipping.productcode',
			'internalcode' => 'freeshipping.productcode',
			'label' => 'Product code of the free shipping product',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => true,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		return $this->checkConfig( $this->beConfig, $attributes );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Provider object for method chaining
	 */
	public function update( \Aimeos\MShop\Order\Item\Base\Iface $base ) : \Aimeos\MShop\Coupon\Provider\Iface
	{
		if( ( $prodcode = $this->getConfigValue( 'freeshipping.productcode' ) ) === null )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Invalid configuration for coupon provider "%1$s", needs "%2$s"' );
			$msg = sprintf( $msg, $this->getItem()->getProvider(), 'freeshipping.productcode' );
			throw new \Aimeos\MShop\Coupon\Exception( $msg );
		}

		$orderProduct = $this->createProduct( $prodcode );
		$price = $orderProduct->getPrice()->clear();

		foreach( $base->getService( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY ) as $service )
		{
			$price = $price->setRebate( $price->getRebate() + $service->getPrice()->getCosts() )
				->setCosts( $price->getCosts() - $service->getPrice()->getCosts() );
		}

		$base->setCoupon( $this->getCode(), [$orderProduct->setPrice( $price )] );
		return $this;
	}
}
