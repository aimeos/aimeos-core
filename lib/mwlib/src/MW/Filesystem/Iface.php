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
	 * @param string $path Path to the filesystem or directory
	 * @return \Iterator|array Iterator over the entries or array with entries
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function scan( $path = null );

	/**
	 * Deletes the file for the given path
	 *
	 * @param string $path Path to the file
	 * @return void
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function rm( $path );

	/**
	 * Tests if a file exists at the given path
	 *
	 * @param string $path Path to the file
	 * @return boolean True if it exists, false if not
	 */
	public function has( $path );

	/**
	 * Returns the content of the file
	 *
	 * This method should only be used for small files as the content will be
	 * held in memory. Using it for bigger files may lead to out of memory
	 * conditions. The reads() method can cope with files of all sizes.
	 *
	 * @param string $path Path to the file
	 * @return string File content
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function read( $path );

	/**
	 * Returns the stream descriptor for the file
	 *
	 * Reading from a file stream is the better alternative for files bigger
	 * than a few kilobyte. To read from a file in stream mode and write it to
	 * a local file:
	 *
	 *  $localfile = '/path/to/local/file';
	 *  if( ( $writehandle = fopen( $localfile, 'w' ) ) === false ) {
	 *      throw new \Exception( $localfile );
	 *  }
	 *
	 *  $readhandle = $fs->reads( '/path/to/remote/file' );
	 *
	 *  while( ( $content = stream_get_contents( $handle, 1024000 ) ) != false ) {
	 *      if( fwrite( $writehandle, $content ) === false ) {
	 *          throw new \Exception( $localfile );
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
	 * @param string $path Path to the file
	 * @return resource File stream descriptor
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function reads( $path );

	/**
	 * Writes the given content to the file
	 *
	 * If the file already exists, the its content will be overwritten. This
	 * method is only suited for smaller files.
	 *
	 * @param string $path Path to the file
	 * @param string $content New file content
	 * @return void
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function write( $path, $content );

	/**
	 * Write the content of the stream descriptor into the remote file
	 *
	 * Writing a file stream to a remote file is the better alternative for
	 * files bigger than a few kilobyte. To read from a local file and write
	 * to a remote file in stream mode:
	 *
	 *  $localfile = '/path/to/local/file';
	 *  if( ( $readhandle = fopen( $localfile, 'r' ) ) === false ) {
	 *      throw new \Exception( $localfile );
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
	 * @param string $path Path to the file
	 * @param resource $stream File stream descriptor
	 * @return void
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function writes( $path, $stream );

	/**
	 * Renames a file, moves it to a new location or both at once
	 *
	 * @param string $from Path to the original file
	 * @param string $to Path to the new file
	 * @return void
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function move( $from, $to );

	/**
	 * Copies a file to a new location
	 *
	 * @param string $from Path to the original file
	 * @param string $to Path to the new file
	 * @return void
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function copy( $from, $to );
}