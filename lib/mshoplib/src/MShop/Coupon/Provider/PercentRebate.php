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
		$percent = (float) $this->getConfigValue( 'percentrebate.rebate', 0 );
		$prodcode = $this->getConfigValue( 'percentrebate.productcode' );

		if( $percent == 0 || $prodcode === null )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Invalid configuration for coupon provider "%1$s", needs "%2$s"' );
			$msg = sprintf( $msg, $this->getItem()->getProvider(), 'percentrebate.productcode, percentrebate.rebate' );
			throw new \Aimeos\MShop\Coupon\Exception( $msg );
		}

		$price = $this->getObject()->calcPrice( $base->setCoupon( $this->getCode(), [] ) );
		$rebate = $this->round( ( $price->getValue() + $price->getCosts() + $price->getRebate() ) * $percent / 100 );
		$base->setCoupon( $this->getCode(), $this->createRebateProducts( $base, $prodcode, $rebate ) );

		return $this;
	}


	/**
	 * Rounds the number to the configured precision
	 *
	 * @param float $number Number to round
	 * @return float Rounded number
	 */
	protected function round( float $number ) : float
	{
		$prec = $this->getConfigValue( 'percentrebate.precision', 2 );
		$value = $this->getConfigValue( 'percentrebate.roundvalue', 0 );

		if( $value == 0 ) {
			return round( $number, $prec );
		}

		return round( round( $number / $value ) * $value, $prec );
	}
}
