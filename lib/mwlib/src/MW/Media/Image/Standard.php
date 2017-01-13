<?php

/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2017
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
	private $info;
	private $image;
	private $origimage;
	private $options;


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

		if( ( $this->image = @imagecreatefromstring( $content ) ) === false ) {
			throw new \Aimeos\MW\Media\Exception( sprintf( 'The image type isn\'t supported by GDlib.') );
		}

		if( ( $this->info = getimagesizefromstring( $content ) ) === false ) {
			throw new \Aimeos\MW\Media\Exception( sprintf( 'Unable to retrieve image size' ) );
		}

		if( imagealphablending( $this->image, false ) === false ) {
			throw new \Aimeos\MW\Media\Exception( sprintf( 'GD library failed (imagealphablending)') );
		}

		$this->options = $options;
	}


	/**
	 * Cleans up
	 */
	public function __destruct()
	{
		if( $this->origimage ) {
			imagedestroy( $this->origimage );
		}

		if( $this->image ) {
			imagedestroy( $this->image );
		}
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
					throw new \Aimeos\MW\Media\Exception( sprintf( 'File format "%1$s" is not supported', $this->getMimeType() ) );
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
	 * @param integer $width New width of the image
	 * @param integer $height New height of the image
	 * @param boolean $fit True to keep the width/height ratio of the image
	 * @return \Aimeos\MW\Media\Iface Self object for method chaining
	 */
	public function scale( $width, $height, $fit = true )
	{
		if( $fit === true )
		{
			list( $width, $height ) = $this->getSizeFitted( $this->info[0], $this->info[1], $width, $height );

			if( $this->info[0] <= $width && $this->info[1] <= $height ) {
				return $this;
			}
		}

		if( function_exists( 'imagescale' ) === true )
		{
			if( ( $result = imagescale( $this->image, $width, $height, IMG_BICUBIC ) ) === false ) {
				throw new \Aimeos\MW\Media\Exception( 'Unable to scale image' );
			}

			$this->image = $result;
		}
		else
		{
			$this->image = $this->resample( $this->image, $this->info[0], $this->info[1], $width, $height );
		}

		$this->info[0] = $width;
		$this->info[1] = $height;

		return $this;
	}


	/**
	 * Resamples the image to the given width and height.
	 *
	 * @param resource GDlib image object
	 * @param integer $srcWidth Width of the existing image
	 * @param integer $srcHeight Height of the existing image
	 * @param integer $destWidth Width of the new image
	 * @param integer $destHeight Height of the new image
	 * @return resource New GDlib image object
	 */
	protected function resample( $image, $srcWidth, $srcHeight, $destWidth, $destHeight )
	{
		if( ( $newImage = imagecreatetruecolor( $destWidth, $destHeight ) ) === false ) {
			throw new \Aimeos\MW\Media\Exception( 'Unable to create new image' );
		}

		try
		{
			if( imagealphablending( $newImage, false ) === false ) {
				throw new \Aimeos\MW\Media\Exception( sprintf( 'GD library failed (imagealphablending)') );
			}

			if( ( $transparent = imagecolorallocatealpha( $newImage, 255, 255, 255, 127 ) ) === false ) {
				throw new \Aimeos\MW\Media\Exception( sprintf( 'GD library failed (imagecolorallocatealpha)') );
			}

			if( imagefilledrectangle( $newImage, 0, 0, $destWidth, $destHeight, $transparent ) === false ) {
				throw new \Aimeos\MW\Media\Exception( sprintf( 'GD library failed (imagefilledrectangle)') );
			}

			if( imagecopyresampled( $newImage, $image, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight ) === false ) {
				throw new \Aimeos\MW\Media\Exception( 'Unable to resize image' );
			}
		}
		catch( \Exception $e )
		{
			imagedestroy( $newImage );
			throw $e;
		}

		return $newImage;
	}
}
