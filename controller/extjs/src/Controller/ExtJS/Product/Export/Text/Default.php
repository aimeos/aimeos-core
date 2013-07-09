<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
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
class Controller_ExtJS_Product_Export_Text_Default
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

		$languages = ( count( $lang ) > 0 ) ? implode( $lang, '-' ) : 'all';

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


	/**
	 * Create an excel file in the filesystem.
	 *
	 * @param stdClass $params Object containing the properties, e.g. the list of product IDs
	 */
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

		$filename = 'product-text-export_' . date('Y-m-d') . '_' . md5( time() . getmypid() ) .'.xls';

		$this->_getContext()->getLogger()->log( sprintf( 'Create export file for product IDs: %1$s', implode( ',', $items ) ), MW_Logger_Abstract::DEBUG );

		$phpExcel = $this->_createDocument( $items, $lang );
		$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5');
		$objWriter->save( $dir . DIRECTORY_SEPARATOR . $filename );

		$downloadFile = $config->get( 'controller/extjs/product/export/text/default/downloaddir', 'uploads' ) . DIRECTORY_SEPARATOR . $filename;

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
	 * Creates a new PHPExcel document object.
	 *
	 * @param array $ids List of item IDs that should be part of the document
	 * @param array $lang List of languages to export (empty array for all)
	 * @return PHPExcel Document object
	 */
	protected function _createDocument( array $ids, array $lang )
	{
		$phpExcel = new PHPExcel();
		$phpExcel->removeSheetByIndex( 0 );

		$phpExcel->getProperties()
			->setCreator( 'Arcavias' )
			->setLastModifiedBy( 'Arcavias export' )
			->setTitle( 'Arcavias product text export' )
			->setSubject( 'Arcavias product text export' )
			->setDescription( 'Export file for all product texts' )
			->setKeywords( 'export product text translation' );


		$manager = MShop_Locale_Manager_Factory::createManager( $this->_getContext() );
		$globalLanguageManager = $manager->getSubManager( 'language' );

		$search = $globalLanguageManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'locale.language.id') ) );

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
				$this->_addLanguage( $phpExcel, $item, $ids );
			}

			$start += count( $result );
			$search->setSlice( $start );
		}
		while( $start < $total );

		return $phpExcel;
	}


	/**
	 * Adds a new sheet for the given language to the document.
	 *
	 * @param PHPExel $phpExcel PHPExcel object
	 * @param MShop_Locale_Item_Language_Interface $langItem Language item object
	 * @param array $items List of of item ids whose texts should be added
	 */
	protected function _addLanguage( PHPExcel $phpExcel, MShop_Locale_Item_Language_Interface $langItem, array $ids )
	{
		$sheet = $this->_createSheet( $phpExcel, $langItem->getId() );

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
				$this->_addItem( $sheet, $langItem, $item );
			}

			$start += count( $result );
			$search->setSlice( $start );
		}
		while( $start < $total );
	}


	/**
	 * Adds all texts belonging to an product item to the given sheet.
	 *
	 * @param PHPExcel_Worksheet $sheet Worksheet where the texts will be added
	 * @param MShop_Locale_Item_Language_Interface $langItem Language item object
	 * @param MShop_Product_Item_Interface $item product item object
	 * @param array $textMap Associative list of text type codes as key and list of text items
	 */
	protected function _addItem( PHPExcel_Worksheet $sheet, MShop_Locale_Item_Language_Interface $langItem,
		MShop_Product_Item_Interface $item )
	{
		$listTypes = array();

		foreach( $item->getListItems( 'text' ) as $listItem ) {
			$listTypes[ $listItem->getRefId() ] = $listItem->getType();
		}

		foreach( $this->_getTextTypes( 'product' ) as $textTypeItem )
		{
			$textItems = $item->getRefItems( 'text', $textTypeItem->getCode() );

			if( count( $textItems ) > 0 )
			{
				foreach( $textItems as $textItem )
				{
					$listType = ( isset( $listTypes[ $textItem->getId() ] ) ? $listTypes[ $textItem->getId() ] : '' );

					$sheet->setCellValueByColumnAndRow( 0, $this->_sheetLine, $langItem->getId() );
					$sheet->setCellValueByColumnAndRow( 1, $this->_sheetLine, $item->getType() );
					$sheet->setCellValueByColumnAndRow( 2, $this->_sheetLine, $item->getCode() );
					$sheet->setCellValueByColumnAndRow( 3, $this->_sheetLine, $listType );
					$sheet->setCellValueByColumnAndRow( 4, $this->_sheetLine, $textTypeItem->getCode() );

					// use language of the text item because it may be null
					if( ( $textItem->getLanguageId() == $langItem->getId() || is_null( $textItem->getLanguageId() ) )
						&& $textItem->getTypeId() == $textTypeItem->getId() )
					{
						$sheet->setCellValueByColumnAndRow( 0, $this->_sheetLine, $textItem->getLanguageId() );
						$sheet->setCellValueByColumnAndRow( 5, $this->_sheetLine, $textItem->getId() );
						$sheet->setCellValueByColumnAndRow( 6, $this->_sheetLine, $textItem->getContent() );
					}

					$this->_sheetLine++;
				}
			}
			else
			{
				$sheet->setCellValueByColumnAndRow( 0, $this->_sheetLine, $langItem->getId() );
				$sheet->setCellValueByColumnAndRow( 1, $this->_sheetLine, $item->getType() );
				$sheet->setCellValueByColumnAndRow( 2, $this->_sheetLine, $item->getCode() );
				$sheet->setCellValueByColumnAndRow( 3, $this->_sheetLine, 'default' );
				$sheet->setCellValueByColumnAndRow( 4, $this->_sheetLine, $textTypeItem->getCode() );

				$this->_sheetLine++;
			}
		}
	}


	/**
	 * Creates a new worksheet that will be attached to the given document.
	 *
	 * @param PHPExcel $phpExcel Document object
	 * @param string $title Title of the sheet
	 * @return PHPExcel_Worksheet New worksheet attached to the document
	 */
	protected function _createSheet( PHPExcel $phpExcel, $title )
	{
		$sheet = $phpExcel->createSheet();
		$sheet->setTitle( $title );

		$style = $sheet->getDefaultStyle();
		$style->getAlignment()->setWrapText(true);
		$style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		$sheet->getStyle('A1:G1')->getFont()->setBold(true);

		$sheet->getColumnDimension('A')->setAutoSize(true);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		$sheet->getColumnDimension('G')->setWidth(60);

		$sheet->setCellValueByColumnAndRow( 0, 1, 'Language ID' );
		$sheet->setCellValueByColumnAndRow( 1, 1, 'Product type' );
		$sheet->setCellValueByColumnAndRow( 2, 1, 'Product code');
		$sheet->setCellValueByColumnAndRow( 3, 1, 'List type');
		$sheet->setCellValueByColumnAndRow( 4, 1, 'Text type');
		$sheet->setCellValueByColumnAndRow( 5, 1, 'Text ID');
		$sheet->setCellValueByColumnAndRow( 6, 1, 'Text');

		$this->_sheetLine = 2;

		return $sheet;
	}
}
