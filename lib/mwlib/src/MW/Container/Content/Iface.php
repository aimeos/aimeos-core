<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
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
