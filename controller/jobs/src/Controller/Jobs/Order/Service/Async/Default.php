<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Updates the payment or delivery status for services with asynchronous methods.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Order_Service_Async_Default
	extends Controller_Jobs_Abstract
	implements Controller_Jobs_Interface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Batch update of payment/delivery status' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Executes payment or delivery service providers that uses batch updates' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$context = $this->_getContext();
		$serviceManager = MShop_Factory::createManager( $context, 'service' );

		$search = $serviceManager->createSearch();
		$start = 0;

		do
		{
			$serviceItems = $serviceManager->searchItems( $search );

			foreach( $serviceItems as $serviceItem )
			{
				try
				{
					$serviceManager->getProvider( $serviceItem )->updateAsync();
				}
				catch( Exception $e )
				{
					$msg = 'Executing updateAsyc() of "%1$s" failed: %2$s';
					$context->getLogger()->log( sprintf( $msg, $serviceItem->getProvider(), $e->getMessage() ) );
				}
			}

			$count = count( $serviceItems );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count >= $search->getSliceSize() );
	}
}
