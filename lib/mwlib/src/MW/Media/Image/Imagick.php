<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 * @package MW
 * @subpackage Media
 */


namespace Aimeos\MW\Media\Image;


/**
 * Default image class using ImageMagick.
 *
 * @package MW
 * @subpackage Media
 */
class Imagick
	extends \Aimeos\MW\Media\Image\Base
	implements \Aimeos\MW\Media\Image\Iface
{
	private $image;
	private $options;

	private static $wmimg;
	private static $wmpath;
	private static $wmimages;


	/**
	 * Initializes the new image object.
	 *
	 * @param string $content File content
	 * @param string $mimetype Mime type of the media data
	 * @param array $options Associative list of configuration options
	 * @throws \Aimeos\MW\Media\Exception If image couldn't be retrieved from the given file name
	 */
	public function __construct( string $content, string $mimetype, array $options )
	{
		parent::__construct( $mimetype );

		try
		{
			if( in_array( $mimetype, ['image/gif', 'image/png'] ) ) {
				$bg = $options['image']['background'] ?? 'transparent';
			} else {
				$bg = $options['image']['background'] ?? '#ffffff';
			}

			$this->image = new \Imagick();
			$this->image->readImageBlob( $content );
			$this->image->setImageBackgroundColor( $bg );

			if( isset( $options['image']['watermark'] ) && self::$wmpath !== $options['image']['watermark'] )
			{
					self::$wmimg = new \Imagick( realpath( $options['image']['watermark'] ) );
					self::$wmpath = $options['image']['watermark'];
					self::$wmimages = [];
			}
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\MW\Media\Exception( $e->getMessage() );
		}

		$this->options = $options;
	}


	/**
	 * Cleans up
	 */
	public function __destruct()
	{
		if( $this->image ) {
			$this->image->clear();
		}
	}


	/**
	 * Clone resources
	 */
	public function __clone()
	{
		$this->image = clone $this->image;
	}


	/**
	 * Returns the height of the image
	 *
	 * @return int Height in pixel
	 */
	public function getHeight() : int
	{
		return $this->image->getImageHeight();
	}


	/**
	 * Returns the width of the image
	 *
	 * @return int Width in pixel
	 */
	public function getWidth() : int
	{
		return $this->image->getImageWidth();
	}


	/**
	 * Stores the media data at the given file name.
	 *
	 * @param string|null $filename File name to save the data into or null to return the data
	 * @param string|null $mimetype Mime type to save the content as or null to leave the mime type unchanged
	 * @return string|null File content if file name is null or null if data is saved to the given file name
	 * @throws \Aimeos\MW\Media\Exception If image couldn't be saved to the given file name
	 */
	public function save( string $filename = null, string $mimetype = null ) : ?string
	{
		if( $mimetype === null ) {
			$mimetype = $this->getMimeType();
		}

		$quality = 90;
		$mime = explode( '/', $mimetype );

		if( isset( $this->options['image']['quality'] ) ) {
			$quality = max( min( (int) $this->options['image']['quality'], 100 ), 0 );
		}

		try
		{
			$this->image->setImageFormat( $mime[1] );
			$this->image->setImageCompressionQuality( $quality );

			if( $filename === null ) {
				return $this->image->getImagesBlob();
			}

			$this->image->writeImage( $filename );
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\MW\Media\Exception( $e->getMessage() );
		}

		return null;
	}


	/**
	 * Scales the image to the given width and height.
	 *
	 * @param int|null $width New width of the image or null for automatic calculation
	 * @param int|null $height New height of the image or null for automatic calculation
	 * @param int $fit "0" keeps image ratio, "1" adds padding while "2" crops image to enforce image size
	 * @return \Aimeos\MW\Media\Image\Iface Self object for method chaining
	 */
	public function scale( int $width = null, int $height = null, int $fit = 0 ) : \Aimeos\MW\Media\Image\Iface
	{
		try
		{
			$newMedia = clone $this;

			if( $fit === 2 && $width && $height )
			{
				$newMedia->image->cropThumbnailImage( (int) $width, (int) $height );
				// see https://www.php.net/manual/en/imagick.cropthumbnailimage.php#106710
				$newMedia->image->setImagePage( 0, 0, 0, 0 );
			}
			elseif( $fit === 1 && $width && $height )
			{
				$w = ( $width - $newMedia->image->getImageWidth() ) / 2;
				$h = ( $height - $newMedia->image->getImageHeight() ) / 2;

				$newMedia->image->extentImage( $width, $height, (int) -$w, (int) -$h );
			}
			else
			{
				$w = $this->image->getImageWidth();
				$h = $this->image->getImageHeight();

				list( $newWidth, $newHeight ) = $this->getSizeFitted( $w, $h, $width, $height );

				if( $w > $newWidth || $h > $newHeight ) {
					$newMedia->image->resizeImage( $newWidth, $newHeight, \Imagick::FILTER_CUBIC, 0.8 );
				}
			}

			return $this->watermark( $newMedia );
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\MW\Media\Exception( $e->getMessage() );
		}
	}


	/**
	 * Adds the configured water mark to the image
	 *
	 * @param \Aimeos\MW\Media\Image\Iface $media Media object the watermark should be applied to
	 * @return \Aimeos\MW\Media\Image\Iface Media object with watermark
	 */
	protected function watermark( \Aimeos\MW\Media\Image\Iface $media )
	{
		if( self::$wmimg === null ) {
			return $media;
		}

		$wh = self::$wmimg->getImageHeight();
		$ww = self::$wmimg->getImageWidth();

		if( $ww > $media->getWidth() )
		{
			$wh = $media->getWidth() / $ww * $wh;
			$ww = $media->getWidth();
		}

		if( $wh > $media->getHeight() )
		{
			$ww = $media->getHeight() / $wh * $ww;
			$wh = $media->getHeight();
		}

		$ww = round( $ww );
		$wh = round( $wh );

		$dx = round( ( $media->getWidth() - $ww ) / 2 );
		$dy = round( ( $media->getHeight() - $wh ) / 2 );

		if( !isset( self::$wmimages[$ww . 'x' . $wh] ) )
		{
			$wmimage = clone self::$wmimg;
			$wmimage->resizeImage( $ww, $wh, \Imagick::FILTER_CUBIC, 0.8 );
			self::$wmimages[$ww . 'x' . $wh] = $wmimage;
		}
		else
		{
			$wmimage = self::$wmimages[$ww . 'x' . $wh];
		}

		$wmimage->setImageColorspace( $media->image->getImageColorspace() );
		$media->image->compositeImage( $wmimage, \Imagick::COMPOSITE_OVER, $dx, $dy );

		return $media;
	}
}
