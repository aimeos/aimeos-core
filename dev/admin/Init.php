<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Init
{
	private $_arcavias;
	private $_context;


	public function __construct( Arcavias $arcavias, $confPath )
	{
		$configPaths = $arcavias->getConfigPaths( 'mysql' );
		$configPaths[] = $confPath;

		$this->_context = $this->_createContext( $configPaths );
		$this->_arcavias = $arcavias;
	}


	public function getJsonRpcController()
	{
		$cntlPaths = $this->_arcavias->getCustomPaths( 'controller/extjs' );

		return new Controller_ExtJS_JsonRpc( $this->_context, $cntlPaths );
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


	public function getHtml( $absdir, $relpath )
	{
		while ( basename( $absdir ) === basename( $relpath ) ) {
			$absdir = dirname( $absdir );
			$relpath = dirname( $relpath );
		}

		$relpath = rtrim( $relpath, '/' );
		$abslen = strlen( $absdir );
		$ds = DIRECTORY_SEPARATOR;
		$html = '';

		foreach( $this->_arcavias->getCustomPaths( 'client/extjs' ) as $base => $paths )
		{
			$relJsbPath = substr( $base, $abslen );

			foreach( $paths as $path )
			{
				$jsbPath = $relpath . $relJsbPath . $ds . $path;
				$jsbAbsPath = $base . $ds . $path;

				if( !is_file( $jsbAbsPath ) ) {
					throw new Exception( sprintf( 'JSB2 file "%1$s" not found', $jsbAbsPath ) );
				}

				$jsb2 = new MW_Jsb2_Default( $jsbAbsPath, dirname( $jsbPath ) );
				$html .= $jsb2->getHTML( 'css' );
				$html .= $jsb2->getHTML( 'js' );
			}
		}

		return $html;
	}


	public function getJsonClientConfig()
	{
		return json_encode( array( 'client' => $this->_context->getConfig()->get( 'client', array() ) ) );
	}


	protected function _createContext( array $conf )
	{
		$context = new MShop_Context_Item_Default();

		$config = new MW_Config_Array( array(), $conf );
		if( function_exists( 'apc_store' ) === true ) {
			$config = new MW_Config_Decorator_APC( $config );
		}
		$config = new MW_Config_Decorator_Memory( $config );
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
