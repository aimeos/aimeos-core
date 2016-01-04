<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Request\Stream;


/**
 * Helper class for accessing file streams
 *
 * @package MW
 * @subpackage View
 */
class Standard implements \Psr\Http\Message\StreamInterface
{
	private $filehandle;

	/**
	 * Initializes the stream helper object
	 *
	 * @param resource $filehandle File handle to the uploaded file
	 */
	public function __construct( $filehandle )
	{
		$this->filehandle = $filehandle;
	}


	/**
	 * Reads all data from the stream into a string, from the beginning to end.
	 *
	 * This method MUST attempt to seek to the beginning of the stream before
	 * reading data and read the stream until the end is reached.
	 *
	 * Warning: This could attempt to load a large amount of data into memory.
	 *
	 * This method MUST NOT raise an exception in order to conform with PHP's
	 * string casting operations.
	 *
	 * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
	 * @return string
	 */
	public function __toString()
	{
		rewind( $this->filehandle );

		if( ( $content = stream_get_contents( $this->filehandle ) ) === false ) {
			throw new \RuntimeException( 'Unable to read from uploaded file' );
		}

		return $content;
	}


	/**
	 * Closes the stream and any underlying resources.
	 */
	public function close()
	{
		fclose( $this->filehandle );
	}


	/**
	 * Separates any underlying resources from the stream.
	 *
	 * After the stream has been detached, the stream is in an unusable state.
	 *
	 * @return resource|null Underlying PHP stream, if any
	 */
	public function detach()
	{
		return $this->filehandle;
	}


	/**
	 * Get the size of the stream if known.
	 *
	 * @throws \RuntimeException Not implemented
	 */
	public function getSize()
	{
		throw new \RuntimeException( 'Not implemented' );
	}


	/**
	 * Returns the current position of the file read/write pointer
	 *
	 * @throws \RuntimeException Not implemented
	 */
	public function tell()
	{
		throw new \RuntimeException( 'Not implemented' );
	}


	/**
	 * Returns true if the stream is at the end of the stream.
	 *
	 * @throws \RuntimeException Not implemented
	 */
	public function eof()
	{
		throw new \RuntimeException( 'Not implemented' );
	}


	/**
	 * Returns whether or not the stream is seekable.
	 *
	 * @throws \RuntimeException Not implemented
	 */
	public function isSeekable()
	{
		throw new \RuntimeException( 'Not implemented' );
	}


	/**
	 * Seek to a position in the stream.
	 *
	 * @throws \RuntimeException Not implemented
	 */
	public function seek($offset, $whence = SEEK_SET)
	{
		throw new \RuntimeException( 'Not implemented' );
	}


	/**
	 * Seek to the beginning of the stream.
	 *
	 * @throws \RuntimeException Not implemented
	 */
	public function rewind()
	{
		throw new \RuntimeException( 'Not implemented' );
	}


	/**
	 * Returns whether or not the stream is writable.
	 *
	 * @throws \RuntimeException Not implemented
	 */
	public function isWritable()
	{
		throw new \RuntimeException( 'Not implemented' );
	}


	/**
	 * Write data to the stream.
	 *
	 * @throws \RuntimeException Not implemented
	 */
	public function write($string)
	{
		throw new \RuntimeException( 'Not implemented' );
	}


	/**
	 * Returns whether or not the stream is readable.
	 *
	 * @throws \RuntimeException Not implemented
	 */
	public function isReadable()
	{
		throw new \RuntimeException( 'Not implemented' );
	}


	/**
	 * Read data from the stream.
	 *
	 * @throws \RuntimeException Not implemented
	 */
	public function read($length)
	{
		throw new \RuntimeException( 'Not implemented' );
	}


	/**
	 * Returns the remaining contents in a string
	 *
	 * @throws \RuntimeException Not implemented
	 */
	public function getContents()
	{
		throw new \RuntimeException( 'Not implemented' );
	}


	/**
	 * Get stream metadata as an associative array or retrieve a specific key.
	 *
	 * @throws \RuntimeException Not implemented
	 */
	public function getMetadata($key = null)
	{
		throw new \RuntimeException( 'Not implemented' );
	}
}
