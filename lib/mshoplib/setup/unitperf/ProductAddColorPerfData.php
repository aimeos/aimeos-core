<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds product color attribute performance records.
 */
class ProductAddColorPerfData extends \Aimeos\MW\Setup\Task\ProductAddBasePerfData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductAddBasePerfData', 'MShopAddTypeDataUnitperf' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildPerfIndex' );
	}


	/**
	 * Insert text data and product/text relations.
	 */
	public function migrate()
	{
		$this->msg( 'Adding product color attribute performance data', 0 );


		$colors = array(
			'#FFEBCD' => 'almond', '#E52B50' => 'amaranth', '#3B7A57' => 'amazon', '#FFBF00' => 'amber', '#9966CC' => 'amethyst',
			'#FAEBD7' => 'antique', '#7FFFD4' => 'aquamarine', '#568203' => 'avocado', '#A52A2A' => 'auburn', '#F0FFFF' => 'azure',
			'#F5F5DC' => 'beige', '#000000' => 'black', '#FAF0BE' => 'blond', '#0000FF' => 'blue', '#B5A642' => 'brass',
			'#A57164' => 'bronze', '#A52A2A' => 'brown', '#F0DC82' => 'buff', '#800020' => 'burgundy', '#FFBCD9' => 'candy',
			'#C41E3A' => 'cardinal', '#FF0038' => 'carmine', '#E9692C' => 'carrot', '#F400A1' => 'cerise', '#F7E7CE' => 'champagne',
			'#B94E48' => 'chestnut', '#7B3F00' => 'chocolate', '#E34234' => 'cinnabar', '#9FA91F' => 'citron', '#7F1734' => 'claret',
			'#0047AB' => 'cobalt', '#6F4E37' => 'coffee', '#B87333' => 'copper', '#FF8050' => 'coral', '#6495ED' => 'cornflower',
			'#FFFDD0' => 'cream', '#DC143C' => 'crimson', '#00FFFF' => 'cyan', '#555D50' => 'ebony', '#C2B280' => 'ecru',
			'#614051' => 'eggplant', '#F0EAD6' => 'eggshell', '#50C878' => 'emerald', '#C19A6B' => 'fallow', '#FF00FF' => 'fuchsia',
			'#B06500' => 'ginger', '#00FF00' => 'green', '#FFD700' => 'gold', '#B2BEB5' => 'grey', '#F0FFF0' => 'honeydew',
			'#6F00FF' => 'indigo', '#FFFFF0' => 'ivory', '#00A86B' => 'jade', '#F0E890' => 'khaki', '#E0E8F0' => 'lavender',
			'#FFF700' => 'lemon', '#C8A2C8' => 'lilac', '#00FF00' => 'lime', '#FF00FF' => 'magenta', '#C04000' => 'mahogany',
			'#800000' => 'maroon', '#E0B0FF' => 'mauve', '#FDBCB4' => 'melon', '#3EB489' => 'mint', '#C54B8C' => 'mulberry',
			'#FFDB58' => 'mustard', '#000080' => 'navy', '#CC7722' => 'ocher', '#808000' => 'olive', '#353839' => 'onyx',
			'#FFA500' => 'orange', '#DA70D6' => 'orchid', '#FFE5B4' => 'peach', '#EAE0C8' => 'pearl', '#FFC0CB' => 'pink',
			'#93C572' => 'pistachio', '#E5E4E2' => 'platinum', '#DDA0DD' => 'plum', '#800080' => 'purple', '#51484F' => 'quartz',
			'#E30B5D' => 'raspberry', '#FF0000' => 'red', '#FF007F' => 'rose', '#65000B' => 'rosewood', '#E0115F' => 'ruby',
			'#FF0028' => 'ruddy', '#80461B' => 'russet', '#B7410E' => 'rust', '#F4C430' => 'saffron', '#FA8072' => 'salmon',
			'#C2B280' => 'sand', '#0F52BA' => 'sapphire', '#FF2400' => 'scarlet', '#704214' => 'sepia', '#A05030' => 'sienna',
			'#C0C0C0' => 'silver', '#6A5ACD' => 'slate', '#738276' => 'smoke', '#FFFAFA' => 'snow', '#4682B4' => 'steel',
			'#FC5A8D' => 'strawberry', '#F28500' => 'tangerine', '#483C32' => 'taupe', '#008080' => 'teal', '#E2725B' => 'terra cotta',
			'#D0C0D0' => 'thistle', '#FFC87C' => 'topaz', '#FF878D' => 'tulip', '#40E0D0' => 'turquoise', '#120A8F' => 'ultramarine',
			'#8A3324' => 'umber', '#F3E5AB' => 'vanilla', '#43B3AE' => 'verdigris', '#E34234' => 'vermilion', '#EE82EE' => 'violet',
			'#F0E0B0' => 'wheaten', '#FFFFFF' => 'white', '#722F37' => 'wine', '#855E42' => 'wooden', '#FFFF00' => 'yellow',
		);


		$this->txBegin();

		$attrList = $this->getAttributeIds( $colors );

		$this->txCommit();


		$context = $this->getContext();
		$productManager = \Aimeos\MShop\Factory::createManager( $context, 'product' );
		$productListManager = \Aimeos\MShop\Factory::createManager( $context, 'product/lists' );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.type.code', 'default' ) );
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );
		$search->setSlice( 0, 1000 );

		$attrListItem = $this->getProductListItem( 'attribute', 'variant' );


		$start = 0;

		do
		{
			$result = $productManager->searchItems( $search );

			$this->txBegin();

			foreach( $result as $id => $item )
			{
				$attrListItem->setId( null );
				$attrListItem->setParentId( $id );
				$attrListItem->setRefId( current( $attrList ) );
				$productListManager->saveItem( $attrListItem, false );

				if( next( $attrList ) === false ) {
					reset( $attrList );
				}
			}

			$this->txCommit();

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start, 1000 );
		}
		while( $count == $search->getSliceSize() );


		$this->status( 'done' );
	}


	/**
	 * Creates and returns the attribute IDs for the given attribute codes.
	 *
	 * @param array $colors List of attribute codes
	 * @throws \Exception If a type isn't found
	 */
	protected function getAttributeIds( array $colors )
	{
		$context = $this->getContext();


		$attrTypeItem = $this->getTypeItem( 'attribute/type', 'product', 'color' );
		$mediaTypeItem = $this->getTypeItem( 'media/type', 'attribute', 'default' );
		$attrListTypeItem = $this->getTypeItem( 'attribute/lists/type', 'media', 'icon' );


		$mediaManager = \Aimeos\MShop\Factory::createManager( $context, 'media' );

		$mediaItem = $mediaManager->createItem();
		$mediaItem->setTypeId( $mediaTypeItem->getId() );
		$mediaItem->setDomain( 'attribute' );
		$mediaItem->setStatus( 1 );
		$mediaItem->setUrl( '' );


		$attrManager = \Aimeos\MShop\Factory::createManager( $context, 'attribute' );

		$attrItem = $attrManager->createItem();
		$attrItem->setTypeId( $attrTypeItem->getId() );
		$attrItem->setDomain( 'product' );
		$attrItem->setStatus( 1 );


		$attrListManager = \Aimeos\MShop\Factory::createManager( $context, 'attribute/lists' );

		$attrListItem = $attrListManager->createItem();
		$attrListItem->setTypeId( $attrListTypeItem->getId() );
		$attrListItem->setDomain( 'media' );
		$attrListItem->setStatus( 1 );


		$pos = 0;
		$mime = '';
		$attrList = [];

		foreach( $colors as $code => $name )
		{
			$imageData = $this->getImageData( $code, $mime );

			$attrItem->setId( null );
			$attrItem->setCode( $code );
			$attrItem->setLabel( $name );
			$attrItem->setPosition( $pos++ );
			$attrManager->saveItem( $attrItem );

			$mediaItem->setId( null );
			$mediaItem->setLabel( $name );
			$mediaItem->setMimetype( $mime );
			$mediaItem->setPreview( $imageData );
			$mediaManager->saveItem( $mediaItem );

			$attrListItem->setId( null );
			$attrListItem->setParentId( $attrItem->getId() );
			$attrListItem->setRefId( $mediaItem->getId() );
			$attrListManager->saveItem( $attrListItem, false );

			$attrList[] = $attrItem->getId();
		}

		return $attrList;
	}


	/**
	 * Returns the base64 encoded image data for the given color code.
	 *
	 * @param string $code Color code in hex notation, e.g. "#000000"
	 * @param string &$mime Contains the mime type of the created image as result
	 * @throws \Exception If the image couldn't be created
	 */
	protected function getImageData( $code, &$mime )
	{
		$list = str_split( ltrim( $code, '#' ), 2 );

		if( count( $list ) !== 3 ) {
			throw new \RuntimeException( sprintf( 'Invalid color code "%1$s"', $code ) );
		}

		if( ( $img = imagecreate( 1, 1 ) ) === false ) {
			throw new \RuntimeException( 'Unable to create image' );
		}

		if( imagecolorallocate( $img, hexdec( $list[0] ), hexdec( $list[1] ), hexdec( $list[2] ) ) === false ) {
			throw new \RuntimeException( 'Unable to allocate color' );
		}

		$image = $this->getImage( $img, $mime );

		if( imagedestroy( $img ) === false ) {
			throw new \RuntimeException( 'Unable to destroy image' );
		}

		return 'data:' . $mime . ';base64,' . base64_encode( $image );
	}


	/**
	 * Returns the GIF or PNG image for the given resource
	 *
	 * @param resource $img GD image resource
	 * @param string &$mime Contains the mime type of the created image as result
	 * @return string Binary image data
	 * @throws \Exception If PHP GD isn't installed
	 */
	protected function getImage( $img, &$mime )
	{
		try
		{
			ob_start();

			if( function_exists( 'imagegif' ) === true && imagegif( $img ) === true ) {
				$mime = 'image/gif';
			} else if( function_exists( 'imagepng' ) === true && imagepng( $img ) === true ) {
				$mime = 'image/png';
			} else {
				throw new \RuntimeException( 'Unable to create image. php-gd not installed?' );
			}

			$image = ob_get_contents();
			ob_end_clean();
		}
		catch( \Exception $e )
		{
			ob_end_clean();
			throw $e;
		}

		return $image;
	}
}
