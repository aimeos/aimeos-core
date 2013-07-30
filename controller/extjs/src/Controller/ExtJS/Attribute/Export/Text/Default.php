<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */



/**
 * ExtJS attribute text export controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Attribute_Export_Text_Default
	extends Controller_ExtJS_Common_Load_Text_Abstract
	implements Controller_ExtJS_Common_Load_Text_Interface
{
	private $_sheetLine = 1;


	/**
	 * Initializes the controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Attribute_Export_Text' );
	}


	/**
	 * Creates a XLS file with all attribute texts and outputs it directly.
	 *
	 * @param stdClass $params Object containing the properties, e.g. the list of attribute IDs
	 */
	public function createHttpOutput( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );
		$lang = ( property_exists( $params, 'lang' ) && is_array( $params->lang ) ? $params->lang : array() );

		$this->_getContext()->getLogger()->log( sprintf( 'Create export for attribute IDs: %1$s', implode( ',', $items ) ), MW_Logger_Abstract::DEBUG );


		@header('Content-Type: application/vnd.ms-excel');
		@header('Content-Disposition: attachment; filename=arcavias-attribute-texts.xls');
		@header('Cache-Control: max-age=0');

		$this->_container = new Controller_ExtJS_Common_Load_Container_PHPExcel( 'php://output', 'attribute' );//$this->_getContext()->getConfig()->get( 'controller/extjs/export/manager', new Controller_ExtJS_Common_Load_Container_PHPExcel( 'php://output', 'attribute' ) );
		$phpExcel = $this->_createDocument( $items, $lang );
		$this->_container->finish();
	}


	/**
	 * Creates a new job to export an excel file.
	 *
	 * @param stdClass $params Object containing the properties, e.g. the list of attribute IDs
	 */
	public function createJob( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$config = $this->_getContext()->getConfig();
		$dir = $config->get( 'controller/extjs/attribute/export/text/default/exportdir', 'uploads' );

		$items = (array) $params->items;
		$lang = ( property_exists( $params, 'lang' ) ) ? (array) $params->lang : array();

		$languages = ( count( $lang ) > 0 ) ? implode( $lang, '-' ) : 'all';

		$result = (object) array(
			'site' => $params->site,
			'items' => array(
				(object) array(
					'job.label' => 'Attribute text export: '. $languages,
					'job.method' => 'Attribute_Export_Text.exportFile',
					'job.parameter' => array(
						'site' => $params->site,
						'items' => $items,
						'lang' => $params->lang,
					),
					'job.status' => 1,
				),
			),
		);

		$jobController = Controller_ExtJS_Admin_Job_Factory::createController( $this->_getContext() );
		$jobController->saveItems( $result );

		return array(
			'items' => $items,
			'success' => true,
		);
	}


	/**
	 * Create an excel file in the filesystem.
	 *
	 * @param stdClass $params Object containing the properties, e.g. the list of attribute IDs
	 */
	public function exportFile( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$items = (array) $params->items;
		$lang = ( property_exists( $params, 'lang' ) ) ? (array) $params->lang : array();

		$config = $this->_getContext()->getConfig();
		$dir = $config->get( 'controller/extjs/attribute/export/text/default/exportdir', 'uploads' );
		$perms = $config->get( 'controller/extjs/attribute/export/text/default/dirperms', 0775 );

		if( is_dir( $dir ) === false && mkdir( $dir, $perms, true ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Couldn\'t create directory "%1$s" with permissions "%2$o"', $dir, $perms ) );
		}

		$filename = 'attribute-text-export_' .date('Y-m-d') . '_' . md5( time() . getmypid() );
		$this->_filepath = $dir . DIRECTORY_SEPARATOR . $filename;

		$this->_getContext()->getLogger()->log( sprintf( 'Create export file for attribute IDs: %1$s', implode( ',', $items ) ), MW_Logger_Abstract::DEBUG );

		$this->_container = $config->get( 'controller/extjs/export/manager', new Controller_ExtJS_Common_Load_Container_PHPExcel( $this->_filepath ) );
		$this->_createDocument( $items, $lang );
		$filename = $this->_container->finish();

		$downloadFile = $config->get( 'controller/extjs/attribute/export/text/default/downloaddir', 'uploads' ) . DIRECTORY_SEPARATOR . $filename;

		return array(
			'file' => '<a href="'.$downloadFile.'">Download</a>',
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
			'Attribute_Export_Text.createHttpOutput' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "items","optional" => false ),
					array( "type" => "array","name" => "lang","optional" => true ),
				),
				"returns" => "",
			),
		);
	}


	/**
	 * Creates a new PHPExcel document object.
	 *
	 * @param array $ids List of item IDs that should be part of the document
	 * @param array $lang List of languages to export (empty array for all)
	 * @return PHPExcel Document object
	 */
	protected function _createDocument( array $ids, array $lang )
	{
		$manager = MShop_Locale_Manager_Factory::createManager( $this->_getContext() );
		$globalLanguageManager = $manager->getSubManager( 'language' );

		$search = $globalLanguageManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'locale.language.id' ) ) );

		if( !empty( $lang ) ) {
			$search->setConditions( $search->compare( '==', 'locale.language.id', $lang ) );
		}

		$start = $temp = $total = 0;
		$items = array();

		do
		{
			$result = $globalLanguageManager->searchItems( $search, array(), $temp );
			if( $temp ) { $total = $temp; $temp = null; }

			foreach ( $result as $item ) {
				$this->_addLanguage( $item->getId(), $ids );
			}

			$start += count( $result );
			$search->setSlice( $start );
		}
		while( $start < $total );
	}


	/**
	 * Adds a new sheet for the given language to the document.
	 *
	 * @param string $langid Language item object
	 * @param array $items List of of item ids whose texts should be added
	 */
	protected function _addLanguage( $langid, array $ids )
	{
		$data = array( 'Language ID', 'Attribute type', 'Attribute code', 'List type', 'Text type', 'Text ID', 'Text' );

		$contentManager = Controller_ExtJS_Common_Load_Content_Default( $this->_filepath, $langid, 'attribute' );
		$contentManager->addEntry( $data );

		$manager = MShop_Attribute_Manager_Factory::createManager( $this->_getContext() );
		$search = $manager->createSearch();

		if( !empty( $ids ) ) {
			$search->setConditions( $search->compare( '==', 'attribute.id', $ids ) );
		}

		$sort = array( $search->sort( '+', 'attribute.type.code' ), $search->sort( '+', 'attribute.position' ) );
		$search->setSortations( $sort );

		$start = $temp = $total = 0;
		$items = array();

		do
		{
			$result = $manager->searchItems( $search, array('text'), $temp );
			if( $temp ) { $total = $temp; $temp = null; }

			foreach( $result as $item ) {
				$this->_addItem( $langid, $item, $contentManager );
			}

			$start += count( $result );
			$search->setSlice( $start );
		}
		while( $start < $total );

		$this->_container->addData( $contentManager->getFilePath() );
	}


	/**
	 * Adds all texts belonging to an attribute item to the given sheet.
	 *
	 * @param PHPExcel_Worksheet $sheet Worksheet where the texts will be added
	 * @param MShop_Locale_Item_Language_Interface $langItem Language item object
	 * @param MShop_Attribute_Item_Interface $item Attribute item object
	 */
	protected function _addItem( $langid, MShop_Attribute_Item_Interface $item, $contentManager )
	{
		$listTypes = array();
		foreach( $item->getListItems( 'text' ) as $listItem ) {
			$listTypes[ $listItem->getRefId() ] = $listItem->getType();
		}

		foreach( $this->_getTextTypes( 'attribute' ) as $textTypeItem )
		{
			$textItems = $item->getRefItems( 'text', $textTypeItem->getCode() );

			if( !empty( $textItems ) )
			{
				foreach( $textItems as $textItem )
				{
					$listType = ( isset( $listTypes[ $textItem->getId() ] ) ? $listTypes[ $textItem->getId() ] : '' );

					$items = array( $langid, $item->getType(), $item->getCode(), $listType, $textTypeItem->getCode(), '', '' );

					// use language of the text item because it may be null
					if( ( $textItem->getLanguageId() == $langid || is_null( $textItem->getLanguageId() ) )
						&& $textItem->getTypeId() == $textTypeItem->getId() )
					{
						$items[0] = $textItem->getLanguageId();
						$items[5] = $textItem->getId();
						$items[6] = $textItem->getContent();
					}
				}
			}
			else
			{
				$items = array( $langid, $item->getType(), $item->getCode(), 'default', $textTypeItem->getCode(), '', '' );
			}

			$contentManager->addEntry( $items );
		}
	}
}
