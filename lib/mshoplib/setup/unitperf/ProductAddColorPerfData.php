<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds product color attribute performance records.
 */
class MW_Setup_Task_ProductAddColorPerfData extends MW_Setup_Task_ProductAddBasePerfData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductAddBasePerfData', 'MShopAddTypeDataUnitperf' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildPerfIndex' );
	}


	/**
	 * Insert text data and product/text relations.
	 */
	protected function _process()
	{
		$this->_msg( 'Adding product color attribute performance data', 0 );


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
			'#6F00FF' => 'indigo', '#FFFFF0' => 'ivory', '#00A86B' => 'jade','#F0E890' => 'khaki','#E0E8F0' => 'lavender',
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


		$context = $this->_getContext();

		$attrTypeManager = MShop_Factory::createManager( $context, 'attribute/type' );

		$search = $attrTypeManager->createSearch();
		$search->setConditions( $search->compare( '==', 'attribute.type.code', 'color' ) );
		$result = $attrTypeManager->searchItems( $search );

		if( ( $attrTypeItem = reset( $result ) ) === false ) {
			throw new Exception( 'No attribute type "color" found' );
		}


		$this->_txBegin();

		$attrList = $this->_getAttributeIds( $colors );

		$this->_txCommit();


		$productManager = MShop_Factory::createManager( $context, 'product' );
		$productListManager = MShop_Factory::createManager( $context, 'product/list' );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.type.code', 'default' ) );
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );

		$attrListItem = $this->_getProductListItem( 'attribute', 'variant' );


		$this->_txBegin();

		$start = 0;

		do
		{
			$result = $productManager->searchItems( $search );

			foreach ( $result as $id => $item )
			{
				$attrListItem->setId( null );
				$attrListItem->setParentId( $id );
				$attrListItem->setRefId( current( $attrList ) );
				$productListManager->saveItem( $attrListItem, false );

				if( next( $attrList ) === false ) {
					reset( $attrList );
				}
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count == $search->getSliceSize() );

		$this->_txCommit();


		$this->_status( 'done' );
	}


	protected function _getAttributeIds( $colors )
	{
		$context = $this->_getContext();


		$mediaTypeManager = MShop_Factory::createManager( $context, 'media/type' );

		$search = $mediaTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'media.type.domain', 'attribute' ),
			$search->compare( '==', 'media.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $mediaTypeManager->searchItems( $search );

		if( ( $mediaTypeItem = reset( $result ) ) === false ) {
			throw new Exception( 'No media type "attribute/default" found' );
		}

		$mediaManager = MShop_Factory::createManager( $context, 'media' );

		$mediaItem = $mediaManager->createItem();
		$mediaItem->setTypeId( $mediaTypeItem->getId() );
		$mediaItem->setDomain( 'attribute' );
		$mediaItem->setStatus( 1 );
		$mediaItem->setUrl( '' );


		$attrTypeManager = MShop_Factory::createManager( $context, 'attribute/type' );

		$search = $attrTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.type.domain', 'product' ),
			$search->compare( '==', 'attribute.type.code', 'color' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $attrTypeManager->searchItems( $search );

		if( ( $attrTypeItem = reset( $result ) ) === false ) {
			throw new Exception( 'No attribute type "product/color" found' );
		}

		$attrManager = MShop_Factory::createManager( $context, 'attribute' );

		$attrItem = $attrManager->createItem();
		$attrItem->setTypeId( $attrTypeItem->getId() );
		$attrItem->setDomain( 'product' );
		$attrItem->setStatus( 1 );


		$attrListTypeManager = MShop_Factory::createManager( $context, 'attribute/list/type' );

		$search = $attrListTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.list.type.domain', 'media' ),
			$search->compare( '==', 'attribute.list.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $attrListTypeManager->searchItems( $search );

		if( ( $attrListTypeItem = reset( $result ) ) === false ) {
			throw new Exception( 'No attribute list type "media/default" found' );
		}

		$attrListManager = MShop_Factory::createManager( $context, 'attribute/list' );

		$attrListItem = $attrListManager->createItem();
		$attrListItem->setTypeId( $attrListTypeItem->getId() );
		$attrListItem->setDomain( 'media' );
		$attrListItem->setStatus( 1 );


		$pos = 0;
		$attrList = array();

		foreach( $colors as $code => $name )
		{
			$list = str_split( ltrim( $code, '#' ), 2 );

			if( count( $list ) !== 3 ) {
				throw new Exception( sprintf( 'Invalid color code "%1$s"', $code ) );
			}

			if( ( $img = imagecreate( 1, 1 ) ) === false ) {
				throw new Exception( 'Unable to create image' );
			}

			if( imagecolorallocate( $img, hexdec( $list[0] ), hexdec( $list[1] ), hexdec( $list[2] ) ) === false ) {
				throw new Exception( 'Unable to allocate color' );
			}

			try
			{
				ob_start();

				if( function_exists( 'imagegif' ) === true && imagegif( $img ) === true ) {
					$mime = 'image/gif';
				} else if( function_exists( 'imagepng' ) === true && imagepng( $img ) === true ) {
					$mime = 'image/png';
				} else {
					throw new Exception( 'Unable to create image. php-gd not installed?' );
				}

				$image = ob_get_contents();
				ob_end_clean();
			}
			catch( Exception $e )
			{
				ob_end_clean();
				throw $e;
			}

			if( imagedestroy( $img ) === false ) {
				throw new Exception( 'Unable to destroy image' );
			}

			$imageData = 'data:' . $mime . ';base64,' . base64_encode( $image );


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
}
