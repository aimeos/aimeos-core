<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage Container
 */


/**
 * Interface to manage content like CSV or Excel.
 *
 * @package MW
 * @subpackage Container
 */
interface MW_Container_Content_Interface extends Iterator
{
	/**
	 * Adds data to the content object.
	 *
	 * @param mixed $data Content data
	 * @return void
	 */
	public function add( $data );

	/**
	 * Cleans up and saves the content.
	 * @return void
	 */
	public function close();

	/**
	 * Returns the resource of content object.
	 *
	 * @return mixed Content resource
	 */
	public function getResource();

	/**
	 * Returns the name of the content object.
	 *
	 * @return string Name of the content object
	 */
	public function getName();
}
