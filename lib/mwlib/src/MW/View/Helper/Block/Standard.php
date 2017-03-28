<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Block;

use \Aimeos\MW\View\Exception;


/**
 * View helper class for handling template blocks
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Block\Iface
{
	private $blocks = [];
	private $stack = [];


	/**
	 * Returns the block helper
	 *
	 * @return \Aimeos\MW\View\Helper\Iface Block object
	 */
	public function transform()
	{
		return $this;
	}


	/**
	 * Returns the content block for the given name
	 *
	 * @param string $name Name of the block
	 * @return string Content of the block
	 */
	public function get( $name )
	{
		if( isset( $this->blocks[$name] ) ) {
			return $this->blocks[$name];
		}
	}


	/**
	 * Sets the content of a block for the given name
	 *
	 * @param string $name Name of the block
	 * @param string $content Block content
	 */
	public function set( $name, $content )
	{
		$this->blocks[$name] = $content;
	}


	/**
	 * Starts a new content block
	 *
	 * @param string $name Name of the block
	 */
	public function start( $name )
	{
		if( in_array( $name, $this->stack ) ) {
			throw new Exception( sprintf( 'Block "%1$s" has already been started', $name ) );
		}

		$this->stack[] = $name;
		ob_start();
	}


	/**
	 * Stores the current content block
	 */
	public function stop()
	{
		if( ( $name = array_pop( $this->stack ) ) === null ) {
			throw new Exception( sprintf( 'No block has been started before' ) );
		}

		$this->blocks[$name] = ob_get_clean();
	}
}
