<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2017
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider;


/**
 * Percentage price coupon model.
 *
 * @package MShop
 * @subpackage Coupon
 */
class PercentRebate
	extends \Aimeos\MShop\Coupon\Provider\Factory\Base
	implements \Aimeos\MShop\Coupon\Provider\Factory\Iface
{
	private $beConfig = array(
		'percentrebate.productcode' => array(
			'code' => 'percentrebate.productcode',
			'internalcode' => 'percentrebate.productcode',
			'label' => 'Product code of the rebate product',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => true,
		),
		'percentrebate.rebate' => array(
			'code' => 'percentrebate.rebate',
			'internalcode' => 'percentrebate.rebate',
			'label' => 'Discount in percent',
			'type' => 'number',
			'internaltype' => 'float',
			'default' => 0,
			'required' => true,
		),
	);


	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 */
	public function addCoupon( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		if( $this->getObject()->isAvailable( $base ) === false ) {
			return;
		}

		$config = $this->getItemBase()->getConfig();

		if( !isset( $config['percentrebate.productcode'] ) || !isset( $config['percentrebate.rebate'] ) )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Invalid configuration for coupon provider "%1$s", needs "%2$s"' );
			$msg = sprintf( $msg, $this->getItemBase()->getProvider(), 'percentrebate.productcode, percentrebate.rebate' );
			throw new \Aimeos\MShop\Coupon\Exception( $msg );
		}


		$sum = 0;
		foreach( $base->getProducts() as $product ) {
			$sum += ( $product->getPrice()->getValue() + $product->getPrice()->getCosts() ) * $product->getQuantity();
		}

		$rebate = round( $sum * (float) $config['percentrebate.rebate'] / 100, 2 );
		$orderProducts = $this->createMonetaryRebateProducts( $base, $config['percentrebate.productcode'], $rebate );

		$base->addCoupon( $this->getCode(), $orderProducts );
	}


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes )
	{
		return $this->checkConfig( $this->beConfig, $attributes );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE()
	{
		return $this->getConfigItems( $this->beConfig );
	}
}
