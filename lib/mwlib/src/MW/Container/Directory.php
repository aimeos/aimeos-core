<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Container
 */


namespace Aimeos\MW\Container;


/**
 * Implementation of directory containers.
 *
 * @package MW
 * @subpackage Container
 */
class Directory
	extends \Aimeos\MW\Container\Base
	implements \Aimeos\MW\Container\Iface
{
	private $content = [];
	private $classname;
	private $resource;


	/**
	 * Opens an existing file or creates a new one.
	 *
	 * Supported options are:
	 * - dir-perm (default: 0755)
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

		$perm = octdec( $this->getOption( 'dir-perm', '0755' ) );

		if( !is_dir( realpath( $resourcepath ) ) && mkdir( $resourcepath, $perm, true ) === false ) {
			throw new \Aimeos\MW\Container\Exception( sprintf( 'Unable to create directory "%1$s"', $resourcepath ) );
		}

		$this->resource = new \DirectoryIterator( $resourcepath );
	}


	/**
	 * Creates a new content object.
	 *
	 * @param string $name Name of the content
	 * @return \Aimeos\MW\Container\Content\Iface New content object
	 */
	public function create( string $name ) : \Aimeos\MW\Container\Content\Iface
	{
		$resource = $this->resource->getPath() . DIRECTORY_SEPARATOR . $name;
		return new $this->classname( $resource, $name, $this->getOptions() );
	}


	/**
	 * Adds a content object to the container.
	 *
	 * @param \Aimeos\MW\Container\Content\Iface $content Content object
	 * @return \Aimeos\MW\Container\Iface Container instance for method chaining
	 */
	public function add( \Aimeos\MW\Container\Content\Iface $content ) : Iface
	{
		$this->content[] = $content;
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
		return new $this->classname( $this->resource->getPath() . DIRECTORY_SEPARATOR . $name, $name, $this->getOptions() );
	}


	/**
	 * Cleans up and saves the container.
	 *
	 * @return \Aimeos\MW\Container\Iface Container instance for method chaining
	 */
	public function close() : Iface
	{
		foreach( $this->content as $content ) {
			$content->close();
		}

		return $this;
	}


	/**
	 * Returns the current element.
	 *
	 * @return \Aimeos\MW\Container\Content\Iface Current content object
	 */
	public function current()
	{
		return new $this->classname( $this->resource->getPathname(), $this->resource->getFilename(), $this->getOptions() );
	}


	/**
	 * Returns the key of the current element.
	 *
	 * @return string Position within the directory
	 */
	public function key()
	{
		return $this->resource->key();
	}


	/**
	 * Moves forward to next element.
	 */
	public function next()
	{
		do {
			$this->resource->next();
		}
		while( $this->resource->valid()
			&& ( $this->resource->isDot() || !is_file( realpath( $this->resource->getPathname() ) ) )
		);
	}


	/**
	 * Rewinds the file pointer to the beginning.
	 */
	public function rewind()
	{
		$this->resource->rewind();
	}


	/**
	 * Checks if the current position is valid.
	 *
	 * @return boolean True on success or false on failure
	 */
	public function valid()
	{
		while( $this->resource->isDot() ) {
			$this->next();
		}

		return $this->resource->valid();
	}
}
