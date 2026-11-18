<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 * @package MShop
 */


namespace Aimeos\MShop;

use \Psr\Http\Message\UploadedFileInterface;


/**
 * Upload trait
 *
 * @package MShop
 */
trait Upload
{
	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\ContextIface Context object
	 */
	abstract protected function context() : \Aimeos\MShop\ContextIface;


	/**
	 * Deletes a file from the file system
	 *
	 * @param string $filepath Relative path to the file in the file system
	 * @param string $fsname File system name
	 * @return self Same object for fluent interface
	 */
	protected function deleteFile( string $filepath, string $fsname = 'fs-media' ) : self
	{
		$fs = $this->context()->fs( $fsname );

		if( $filepath && $fs->has( $filepath ) ) {
			$fs->rm( $filepath );
		}

		return $this;
	}


	/**
	 * Returns the file mime type for the uploaded file
	 *
	 * Caution: This method must be called before storeFile() to be able to
	 * determine the file mime type!
	 *
	 * @param \Psr\Http\Message\UploadedFileInterface $file Uploaded file object
	 * @return string File mime type
	 */
	protected function mimetype( UploadedFileInterface $file ) : string
	{
		$stream = $file->getStream();

		if( !$stream->isSeekable() ) {
			return '';
		}

		$stream->rewind();
		$content = $stream->read( 100 );
		$stream->rewind();

		$finfo = new \finfo( FILEINFO_MIME_TYPE );
		return $finfo->buffer( $content ) ?: 'application/octet-stream';
	}


	/**
	 * Stores the uploaded file
	 *
	 * @param \Psr\Http\Message\UploadedFileInterface $file Uploaded file object
	 * @param string $filepath Relative path to the file in the file system
	 * @param string $fsname File system name
	 * @return self Same object for fluent interface
	 */
	protected function storeFile( UploadedFileInterface $file, string $filepath, string $fsname = 'fs-media' ) : self
	{
		if( ( $code = $file->getError() ) !== UPLOAD_ERR_OK )
		{
			$errors = [
				0 => 'There is no error, the file uploaded with success',
				1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
				2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
				3 => 'The uploaded file was only partially uploaded',
				4 => 'No file was uploaded',
				6 => 'Missing a temporary folder',
				7 => 'Failed to write file to disk.',
				8 => 'A PHP extension stopped the file upload.',
			];

			throw new \RuntimeException( $errors[$code] ?? sprintf( 'An unknown error occured with code %1$d', $code ) );
		}

		$fs = $this->context()->fs( $fsname );

		if( ( $fs instanceof \Aimeos\Base\Filesystem\DirIface ) && !$fs->has( $dirname = dirname( $filepath ) ) ) {
			$fs->mkdir( $dirname );
		}

		if( ( $mime = $this->mimetype( $file ) ) === '' ) {
			throw new \RuntimeException( 'Unable to determine the file type of the uploaded file' );
		}

		if( strncmp( 'image/svg', $mime, 9 ) === 0 ) {
			$fs->write( $filepath, $this->sanitizeSvg( (string) $file->getStream() ) );
		} else {
			// @phpstan-ignore argument.type
			$fs->writes( $filepath, $file->getStream()->detach() );
		}

		return $this;
	}


	/**
	 * Removes scripts and other unsafe content from the SVG image
	 *
	 * @param string $content SVG image content
	 * @return string Sanitized SVG image content
	 * @throws \Aimeos\MShop\Exception If the SVG image is invalid
	 */
	protected function sanitizeSvg( string $content ) : string
	{
		$sanitizer = new \enshrined\svgSanitize\Sanitizer();
		$sanitizer->removeRemoteReferences( true );

		$msg = $this->context()->translate( 'mshop', 'Invalid SVG file: %1$s' );

		try {
			$result = $sanitizer->sanitize( $content );
		} catch( \Throwable $t ) { // svg-sanitize throws on some malformed input
			throw new \Aimeos\MShop\Exception( sprintf( $msg, $t->getMessage() ), previous: $t );
		}

		if( $result === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( $msg, print_r( $sanitizer->getXmlIssues(), true ) ) );
		}

		return $result;
	}
}
