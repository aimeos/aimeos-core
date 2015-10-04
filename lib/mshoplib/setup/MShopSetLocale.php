<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Sets locale in context.
 */
class MW_Setup_Task_MShopSetLocale extends MW_Setup_Task_Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddLocaleData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'MShopAddTypeData' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process();
	}


	/**
	 * Adds locale data.
	 */
	protected function process()
	{
		$iface = 'MShop_Context_Item_Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$site = $this->additional->getConfig()->get( 'setup/site', 'default' );


		$this->msg( sprintf( 'Setting locale to "%1$s"', $site ), 0 );

		// Set locale for further tasks
		$localeManager = MShop_Locale_Manager_Factory::createManager( $this->additional, 'Standard' );
		$this->additional->setLocale( $localeManager->bootstrap( $site, '', '', false ) );

		$this->status( 'OK' );
	}
}