<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Sets locale in context.
 */
class MW_Setup_Task_MShopSetLocale extends MW_Setup_Task_Abstract
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
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Adds locale data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$site = $this->_additional->getConfig()->get( 'setup/site', 'default' );


		$this->_msg( sprintf( 'Setting locale to "%1$s"', $site ), 0 );

		// Set locale for further tasks
		$localeManager = MShop_Locale_Manager_Factory::createManager( $this->_additional, 'Default' );
		$this->_additional->setLocale( $localeManager->bootstrap( $site, '', '', false ) );

		$this->_status( 'OK' );
	}
}