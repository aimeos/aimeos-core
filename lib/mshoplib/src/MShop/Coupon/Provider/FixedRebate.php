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
 * Fixed price coupon model.
 *
 * @package MShop
 * @subpackage Coupon
 */
class FixedRebate
	extends \Aimeos\MShop\Coupon\Provider\Factory\Base
	implements \Aimeos\MShop\Coupon\Provider\Iface, \Aimeos\MShop\Coupon\Provider\Factory\Iface
{
	private $beConfig = array(
		'fixedrebate.productcode' => array(
			'code' => 'fixedrebate.productcode',
			'internalcode' => 'fixedrebate.productcode',
			'label' => 'Product code of the rebate product',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => true,
		),
		'fixedrebate.rebate' => array(
			'code' => 'fixedrebate.rebate',
			'internalcode' => 'fixedrebate.rebate',
			'label' => 'Map of currency ID and rebate amount',
			'type' => 'map',
			'internaltype' => 'array',
			'default' => [],
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
		$currency = $base->getPrice()->getCurrencyId();
		$rebate = $this->getConfigValue( 'fixedrebate.rebate', 0 );
		$productCode = $this->getConfigValue( 'fixedrebate.productcode' );

		if( $rebate == 0 || $productCode === null )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Invalid configuration for coupon provider "%1$s", needs "%2$s"' );
			$msg = sprintf( $msg, $this->getItemBase()->getProvider(), 'fixedrebate.productcode, fixedrebate.rebate' );
			throw new \Aimeos\MShop\Coupon\Exception( $msg );
		}

		if( is_array( $rebate ) )
		{
			if( !isset( $rebate[$currency] ) ) {
				throw new \Aimeos\MShop\Coupon\Exception( sprintf( 'No rebate for currency "%1$s" available', $currency ) );
			}

			$rebate = $rebate[$currency];
		}

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
}
