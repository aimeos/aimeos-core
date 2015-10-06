<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage Container
 */


/**
 * Implementation of directory containers.
 *
 * @package MW
 * @subpackage Container
 */
class MW_Container_Directory
	extends MW_Container_Abstract
	implements MW_Container_Interface
{
	private $content = array();
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
	public function __construct( $resourcepath, $format, array $options = array() )
	{
		$this->classname = 'MW_Container_Content_' . $format;

		if( class_exists( $this->classname ) === false ) {
			throw new MW_Container_Exception( sprintf( 'Unknown format "%1$s"', $format ) );
		}

		parent::__construct( $resourcepath, $options );
		
		$perm = octdec( $this->getOption( 'dir-perm', '0755' ) );

		if( !is_dir( realpath( $resourcepath ) ) && mkdir( $resourcepath, $perm, true ) === false ) {
			throw new MW_Container_Exception( sprintf( 'Unable to create directory "%1$s"', $resourcepath ) );
		}

		$this->resource = new DirectoryIterator( $resourcepath );
	}


	/**
	 * Creates a new content object.
	 *
	 * @param string $name Name of the content
	 * @return MW_Container_Content_Interface New content object
	 */
	public function create( $name )
	{
		$resource = $this->resource->getPath() . DIRECTORY_SEPARATOR . $name;
		return new $this->classname( $resource, $name, $this->getOptions() );
	}


	/**
	 * Adds a content object to the container.
	 *
	 * @param MW_Container_Content_Interface $content Content object
	 */
	public function add( MW_Container_Content_Interface $content )
	{
		$this->content[] = $content;
	}


	/**
	 * Returns the element specified by its name.
	 *
	 * @param string $name Name of the content object that should be returned
	 * @return MW_Container_Content_Interface Content object
	 */
	function get( $name )
	{
		return new $this->classname( $this->resource->getPath() . DIRECTORY_SEPARATOR . $name, $name );
	}


	/**
	 * Cleans up and saves the container.
	 */
	public function close()
	{
		foreach( $this->content as $content ) {
			$content->close();
		}
	}


	/**
	 * Returns the current element.
	 *
	 * @return MW_Container_Content_Interface Current content object
	 */
	function current()
	{
		return new $this->classname( $this->resource->getPathname(), $this->resource->getFilename() );
	}


	/**
	 * Returns the key of the current element.
	 *
	 * @return integer Position within the directory
	 */
	function key()
	{
		return $this->resource->key();
	}


	/**
	 * Moves forward to next element.
	 */
	function next()
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
	function rewind()
	{
		$this->resource->rewind();
	}


	/**
	 * Checks if the current position is valid.
	 *
	 * @return boolean True on success or false on failure
	 */
	function valid()
	{
		while( $this->resource->isDot() ) {
			$this->next();
		}

		return $this->resource->valid();
	}
}