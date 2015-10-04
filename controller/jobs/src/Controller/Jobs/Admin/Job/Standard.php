<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Jobs admin job controller for admin interfaces.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Admin_Job_Standard
	extends Controller_Jobs_Base
	implements Controller_Jobs_Iface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Admin interface jobs' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Executes the jobs created by the admin interface, e.g. the text exports' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$context = $this->getContext();

		$jobManager = MAdmin_Job_Manager_Factory::createManager( $context );
		$criteria = $jobManager->createSearch( true );

		$start = 0;

		do
		{
			$items = $jobManager->searchItems( $criteria );

			foreach( $items as $item )
			{
				try
				{
					$job = $item->getMethod();

					if( preg_match( '/^[a-zA-Z0-9\_]+\.[a-zA-Z0-9\_]+$/', $job ) !== 1 ) {
						throw new Controller_Jobs_Exception( sprintf( 'Invalid characters in job name "%1$s"', $job ) );
					}

					$parts = explode( '.', $job );

					if( count( $parts ) !== 2 ) {
						throw new Controller_Jobs_Exception( sprintf( 'Invalid job method "%1$s"', $job ) );
					}

					$name = "Controller_ExtJS_{$parts[0]}_Factory";
					$method = $parts[1];

					if( class_exists( $name ) === false ) {
						throw new Controller_Jobs_Exception( sprintf( 'Class "%1$s" not available', $name ) );
					}

					$name .= '::createController';

					if( ( $controller = call_user_func_array( $name, array( $context ) ) ) === false ) {
						throw new Controller_Jobs_Exception( sprintf( 'Unable to call factory method "%1$s"', $name ) );
					}

					if( method_exists( $controller, $method ) === false ) {
						throw new Controller_Jobs_Exception( sprintf( 'Method "%1$s" not available', $method ) );
					}

					$result = $controller->$method( (object) $item->getParameter() );

					$item->setResult( $result );
					$item->setStatus( -1 );
				}
				catch( Exception $e )
				{
					$str = 'Error while processing job "%1$s": %2$s';
					$context->getLogger()->log( sprintf( $str, $item->getMethod(), $e->getMessage() ) );
					$item->setStatus( 0 );
				}

				$jobManager->saveItem( $item );
			}

			$count = count( $items );
			$start += $count;
			$criteria->setSlice( $start );
		}
		while( $count > 0 );
	}
}
