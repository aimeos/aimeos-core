<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */



/**
 * ExtJS product text export controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Product_Export_Text_CSV
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
		parent::__construct( $context, 'Product_Export_Text' );
	}


	/**
	 * Creates a XLS file with all attribute texts and outputs it directly.
	 *
	 * @param stdClass $params Object containing the properties, e.g. the list of product IDs
	 */
	public function createHttpOutput( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );

		$this->_setLocale( $params->site );

		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );
		$lang = ( property_exists( $params, 'lang' ) && is_array( $params->lang ) ? $params->lang : array() );

		$this->_getContext()->getLogger()->log( sprintf( 'Create export for product IDs: %1$s', implode( ',', $items ) ), MW_Logger_Abstract::DEBUG );

		@header('Content-Type: application/vnd.ms-excel');
		@header('Content-Disposition: attachment; filename=arcavias-product-texts.xls');
		@header('Cache-Control: max-age=0');

		$phpExcel = $this->_createDocument( $items, $lang );
		$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5');
		$objWriter->save('php://output');
	}


	/**
	 * Creates a new job to export an excel file.
	 *
	 * @param stdClass $params Object containing the properties, e.g. the list of product IDs
	 */
	public function createJob( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$config = $this->_getContext()->getConfig();
		$dir = $config->get( 'controller/extjs/product/export/text/default/exportdir', 'uploads' );

		$items = (array) $params->items;
		$lang = ( property_exists( $params, 'lang' ) ) ? (array) $params->lang : array();

		$languages = ( !empty( $lang ) ) ? implode( $lang, '-' ) : 'all';

		$result = (object) array(
			'site' => $params->site,
			'items' => array(
				(object) array(
					'job.label' => 'Product text export: '. $languages,
					'job.method' => 'Product_Export_Text.exportFile',
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


	public function exportFile( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$items = (array) $params->items;
		$lang = ( property_exists( $params, 'lang' ) ) ? (array) $params->lang : array();

		$config = $this->_getContext()->getConfig();
		$dir = $config->get( 'controller/extjs/product/export/text/default/exportdir', 'uploads' );
		$perms = $config->get( 'controller/extjs/product/export/text/default/dirperms', 0775 );

		if( is_dir( $dir ) === false && mkdir( $dir, $perms, true ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Couldn\'t create directory "%1$s" with permissions "%2$o"', $dir, $perms ) );
		}

		$filename = 'product-text-export_' . date('Y-m-d') . '_' . md5( time() . getmypid() ) .'.csv';

		$this->_getContext()->getLogger()->log( sprintf( 'Create export file for product IDs: %1$s', implode( ',', $items ) ), MW_Logger_Abstract::DEBUG );

		$data = $this->_addData( $items, $lang );
		$fp = fopen( $dir . DIRECTORY_SEPARATOR . $filename, 'a' );

		echo var_dump( $data );
		foreach( $data as $key => $row ) {
			fputcsv( $fp, $row );
		}

		fclose( $fp );

		return array(
			'succes' => true,
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
			'Product_Export_Text.createHttpOutput' => array(
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
	 * Adds data for export.
	 *
	 * @param array $ids List of item IDs that should be part of the document
	 * @param array $lang List of languages to export (empty array for all)
	 * @return array List of data to export
	 */
	protected function _addData( array $ids, array $lang )
	{
		$data = array();
		$manager = MShop_Locale_Manager_Factory::createManager( $this->_getContext() );
		$globalLanguageManager = $manager->getSubManager( 'language' );

		$search = $globalLanguageManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'locale.language.id') ) );

		if( !empty( $lang ) ) {
			$search->setConditions( $search->compare( '==', 'locale.language.id', $lang ) );
		}

		$start = $temp = $total = 0;


		do
		{
			$result = $globalLanguageManager->searchItems( $search, array(), $temp );
			if( $temp ) { $total = $temp; $temp = null; }

			foreach ( $result as $item ) {
				$data = $this->_addLanguage( $data, $item, $ids );
			}

			$start += count( $result );
			$search->setSlice( $start );
		}
		while( $start < $total );

		return $data;
	}


	/**
	 * Adds data for the given language.
	 *
	 * @param array $data PHPExcel object
	 * @param MShop_Locale_Item_Language_Interface $langItem Language item object
	 * @param array $items List of of item ids whose texts should be added
	 */
	protected function _addLanguage( array $data, MShop_Locale_Item_Language_Interface $langItem, array $ids )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->_getContext() );
		$search = $manager->createSearch();

		if( count( $ids ) > 0 ) {
			$search->setConditions( $search->compare( '==', 'product.id', $ids ) );
		}

		$sort = array( $search->sort( '+', 'product.type.code' ), $search->sort( '+', 'product.code' ) );
		$search->setSortations( $sort );

		$start = $temp = $total = 0;
		$items = array();

		do
		{
			$result = $manager->searchItems( $search, array('text'), $temp );
			if( $temp ) { $total = $temp; $temp = null; }

			foreach( $result as $item ) {
				$data = $this->_addItem( $data, $langItem, $item );
			}

			$start += count( $result );
			$search->setSlice( $start );
		}
		while( $start < $total );

		return $data;
	}


	/**
	 * Adds all texts belonging to an product item.
	 *
	 * @param MShop_Locale_Item_Language_Interface $langItem Language item object
	 * @param MShop_Product_Item_Interface $item product item object
	 * @param array $textMap Associative list of text type codes as key and list of text items
	 */
	protected function _addItem( array $data, MShop_Locale_Item_Language_Interface $langItem,
		MShop_Product_Item_Interface $item )
	{
		$data[] = array( 'Language ID', 'Product type', 'Product code', 'List type', 'Text type', 'Text ID', 'Text' );

		$listTypes = $items = array();
		foreach( $item->getListItems( 'text' ) as $listItem ) {
			$listTypes[ $listItem->getRefId() ] = $listItem->getType();
		}

		foreach( $this->_getTextTypes( 'product' ) as $textTypeItem )
		{
			$textItems = $item->getRefItems( 'text', $textTypeItem->getCode() );

			if( !empty( $textItems ) )
			{
				foreach( $textItems as $textItem )
				{
					$listType = ( isset( $listTypes[ $textItem->getId() ] ) ? $listTypes[ $textItem->getId() ] : '' );

					$items = array( $langItem->getId(), $item->getType(), $item->getCode(), $listType, $textTypeItem->getCode() );

					// use language of the text item because it may be null
					if( ( $textItem->getLanguageId() == $langItem->getId() || is_null( $textItem->getLanguageId() ) )
						&& $textItem->getTypeId() == $textTypeItem->getId() )
					{
						array_shift( $items );
						array_unshift( $items , $textItem->getLanguageId() );
						array_push( $items, $textItem->getId(), $textItem->getContent() );
					}
				}
			}
			else
			{
				$items = array( $langItem->getId(), $item->getType(), $item->getCode(), 'default', $textTypeItem->getCode() );
			}

			$data[] = $items;
		}

		return $data;
	}
}