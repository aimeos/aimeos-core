<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Sends paid orders to the ERP system or logistic partner.
 *
 * @package Controller
 * @subpackage Jobs
 * @deprecated Use Controller_Jobs_Order_Service_Delivery_Default instead
 */
class Controller_Jobs_Service_Delivery_Process_Default
	extends Controller_Jobs_Order_Service_Delivery_Default
	implements Controller_Jobs_Interface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Deprecated: Use order/service/delivery' );
	}
}
