<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS catalog text import controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Catalog_Import_Text_Default
	extends Controller_ExtJS_Common_Load_Text_Abstract
	implements Controller_ExtJS_Common_Load_Text_Interface
{
	/**
	 * Initializes the controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Catalog_Import_Text' );
	}


	/**
	 * Uploads a CSV file with all catalog texts.
	 *
	 * @param stdClass $params Object containing the properties
	 */
	public function uploadFile( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site' ) );
		$this->_setLocale( $params->site );

		if( ( $fileinfo = reset( $_FILES ) ) === false ) {
			throw new Controller_ExtJS_Exception( 'No file was uploaded' );
		}

		$config = $this->_getContext()->getConfig();
		$dir = $config->get( 'controller/extjs/catalog/import/text/default/uploaddir', 'uploads' );

		if( $config->get( 'controller/extjs/catalog/import/text/default/enablecheck', true ) ) {
			$this->_checkFileUpload( $fileinfo['tmp_name'], $fileinfo['error'] );
		}

		$fileext = pathinfo( $fileinfo['name'], PATHINFO_EXTENSION );
		$dest = $dir . DIRECTORY_SEPARATOR . md5( $fileinfo['name'] . time() . getmypid() ) . '.' . $fileext;

		if( rename( $fileinfo['tmp_name'], $dest ) !== true )
		{
			$msg = sprintf( 'Uploaded file could not be moved to upload directory "%1$s"', $dir );
			throw new Controller_ExtJS_Exception( $msg );
		}

		$perms = $config->get( 'controller/extjs/catalog/import/text/default/fileperms', 0660 );
		if( chmod( $dest, $perms ) !== true )
		{
			$msg = sprintf( 'Could not set permissions "%1$s" for file "%2$s"', $perms, $dest );
			throw new Controller_ExtJS_Exception( $msg );
		}

		$result = (object) array(
			'site' => $params->site,
			'items' => array(
				(object) array(
					'job.label' => 'Catalog text import: ' . $fileinfo['name'],
					'job.method' => 'Catalog_Import_Text.importFile',
					'job.parameter' => array(
						'site' => $params->site,
						'items' => $dest,
					),
					'job.status' => 1,
				),
			),
		);

		$jobController = Controller_ExtJS_Admin_Job_Factory::createController( $this->_getContext() );
		$jobController->saveItems( $result );

		return array(
			'items' => $dest,
			'success' => true,
		);
	}


	/**
	 * Imports a CSV file with all catalog texts.
	 *
	 * @param stdClass $params Object containing the properties
	 */
	public function importFile( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $path )
		{
			$container = $this->_createContainer( $path, 'controller/extjs/catalog/import/text/default/container' );

			$textTypeMap = array();
			foreach( $this->_getTextTypes( 'catalog' ) as $item ) {
				$textTypeMap[ $item->getCode() ] = $item->getId();
			}

			foreach( $container as $content ) {
				$itemTextMap = $this->_importTextsFromContent( $content, $textTypeMap, 'catalog' );
			}

			unlink( $path );
		}

		return array(
			'success' => true,
		);
	}


	/**
	 * Returns the service description of the class.
	 * It describes the class methods and its parameters including their types
	 *
	 * @return array Associative list of class/method names, their parameters and types
	 */
	public function getServiceDescription()
	{
		return array(
			'Catalog_Import_Text.uploadFile' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
				),
				"returns" => "",
			),
			'Catalog_Import_Text.importFile' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "items","optional" => false ),
				),
				"returns" => "",
			),
		);
	}


	/**
	 * Associates the texts with the products.
	 *
	 * @param MShop_Common_Manager_Interface $manager Manager object (attribute, product, etc.) for associating the list items
	 * @param array $itemTextMap Two dimensional associated list of codes and text IDs as key
	 * @param string $domain Name of the domain this text belongs to, e.g. product, catalog, attribute
	 */
	protected function _importReferences( MShop_Common_Manager_Interface $manager, array $itemTextMap, $domain )
	{
		$catalogStart = $catalogTotal = 0;
		$listManager = $manager->getSubManager( 'list' );

		do
		{
			$criteria = $manager->createSearch();
			$criteria->setConditions( $criteria->compare( '==', 'catalog.id', array_keys( $itemTextMap ) ) );
			$catalogItems = $manager->searchItems( $criteria );
			$catalogStart += count( $catalogItems );

			$catalogIds = array();

			foreach( $catalogItems as $item ) {
				$catalogIds[] = $item->getId();
			}

			$listStart = $listTotal = 0;

			do
			{
				$criteria = $listManager->createSearch();
				$expr[] = $criteria->compare( '==', 'catalog.list.parentid', $catalogIds );
				$expr[] = $criteria->compare( '==', 'catalog.list.domain', 'text' );
				$criteria->setConditions( $criteria->combine( '&&', $expr ) );
				$listItems = $listManager->searchItems( $criteria, array(), $listTotal );
				$listStart += count( $catalogItems );

				foreach( $listItems as $item ) {
					unset( $itemTextMap[ $item->getParentId() ][ $item->getRefId() ] );
				}
			}
			while( $listStart < $listTotal );

		}
		while( $catalogStart < $catalogTotal );


		$listTypes = $this->_getTextListTypes( $manager, 'catalog' );

		foreach( $itemTextMap as $catalogCode => $textIds )
		{
			foreach( $textIds as $textId => $listType )
			{
				try
				{
					$iface = 'MShop_Common_Item_Type_Interface';
					if( !isset( $listTypes[$listType] ) || ( $listTypes[$listType] instanceof $iface ) === false ) {
						throw new Controller_ExtJS_Exception( sprintf( 'Invalid list type "%1$s"', $listType ) );
					}

					$item = $listManager->createItem();
					$item->setParentId( $catalogCode );
					$item->setTypeId( $listTypes[$listType]->getId() );
					$item->setDomain( 'text' );
					$item->setRefId( $textId );

					$listManager->saveItem( $item );
				}
				catch( Exception $e )
				{
					$this->_getContext()->getLogger()->log( 'catalog text reference: ' . $e->getMessage(), MW_Logger_Abstract::ERR, 'import' );
				}
			}
		}
	}
}