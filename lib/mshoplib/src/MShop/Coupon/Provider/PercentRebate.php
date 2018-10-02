<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	implements \Aimeos\MShop\Coupon\Provider\Iface, \Aimeos\MShop\Coupon\Provider\Factory\Iface
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
		'percentrebate.precision' => array(
			'code' => 'percentrebate.precision',
			'internalcode' => 'percentrebate.precision',
			'label' => 'Number of decimal digits to round to',
			'type' => 'integer',
			'internaltype' => 'integer',
			'default' => 2,
			'required' => false,
		),
		'percentrebate.roundvalue' => array(
			'code' => 'percentrebate.roundvalue',
			'internalcode' => 'percentrebate.roundvalue',
			'label' => 'Value to round rebate up/down',
			'type' => 'number',
			'internaltype' => 'float',
			'default' => 0,
			'required' => false,
		),
	);


	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 */
	public function addCoupon( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		$rebate = (float) $this->getConfigValue( 'percentrebate.rebate', 0 );
		$productCode = $this->getConfigValue( 'percentrebate.productcode' );

		if( $rebate == 0 || $productCode === null )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Invalid configuration for coupon provider "%1$s", needs "%2$s"' );
			$msg = sprintf( $msg, $this->getItemBase()->getProvider(), 'percentrebate.productcode, percentrebate.rebate' );
			throw new \Aimeos\MShop\Coupon\Exception( $msg );
		}

		$sum = 0;
		foreach( $base->getProducts() as $product )
		{
			if( $product->getPrice()->getValue() > 0 ) {
				$sum += ( $product->getPrice()->getValue() + $product->getPrice()->getCosts() ) * $product->getQuantity();
			}
		}

		$rebate = $this->round( $sum * $rebate / 100 );
		$orderProducts = $this->createMonetaryRebateProducts( $base, $productCode, $rebate );

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


	/**
	 * Rounds the number to the configured precision
	 *
	 * @param float $number Number to round
	 * @return Rounded number
	 */
	protected function round( $number )
	{
		$prec = $this->getConfigValue( 'percentrebate.precision', 2 );
		$value = $this->getConfigValue( 'percentrebate.roundvalue', 0 );

		if( $value == 0 ) {
			return round( $number, $prec );
		}

		return round( round( $number / $value ) * $value, $prec );
	}
}
