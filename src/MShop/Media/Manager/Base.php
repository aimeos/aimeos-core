<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 * @package MShop
 * @subpackage Media
 */


namespace Aimeos\MShop\Media\Manager;

use \Enshrined\Svgsanitize\Sanitizer;
use \Intervention\Image\Interfaces\ImageInterface;


/**
 * Base media manager implementation
 *
 * @package MShop
 * @subpackage Media
 */
abstract class Base extends \Aimeos\MShop\Common\Manager\Base
{
	/**
	 * Checks if the mime type is allowed
	 *
	 * @param string $mimetype Mime type
	 * @return bool TRUE if mime type is allowed
	 * @throws \Aimeos\MShop\Media\Exception If mime type is not allowed
	 */
	protected function isAllowed( string $mimetype ) : bool
	{
		$context = $this->context();

		/** mshop/media/manager/allowedtypes
		 * A list of mime types that are allowed for uploaded files
		 *
		 * The list of allowed mime types must be explicitly configured for the
		 * uploaded files. Trying to upload and store a file not available in
		 * the list of allowed mime types will result in an exception.
		 *
		 * @param array List of image mime types
		 * @since 2024.01
		 */
		$default = [
			'image/webp', 'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml',
			'application/epub+zip', 'application/pdf', 'application/zip',
			'video/mp4', 'video/webm',
			'audio/mpeg', 'audio/ogg', 'audio/weba'
		];
		$allowed = $context->config()->get( 'mshop/media/manager/allowedtypes', $default );

		if( !in_array( $mimetype, $allowed ) )
		{
			$msg = sprintf( $context->translate( 'mshop', 'Uploading mimetype "%1$s" is not allowed' ), $mimetype );
			throw new \Aimeos\MShop\Media\Exception( $msg, 406 );
		}

		return true;
	}


	/**
	 * Creates a new file path from the given arguments
	 *
	 * @param string $filepath Original file name, can contain the path as well
	 * @param string $mimetype Mime type
	 * @param string $domain data domain
	 * @return string New file name including the file path
	 */
	protected function path( string $filepath, string $mimetype, string $domain ) : string
	{
		$context = $this->context();

		/** mshop/media/manager/extensions
		 * Available files extensions for mime types of uploaded files
		 *
		 * Uploaded files should have the right file extension (e.g. ".jpg" for
		 * JPEG images) so files are recognized correctly if downloaded by users.
		 * The extension of the uploaded file can't be trusted and only its mime
		 * type can be determined automatically. This configuration setting
		 * provides the file extensions for the configured mime types. You can
		 * add more mime type / file extension combinations if required.
		 *
		 * @param array Associative list of mime types as keys and file extensions as values
		 * @since 2018.04
		 */
		$default = ['image/gif' => 'gif', 'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
		$list = $context->config()->get( 'mshop/media/manager/extensions', $default );

		$filename = basename( $filepath );
		$filename = \Aimeos\Base\Str::slug( substr( $filename, 0, strrpos( $filename, '.' ) ?: null ) );
		$filename = substr( md5( $filename . getmypid() . microtime( true ) ), -8 ) . '_' . $filename;

		$ext = isset( $list[$mimetype] ) ? '.' . $list[$mimetype] : '';
		$siteid = $context->locale()->getSiteId();

		// the "d" after {siteid} is the required extension for Windows (no dots at the end allowed)
		return "{$siteid}d/{$domain}/{$filename[0]}/{$filename[1]}/{$filename}{$ext}";
	}


	/**
	 * Returns the quality level of the resized images
	 *
	 * @return int Quality level from 0 to 100
	 */
	protected function quality() : int
	{
		/** mshop/media/manager/quality
		 * Quality level of saved images
		 *
		 * Qualitity level must be an integer from 0 (worst) to 100 (best).
		 * The higher the quality, the bigger the file size.
		 *
		 * @param int Quality level from 0 to 100
		 * @since 2024.01
		 */
		return $this->context()->config()->get( 'mshop/media/manager/quality', 75 );
	}


	/**
	 * Sanitizes the uploaded file
	 *
	 * @param string $content File content
	 * @param string $mimetype File mime type
	 * @return string Sanitized content
	 */
	protected function sanitize( string $content, string $mimetype ) : string
	{
		if( strncmp( 'image/svg', $mimetype, 9 ) === 0 )
		{
			$sanitizer = new Sanitizer();
			$sanitizer->removeRemoteReferences( true );

			if( ( $content = $sanitizer->sanitize( $content ) ) === false )
			{
				$msg = $this->context()->translate( 'mshop', 'Invalid SVG file: %1$s' );
				throw new \Aimeos\MShop\Media\Exception( sprintf( $msg, print_r( $sanitizer->getXmlIssues(), true ) ) );
			}
		}

		if( $fcn = self::macro( 'sanitize' ) ) {
			$content = $fcn( $content, $mimetype );
		}

		return $content;
	}


	/**
	 * Called after the image has been scaled
	 * Can be used to update the media item with image information.
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item with new preview URLs
	 * @param \Intervention\Image\Interfaces\ImageInterface $image Media object
	 */
	protected function scaled( \Aimeos\MShop\Media\Item\Iface $item, ImageInterface $image )
	{
	}
}
