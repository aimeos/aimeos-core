<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * Interface to manage containers like zip or excel.
 *
 * @package Controller
 * @subpackage ExtJS
 */
interface Controller_ExtJS_Common_Load_Container_Interface
{
	/**
	 * Creates empty container.
	 *
	 * @param string $path Path to the created file
	 */
	public function __construct( $path, $domain = null );


	/**
	 * Adds data file to the container i.e. csv file.
	 *
	 * @param Controller_ExtJS_Common_Load_Content_Interface $content Content object
	 */
	public function addContent( Controller_ExtJS_Common_Load_Content_Interface $content );


	/**
	 * Removes content object specified by language id.
	 *
	 * @param string $langid Language id
	 */
	public function removeContent( $langid );


	/**
	 * Creates content object for specified language id and in specified format.
	 *
	 * @param string $langid Language id
	 * @param string $format Optional format i.e. csv, xls
	 * @return Controller_ExtJS_Common_Load_Content_Interface $content Content object
	 */
	public function createContent( $langid, $format = '' );


	/**
	 * Gets one content object.
	 *
	 * @param string $langid Language id
	 * @return Controller_ExtJS_Common_Load_Content_Interface $content Content object
	 */
	public function get( $langid );


	/**
	 * Cleans up and saves the file.
	 */
	public function finish();
}