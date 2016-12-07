<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Block;


/**
 * View helper class for handling template blocks
 *
 * @package MW
 * @subpackage View
 */
interface Iface extends \Aimeos\MW\View\Helper\Iface
{
	/**
	 * Returns the block helper
	 *
	 * @return \Aimeos\MW\View\Helper\Iface Block object
	 */
	public function transform();

	/**
	 * Returns the content block for the given name
	 *
	 * @param string $name Name of the block
	 * @return string Content of the block
	 */
	public function get( $name );

	/**
	 * Sets the content of a block for the given name
	 *
	 * @param string $name Name of the block
	 * @param string $content Block content
	 * @return null
	 */
	public function set( $name, $content );

	/**
	 * Starts a new content block
	 *
	 * @param string $name Name of the block
	 * @return null
	 */
	public function start( $name );

	/**
	 * Stores the current content block
	 *
	 * @return null
	 */
	public function stop();
}
