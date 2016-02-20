<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */



namespace Aimeos\Controller\ExtJS\Product\Import\Text;


/**
 * ExtJS product text import controller for admin interfaces.
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
		parent::__construct( $context, 'Product_Import_Text' );
	}


	/**
	 * Uploads a CSV file with all product texts.
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
					'job.label' => 'Product text import: ' . $clientFilename,
					'job.method' => 'Product_Import_Text.importFile',
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
	 * Imports a CSV file with all product texts.
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

			/** controller/extjs/product/import/text/standard/container/type
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
			 * @see controller/extjs/product/import/text/standard/container/format
			 */

			/** controller/extjs/product/import/text/standard/container/format
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
			 * @see controller/extjs/product/import/text/standard/container/type
			 * @see controller/extjs/product/import/text/standard/container/options
			 */

			/** controller/extjs/product/import/text/standard/container/options
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
			 * @see controller/extjs/product/import/text/standard/container/format
			 */
			$container = $this->createContainer( $tmpfile, 'controller/extjs/product/import/text/standard/container' );

			$textTypeMap = array();
			foreach( $this->getTextTypes( 'product' ) as $item ) {
				$textTypeMap[$item->getCode()] = $item->getId();
			}

			foreach( $container as $content ) {
				$this->importTextsFromContent( $content, $textTypeMap, 'product' );
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
			'Product_Import_Text.uploadFile' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
				),
				"returns" => "",
			),
			'Product_Import_Text.importFile' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "",
			),
		);
	}
}