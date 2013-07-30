<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * Interface to manage file content like csv or excel.
 *
 * @package Controller
 * @subpackage ExtJS
 */
interface Controller_ExtJS_Common_Load_Content_Interface
{
	/**
	 * Initialize manager for content entries.
	 *
	 * @param string $path Path to the result file
	 * @param string $title Title or filename
	 */
	public function __construct( $path, $title );


	/**
	 * Adds row to the content object.
	 *
	 * @param array $data Data to add
	 */
	public function addRow( array $data );


	/**
	 * Gets path of actual file.
	 */
	public function getResource();


	/**
	 * Gets language id of actual content object.
	 */
	public function getLanguageId();
}