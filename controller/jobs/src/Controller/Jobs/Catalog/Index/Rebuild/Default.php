<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Rebuild catalog index job controller.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Catalog_Index_Rebuild_Default
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
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$timestamp = date( 'Y-m-d H:i:s' );
		$context = clone $this->getContext();

		$context->getLocale()->setLanguageId( null );
		$context->getLocale()->setCurrencyId( null );

		$manager = MShop_Catalog_Manager_Factory::createManager( $context )->getSubManager( 'index' );

		$manager->rebuildIndex();
		$manager->cleanupIndex( $timestamp );
	}
}
