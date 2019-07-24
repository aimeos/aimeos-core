<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2019
 * @package MW
 * @subpackage Media
 */


namespace Aimeos\MW\Media\Image;


/**
 * Default image class using GDLib.
 *
 * @package MW
 * @subpackage Media
 */
class Standard
	extends \Aimeos\MW\Media\Image\Base
	implements \Aimeos\MW\Media\Image\Iface
{
	private static $watermark = null;

	private $options;
	private $image;


	/**
	 * Initializes the new image object.
	 *
	 * @param string $content File content
	 * @param string $mimetype Mime type of the media data
	 * @param array $options Associative list of configuration options
	 * @throws \Aimeos\MW\Media\Exception If image couldn't be retrieved from the given file name
	 */
	public function __construct( $content, $mimetype, array $options )
	{
		parent::__construct( $mimetype );

		if( !self::$watermark && isset( $options['image']['watermark'] ) )
		{
			if( ( $watermark = @file_get_contents( $options['image']['watermark'] ) ) === false )
			{
				$msg = sprintf( 'Watermark image "%1$s" not found', $options['image']['watermark'] );
				throw new \Aimeos\MW\Media\Exception( $msg );
			}

			if( ( $image = @imagecreatefromstring( $watermark ) ) === false ) {
				throw new \Aimeos\MW\Media\Exception( sprintf( 'The watermark image isn\'t supported by GDlib') );
			}

			self::$watermark = $image;
		}

		if( ( $this->image = @imagecreatefromstring( $content ) ) === false ) {
			throw new \Aimeos\MW\Media\Exception( sprintf( 'The image type isn\'t supported by GDlib') );
		}

		if( imagealphablending( $this->image, true ) === false ) {
			throw new \Aimeos\MW\Media\Exception( sprintf( 'GD library failed (imagealphablending)') );
		}

		$this->options = $options;
	}


	/**
	 * Cleans up
	 */
	public function __destruct()
	{
		if( $this->image ) {
			imagedestroy( $this->image );
		}
	}


	/**
	 * Returns the height of the image
	 *
	 * @return integer Height in pixel
	 */
	public function getHeight()
	{
		return imagesy( $this->image );
	}


	/**
	 * Returns the width of the image
	 *
	 * @return integer Width in pixel
	 */
	public function getWidth()
	{
		return imagesx( $this->image );
	}


	/**
	 * Stores the media data at the given file name.
	 *
	 * @param string|null $filename File name to save the data into or null to return the data
	 * @param string|null $mimetype Mime type to save the content as or null to leave the mime type unchanged
	 * @return string|null File content if file name is null or null if data is saved to the given file name
	 * @throws \Aimeos\MW\Media\Exception If image couldn't be saved to the given file name
	 */
	public function save( $filename = null, $mimetype = null )
	{
		if( $mimetype === null ) {
			$mimetype = $this->getMimeType();
		}

		if( self::$watermark !== null ) {
			$this->watermark();
		}

		$quality = 90;
		if( isset( $this->options['image']['quality'] ) ) {
			$quality = max( min( (int) $this->options['image']['quality'], 100 ), 0 );
		}

		try
		{
			ob_start();

			switch( $mimetype )
			{
				case 'image/gif':

					if( @imagegif( $this->image, $filename ) === false ) {
						throw new \Aimeos\MW\Media\Exception( sprintf( 'Unable to save image to file "%1$s"', $filename ) );
					}

					break;

				case 'image/jpeg':

					if( @imagejpeg( $this->image, $filename, $quality ) === false ) {
						throw new \Aimeos\MW\Media\Exception( sprintf( 'Unable to save image to file "%1$s"', $filename ) );
					}

					break;

				case 'image/png':

					if( imagesavealpha( $this->image, true ) === false ) {
						throw new \Aimeos\MW\Media\Exception( sprintf( 'GD library failed (imagesavealpha)') );
					}

					if( @imagepng( $this->image, $filename, (int) 10 - $quality / 10 ) === false ) {
						throw new \Aimeos\MW\Media\Exception( sprintf( 'Unable to save image to file "%1$s"', $filename ) );
					}

					break;

				default:
					throw new \Aimeos\MW\Media\Exception( sprintf( 'File format "%1$s" is not supported', $mimetype ) );
			}

			if( $filename === null ) {
				return ob_get_clean();
			}
		}
		catch( \Exception $e )
		{
			ob_end_clean();
			throw $e;
		}
	}


	/**
	 * Scales the image to the given width and height.
	 *
	 * @param integer|null $width New width of the image or null for automatic calculation
	 * @param integer|null $height New height of the image or null for automatic calculation
	 * @param boolean $fit True to keep the width/height ratio of the image
	 * @return \Aimeos\MW\Media\Iface Self object for method chaining
	 */
	public function scale( $width, $height, $fit = true )
	{
		$w = imagesx( $this->image );
		$h = imagesy( $this->image );
		$fit = (bool) $fit;

		$newWidth = $width;
		$newHeight = $height;

		if( $fit === true )
		{
			list( $newWidth, $newHeight ) = $this->getSizeFitted( $w, $h, $width, $height );

			if( $w <= $newWidth && $h <= $newHeight ) {
				return $this;
			}
		}
		elseif( $width && $height )
		{
			$ratio = ( $w < $h ? $width / $w : $height / $h );
			$newHeight = (int) $h * $ratio;
			$newWidth = (int) $w * $ratio;
		}

		return $this->resize( $newWidth, $newHeight, $width, $height, $fit );
	}


	/**
	 * Resizes and crops the image if necessary
	 *
	 * @param integer $scaleWidth Width of the image before cropping
	 * @param integer $scaleHeight Height of the image before cropping
	 * @param integer $width New width of the image
	 * @param integer $height New height of the image
	 * @param boolean $fit True to keep the width/height ratio of the image
	 * @return \Aimeos\MW\Media\Image\Standard Resized media object
	 */
	protected function resize( $scaleWidth, $scaleHeight, $width, $height, $fit )
	{
		if( ( $result = imagescale( $this->image, $scaleWidth, $scaleHeight, IMG_BICUBIC ) ) === false ) {
			throw new \Aimeos\MW\Media\Exception( 'Unable to scale image' );
		}

		$newMedia = clone $this;

		$width = ( $width ?: $scaleWidth );
		$height = ( $height ?: $scaleHeight );

		$x0 = (int) ( $scaleWidth / 2 - $width / 2 );
		$y0 = (int) ( $scaleHeight / 2 - $height / 2 );

		if( $fit === false && ( $x0 || $y0 ) )
		{
			if( ( $newImage = imagecreatetruecolor( $width, $height ) ) === false ) {
				throw new \Aimeos\MW\Media\Exception( 'Unable to create new image' );
			}

			if( imagecopy( $newImage, $result, 0, 0, $x0, $y0, $width, $height ) === false ) {
				throw new \Aimeos\MW\Media\Exception( 'Unable to crop image' );
			}

			imagedestroy( $result );
			$newMedia->image = $newImage;
		}
		else
		{
			$newMedia->image = $result;
		}

		return $newMedia;
	}


	/**
	 * Adds the configured water mark to the image
	 */
	protected function watermark()
	{
		$ww = imagesx( self::$watermark );
		$wh = imagesy( self::$watermark );

		$ratio = min( $this->getWidth() / $ww, $this->getHeight() / $wh );
		$newHeight = (int) ($wh * $ratio);
		$newWidth = (int) ($ww * $ratio);

		if( ( $wimage = imagescale( self::$watermark, $newWidth, $newHeight, IMG_BICUBIC ) ) === false ) {
			throw new \Aimeos\MW\Media\Exception( 'Unable to scale image' );
		}

		$dx = (int) ( $this->getWidth() - $newWidth ) / 2;
		$dy = (int) ( $this->getHeight() - $newHeight ) / 2;

		if( imagecopy( $this->image, $wimage, $dx, $dy, 0, 0, $newWidth, $newHeight ) === false ) {
			throw new \Aimeos\MW\Media\Exception( sprintf( 'Failed to apply watermark immage to file "%1$s"', $filename ) );
		}

		imagedestroy( $wimage );
	}
}
