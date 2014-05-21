<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Order coupon job controller for decreasing coupon counts.
 *
 * @package Controller
 * @subpackage Jobs
 * @deprecated Use Controller_Jobs_Order_Cleanup_Unfinished_Default instead
 */
class Controller_Jobs_Order_Coupon_Count_Default
	extends Controller_Jobs_Order_Cleanup_Unfinished_Default
	implements Controller_Jobs_Interface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Deprecated: Use "Remove unfinised orders"' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Decreases the counts of successfully redeemed coupons' );
	}
}
