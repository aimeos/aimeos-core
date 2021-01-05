<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 * @package MW
 * @subpackage Container
 */


namespace Aimeos\MW\Container;


/**
 * Implementation of a single file container
 *
 * @package MW
 * @subpackage Container
 */
class File
	extends \Aimeos\MW\Container\Base
	implements \Aimeos\MW\Container\Iface
{
	private $content;
	private $classname;
	private $pointer = true;


	/**
	 * Opens an existing file or creates a new one.
	 *
	 * @param string $resourcepath Path to the resource like a file
	 * @param string $format Format of the content objects inside the container
	 * @param array $options Associative list of key/value pairs for configuration
	 */
	public function __construct( string $resourcepath, string $format, array $options = [] )
	{
		$this->classname = '\Aimeos\MW\Container\Content\\' . $format;

		if( class_exists( $this->classname ) === false ) {
			throw new \Aimeos\MW\Container\Exception( sprintf( 'Unknown format "%1$s"', $format ) );
		}

		parent::__construct( $resourcepath, $options );
	}


	/**
	 * Creates a new content object.
	 *
	 * @param string $name Name of the content
	 * @return \Aimeos\MW\Container\Content\Iface New content object
	 */
	public function create( string $name ) : \Aimeos\MW\Container\Content\Iface
	{
		if( $this->content ) {
			throw new \Aimeos\MW\Container\Exception( sprintf( 'Only one content object is possible within a file container' ) );
		}

		return new $this->classname( $this->getName(), $name, $this->getOptions() );
	}


	/**
	 * Adds a content object to the container.
	 *
	 * @param \Aimeos\MW\Container\Content\Iface $content Content object
	 * @return \Aimeos\MW\Container\Iface Container instance for method chaining
	 */
	public function add( \Aimeos\MW\Container\Content\Iface $content ) : Iface
	{
		if( $this->content ) {
			throw new \Aimeos\MW\Container\Exception( sprintf( 'Only one content object is possible within a file container' ) );
		}

		$this->content = $content;
		return $this;
	}


	/**
	 * Returns the element specified by its name.
	 *
	 * @param string $name Name of the content object that should be returned
	 * @return \Aimeos\MW\Container\Content\Iface Content object
	 */
	public function get( string $name ) : \Aimeos\MW\Container\Content\Iface
	{
		if( $this->content == null ) {
			$this->content = new $this->classname( $this->getName(), $name, $this->getOptions() );
		}

		return $this->content;
	}


	/**
	 * Cleans up and saves the container.
	 *
	 * @return \Aimeos\MW\Container\Iface Container instance for method chaining
	 */
	public function close() : Iface
	{
		$this->content->close();
		return $this;
	}


	/**
	 * Returns the current element.
	 *
	 * @return \Aimeos\MW\Container\Content\Iface Current content object
	 */
	public function current()
	{
		if( $this->content == null ) {
			$this->content = new $this->classname( $this->getName(), basename( $this->getName() ), $this->getOptions() );
		}

		return $this->content;
	}


	/**
	 * Returns the key of the current element.
	 *
	 * @return string Position within the directory
	 */
	public function key()
	{
		return $this->getName();
	}


	/**
	 * Moves forward to next element.
	 */
	public function next()
	{
		$this->pointer = false;
	}


	/**
	 * Rewinds the file pointer to the beginning.
	 */
	public function rewind()
	{
		$this->pointer = true;
	}


	/**
	 * Checks if the current position is valid.
	 *
	 * @return boolean True on success or false on failure
	 */
	public function valid()
	{
		return $this->pointer;
	}
}
