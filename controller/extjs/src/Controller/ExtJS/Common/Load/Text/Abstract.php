<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */



/**
 * ExtJS text export base class.
 *
 * @package Controller
 * @subpackage ExtJS
 */
abstract class Controller_ExtJS_Common_Load_Text_Abstract
{
	private $_context;
	private $_textListTypes = array();
	private $_textTypes = array();
	private $_name;


	/**
	 * Initializes the controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 * @param string $name Domain name of the export class
	 */
	public function __construct( MShop_Context_Item_Interface $context, $name )
	{
		$this->_context = $context;
		$this->_name = $name;
	}


	/**
	 * Returns the schema of the item.
	 *
	 * @return array Associative list of "name" and "properties" list (including "description", "type" and "optional")
	 */
	public function getItemSchema()
	{
		return array(
			'name' => $this->_name,
			'properties' => array(),
		);
	}


	/**
	 * Returns the schema of the available search criteria and operators.
	 *
	 * @return array Associative list of "criteria" list (including "description", "type" and "optional") and "operators" list (including "compare", "combine" and "sort")
	 */
	public function getSearchSchema()
	{
		return array(
			'criteria' => array(),
		);
	}


	protected function _checkFileUpload( $filename, $errcode )
	{
		switch( $errcode )
		{
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new Controller_ExtJS_Exception( 'The uploaded file exceeds the max. allowed filesize' );
			case UPLOAD_ERR_PARTIAL:
				throw new Controller_ExtJS_Exception( 'The uploaded file was only partially uploaded' );
			case UPLOAD_ERR_NO_FILE:
				throw new Controller_ExtJS_Exception( 'No file was uploaded' );
			case UPLOAD_ERR_NO_TMP_DIR:
				throw new Controller_ExtJS_Exception( 'Temporary folder is missing' );
			case UPLOAD_ERR_CANT_WRITE:
				throw new Controller_ExtJS_Exception( 'Failed to write file to disk' );
			case UPLOAD_ERR_EXTENSION:
				throw new Controller_ExtJS_Exception( 'File upload stopped by extension' );
			default:
				throw new Controller_ExtJS_Exception( 'Unknown upload error' );
		}

		if( is_uploaded_file( $filename ) === false ) {
			throw new Controller_ExtJS_Exception( 'File was not uploaded' );
		}
	}


	/**
	 * Checks if the required parameter are available.
	 *
	 * @param stdClass $params Item object containing the parameter
	 * @param array $names List of names of the required parameter
	 * @throws Controller_ExtJS_Exception if a required parameter is missing
	 */
	protected function _checkParams( stdClass $params, array $names )
	{
		foreach( $names as $name )
		{
			if( !property_exists( $params, $name ) ) {
				throw new Controller_ExtJS_Exception( sprintf( 'Missing parameter "%1$s"', $name ), -1 );
			}
		}
	}


