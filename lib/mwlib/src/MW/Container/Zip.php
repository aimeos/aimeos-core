<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
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
	extends MW_Container_Abstract
	implements MW_Container_Interface
{
	private $_container;
	private $_classname;
	private $_position = 0;
	private $_content = array();
	private $_options;


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
		parent::__construct( $options );

		$this->_classname = 'MW_Container_Content_' . $format;

		if( !class_exists( $this->_classname) ) {
			throw new MW_Container_Exception( sprintf( 'Unknown format "%1$s"', $format ) );
		}

		$this->_options = $options;

		$this->_container = new ZipArchive();
		$this->_container->open( $resourcepath, ZipArchive::CREATE );
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
		$tmpfile = tempnam( $this->_getOption( 'tempdir', sys_get_temp_dir() ), '' );

		return new $this->_classname( $tmpfile, $name, $this->_options );
	}


	/**
	 * Adds content data to the container.
	 *
	 * @param MW_Container_Content_Interface $content Content object
	 */
	public function add( MW_Container_Content_Interface $content )
	{
		$this->_content[] = $content;
	}


	/**
	 * Cleans up and saves the container.
	 */
	public function close()
	{
		foreach( $this->_content as $content )
		{
			$content->close();

			if( $this->_container->addFile( $content->getResource(), $content->getName() ) === false )
			{
				$msg = 'Unable to add content in "%1$s" to file "%2$s"';
				throw new MW_Content_Exception( sprinf( $msg, $content->getResource(), $this->_container->filename ) );
			}
		}

		if( $this->_container->close() === false ) {
			throw new MW_Container_Exception( sprintf( 'Unable to close zip file "%1$s"', $this->_container->filename ) );
		}

		foreach( $this->_content as $content ) {
			unlink( $content->getResource() );
		}
	}


	/**
	 * Return the current element.
	 *
	 * @return array List of values
	 */
	function current()
	{
		if( ( $name = $this->_container->getNameIndex( $this->_position ) ) === false )
		{
			$msg = 'Unable to get name of file at index "%1$s" in "%2$s"';
			throw new MW_Container_Exception( sprintf( $msg, $this->_position, $this->_container->filename ) );
		}

		if( ( $resource = $this->_container->getStream( $name ) ) === false )
		{
			$msg = 'Unable to get stream for "%1$s" in "%2$s"';
			throw new MW_Container_Exception( $msg, $name, $this->_container->filename );
		}

		return new $this->_classname( $resource, $name );
	}


	/**
	 * Returns the key of the current element.
	 *
	 * @return integer Position within the CSV file
	 */
	function key()
	{
		return $this->_position;
	}


	/**
	 * Moves forward to next element.
	 */
	function next()
	{
		$this->_position++;
	}


	/**
	 * Rewinds the file pointer to the beginning.
	 */
	function rewind()
	{
		$this->_position = 0;
	}


	/**
	 * Checks if the current position is valid.
	 *
	 * @return boolean True on success or false on failure
	 */
	function valid()
	{
		return $this->_position < $this->_container->numFiles;
	}
}