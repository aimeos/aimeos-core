<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Decreases the stock levels of completed orders.
 *
 * @package Controller
 * @subpackage Jobs
 * @deprecated Use Controller_Jobs_Order_Cleanup_Unfinished_Default instead
 */
class Controller_Jobs_Order_Product_Stock_Default
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
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Decreases the stock levels of products in completed orders' );
	}
}
