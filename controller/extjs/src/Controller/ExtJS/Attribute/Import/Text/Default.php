<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS attribute text import controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Attribute_Import_Text_Default
	extends Controller_ExtJS_Common_Load_Text_Base
	implements Controller_ExtJS_Common_Load_Text_Iface
{
	/**
	 * Initializes the controller.
	 *
	 * @param MShop_Context_Item_Iface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Iface $context )
	{
		parent::__construct( $context, 'Attribute_Import_Text' );
	}


	/**
	 * Uploads a XLS file with all attribute texts.
	 *
	 * @param stdClass $params Object containing the properties
	 */
	public function uploadFile( stdClass $params )
	{
		$this->checkParams( $params, array( 'site' ) );
		$this->setLocale( $params->site );

		if( ( $fileinfo = reset( $_FILES ) ) === false ) {
			throw new Controller_ExtJS_Exception( 'No file was uploaded' );
		}

		$config = $this->getContext()->getConfig();

		/** controller/extjs/attribute/import/text/default/uploaddir
		 * Upload directory for text files that should be imported
		 *
		 * The upload directory must be an absolute path. Avoid a trailing slash
		 * at the end of the upload directory string!
		 *
		 * @param string Absolute path including a leading slash
		 * @since 2014.03
		 * @category Developer
		 */
		$dir = $config->get( 'controller/extjs/attribute/import/text/default/uploaddir', 'uploads' );

		/** controller/extjs/attribute/import/text/default/enablecheck
		 * Enables checking uploaded files if they are valid and not part of an attack
		 *
		 * This configuration option is for unit testing only! Please don't disable
		 * the checks for uploaded files in production environments as this
		 * would give attackers the possibility to infiltrate your installation!
		 *
		 * @param boolean True to enable, false to disable
		 * @since 2014.03
		 * @category Developer
		 */
		if( $config->get( 'controller/extjs/attribute/import/text/default/enablecheck', true ) ) {
			$this->checkFileUpload( $fileinfo['tmp_name'], $fileinfo['error'] );
		}

		$fileext = pathinfo( $fileinfo['name'], PATHINFO_EXTENSION );
		$dest = $dir . DIRECTORY_SEPARATOR . md5( $fileinfo['name'] . time() . getmypid() ) . '.' . $fileext;

		if( rename( $fileinfo['tmp_name'], $dest ) !== true )
		{
			$msg = sprintf( 'Uploaded file could not be moved to upload directory "%1$s"', $dir );
			throw new Controller_ExtJS_Exception( $msg );
		}

		/** controller/extjs/attribute/import/text/default/fileperms
		 * File permissions used when storing uploaded files
		 *
		 * The representation of the permissions is in octal notation (using 0-7)
		 * with a leading zero. The first number after the leading zero are the
		 * permissions for the web server creating the directory, the second is
		 * for the primary group of the web server and the last number represents
		 * the permissions for everyone else.
		 *
		 * You should use 0660 or 0600 for the permissions as the web server needs
		 * to manage the files. The group permissions are important if you plan
		 * to upload files directly via FTP or by other means because then the
		 * web server needs to be able to read and manage those files. In this
		 * case use 0660 as permissions, otherwise you can limit them to 0600.
		 *
		 * A more detailed description of the meaning of the Unix file permission
		 * bits can be found in the Wikipedia article about
		 * {@link https://en.wikipedia.org/wiki/File_system_permissions#Numeric_notation file system permissions}
		 *
		 * @param integer Octal Unix permission representation
		 * @since 2014.03
		 * @category Developer
		 */
		$perms = $config->get( 'controller/extjs/attribute/import/text/default/fileperms', 0660 );
		if( chmod( $dest, $perms ) !== true )
		{
			$msg = sprintf( 'Could not set permissions "%1$s" for file "%2$s"', $perms, $dest );
			throw new Controller_ExtJS_Exception( $msg );
		}

		$result = (object) array(
			'site' => $params->site,
			'items' => array(
				(object) array(
					'job.label' => 'Attribute text import: ' . $fileinfo['name'],
					'job.method' => 'Attribute_Import_Text.importFile',
					'job.parameter' => array(
						'site' => $params->site,
						'items' => $dest,
					),
					'job.status' => 1,
				),
			),
		);

		$jobController = Controller_ExtJS_Admin_Job_Factory::createController( $this->getContext() );
		$jobController->saveItems( $result );

		return array(
			'items' => $dest,
			'success' => true,
		);
	}


	/**
	 * Imports a XLS file with all attribute texts.
	 *
	 * @param stdClass $params Object containing the properties
	 */
	public function importFile( stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'items' ) );
		$this->setLocale( $params->site );

		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $path )
		{
			/** controller/extjs/attribute/import/text/default/container/type
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
			 * @see controller/extjs/attribute/import/text/default/container/format
			 */

			/** controller/extjs/attribute/import/text/default/container/format
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
			 * @see controller/extjs/attribute/import/text/default/container/type
			 * @see controller/extjs/attribute/import/text/default/container/options
			 */

			/** controller/extjs/attribute/import/text/default/container/options
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
			 * @see controller/extjs/attribute/import/text/default/container/format
			 */
			$container = $this->createContainer( $path, 'controller/extjs/attribute/import/text/default/container' );

			$textTypeMap = array();
			foreach( $this->getTextTypes( 'attribute' ) as $item ) {
				$textTypeMap[$item->getCode()] = $item->getId();
			}

			foreach( $container as $content ) {
				$this->importTextsFromContent( $content, $textTypeMap, 'attribute' );
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
			'Attribute_Import_Text.uploadFile' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
				),
				"returns" => "",
			),
			'Attribute_Import_Text.importFile' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "",
			),
		);
	}
}
