<?php

/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs\Admin\Log;


/**
 * Admin log controller.
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
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Log cleanup' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Removes the old log entries from the database and archives them (optional)' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
	 */
	public function run()
	{
		$context = $this->getContext();
		$config = $context->getConfig();
		$container = null;

		/** controller/jobs/admin/log/standard/limit-days
		 * Only remove log entries that were created berore the configured number of days
		 *
		 * This option specifies the number of days log entries will be kept in
		 * the database. Afterwards, they will be removed and archived if a
		 * path for storing the archive files is configured.
		 *
		 * @param integer Number of days
		 * @since 2014.09
		 * @category User
		 * @category Developer
		 * @see controller/jobs/admin/log/standard/path
		 * @see controller/jobs/admin/log/standard/container/type
		 * @see controller/jobs/admin/log/standard/container/format
		 * @see controller/jobs/admin/log/standard/container/options
		 */
		$limit = $config->get( 'controller/jobs/admin/log/standard/limit-days', 30 );
		$limitDate = date( 'Y-m-d H:i:s', time() - $limit * 86400 );

		/** controller/jobs/admin/log/standard/path
		 * Path to a writable directory where the log archive files should be stored
		 *
		 * During normal operation, a lot of data can be logged, not only for
		 * errors that have occured. By default, these data is written into the
		 * log database and its size will grow if old log entries are not
		 * removed. There's a job controller available that can delete old log
		 * entries.
		 *
		 * If an absolute path to a writeable directory in the file system is
		 * set via this configuration option, the job controller will save the
		 * old log entries to a file in this path. They can be analysed later
		 * if required.
		 *
		 * The type and format of these files as well as the time frame after
		 * the log entries are removed from the log database can be configured
		 * too.
		 *
		 * @param string Absolute file system path to a writable directory
		 * @since 2014.09
		 * @category Developer
		 * @category User
		 * @see controller/jobs/admin/log/standard/container/type
		 * @see controller/jobs/admin/log/standard/container/format
		 * @see controller/jobs/admin/log/standard/container/options
		 * @see controller/jobs/admin/log/standard/limit-days
		 */
		if( ( $path = $config->get( 'controller/jobs/admin/log/standard/path', null ) ) !== null )
		{
			/** controller/jobs/admin/log/standard/container/type
			 * Container file type storing all coupon code files to import
			 *
			 * All coupon code files or content objects must be put into one
			 * container file so editors don't have to upload one file for each
			 * coupon code file.
			 *
			 * The container file types that are supported by default are:
			 * * Zip
			 *
			 * Extensions implement other container types like spread sheets, XMLs or
			 * more advanced ways of handling the exported data.
			 *
			 * @param string Container file type
			 * @since 2014.09
			 * @category Developer
			 * @category User
			 * @see controller/jobs/admin/log/standard/path
			 * @see controller/jobs/admin/log/standard/container/format
			 * @see controller/jobs/admin/log/standard/container/options
			 * @see controller/jobs/admin/log/standard/limit-days
			 */

			/** controller/jobs/admin/log/standard/container/format
			 * Format of the coupon code files to import
			 *
			 * The coupon codes are stored in one or more files or content
			 * objects. The format of that file or content object can be configured
			 * with this option but most formats are bound to a specific container
			 * type.
			 *
			 * The formats that are supported by default are:
			 * * CSV (requires container type "Zip")
			 *
			 * Extensions implement other container types like spread sheets, XMLs or
			 * more advanced ways of handling the exported data.
			 *
			 * @param string Content file type
			 * @since 2014.09
			 * @category Developer
			 * @category User
			 * @see controller/jobs/admin/log/standard/path
			 * @see controller/jobs/admin/log/standard/container/type
			 * @see controller/jobs/admin/log/standard/container/options
			 * @see controller/jobs/admin/log/standard/limit-days
			 */

			/** controller/jobs/admin/log/standard/container/options
			 * Options changing the expected format of the coupon codes to import
			 *
			 * Each content format may support some configuration options to change
			 * the output for that content type.
			 *
			 * The options for the CSV content format are:
			 * * csv-separator, default ','
			 * * csv-enclosure, default '"'
			 * * csv-escape, default '"'
			 * * csv-lineend, default '\n'
			 *
			 * For format options provided by other container types implemented by
			 * extensions, please have a look into the extension documentation.
			 *
			 * @param array Associative list of options with the name as key and its value
			 * @since 2014.09
			 * @category Developer
			 * @category User
			 * @see controller/jobs/admin/log/standard/path
			 * @see controller/jobs/admin/log/standard/container/type
			 * @see controller/jobs/admin/log/standard/container/format
			 * @see controller/jobs/admin/log/standard/limit-days
			 */

			$type = $config->get( 'controller/jobs/admin/log/standard/container/type', 'Zip' );
			$format = $config->get( 'controller/jobs/admin/log/standard/container/format', 'CSV' );
			$options = $config->get( 'controller/jobs/admin/log/standard/container/options', array() );

			$path .= DIRECTORY_SEPARATOR . str_replace( ' ', '_', $limitDate );
			$container = \Aimeos\MW\Container\Factory::getContainer( $path, $type, $format, $options );
		}

		$manager = \Aimeos\MAdmin\Factory::createManager( $context, 'log' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '<=', 'log.timestamp', $limitDate ) );
		$search->setSortations( array( $search->sort( '+', 'log.timestamp' ) ) );

		$start = 0;
		$contents = array();

		do
		{
			$ids = array();
			$items = $manager->searchItems( $search );

			foreach( $items as $id => $item )
			{
				if( $container !== null )
				{
					$facility = $item->getFacility();

					if( !isset( $contents[$facility] ) ) {
						$contents[$facility] = $container->create( $facility );
					}

					$contents[$facility]->add( $item->toArray() );
				}

				$ids[] = $id;
			}

			$manager->deleteItems( $ids );

			$count = count( $items );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count >= $search->getSliceSize() );


		if( $container !== null && !empty( $contents ) )
		{
			foreach( $contents as $content ) {
				$container->add( $content );
			}

			$container->close();
		}
	}
}
