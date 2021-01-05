<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
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
interface Iface
{
	/**
	 * Returns the entries in the given path
	 *
	 * This method returns an iterator or array!
	 * To get the file name, you have to convert the entry to a string value:
	 *
	 *  foreach( $fs->scan() as $entry ) {
	 *      echo (string) $entry . "\n";
	 *  }
	 *
	 * @param string|null $path Path to the filesystem or directory
	 * @return iterable Iterator over the entries or array with entries
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function scan( string $path = null ) : iterable;

	/**
	 * Deletes the file for the given path
	 *
	 * @param string $path Path to the file
	 * @return \Aimeos\MW\Filesystem\Iface Filesystem object for fluent interface
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function rm( string $path ) : Iface;

	/**
	 * Tests if a file exists at the given path
	 *
	 * @param string $path Path to the file
	 * @return bool True if it exists, false if not
	 */
	public function has( string $path ) : bool;

	/**
	 * Returns the content of the remote file
	 *
	 * This method should only be used for small files as the content will be
	 * held in memory. Using it for bigger files may lead to out of memory
	 * conditions. The reads() method can cope with files of all sizes.
	 *
	 * @param string $path Path to the remote file
	 * @return string File content
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function read( string $path ) : string;

	/**
	 * Reads the content of the remote file and writes it to a local one
	 *
	 * @param string $path Path to the remote file
	 * @return string Path of the local file
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function readf( string $path ) : string;

	/**
	 * Returns the stream descriptor for the remote file
	 *
	 * Reading from a file stream is the better alternative for files bigger
	 * than a few kilobyte. To read from a file in stream mode and write it to
	 * a local file:
	 *
	 *  $localfile = '/path/to/local/file';
	 *  if( ( $writehandle = fopen( $localfile, 'w' ) ) === false ) {
	 *      throw new \RuntimeException( $localfile );
	 *  }
	 *
	 *  $readhandle = $fs->reads( '/path/to/remote/file' );
	 *
	 *  while( ( $content = stream_get_contents( $handle, 1024000 ) ) != false ) {
	 *      if( fwrite( $writehandle, $content ) === false ) {
	 *          throw new \RuntimeException( $localfile );
	 *      }
	 *  }
	 *
	 *  fclose( $writehandle );
	 *  if( is_resource( $readhandle ) {
	 *      fclose( $readhandle );
	 *  }
	 *
	 * Checking if "$readhandle" is a resource is necessary to avoid errors
	 * because some drivers will close the handle automatically!
	 *
	 * If you want to copy the remote file to a local file only, you can also use
	 * the readf() method instead which implements the code listed above
	 *
	 * @param string $path Path to the remote file
	 * @return resource File stream descriptor
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function reads( string $path );

	/**
	 * Writes the given content to the file
	 *
	 * If the file already exists, its content will be overwritten. This
	 * method is only suited for smaller files.
	 *
	 * @param string $path Path to the remote file
	 * @param string $content New file content
	 * @return \Aimeos\MW\Filesystem\Iface Filesystem object for fluent interface
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function write( string $path, string $content ) : Iface;

	/**
	 * Writes the content of the local file to the remote path
	 *
	 * If the local file already exists, its content will be overwritten.
	 *
	 * @param string $path Path to the remote file
	 * @param string $file Path to the local file
	 * @return \Aimeos\MW\Filesystem\Iface Filesystem object for fluent interface
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function writef( string $path, string $file ) : Iface;

	/**
	 * Write the content of the stream descriptor into the remote file
	 *
	 * Writing a file stream to a remote file is the better alternative for
	 * files bigger than a few kilobyte. To read from a local file and write
	 * to a remote file in stream mode:
	 *
	 *  $localfile = '/path/to/local/file';
	 *  if( ( $readhandle = fopen( $localfile, 'r' ) ) === false ) {
	 *      throw new \RuntimeException( $localfile );
	 *  }
	 *
	 *  $fs->writes( '/path/to/remote/file', $readhandle );
	 *
	 *  if( is_resource( $readhandle ) {
	 *      fclose( $readhandle );
	 *  }
	 *
	 * Checking if "$readhandle" is a resource is necessary to avoid errors
	 * because some drivers will close the handle automatically!
	 *
	 * @param string $path Path to the remote file
	 * @param resource $stream File stream descriptor
	 * @return \Aimeos\MW\Filesystem\Iface Filesystem object for fluent interface
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function writes( string $path, $stream ) : Iface;

	/**
	 * Renames a file, moves it to a new location or both at once
	 *
	 * @param string $from Path to the original file
	 * @param string $to Path to the new file
	 * @return \Aimeos\MW\Filesystem\Iface Filesystem object for fluent interface
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function move( string $from, string $to ) : Iface;

	/**
	 * Copies a file to a new location
	 *
	 * @param string $from Path to the original file
	 * @param string $to Path to the new file
	 * @return \Aimeos\MW\Filesystem\Iface Filesystem object for fluent interface
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function copy( string $from, string $to ) : Iface;
}
