<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: Init.php 14843 2012-01-13 08:11:39Z nsendetzky $
 */


class Init
{
	private $_context;


	public function __construct( array $conf )
	{
		$this->_context = $this->_createContext( $conf );
	}


	public function getJsonRpcController()
	{
		return Controller_ExtJS_JsonRpc::getInstance( $this->_context );
	}


	public function getJsonSite( $site )
	{
		$localeManager = MShop_Locale_Manager_Factory::createManager( $this->_context );
		$manager = $localeManager->getSubManager( 'site' );

		if(  $site === null || $site === '' ) {
			return json_encode( $manager->createItem()->toArray() );
		}

		$criteria = $manager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'locale.site.code', $site ) );
		$items = $manager->searchItems( $criteria );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No site found for code "%1$s"', $site ) );
		}

		return json_encode( $item->toArray() );
	}


	protected function _createContext( array $conf )
	{
		$context = new MShop_Context_Item_Default();

		$config = new MW_Config_Zend( new Zend_Config( array(), true ), $conf );
                if( function_exists( 'apc_store' ) === true ) {
                        $config = new MW_Config_Decorator_APC( $config );
                }
		$context->setConfig( $config );

		$dbm = new MW_DB_Manager_PDO( $config );
		$context->setDatabaseManager( $dbm );

		$locale = MShop_Locale_Manager_Factory::createManager( $context )->createItem();
		$context->setLocale( $locale );

		$logger = new MAdmin_Log_Manager_Default( $context );
		$context->setLogger( $logger );

		$context->setEditor( 'tests' );

		return $context;
	}
}
