<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Container
 */


namespace Aimeos\MW\Container\Content;


/**
 * Interface to manage content like CSV or Excel.
 *
 * @package MW
 * @subpackage Container
 */
interface Iface extends \Iterator
{
	/**
	 * Adds data to the content object.
	 *
	 * @param mixed $data Content data
	 * @return \Aimeos\MW\Container\Content\Iface Container content instance for method chaining
	 */
	public function add( $data ) : Iface;

	/**
	 * Cleans up and saves the content.
	 * @return \Aimeos\MW\Container\Content\Iface Container content instance for method chaining
	 */
	public function close() : Iface;

	/**
	 * Returns the resource of content object.
	 *
	 * @return string Content resource
	 */
	public function getResource() : string;

	/**
	 * Returns the name of the content object.
	 *
	 * @return string Name of the content object
	 */
	public function getName() : string;
}
