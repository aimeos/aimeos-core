<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	private $tempdir;


	/**
	 * Initializes the object
	 *
	 * @param array $config Adapter configuration
	 */
	public function __construct( array $config )
	{
		if( !isset( $config['tempdir'] ) ) {
			$config['tempdir'] = sys_get_temp_dir();
		}

		if( !is_dir( $config['tempdir'] ) && mkdir( $config['tempdir'], 0755, true ) === false ) {
			throw new Exception( sprintf( 'Directory "%1$s" could not be created', $config['tempdir'] ) );
		}

		if( !isset( $config['basedir'] ) ) {
			throw new Exception( sprintf( 'Configuration option "%1$s" missing', 'basedir' ) );
		}

		if( !is_dir( $config['basedir'] ) && mkdir( $config['basedir'], 0755, true ) === false ) {
			throw new Exception( sprintf( 'Directory "%1$s" could not be created', $config['basedir'] ) );
		}

		$ds = DIRECTORY_SEPARATOR;
		$this->basedir = realpath( str_replace( '/', $ds, rtrim( $config['basedir'], '/' ) ) ) . $ds;
		$this->tempdir = realpath( str_replace( '/', $ds, rtrim( $config['tempdir'], '/' ) ) ) . $ds;
	}


	/**
	 * Tests if the given path is a directory
	 *
	 * @param string $path Path to the file or directory
	 * @return bool True if directory, false if not
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function isdir( string $path ) : bool
	{
		return is_dir( $this->resolve( $path ) );
	}


	/**
	 * Creates a new directory for the given path
	 *
	 * @param string $path Path to the directory
	 * @return \Aimeos\MW\Filesystem\DirIface Filesystem object for fluent interface
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function mkdir( string $path ) : DirIface
	{
		if( @mkdir( $this->resolve( $path ), 0775, true ) === false ) {
			throw new Exception( sprintf( 'Couldn\'t create directory "%1$s"', $path ) );
		}

		return $this;
	}


	/**
	 * Deletes the directory for the given path
	 *
	 * @param string $path Path to the directory
	 * @return \Aimeos\MW\Filesystem\DirIface Filesystem object for fluent interface
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function rmdir( string $path ) : DirIface
	{
		if( @rmdir( $this->resolve( $path ) ) === false ) {
			throw new Exception( sprintf( 'Couldn\'t remove directory "%1$s"', $path ) );
		}

		return $this;
	}


	/**
	 * Returns an iterator over the entries in the given path
	 *
	 * @param string|null $path Path to the filesystem or directory
	 * @return iterable Iterator over the entries or array with entries
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function scan( string $path = null ) : iterable
	{
		try {
			return new \DirectoryIterator( $this->resolve( $path ) );
		} catch( \Exception $e ) {
			throw new Exception( $e->getMessage(), 0, $e );
		}
	}


	/**
	 * Returns the file size
	 *
	 * @param string $path Path to the file
	 * @return int Size in bytes
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function size( string $path ) : int
	{
		if( ( $size = @filesize( $this->resolve( $path ) ) ) === false ) {
			throw new Exception( sprintf( 'Couldn\'t get file size for "%1$s"', $path ) );
		}

		return $size;
	}


	/**
	 * Returns the Unix time stamp for the file
	 *
	 * @param string $path Path to the file
	 * @return int Unix time stamp in seconds
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function time( string $path ) : int
	{
		$path = $this->resolve( $path );

		if( ( $mtime = @filemtime( $path ) ) === false ) {
			throw new Exception( sprintf( 'Couldn\'t get file mtime for "%1$s"', $path ) );
		}

		if( ( $ctime = @filectime( $path ) ) === false ) {
			throw new Exception( sprintf( 'Couldn\'t get file ctime for "%1$s"', $path ) );
		}

		return max( $mtime, $ctime );
	}


	/**
	 * Deletes the file for the given path
	 *
	 * @param string $path Path to the file
	 * @return \Aimeos\MW\Filesystem\Iface Filesystem object for fluent interface
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function rm( string $path ) : Iface
	{
		if( @unlink( $this->resolve( $path ) ) === false ) {
			throw new Exception( sprintf( 'Couldn\'t delete file "%1$s"', $path ) );
		}

		return $this;
	}


	/**
	 * Tests if a file exists at the given path
	 *
	 * @param string $path Path to the file
	 * @return bool True if it exists, false if not
	 */
	public function has( string $path ) : bool
	{
		return file_exists( $this->resolve( $path ) );
	}


	/**
	 * Returns the content of the file
	 *
	 * @param string $path Path to the file
	 * @return string File content
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function read( string $path ) : string
	{
		if( ( $content = @file_get_contents( $this->resolve( $path ) ) ) === false ) {
			throw new Exception( sprintf( 'Couldn\'t read file "%1$s"', $path ) );
		}

		return $content;
	}


	/**
	 * Reads the content of the remote file and writes it to a local one
	 *
	 * @param string $path Path to the remote file
	 * @return string Path of the local file
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function readf( string $path ) : string
	{
		if( ( $filename = tempnam( $this->tempdir, 'ai-' ) ) === false ) {
			throw new Exception( sprintf( 'Unable to create file in "%1$s"', $this->tempdir ) );
		}

		if( @copy( $this->resolve( $path ), $filename ) === false ) {
			throw new Exception( sprintf( 'Couldn\'t copy file from "%1$s" to "%2$s"', $path, $filename ) );
		}

		return $filename;
	}


	/**
	 * Returns the stream descriptor for the file
	 *
	 * @param string $path Path to the file
	 * @return resource File stream descriptor
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function reads( string $path )
	{
		if( ( $handle = @fopen( $this->resolve( $path ), 'r' ) ) === false ) {
			throw new Exception( sprintf( 'Couldn\'t read file "%1$s"', $path ) );
		}

		return $handle;
	}


	/**
	 * Writes the given content to the file
	 *
	 * @param string $path Path to the file
	 * @param string $content New file content
	 * @return \Aimeos\MW\Filesystem\Iface Filesystem object for fluent interface
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function write( string $path, string $content ) : Iface
	{
		if( !$this->isDir( dirname( $path ) ) ) {
			$this->mkdir( dirname( $path ) );
		}

		if( @file_put_contents( $this->resolve( $path ), $content ) === false ) {
			throw new Exception( sprintf( 'Couldn\'t write file "%1$s"', $path ) );
		}

		return $this;
	}


	/**
	 * Writes the content of the local file to the remote path
	 *
	 * @param string $path Path to the remote file
	 * @param string $local Path to the local file
	 * @return \Aimeos\MW\Filesystem\Iface Filesystem object for fluent interface
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function writef( string $path, string $local ) : Iface
	{
		if( ( $handle = @fopen( $local, 'r' ) ) === false ) {
			throw new Exception( sprintf( 'Unable to open file "%1$s"', $local ) );
		}

		$this->writes( $path, $handle );

		fclose( $handle );
		return $this;
	}


	/**
	 * Write the content of the stream descriptor into the remote file
	 *
	 * @param string $path Path to the file
	 * @param resource $stream File stream descriptor
	 * @return \Aimeos\MW\Filesystem\Iface Filesystem object for fluent interface
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function writes( string $path, $stream ) : Iface
	{
		if( !$this->isDir( dirname( $path ) ) ) {
			$this->mkdir( dirname( $path ) );
		}

		if( ( $handle = @fopen( $this->resolve( $path ), 'w' ) ) === false ) {
			throw new Exception( sprintf( 'Couldn\'t open file "%1$s"', $path ) );
		}

		try
		{
			if( !is_resource( $stream ) || @stream_copy_to_stream( $stream, $handle ) === false ) {
				throw new Exception( sprintf( 'Couldn\'t copy stream for "%1$s"', $path ) );
			}
		}
		finally
		{
			fclose( $handle );
		}

		return $this;
	}


	/**
	 * Renames a file, moves it to a new location or both at once
	 *
	 * @param string $from Path to the original file
	 * @param string $to Path to the new file
	 * @return \Aimeos\MW\Filesystem\Iface Filesystem object for fluent interface
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function move( string $from, string $to ) : Iface
	{
		if( !$this->isDir( dirname( $to ) ) ) {
			$this->mkdir( dirname( $to ) );
		}

		if( @rename( $this->resolve( $from ), $this->resolve( $to ) ) === false ) {
			throw new Exception( sprintf( 'Couldn\'t move file from "%1$s" to "%2$s"', $from, $to ) );
		}

		return $this;
	}


	/**
	 * Copies a file to a new location
	 *
	 * @param string $from Path to the original file
	 * @param string $to Path to the new file
	 * @return \Aimeos\MW\Filesystem\Iface Filesystem object for fluent interface
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function copy( string $from, string $to ) : Iface
	{
		if( !$this->isDir( dirname( $to ) ) ) {
			$this->mkdir( dirname( $to ) );
		}

		if( @copy( $this->resolve( $from ), $this->resolve( $to ) ) === false ) {
			throw new Exception( sprintf( 'Couldn\'t copy file from "%1$s" to "%2$s"', $from, $to ) );
		}

		return $this;
	}


	/**
	 * Resolves the relative path to the absolute one
	 *
	 * @param string $path Relative path within file system
	 * @return string Absolute path
	 * @throws Exception If relative path is invalid
	 */
	protected function resolve( string $path = null ) : string
	{
		$path = trim( $path, '/' );

		if( strncmp( $path, '../', 3 ) === 0 || strpos( $path, '/../' ) !== false ) {
			throw new Exception( sprintf( 'No ".." allowed in path "%1$s"', $path ) );
		}

		return $this->basedir . str_replace( '/', DIRECTORY_SEPARATOR, $path );
	}
}
