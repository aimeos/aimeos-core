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
		$path = $this->basedir . $path;

		if( @mkdir( $path, 0775, true ) === false ) {
			throw new Exception( sprintf( 'Couldn\'t create directory "%1$s"', $this->basedir . $path ) );
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
			throw new Exception( sprintf( 'Couldn\'t remove directory "%1$s"', $this->basedir . $path ) );
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
			throw new Exception( sprintf( 'Couldn\'t get file size for "%1$s"', $this->basedir . $path ) );
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
			throw new Exception( sprintf( 'Couldn\'t get file time for "%1$s"', $this->basedir . $path ) );
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
			throw new Exception( sprintf( 'Couldn\'t delete file "%1$s"', $this->basedir . $path ) );
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
			throw new Exception( sprintf( 'Couldn\'t read file "%1$s"', $this->basedir . $path ) );
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
			throw new Exception( sprintf( 'Couldn\'t read file "%1$s"', $this->basedir . $path ) );
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
			throw new Exception( sprintf( 'Couldn\'t write file "%1$s"', $this->basedir . $path ) );
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
			throw new Exception( sprintf( 'Couldn\'t open file "%1$s"', $this->basedir . $path ) );
		}

		do
		{
			if( ( $content = @stream_get_contents( $stream, 1048576 ) ) === false ) {
				throw new Exception( sprintf( 'Couldn\'t read from stream for "%1$s"', $this->basedir . $path ) );
			}

			if( @fwrite( $handle, $content ) === false ) {
				throw new Exception( sprintf( 'Couldn\'t write to stream for "%1$s"', $this->basedir . $path ) );
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
			throw new Exception( sprintf( 'Couldn\'t move file from "%1$s" to "%2$s"', $this->basedir . $from, $this->basedir . $to ) );
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
			throw new Exception( sprintf( 'Couldn\'t copy file from "%1$s" to "%2$s"', $this->basedir . $from, $this->basedir . $to ) );
		}
	}
}
