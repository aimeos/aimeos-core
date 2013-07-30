<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */



/**
 * Implementation of the zip manager.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Common_Load_Container_ZIP implements Controller_ExtJS_Common_Load_Container_Interface, Iterator
{
	private $_zip;
	private $_path;
	private $_tmppath;
	private $_position;
	private $_keys;
	private $_data;
	private $_domain;

	/**
	 * Creates empty container.
	 *
	 * @param string $path Path to the created file
	 */
	public function __construct( $path, $domain = null )
	{
		$this->_domain = $domain;
		$this->_zip = new ZipArchive();

		if( is_file( $path ) ) {
			$this->_tmppath = substr( $path, 0,-4 );
			$this->_path = $path;

			$res = $this->_zip->open( $this->_path );

			if($res !== true) {
				throw new Controller_ExtJS_Common_Load_Exception( sprintf( 'Error opening zip file "%1$s"' ), $this->_path );
			}

			if( !$this->_zip->extractTo( $this->_tmppath ) ) {
				throw new Controller_ExtJS_Common_Load_Exception( sprintf( 'Error while extracting files' ) );
			}

			$i = 0;
			while( $name = $this->_zip->getNameIndex( $i++ ) )
			{
				$langid = substr( $name, -6, 2 );
				$format = substr( $name, -3 );
				$this->_data[ $langid ] = $this->createContent( $langid, $format );
			}
		}
		else
		{
			$this->_tmppath = $path;
			$this->_path = $path . '.zip';
			$this->_zip->open( $this->_path, ZipArchive::OVERWRITE );
			$this->_data = array();

			if( mkdir( $this->_tmppath, 0775, true ) === false ) {
				throw new Controller_ExtJS_Common_Load_Exception( sprintf( 'Couldn\'t create directory "%1$s" with permissions 0775', $this->_tmppath ) );
			}
		}

		$this->_position = 0;
		$this->_keys = array_keys( $this->_data );
	}


	/**
	 * Adds data file to the container i.e. csv file.
	 *
	 * @param Controller_ExtJS_Common_Load_Content_Interface $content Content object
	 */
	public function addContent( Controller_ExtJS_Common_Load_Content_Interface $content )
	{
		$langid = $content->getLanguageId();

		if( is_null( $langid ) || isset( $this->_data[ $langid ] ) ) {
			throw new Controller_ExtJS_Common_Load_Exception( sprintf( 'Language id null or already taken' ) );
		}

		$this->_data[ $langid ] = $content;
		$this->_keys[] = $langid;
		$path = $content->getResource();
		$this->_zip->addFile( $path, substr( $path, -6 ) );
	}


	/**
	 * Removes content object specified by language id.
	 *
	 * @param string $langid Language id
	 */
	public function removeContent( $langid )
	{
		if( !isset( $this->_data[ $langid ] ) ) {
			throw new Controller_ExtJS_Common_Load_Exception( sprintf( 'No language "%1$s" found in the container' ) . $langid );
		}

		if( !$this->_zip->deleteName( substr( $this->_data[ $langid ]->getResource(), -6 ) ) ) {
			throw new Controller_ExtJS_Common_Load_Exception( sprintf( 'Error while removing language id: "%1$s"' ),  $langid );
		}

		foreach( $this->_keys as $id => $lang )
		{
			if( $langid === $lang )
			{
				unset( $this->_keys[$id] );
				break;
			}
		}

		unset( $this->_data[ $langid ] );
	}


	/**
	 * Creates content object for specified language id and in specified format.
	 *
	 * @param string $langid Language id
	 * @param string $format Format i.e. csv, xls
	 * @return Controller_ExtJS_Common_Load_Content_Interface $content Content object
	 */
	public function createContent( $langid, $format = 'csv' )
	{
		$filepath = $this->_tmppath . DIRECTORY_SEPARATOR . $langid . '.' . $format;

		switch( strtolower( $format ) )
		{
			case 'csv': return new Controller_ExtJS_Common_Load_Content_CSV( $filepath, $langid );
			default : throw new Controller_ExtJS_Common_Load_Exception( sprintf( 'Undefined file format "%1$s"' ), $format );
		}
	}


	/**
	 * Gets one content object.
	 *
	 * @param string $langid Language id
	 */
	public function get( $langid )
	{
		if( isset( $this->_data[ $langid ] ) ) {
			return $this->_data[ $langid ];
		}

		throw new Controller_ExtJS_Common_Load_Exception( sprintf( 'No content object for the language id "%1$s"' ), $langid );
	}


	/**
	 * Cleans up and saves the file.
	 */
	public function finish()
	{
		$this->_zip->close();

		$this->_removeTempFiles();

		return basename( $this->_path );
	}

	//iterator methods
	function rewind()
	{
		$this->_position = 0;
	}

	function current()
	{
		return $this->_data[ $this->_keys[ $this->_position ] ];
	}

	function key()
	{
		return $this->_keys[ $this->_position ];
	}

	function next()
	{
		++$this->_position;
	}

	function valid()
	{
		return isset( $this->_keys[ $this->_position ] );
	}


	/**
	 * Removes temporary directory and files.
	 *
	 * @param string $path Path to the directory
	 */
	protected function _removeTempFiles()
	{
		foreach( $this->_data as $lang => $content )
		{
			$path = $content->getResource();
			if( is_file( $path ) && !unlink( $path ) ) {
				throw new Controller_ExtJS_Common_Load_Exception( sprintf( 'Unable to remove temporary file "%1$s"' ), $path );
			}
		}

		if( is_dir( $this->_tmppath ) )
		{
			$handle = opendir( $this->_tmppath );

			while ( $file = readdir( $handle ) )
			{
				if( $file !== '.' && $file !== '..' ) {
					if( unlink( $this->_tmppath . DIRECTORY_SEPARATOR . $file ) === false ) {
						throw new Controller_ExtJS_Common_Load_Exception( sprintf( 'Unable to remove temp file "%1$s"' ), $this->_tmppath . DIRECTORY_SEPARATOR . $file );
					}
				}
			}

			closedir( $handle );

			if( rmdir( $this->_tmppath ) === false ) {
				throw new Controller_ExtJS_Common_Load_Exception( sprintf( 'Unable to remove temporary directory "%1$s"' ), $this->_tmppath );
			}
		}
	}
}