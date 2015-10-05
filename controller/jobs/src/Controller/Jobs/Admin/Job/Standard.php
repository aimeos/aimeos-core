<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs\Admin\Job;


/**
 * Jobs admin job controller for admin interfaces.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Standard
	extends \Aimeos\Controller\Jobs\Base
	implements \Aimeos\Controller\Jobs\Iface
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
	 * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
	 */
	public function run()
	{
		$context = $this->getContext();

		$jobManager = \Aimeos\MAdmin\Job\Manager\Factory::createManager( $context );
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
						throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Invalid characters in job name "%1$s"', $job ) );
					}

					$parts = explode( '.', $job );

					if( count( $parts ) !== 2 ) {
						throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Invalid job method "%1$s"', $job ) );
					}

					$method = $parts[1];
					$class = str_replace( '_', '\\', $parts[0] );
					$name = '\\Aimeos\\Controller\\ExtJS\\' . $class . '\\Factory';

					if( class_exists( $name ) === false ) {
						throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Class "%1$s" not available', $name ) );
					}

					$name .= '::createController';

					if( ( $controller = call_user_func_array( $name, array( $context ) ) ) === false ) {
						throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Unable to call factory method "%1$s"', $name ) );
					}

					if( method_exists( $controller, $method ) === false ) {
						throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Method "%1$s" not available', $method ) );
					}

					$result = $controller->$method( (object) $item->getParameter() );

					$item->setResult( $result );
					$item->setStatus( -1 );
				}
				catch( \Exception $e )
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
