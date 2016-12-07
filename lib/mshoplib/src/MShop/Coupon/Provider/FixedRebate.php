<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	implements \Aimeos\MShop\Coupon\Provider\Factory\Iface
{
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

		$rebate = '0.00';
		$currency = $base->getPrice()->getCurrencyId();
		$config = $this->getItemBase()->getConfig();

		if( !isset( $config['fixedrebate.productcode'] ) || !isset( $config['fixedrebate.rebate'] ) )
		{
			throw new \Aimeos\MShop\Coupon\Exception( sprintf(
				'Invalid configuration for coupon provider "%1$s", needs "%2$s"',
				$this->getItemBase()->getProvider(), 'fixedrebate.productcode, fixedrebate.rebate'
			) );
		}

		if( is_array( $config['fixedrebate.rebate'] ) )
		{
			if( isset( $config['fixedrebate.rebate'][$currency] ) ) {
				$rebate = $config['fixedrebate.rebate'][$currency];
			}
		}
		else
		{
			$rebate = $config['fixedrebate.rebate'];
		}


		$orderProducts = $this->createMonetaryRebateProducts( $base, $config['fixedrebate.productcode'], $rebate );

		$base->addCoupon( $this->getCode(), $orderProducts );
	}
}
