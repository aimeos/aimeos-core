<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */



/**
 * ExtJS catalog text export controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Catalog_Export_Text_CSV
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
		parent::__construct( $context, 'Catalog_Export_Text' );
	}


	/**
	 * Creates a new job to export an csv file.
	 *
	 * @param stdClass $params Object containing the properties, e.g. the list of catalog IDs
	 */
	public function createJob( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$config = $this->_getContext()->getConfig();
		$dir = $config->get( 'controller/extjs/catalog/export/text/default/exportdir', 'uploads' );

		$items = (array) $params->items;
		$lang = ( property_exists( $params, 'lang' ) ) ? (array) $params->lang : array();

		$languages = ( !empty( $lang ) ) ? implode( $lang, '-' ) : 'all';

		$result = (object) array(
			'site' => $params->site,
			'items' => array(
				(object) array(
					'job.label' => 'Catalog text export: '. $languages,
					'job.method' => 'Catalog_Export_Text.exportFile',
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
	 * @param stdClass $params Object containing the properties, e.g. the list of catalog IDs
	 */
	public function exportFile( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );
		$actualLangid = $this->_getContext()->getLocale()->getLanguageId();

		$items = (array) $params->items;
		$lang = ( property_exists( $params, 'lang' ) ) ? (array) $params->lang : array();

		$config = $this->_getContext()->getConfig();
		$dir = $config->get( 'controller/extjs/catalog/export/text/default/exportdir', 'uploads' );
		$perms = $config->get( 'controller/extjs/catalog/export/text/default/dirperms', 0775 );

		$foldername = 'catalog-text-export_' . date('Y-m-d') . '_' . md5( time() . getmypid() );
		$tmpfolder = $dir . DIRECTORY_SEPARATOR . $foldername;

		if( is_dir( $dir ) === false && mkdir( $dir, $perms, true ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Couldn\'t create directory "%1$s" with permissions "%2$o"', $dir, $perms ) );
		}

		if( mkdir( $tmpfolder, $perms, true ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Couldn\'t create directory "%1$s" with permissions "%2$o"', $tmpfolder, $perms ) );
		}

		$this->_getContext()->getLogger()->log( sprintf( 'Create export directory for catalog IDs: %1$s', implode( ',', $items ) ), MW_Logger_Abstract::DEBUG );

		try
		{
			$files = $this->_exportData( $items, $lang, $tmpfolder );

			$this->_getContext()->getLocale()->setLanguageId( $actualLangid );

			$filename = $this->_createZip( $foldername, $dir, $files );
		}
		catch ( Exception $e )
		{
			$this->_removeTempFiles( $tmpfolder );
			throw $e;
		}

		$this->_removeTempFiles( $tmpfolder );

		return array(
			'file' => '<a href="'.$filename.'">Download</a>',
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
			'Catalog_Export_Text.createHttpOutput' => array(
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
	 * Exports data to csv files.
	 *
	 * @param array $ids List of item IDs that should be part of the document
	 * @param array $lang List of languages to export (empty array for all)
	 * @param string $tmpfolder Temporary folder name where to write export files
	 * @return array List of exported files
	 */
	protected function _exportData( array $ids, array $lang, $tmpfolder )
	{
		$manager = MShop_Locale_Manager_Factory::createManager( $this->_getContext() );
		$globalLanguageManager = $manager->getSubManager( 'language' );

		$search = $globalLanguageManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'locale.language.id' ) ) );

		if( !empty( $lang ) ) {
			$search->setConditions( $search->compare( '==', 'locale.language.id', $lang ) );
		}

		$start = 0;

		do
		{
			$result = $globalLanguageManager->searchItems( $search );

			foreach ( $result as $item )
			{
				$langid = $item->getId();
				$files[$langid] = $tmpfolder . DIRECTORY_SEPARATOR . $langid . '.csv';
				$fh = fopen( $files[$langid], 'a' );
				fputcsv( $fh, array( 'Language ID', 'Product type', 'Product code', 'List type', 'Text type', 'Text ID', 'Text' ) );
				$this->_addLanguage( $langid, $ids, $fh );
				fclose( $fh );
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count == $search->getSliceSize() );

		return $files;
	}


	/**
	 * Adds a new sheet for the given language to the document.
	 *
	 * @param string $langid Language id
	 * @param array $ids List of item ids
	 * @param resource $fp File handler
	 */
	protected function _addLanguage( $langid, array $ids, $fp )
	{
		$manager = MShop_Catalog_Manager_Factory::createManager( $this->_getContext() );

		foreach( $ids as $id )
		{
			foreach( $this->_getNodeList( $manager->getTree( $id, array('text') ) ) as $item ) {
				$this->_addItem( $langid, $item, $fp );
			}
		}
	}


	/**
	 * Adds all texts belonging to an catalog item to the csv file.
	 *
	 * @param string $langid Language id
	 * @param MShop_Catalog_Item_Interface $item Catalog node object
	 * @param resource $fh File handler
	 */
	protected function _addItem( $langid, MShop_Catalog_Item_Interface $item, $fh )
	{
		$listTypes = array();
		foreach( $item->getListItems( 'text' ) as $listItem ) {
			$listTypes[ $listItem->getRefId() ] = $listItem->getType();
		}

		foreach( $this->_getTextTypes( 'catalog' ) as $textTypeItem )
		{
			$textItems = $item->getRefItems( 'text', $textTypeItem->getCode() );

			if( !empty( $textItems ) )
			{
				foreach( $textItems as $textItem )
				{
					$listType = ( isset( $listTypes[ $textItem->getId() ] ) ? $listTypes[ $textItem->getId() ] : '' );

					$items = array( $langid, $item->getLabel(), $item->getId(), $listType, $textTypeItem->getCode(), '', '' );

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
				$items = array( $langid, $item->getLabel(), $item->getId(), 'default', $textTypeItem->getCode(), '', '' );
			}

			fputcsv( $fh, $items );
		}
	}


	/**
	 * Get all child nodes.
	 *
	 * @param MShop_Catalog_Item_Interface $node
	 * @return array $nodes List of nodes
	 */
	protected function _getNodeList( MShop_Catalog_Item_Interface $node )
	{
		$nodes = array( $node );

		foreach( $node->getChildren() as $child ) {
			$nodes = array_merge( $nodes, $this->_getNodeList( $child ) );
		}

		return $nodes;
	}


	/**
	 * Creates zip from csv files in a temp folder.
	 *
	 * @param string $srcdir Temporary source directory name
	 * @param string $destdir Destination directory for export files
	 * @param array $files List of file names
	 * @throws Exception if a file couldn't be created or removed
	 */
	protected function _createZip( $srcdir, $destdir, $files )
	{
		$zip = new ZipArchive();
		$filename = $destdir . DIRECTORY_SEPARATOR . $srcdir . '.zip';

		$zip->open( $filename, ZipArchive::OVERWRITE );

		foreach( $files as $id => $file ) {
			$zip->addFile( $file, substr( $file, -6 ) );
		}

		$zip->close();

		if( !file_exists($filename) ) {
			throw new Exception( 'Unable to create zip file');
		}

		return $filename;
	}
}