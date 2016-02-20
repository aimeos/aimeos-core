<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Catalog\Import\Text;


/**
 * ExtJS catalog text import controller for admin interfaces.
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
		parent::__construct( $context, 'Catalog_Import_Text' );
	}


	/**
	 * Uploads a CSV file with all catalog texts.
	 *
	 * @param \stdClass $params Object containing the properties
	 */
	public function uploadFile( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site' ) );
		$this->setLocale( $params->site );

		$clientFilename = '';
		$context = $this->getContext();

		$request = $context->getView()->request();
		$dest = $this->storeFile( $request, $clientFilename );

		$result = (object) array(
			'site' => $params->site,
			'items' => array(
				(object) array(
					'job.label' => 'Catalog text import: ' . $clientFilename,
					'job.method' => 'Catalog_Import_Text.importFile',
					'job.parameter' => array(
						'site' => $params->site,
						'items' => $dest,
					),
					'job.status' => 1,
				),
			),
		);

		$jobController = \Aimeos\Controller\ExtJS\Factory::createController( $context, 'admin/job' );
		$jobController->saveItems( $result );

		return array(
			'items' => $dest,
			'success' => true,
		);
	}


	/**
	 * Imports a CSV file with all catalog texts.
	 *
	 * @param \stdClass $params Object containing the properties
	 */
	public function importFile( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'items' ) );
		$this->setLocale( $params->site );

		$fs = $this->getContext()->getFilesystemManager()->get( 'fs-admin' );
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $path )
		{
			$tmpfile = $fs->readf( $path );

			/** controller/extjs/catalog/import/text/standard/container/type
			 * Container file type storing all language files of the texts to import
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
			 * @see controller/extjs/catalog/import/text/standard/container/format
			 */

			/** controller/extjs/catalog/import/text/standard/container/format
			 * Format of the language files for the texts to import
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
			 * @see controller/extjs/catalog/import/text/standard/container/type
			 * @see controller/extjs/catalog/import/text/standard/container/options
			 */

			/** controller/extjs/catalog/import/text/standard/container/options
			 * Options changing the expected format for the texts to import
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
			 * @see controller/extjs/catalog/import/text/standard/container/format
			 */
			$container = $this->createContainer( $tmpfile, 'controller/extjs/catalog/import/text/standard/container' );

			$textTypeMap = array();
			foreach( $this->getTextTypes( 'catalog' ) as $item ) {
				$textTypeMap[$item->getCode()] = $item->getId();
			}

			foreach( $container as $content ) {
				$this->importTextsFromContent( $content, $textTypeMap, 'catalog' );
			}

			unlink( $tmpfile );
			$fs->rm( $path );
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
					array( "type" => "string", "name" => "site", "optional" => false ),
				),
				"returns" => "",
			),
			'Catalog_Import_Text.importFile' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "",
			),
		);
	}


	/**
	 * Associates the texts with the products.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object (attribute, product, etc.) for associating the list items
	 * @param array $itemTextMap Two dimensional associated list of codes and text IDs as key
	 * @param string $domain Name of the domain this text belongs to, e.g. product, catalog, attribute
	 */
	protected function importReferences( \Aimeos\MShop\Common\Manager\Iface $manager, array $itemTextMap, $domain )
	{
		$catalogStart = $catalogTotal = 0;
		$listManager = $manager->getSubManager( 'lists' );

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
				$expr = array(
					$criteria->compare( '==', 'catalog.lists.parentid', $catalogIds ),
					$criteria->compare( '==', 'catalog.lists.domain', 'text' ),
				);
				$criteria->setConditions( $criteria->combine( '&&', $expr ) );
				$listItems = $listManager->searchItems( $criteria, array(), $listTotal );
				$listStart += count( $catalogItems );

				foreach( $listItems as $item ) {
					unset( $itemTextMap[$item->getParentId()][$item->getRefId()] );
				}
			}
			while( $listStart < $listTotal );

		}
		while( $catalogStart < $catalogTotal );


		$listTypes = $this->getTextListTypes( $manager, 'catalog' );

		foreach( $itemTextMap as $catalogCode => $textIds )
		{
			foreach( $textIds as $textId => $listType )
			{
				try
				{
					$iface = '\\Aimeos\\MShop\\Common\\Item\\Type\\Iface';
					if( !isset( $listTypes[$listType] ) || ( $listTypes[$listType] instanceof $iface ) === false ) {
						throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Invalid list type "%1$s"', $listType ) );
					}

					$item = $listManager->createItem();
					$item->setParentId( $catalogCode );
					$item->setTypeId( $listTypes[$listType]->getId() );
					$item->setDomain( 'text' );
					$item->setRefId( $textId );

					$listManager->saveItem( $item );
				}
				catch( \Exception $e )
				{
					$this->getContext()->getLogger()->log( 'catalog text reference: ' . $e->getMessage(), \Aimeos\MW\Logger\Base::ERR, 'import' );
				}
			}
		}
	}
}