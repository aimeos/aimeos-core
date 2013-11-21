<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
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
		parent::__construct( $context, 'Attribute_Import_Text' );
	}


	/**
	 * Uploads a XLS file with all attribute texts.
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
		$dir = $config->get( 'controller/extjs/attribute/import/text/default/uploaddir', 'uploads' );

		if( $config->get( 'controller/extjs/attribute/import/text/default/enablecheck', true ) ) {
			$this->_checkFileUpload( $fileinfo['tmp_name'], $fileinfo['error'] );
		}

		$fileext = pathinfo( $fileinfo['name'], PATHINFO_EXTENSION );
		$dest = $dir . DIRECTORY_SEPARATOR . md5( $fileinfo['name'] . time() . getmypid() ) . '.' . $fileext;

		if( rename( $fileinfo['tmp_name'], $dest ) !== true )
		{
			$msg = sprintf( 'Uploaded file could not be moved to upload directory "%1$s"', $dir );
			throw new Controller_ExtJS_Exception( $msg );
		}

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

		$jobController = Controller_ExtJS_Admin_Job_Factory::createController( $this->_getContext() );
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
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$this->_importFile( $entry );
			unlink( $entry );
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
					array( "type" => "string","name" => "site","optional" => false ),
				),
				"returns" => "",
			),
			'Attribute_Import_Text.importFile' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "items","optional" => false ),
				),
				"returns" => "",
			),
		);
	}


	/**
	 * Imports a file that can be understood by PHPExcel.
	 *
	 * @param string $path Path to file for importing
	 */
	protected function _importFile( $path )
	{
		$container = $this->_createContainer( $path, 'attribute' );

		$textTypeMap = array();
		foreach( $this->_getTextTypes( 'attribute' ) as $item ) {
			$textTypeMap[ $item->getCode() ] = $item->getId();
		}

		foreach( $container as $content ) {
			$itemTextMap = $this->_importTextsFromContent( $content, $textTypeMap, 'attribute' );
		}
	}
}