	/**
	 * Returns the actual context item
	 *
	 * @return MShop_Context_Item_Interface
	 */
	protected function _getContext()
	{
		return $this->_context;
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
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', $domain . '.code', array_keys( $itemTextMap ) ) );
		$search->setSortations( array( $search->sort( '+', $domain.'.id' ) ) );

		$start = 0;
		$itemIdMap = $itemCodeMap = array();

		do
		{
			$result = $manager->searchItems( $search );

			foreach( $result as $item )
			{
				$itemIdMap[ $item->getId() ] = $item->getCode();
				$itemCodeMap[ $item->getCode() ] = $item->getId();
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count > 0 );


		$listManager = $manager->getSubManager( 'list' );

		$search = $listManager->createSearch();
		$expr = array(
				$search->compare( '==', $domain . '.list.parentid', array_keys( $itemIdMap ) ),
				$search->compare( '==', $domain . '.list.domain', 'text' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', $domain.'.list.id' ) ) );

		$start = 0;

		do
		{
			$result = $listManager->searchItems( $search );

			foreach( $result as $item ) {
				unset( $itemTextMap[ $itemIdMap[ $item->getParentId() ] ][ $item->getRefId() ] );
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count > 0 );


		$listTypes = $this->_getTextListTypes( $manager, $domain );

		foreach( $itemTextMap as $itemCode => $textIds )
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
					$item->setParentId( $itemCodeMap[ $itemCode ] );
					$item->setTypeId( $listTypes[$listType]->getId() );
					$item->setDomain( 'text' );
					$item->setRefId( $textId );

					$listManager->saveItem( $item );
				}
				catch( Exception $e ) {
					$this->_getContext()->getLogger()->log( 'text reference: ' . $e->getMessage(), MW_Logger_Abstract::ERR, 'import' );
				}
			}
		}
	}


	/**
	 * Returns a list of list type items.
	 *
	 * @param MShop_Common_Manager_Interface $manager Manager object (attribute, product, etc.)
	 * @param string $domain Domain the list items must be associated to
	 * @return array Associative list of list type codes and items implementing MShop_Common_Item_Type_Interface
	 */
	protected function _getTextListTypes( MShop_Common_Manager_Interface $manager, $domain )
	{
		if( isset( $this->_textListTypes[$domain] ) ) {
			return $this->_textListTypes[$domain];
		}

		$this->_textListTypes[$domain] = array();

		$typeManager = $manager->getSubManager( 'list' )->getSubManager( 'type' );

		$search = $typeManager->createSearch();
		$search->setConditions( $search->compare( '==', $domain . '.list.type.domain', 'text' ) );
		$search->setSortations( array( $search->sort( '+', $domain . '.list.type.code' ) ) );

		$start = 0;

		do
		{
			$result = $typeManager->searchItems( $search );

			foreach( $result as $typeItem ) {
				$this->_textListTypes[$domain][ $typeItem->getCode() ] = $typeItem;
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count > 0 );

		return $this->_textListTypes[$domain];
	}


	/**
	 * Returns a list of text type items.
	 *
	 * @param string $domain Domain the text items must be associated to
	 * @return array List of text type items implementing MShop_Common_Item_Type_Interface
	 */
	protected function _getTextTypes( $domain )
	{
		if( isset( $this->_textTypes[$domain] ) ) {
			return $this->_textTypes[$domain];
		}

		$this->_textTypes[$domain] = array();

		$textManager = MShop_Text_Manager_Factory::createManager( $this->_getContext() );
		$manager = $textManager->getSubManager( 'type' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'text.type.domain', $domain ) );
		$search->setSortations( array( $search->sort( '+', 'text.type.code' ) ) );

		$start = 0;

		do
		{
			$result = $manager->searchItems( $search );

			$this->_textTypes[$domain] = array_merge( $this->_textTypes[$domain], $result );

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count > 0 );

		return $this->_textTypes[$domain];
	}


	/**
	 * Creates a new locale object and adds this object to the context.
	 *
	 * @param string $site Site code
	 */
	protected function _setLocale( $site )
	{
		$context = $this->_getContext();
		$locale = $context->getLocale();

		$siteItem = null;
		$siteManager = MShop_Locale_Manager_Factory::createManager( $context )->getSubManager( 'site' );

		if( $site != '' )
		{
			$search = $siteManager->createSearch();
			$search->setConditions( $search->compare( '==', 'locale.site.code', $site ) );
			$sites = $siteManager->searchItems( $search );

			if ( ( $siteItem = reset( $sites ) ) === false ) {
				throw new Controller_ExtJS_Exception( 'Site item not found.' );
			}
		}

		$localeItem = new MShop_Locale_Item_Default( array(), $siteItem );
		$localeItem->setLanguageId( $locale->getLanguageId() );
		$localeItem->setCurrencyId( $locale->getCurrencyId() );

		if( $siteItem !== null ) {
			$localeItem->setSiteId( $siteItem->getId() );
		}

		$context->setLocale( $localeItem );
	}


	/**
	 * Imports a sheet of texts using the given text types.
	 *
	 * @param PHPExcel_Worksheet $sheet Sheet containing texts and associated data
	 * @param array $textTypeMap Associative list of text type IDs as keys and text type codes as values
	 * @param string $domain Name of the domain this text belongs to, e.g. product, catalog, attribute
	 * @return array Two dimensional associated list of codes and text IDs as key
	 */
	protected function _importTextsFromContent( MW_Container_Content_Interface $contentItem, array $textTypeMap, $domain )
	{
		$count = 0;
		$codeIdMap = array();
		$context = $this->_getContext();
		$textManager = MShop_Text_Manager_Factory::createManager( $context );
		$manager = MShop_Factory::createManager( $context, $domain );
		$listManager = $manager->getSubManager( 'list' );

		$contentItem->next(); // skip description row

		while( ( $row = $contentItem->current() ) !== null )
		{
			try
			{
				$value = isset( $row[6] ) ? $row[6] : '';
				$textId = isset( $row[5] ) ? $row[5] : '';
				$textType =  isset( $row[4] ) ? $row[4] : null;

				if( !isset( $textTypeMap[ $textType ] ) ) {
					throw new Controller_ExtJS_Exception( sprintf( 'Invalid text type "%1$s"', $textType ) );
				}

				if( $textId == '' && $value == '' ) {
					continue;
				}

				$item = $textManager->createItem();

				if( $textId != '' ) {
					$item->setId( $textId );
				}

				$item->setLanguageId( ( $row[0] != '' ? $row[0] : null ) );
				$item->setTypeId( $textTypeMap[ $textType ] );
				$item->setDomain( $domain );
				$item->setContent( $value );
				$item->setStatus( 1 );

				$textManager->saveItem( $item );

				if( $textId === '' ) {
					$codeIdMap[ $row[2] ][ $item->getId() ] = $row[3];
				}

				if( ++$count == 1000 )
				{
					$this->_importReferences( $manager, $codeIdMap, $domain );
					$codeIdMap = array();
					$count = 0;
				}
			}
			catch( Exception $e )
			{
				$this->_getContext()->getLogger()->log( sprintf( '%1$s text insert: %2$s', $domain, $e->getMessage() ), MW_Logger_Abstract::ERR, 'import' );
			}

			$contentItem->next();
		}

		if( !empty( $codeIdMap ) ) {
			$this->_importReferences( $manager, $codeIdMap, $domain );
		}
	}


	/**
	 * Creates container for storing export files.
	 *
	 * @param string $resource Path to the file
	 * @param string $container Extension of the container file
	 * @param array $containerOptions Options for the container
	 * @return MW_Container_Interface Container item
	 */
	protected function _createContainer( $resource, $key )
	{
		$config = $this->_getContext()->getConfig();

		$type = $config->get( $key . '/type', 'Zip' );
		$format = $config->get( $key . '/format', 'CSV' );
		$options = $config->get( $key . '/options', array() );

		return MW_Container_Factory::getContainer( $resource, $type, $format, $options );
	}
}