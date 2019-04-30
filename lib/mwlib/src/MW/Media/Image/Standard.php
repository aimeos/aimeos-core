<?php

/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	private $image;
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
	 * @param integer $width New width of the image
	 * @param integer $height New height of the image
	 * @param boolean $fit True to keep the width/height ratio of the image
	 * @return \Aimeos\MW\Media\Iface Self object for method chaining
	 */
	public function scale( $width, $height, $fit = true )
	{
		$w = imagesx( $this->image );
		$h = imagesy( $this->image );

		$newWidth = $width;
		$newHeigth = $height;

		if( $fit === true )
		{
			list( $newWidth, $newHeigth ) = $this->getSizeFitted( $w, $h, $width, $height );

			if( $w <= $newWidth && $h <= $newHeigth ) {
				return $this;
			}
		}
		elseif( $width && $height )
		{
			$ratio = ( $w < $h ? $width / $w : $height / $h );
			$newHeigth = $h * $ratio;
			$newWidth = $w * $ratio;
		}

		$this->resize( $newWidth, $newHeigth, $width, $height, $fit );
		return $this;
	}


	/**
	 * Resizes and crops the image if necessary
	 *
	 * @param integer $newWidth Width of the image before cropping
	 * @param integer $newHeigth Height of the image before cropping
	 * @param integer $width New width of the image
	 * @param integer $height New height of the image
	 * @param boolean $fit True to keep the width/height ratio of the image
	 */
	protected function resize( $newWidth, $newHeigth, $width, $height, $fit )
	{
		if( ( $result = imagescale( $this->image, $newWidth, $newHeigth, IMG_BICUBIC ) ) === false ) {
			throw new \Aimeos\MW\Media\Exception( 'Unable to scale image' );
		}

		imagedestroy( $this->image );
		$this->image = $result;

		$x0 = $newWidth / 2 - $width / 2;
		$y0 = $newHeigth / 2 - $height / 2;

		if( $fit == false && ( $x0 || $y0 ) )
		{
			if( ( $newImage = imagecreatetruecolor( $width, $height ) ) === false ) {
				throw new \Aimeos\MW\Media\Exception( 'Unable to create new image' );
			}

			if( imagecopy( $newImage, $this->image, 0, 0, $x0, $y0, $width, $height ) === false ) {
				throw new \Aimeos\MW\Media\Exception( 'Unable to crop image' );
			}

			imagedestroy( $this->image );
			$this->image = $newImage;
		}
	}
}
