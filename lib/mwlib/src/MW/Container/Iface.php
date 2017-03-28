<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Container
 */


namespace Aimeos\MW\Container;


/**
 * Interface to manage containers like Zip or Excel.
 *
 * @package MW
 * @subpackage Container
 */
interface Iface extends \Iterator
{
	/**
	 * Opens an existing container or creates a new one.
	 *
	 * @param string $resourcepath Path to the resource like a file
	 * @param string $format Format of the content objects inside the container
	 * @param array $options Associative list of key/value pairs for configuration
	 * @return null
	 */
	public function __construct( $resourcepath, $format, array $options = [] );

	/**
	 * Adds content data to the container.
	 *
	 * @param \Aimeos\MW\Container\Content\Iface $content Content object
	 * @return void
	 */
	public function add( \Aimeos\MW\Container\Content\Iface $content );

	/**
	 * Cleans up and saves the container.
	 * @return void
	 */
	public function close();

	/**
	 * Creates a new content object.
	 *
	 * @param string $name Name of the content
	 * @return \Aimeos\MW\Container\Content\Iface New content object
	 */
	public function create( $name );

	/**
	 * Returns the element specified by its name.
	 *
	 * @param string $name Name of the content object that should be returned
	 * @return \Aimeos\MW\Container\Content\Iface Content object
	 */
	public function get( $name );
}