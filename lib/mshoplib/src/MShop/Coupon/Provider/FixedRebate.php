<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Fixed price coupon model.
 *
 * @package MShop
 * @subpackage Coupon
 */
class MShop_Coupon_Provider_FixedRebate
	extends MShop_Coupon_Provider_Factory_Base
	implements MShop_Coupon_Provider_Factory_Interface
{
	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 */
	public function addCoupon( MShop_Order_Item_Base_Interface $base )
	{
		if( $this->getObject()->isAvailable( $base ) === false ) {
			return;
		}

		$rebate = '0.00';
		$currency = $base->getPrice()->getCurrencyId();
		$config = $this->getItemBase()->getConfig();

		if( !isset( $config['fixedrebate.productcode'] ) || !isset( $config['fixedrebate.rebate'] ) )
		{
			throw new MShop_Coupon_Exception( sprintf(
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
