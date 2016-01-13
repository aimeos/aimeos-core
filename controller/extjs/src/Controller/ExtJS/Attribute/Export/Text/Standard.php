<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */



namespace Aimeos\Controller\ExtJS\Attribute\Export\Text;


/**
 * ExtJS attribute text export controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Standard
	extends \Aimeos\Controller\ExtJS\Common\Load\Text\Base
	implements \Aimeos\Controller\ExtJS\Common\Load\Text\Iface
{


	/**
	 * Initializes the controller.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context, 'Attribute_Export_Text' );
	}


	/**
	 * Creates a new job to export a file.
	 *
	 * @param \stdClass $params Object containing the properties, e.g. the list of attribute IDs
	 */
	public function createJob( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'items' ) );
		$this->setLocale( $params->site );

		$context = $this->getContext();

		$items = (array) $params->items;
		$lang = ( property_exists( $params, 'lang' ) ) ? (array) $params->lang : array();

		$languages = ( count( $lang ) > 0 ) ? implode( $lang, '-' ) : 'all';

		$result = (object) array(
			'site' => $params->site,
			'items' => array(
				(object) array(
					'job.label' => 'Attribute text export: ' . $languages,
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

		$jobController = \Aimeos\Controller\ExtJS\Admin\Job\Factory::createController( $context );
		$jobController->saveItems( $result );

		return array(
			'items' => $items,
			'success' => true,
		);
	}


	/**
	 * Exports content files in container.
	 *
	 * @param \stdClass $params Object containing the properties, e.g. the list of attribute IDs
	 */
	public function exportFile( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'items' ) );
		$this->setLocale( $params->site );
		$context = $this->getContext();

		$items = (array) $params->items;
		$lang = ( property_exists( $params, 'lang' ) ) ? (array) $params->lang : array();

		$config = $context->getConfig();

		/** controller/extjs/attribute/export/text/standard/downloaddir
		 * Directory where the exported files can be found through the web
		 *
		 * The exported files are stored in this directory.
		 *
		 * The download directory must be relative to the document root of your
		 * virtual host. If the document root is
		 *
		 *  /var/www/test/htdocs
		 *
		 * and the exported files will be in
		 *
		 *  /var/www/test/htdocs/files/exports
		 *
		 * then the configuration for the download directory must be
		 *
		 *  files/exports
		 *
		 * Avoid leading and trailing slashes for the export directory string!
		 *
		 * @param string Relative path in the URL
		 * @since 2014.03
		 * @category Developer
		 */
		$downloaddir = $config->get( 'controller/extjs/attribute/export/text/standard/downloaddir', 'uploads' );

		$foldername = 'attribute-text-export_' . date( 'Y-m-d_H:i:s' ) . '_' . md5( time() . getmypid() );
		$tmppath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $foldername;

		$filename = $this->exportData( $items, $lang, $tmppath );
		$downloadFile = $downloaddir . '/' . basename( $filename );

		$fs = $context->getFilesystemManager()->get( 'fs-admin' );
		$fs->writef( basename( $filename ), $filename );

		return array(
			'file' => '<a href="' . $downloadFile . '">Download</a>',
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
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
					array( "type" => "array", "name" => "lang", "optional" => true ),
				),
				"returns" => "",
			),
		);
	}


	/**
	 * Gets all data and exports it to the content files.
	 *
	 * @param array $ids List of item IDs that should be part of the document
	 * @param array $lang List of languages to export (empty array for all)
	 * @param string $filename Temporary folder name where to write export files
	 * @return string Path to the exported file
	 */
	protected function exportData( array $ids, array $lang, $filename )
	{
		$context = $this->getContext();
		$manager = \Aimeos\MShop\Locale\Manager\Factory::createManager( $context );
		$globalLanguageManager = $manager->getSubManager( 'language' );

		$search = $globalLanguageManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'locale.language.id' ) ) );

		if( !empty( $lang ) ) {
			$search->setConditions( $search->compare( '==', 'locale.language.id', $lang ) );
		}

		/** controller/extjs/attribute/export/text/standard/container/type
		 * Container file type storing all language files for the exported texts
		 *
		 * When exporting texts, one file or content object is created per
		 * language. All those files or content objects are put into one container
		 * file so editors don't have to download one file for each language.
		 *
		 * The container file types that are supported by default are:
		 * * Zip
		 *
		 * Extensions implement other container types like spread sheets, XMLs or
		 * more advanced ways of handling the exported data.
		 *
		 * @param string Container file type
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */

		/** controller/extjs/attribute/export/text/standard/container/format
		 * Format of the language files for the exported texts
		 *
		 * The exported texts are stored in one file or content object per
		 * language. The format of that file or content object can be configured
		 * with this option but most formats are bound to a specific container
		 * type.
		 *
		 * The formats that are supported by default are:
		 * * CSV (requires container type "Zip")
		 *
		 * Extensions implement other container types like spread sheets, XMLs or
		 * more advanced ways of handling the exported data.
		 *
		 * @param string Content file type
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */

		/** controller/extjs/attribute/export/text/standard/container/options
		 * Options changing the output format of the exported texts
		 *
		 * Each content format may support some configuration options to change
		 * the output for that content type.
		 *
		 * The options for the CSV content format are:
		 * * csv-separator, default ','
		 * * csv-enclosure, default '"'
		 * * csv-escape, default '"'
		 * * csv-lineend, default '\n'
		 *
		 * For format options provided by other container types implemented by
		 * extensions, please have a look into the extension documentation.
		 *
		 * @param array Associative list of options with the name as key and its value
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */
		$containerItem = $this->createContainer( $filename, 'controller/extjs/attribute/export/text/standard/container' );
		$actualLangid = $context->getLocale()->getLanguageId();
		$start = 0;

		do
		{
			$result = $globalLanguageManager->searchItems( $search );

			foreach( $result as $item )
			{
				$langid = $item->getId();

				$contentItem = $containerItem->create( $langid );
				$contentItem->add( array( 'Language ID', 'Attribute type', 'Attribute code', 'List type', 'Text type', 'Text ID', 'Text' ) );
				$context->getLocale()->setLanguageId( $langid );
				$this->addLanguage( $contentItem, $langid, $ids );

				$containerItem->add( $contentItem );
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count == $search->getSliceSize() );

		$context->getLocale()->setLanguageId( $actualLangid );
		$containerItem->close();

		return $containerItem->getName();
	}


	/**
	 * Adds data for the given language.
	 *
	 * @param \Aimeos\MW\Container\Content\Iface $contentItem Content item
	 * @param string $langid Language id
	 * @param array $ids List of of item ids whose texts should be added
	 */
	protected function addLanguage( \Aimeos\MW\Container\Content\Iface $contentItem, $langid, array $ids )
	{
		$manager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $this->getContext() );
		$search = $manager->createSearch();

		if( !empty( $ids ) ) {
			$search->setConditions( $search->compare( '==', 'attribute.id', $ids ) );
		}

		$sort = array(
			$search->sort( '+', 'attribute.siteid' ),
			$search->sort( '+', 'attribute.domain' ),
			$search->sort( '-', 'attribute.code' ),
			$search->sort( '-', 'attribute.typeid' ),
		);
		$search->setSortations( $sort );

		$start = 0;

		do
		{
			$result = $manager->searchItems( $search, array( 'text' ) );

			foreach( $result as $item ) {
				$this->addItem( $contentItem, $item, $langid );
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count == $search->getSliceSize() );
	}


	/**
	 * Adds all texts belonging to an attribute item.
	 *
	 * @param \Aimeos\MW\Container\Content\Iface $contentItem Content item
	 * @param \Aimeos\MShop\Attribute\Item\Iface $item product item object
	 * @param string $langid Language id
	 */
	protected function addItem( \Aimeos\MW\Container\Content\Iface $contentItem, \Aimeos\MShop\Attribute\Item\Iface $item, $langid )
	{
		$listTypes = array();

		foreach( $item->getListItems( 'text' ) as $listItem ) {
			$listTypes[$listItem->getRefId()] = $listItem->getType();
		}

		foreach( $this->getTextTypes( 'attribute' ) as $textTypeItem )
		{
			$textItems = $item->getRefItems( 'text', $textTypeItem->getCode() );

			if( !empty( $textItems ) )
			{
				foreach( $textItems as $textItem )
				{
					$listType = ( isset( $listTypes[$textItem->getId()] ) ? $listTypes[$textItem->getId()] : '' );
					$items = array( $langid, $item->getType(), $item->getCode(), $listType, $textTypeItem->getCode(), '', '' );

					// use language of the text item because it may be null
					if( ( $textItem->getLanguageId() == $langid || is_null( $textItem->getLanguageId() ) )
						&& $textItem->getTypeId() == $textTypeItem->getId() )
					{
						$items[0] = $textItem->getLanguageId();
						$items[5] = $textItem->getId();
						$items[6] = $textItem->getContent();
					}

					$contentItem->add( $items );
				}
			}
			else
			{
				$items = array( $langid, $item->getType(), $item->getCode(), 'default', $textTypeItem->getCode(), '', '' );
				$contentItem->add( $items );
			}
		}
	}
}