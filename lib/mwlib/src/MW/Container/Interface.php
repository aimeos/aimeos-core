<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage Container
 */


/**
 * Interface to manage containers like Zip or Excel.
 *
 * @package MW
 * @subpackage Container
 */
interface MW_Container_Interface extends Iterator
{
	/**
	 * Opens an existing container or creates a new one.
	 *
	 * @param string $resourcepath Path to the resource like a file
	 * @param string $format Format of the content objects inside the container
	 * @param array $options Associative list of key/value pairs for configuration
	 */
	public function __construct( $resourcepath, $format, array $options = array() );

	/**
	 * Adds content data to the container.
	 *
	 * @param MW_Container_Content_Interface $content Content object
	 */
	public function add( MW_Container_Content_Interface $content );

	/**
	 * Cleans up and saves the container.
	 */
	public function close();

	/**
	 * Creates a new content object.
	 *
	 * @param string $name Name of the content
	 * @return MW_Container_Content_Interface New content object
	 */
	public function create( $name );
}