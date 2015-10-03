<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MW
 * @subpackage Container
 */


/**
 * Implementation of Zip containers.
 *
 * @package MW
 * @subpackage Container
 */
class MW_Container_Zip
	extends MW_Container_Base
	implements MW_Container_Interface
{
	private $container;
	private $classname;
	private $position = 0;
	private $content = array();
	private $resourcepath;


	/**
	 * Opens an existing container or creates a new one.
	 *
	 * Supported options are:
	 * - tempdir (default: system temp directory)
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

		if( is_file( $resourcepath ) === false && substr( $resourcepath, -4 ) !== '.zip' ) {
			$resourcepath .= '.zip';
		}

		parent::__construct( $resourcepath, $options );

		$this->resourcepath = $resourcepath;

		$this->container = new ZipArchive();
		$this->container->open( $resourcepath, ZipArchive::CREATE );
	}


	/**
	 * Creates a new content object.
	 *
	 * It creates a new file on the storage because the Zip implementation can't
	 * add file streams. This can result in a security issue if the files for
	 * all vhosts are created using the same user account for the web server or
	 * the cron jobs.
	 *
	 * @param string $name Name of the content
	 * @return MW_Container_Content_Interface New content object
	 */
	public function create( $name )
	{
		$tmpfile = tempnam( $this->getOption( 'tempdir', sys_get_temp_dir() ), '' );

		return new $this->classname( $tmpfile, $name, $this->getOptions() );
	}


	/**
	 * Adds content data to the container.
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
	public function get( $name )
	{
		if( $this->container->locateName( $name ) === false )
		{
			$msg = 'No content object "%1$s" available in "%2$s"';
			throw new MW_Container_Exception( sprintf( $msg, $name, $this->container->filename ) );
		}

		// $this->container->getStream( $name ) doesn't work correctly because the stream can't be rewinded
		return new $this->classname( 'zip://' . $this->resourcepath . '#' . $name, $name );
	}


	/**
	 * Cleans up and saves the container.
	 */
	public function close()
	{
		foreach( $this->content as $content )
		{
			$content->close();

			if( $this->container->addFile( $content->getResource(), $content->getName() ) === false )
			{
				$msg = 'Unable to add content in "%1$s" to file "%2$s"';
				throw new MW_Content_Exception( sprinf( $msg, $content->getResource(), $this->container->filename ) );
			}
		}

		if( $this->container->close() === false ) {
			throw new MW_Container_Exception( sprintf( 'Unable to close zip file "%1$s"', $this->container->filename ) );
		}

		foreach( $this->content as $content ) {
			unlink( $content->getResource() );
		}
	}


	/**
	 * Returns the current element.
	 *
	 * @return MW_Container_Content_Interface Current content object
	 */
	function current()
	{
		if( ( $name = $this->container->getNameIndex( $this->position ) ) === false )
		{
			$msg = 'Unable to get name of file at index "%1$s" in "%2$s"';
			throw new MW_Container_Exception( sprintf( $msg, $this->position, $this->container->filename ) );
		}

		// $this->container->getStream( $name ) doesn't work correctly because the stream can't be rewinded
		return new $this->classname( 'zip://' . $this->resourcepath . '#' . $name, $name );
	}


	/**
	 * Returns the key of the current element.
	 *
	 * @return integer Position within the CSV file
	 */
	function key()
	{
		return $this->position;
	}


	/**
	 * Moves forward to next element.
	 */
	function next()
	{
		$this->position++;
	}


	/**
	 * Rewinds the file pointer to the beginning.
	 */
	function rewind()
	{
		$this->position = 0;
	}


	/**
	 * Checks if the current position is valid.
	 *
	 * @return boolean True on success or false on failure
	 */
	function valid()
	{
		return $this->position < $this->container->numFiles;
	}
}