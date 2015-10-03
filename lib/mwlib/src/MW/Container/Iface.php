<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MW
 * @subpackage Container
 */


/**
 * Interface to manage containers like Zip or Excel.
 *
 * @package MW
 * @subpackage Container
 */
interface MW_Container_Iface extends Iterator
{
	/**
	 * Opens an existing container or creates a new one.
	 *
	 * @param string $resourcepath Path to the resource like a file
	 * @param string $format Format of the content objects inside the container
	 * @param array $options Associative list of key/value pairs for configuration
	 * @return void
	 */
	public function __construct( $resourcepath, $format, array $options = array() );

	/**
	 * Adds content data to the container.
	 *
	 * @param MW_Container_Content_Iface $content Content object
	 * @return void
	 */
	public function add( MW_Container_Content_Iface $content );

	/**
	 * Cleans up and saves the container.
	 * @return void
	 */
	public function close();

	/**
	 * Creates a new content object.
	 *
	 * @param string $name Name of the content
	 * @return MW_Container_Content_Iface New content object
	 */
	public function create( $name );

	/**
	 * Returns the element specified by its name.
	 *
	 * @param string $name Name of the content object that should be returned
	 * @return MW_Container_Content_Iface Content object
	 */
	public function get( $name );
}