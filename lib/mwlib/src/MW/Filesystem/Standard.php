<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage Filesystem
 */


namespace Aimeos\MW\Filesystem;


/**
 * Interface for basic file system methods
 *
 * @package MW
 * @subpackage Filesystem
 */
class Standard extends Base implements BasicIface, DirIface, MetaIface
{
	private $basepath;
	private $finfo;


	/**
	 * Initializes the object
	 *
	 * @param string $basepath Root path to the file system
	 */
	public function __construct( $basepath )
	{
		if( ( $this->finfo = @finfo_open( FILEINFO_MIME_TYPE ) ) === false ) {
			$error = error_get_last();
			throw new Exception( $error['message'] );
		}

		$this->basepath = rtrim( $basepath, '/' ) . '/';
	}


	/**
	 * Cleans up all object resources
	 */
	public function __destruct()
	{
		if( is_resource( $this->finfo ) ) {
			finfo_close( $this->finfo );
		}
	}


	/**
	 * Tests if the given path is a directory
	 *
	 * @param string $path Path to the file or directory
	 * @return boolean True if directory, false if not
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function isdir( $path )
	{
		return is_dir( $this->basepath . $path );
	}


	/**
	 * Creates a new directory for the given path
	 *
	 * @param string $path Path to the directory
	 * @return void
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	*/
	public function mkdir( $path )
	{
		if( @mkdir( $this->basepath . $path ) === false ) {
			$error = error_get_last();
			throw new Exception( $error['message'] );
		}
	}


	/**
	 * Deletes the directory for the given path
	 *
	 * @param string $path Path to the directory
	 * @return void
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	*/
	public function rmdir( $path )
	{
		if( @rmdir( $this->basepath . $path ) === false ) {
			$error = error_get_last();
			throw new Exception( $error['message'] );
		}
	}


	/**
	 * Returns an iterator over the entries in the given path
	 *
	 * {@inheritDoc}
	 *
	 * @param string $path Path to the filesystem or directory
	 * @return \Iterator Iterator over the entries
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function scan( $path = null )
	{
		try {
			return new \DirectoryIterator( $this->basepath . $path );
		} catch( \Exception $e ) {
			throw new Exception( $e->getMessage(), 0, $e );
		}
	}


	/**
	 * Returns the file size
	 *
	 * @param string $path Path to the file
	 * @return integer Size in bytes
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function size( $path )
	{
		if( ( $size = @filesize( $this->basepath . $path ) ) === false ) {
			$error = error_get_last();
			throw new Exception( $error['message'] );
		}

		return $size;
	}


	/**
	 * Returns the mime type of the file
	 *
	 * @param string $path Path to the file
	 * @return string Mime type
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function mime( $path )
	{
		if( ( $mime = @finfo_file( $this->finfo, $this->basepath . $path ) ) === false ) {
			$error = error_get_last();
			throw new Exception( $error['message'] );
		}

		return $mime;
	}


	/**
	 * Returns the Unix time stamp for the file
	 *
	 * @param string $path Path to the file
	 * @return integer Unix time stamp in seconds
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function time( $path )
	{
		if( ( $time = @filemtime( $this->basepath . $path ) ) === false ) {
			$error = error_get_last();
			throw new Exception( $error['message'] );
		}

		return $time;
	}


	/**
	 * Deletes the file for the given path
	 *
	 * @param string $path Path to the file
	 * @return void
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function rm( $path )
	{
		if( @unlink( $this->basepath . $path ) === false ) {
			$error = error_get_last();
			throw new Exception( $error['message'] );
		}
	}


	/**
	 * Tests if a file exists at the given path
	 *
	 * @param string $path Path to the file
	 * @return boolean True if it exists, false if not
	 */
	public function has( $path )
	{
		return file_exists( $this->basepath . $path );
	}


	/**
	 * Returns the content of the file
	 *
	 * {@inheritDoc}
	 *
	 * @param string $path Path to the file
	 * @return string File content
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function read( $path )
	{
		if( ( $content = @file_get_contents( $this->basepath . $path ) ) === false ) {
			$error = error_get_last();
			throw new Exception( $error['message'] );
		}

		return $content;
	}


	/**
	 * Returns the stream descriptor for the file
	 *
	 * {@inheritDoc}
	 *
	 * @param string $path Path to the file
	 * @return resource File stream descriptor
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function reads( $path )
	{
		if( ( $handle = @fopen( $this->basepath . $path, 'r' ) ) === false ) {
			$error = error_get_last();
			throw new Exception( $error['message'] );
		}

		return $handle;
	}


	/**
	 * Writes the given content to the file
	 *
	 * {@inheritDoc}
	 *
	 * @param string $path Path to the file
	 * @param string $content New file content
	 * @param integer $option File options from \Aimeos\MW\Filesystem\Base
	 * @return void
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function write( $path, $content, $option = Base::OPT_NONE )
	{
		if( @file_put_contents( $this->basepath . $path, $content ) === false ) {
			$error = error_get_last();
			throw new Exception( $error['message'] );
		}

		if( $option & \Aimeos\MW\Filesystem\Base::OPT_PRIVATE && @chmod( $this->basepath . $path, 0600 ) === false ) {
			$error = error_get_last();
			throw new Exception( $error['message'] );
		}
	}


	/**
	 * Write the content of the stream descriptor into the remote file
	 *
	 * {@inheritDoc}
	 *
	 * @param string $path Path to the file
	 * @param resource $stream File stream descriptor
	 * @param integer $option File options from \Aimeos\MW\Filesystem\Base
	 * @return void
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function writes( $path, $stream, $option = Base::OPT_NONE )
	{
		if( ( $handle = @fopen( $this->basepath . $path, 'w' ) ) === false ) {
			$error = error_get_last();
			throw new Exception( $error['message'] );
		}

		do
		{
			if( ( $content = @stream_get_contents( $stream, 1048576 ) ) === false ) {
				$error = error_get_last();
				throw new Exception( $error['message'] );
			}

			if( @fwrite( $handle, $content ) === false ) {
				$error = error_get_last();
				throw new Exception( $error['message'] );
			}
		}
		while( $content !== '' );
	}


	/**
	 * Renames a file, moves it to a new location or both at once
	 *
	 * @param string $from Path to the original file
	 * @param string $to Path to the new file
	 * @return void
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function move( $from, $to )
	{
		if( @rename( $this->basepath . $from, $this->basepath . $to ) === false ) {
			$error = error_get_last();
			throw new Exception( $error['message'] );
		}
	}


	/**
	 * Copies a file to a new location
	 *
	 * @param string $from Path to the original file
	 * @param string $to Path to the new file
	 * @return void
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function copy( $from, $to )
	{
		if( @copy( $this->basepath . $from, $this->basepath . $to ) === false ) {
			$error = error_get_last();
			throw new Exception( $error['message'] );
		}
	}
}
