<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs\Catalog\Index\Rebuild;


/**
 * Rebuild catalog index job controller.
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
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Catalog index rebuild' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Rebuilds the catalog index for searching products' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
	 */
	public function run()
	{
		$timestamp = date( 'Y-m-d H:i:s' );
		$context = clone $this->getContext();

		$context->getLocale()->setLanguageId( null );
		$context->getLocale()->setCurrencyId( null );

		$manager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $context )->getSubManager( 'index' );

		$manager->rebuildIndex();
		$manager->cleanupIndex( $timestamp );
	}
}
