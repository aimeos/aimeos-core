<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage Filesystem
 */


namespace Aimeos\MW\Filesystem;


/**
 * Implementation of basic file system methods
 *
 * @package MW
 * @subpackage Filesystem
 */
class Standard implements Iface, DirIface, MetaIface
{
	private $basedir;


	/**
	 * Initializes the object
	 *
	 * @param array $config Adapter configuration
	 */
	public function __construct( array $config )
	{
		if( isset( $config['basedir'] ) ) {
			$this->basedir = rtrim( $config['basedir'], '/' ) . '/';
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
		return is_dir( $this->basedir . $path );
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
		if( @mkdir( $this->basedir . $path, 0775, true ) === false ) {
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
		if( @rmdir( $this->basedir . $path ) === false ) {
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
	 * @return \Iterator|array Iterator over the entries or array with entries
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function scan( $path = null )
	{
		try {
			return new \DirectoryIterator( $this->basedir . $path );
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
		if( ( $size = @filesize( $this->basedir . $path ) ) === false ) {
			$error = error_get_last();
			throw new Exception( $error['message'] );
		}

		return $size;
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
		if( ( $time = @filemtime( $this->basedir . $path ) ) === false ) {
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
		if( @unlink( $this->basedir . $path ) === false ) {
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
		return file_exists( $this->basedir . $path );
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
		if( ( $content = @file_get_contents( $this->basedir . $path ) ) === false ) {
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
		if( ( $handle = @fopen( $this->basedir . $path, 'r' ) ) === false ) {
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
	 * @return void
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function write( $path, $content )
	{
		if( @file_put_contents( $this->basedir . $path, $content ) === false ) {
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
	 * @return void
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function writes( $path, $stream )
	{
		if( ( $handle = @fopen( $this->basedir . $path, 'w' ) ) === false ) {
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
		if( @rename( $this->basedir . $from, $this->basedir . $to ) === false ) {
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
		if( @copy( $this->basedir . $from, $this->basedir . $to ) === false ) {
			$error = error_get_last();
			throw new Exception( $error['message'] );
		}
	}
}
