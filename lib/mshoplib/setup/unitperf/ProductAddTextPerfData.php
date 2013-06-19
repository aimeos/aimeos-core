<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds performance records to product table.
 */
class MW_Setup_Task_ProductAddTextPerfData extends MW_Setup_Task_ProductAddBasePerfData
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
		$this->_msg('Adding product text performance data', 0);


		$context =  $this->_getContext();

		$textManager = MShop_Text_Manager_Factory::createManager( $context );
		$textTypeManager = $textManager->getSubManager( 'type' );

		$expr = array();
		$search = $textTypeManager->createSearch();
		$expr[] = $search->compare('==', 'text.type.domain', 'product');
		$expr[] = $search->compare('==', 'text.type.code', array( 'name', 'short', 'long' ) );
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $textTypeManager->searchItems($search);

		$textTypes = array();
		foreach( $types as $type ) {
			$textTypes[ $type->getCode() ] = $type->getId();
		}


		$productManager = MShop_Product_Manager_Factory::createManager( $context );
		$productListManager = $productManager->getSubManager( 'list' );
		$productListTypeManager = $productListManager->getSubManager( 'type' );

		$expr = array();
		$search = $productListTypeManager->createSearch();
		$expr[] = $search->compare('==', 'product.list.type.code', 'default');
		$expr[] = $search->compare('==', 'product.list.type.domain', 'text');
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productListTypeManager->searchItems($search);

		if ( ($listTypeItem = reset($types)) === false) {
			throw new Exception('Product list type item not found');
		}


		$listItem = $productListManager->createItem();
		$listItem->setTypeId( $listTypeItem->getId() );
		$listItem->setDomain( 'text' );

		$textItem = $textManager->createItem();
		$textItem->setLanguageId( 'en' );
		$textItem->setDomain( 'product' );
		$textItem->setStatus( 1 );


		$colors = array(
			'red', 'green', 'blue', 'black', 'white', 'orange', 'yellow', 'purple', 'brown', 'turquise',
			'violet', 'teal', 'sienna', 'olive', 'navy', 'maroon', 'magenta', 'lime', 'lemon', 'khaki',
			'pink', 'grey', 'ocher', 'terra cotta', 'champagne', 'garnet', 'bronze', 'ruby', 'silver', 'gold',
			'ivory', 'indigo', 'fuchsia', 'cyan', 'crimson', 'chocolate', 'beige', 'azure', 'aquamarine', 'aqua',
			'titan', 'platin', 'cobalt', 'chrome', 'copper', 'rose', 'almond', 'wooden', 'coral', 'cornflower',
			'orchid', 'salmon', 'honey', 'lavender', 'peach', 'plum', 'sand', 'steel', 'smoke', 'snow',
			'wine', 'melon', 'strawberry', 'wheaten', 'tangerine', 'cerise', 'burgundy', 'auburn', 'cinnabar', 'verdigris',
			'vanilla', 'cardinal', 'umber', 'ultramarine', 'topaz', 'thistle', 'taupe', 'slate', 'sepia', 'scarlet',
			'sapphire', 'saffron', 'rust', 'russet', 'ruddy', 'amaranth', 'rosewood', 'lila', 'candy', 'carmine',
			'raspberry', 'quartz', 'pistachio', 'pearl', 'onyx', 'mustard', 'mulberry', 'mint', 'vermilion', 'mauve',
			'mahogany', 'jade', 'ginger', 'fallow', 'emerald', 'eggshell', 'eggplant', 'ecru', 'ebony', 'coffee',
			'chestnut', 'carrot', 'claret', 'buff', 'brass', 'blond', 'avocado', 'antique', 'amethyst', 'amber',
		);
		$attribute = array(
			'plain', 'checked', 'striped', 'curled', 'colored', 'bubbled', 'geometric', 'quilted', 'pimpled', 'dotted',
			'light', 'heavy', 'simple', 'clear', 'cool', 'thin', 'thick', 'airy', 'breezy', 'blowy',
			'dark', 'gloomy', 'shiny', 'big', 'tight', 'alpaca', 'horsehair', 'viscose', 'polyester', 'mohair',
			'brocade', 'chiffon', 'cotton', 'damask', 'denim', 'flannel', 'jersey', 'satin', 'seersucker', 'tweed',
			'linen', 'satin', 'silk', 'sheen', 'velvet', 'wool', 'sisal', 'jute', 'angora', 'cashmere',
		);
		$articles = array(
			'shirt', 'skirt', 'jacket', 'pants', 'socks', 'blouse', 'slip', 'sweater', 'dress', 'top',
			'anorak', 'babydoll', 'swimsuit', 'trunks', 'bathrobe', 'beret', 'bra', 'bikini', 'blazer', 'bodysuit',
			'bolero', 'bowler', 'trousers', 'bustier', 'cape', 'catsuit', 'chucks', 'corduroys', 'corsage', 'cutaway',
			'lingerie', 'tricorn', 'bow tie', 'tails', 'leggings', 'galoshes', 'string', 'belt', 'hotpants', 'hat',
			'jumpsuit', 'jumper', 'caftan', 'hood', 'kimono', 'headscarf', 'scarf', 'corset', 'costume', 'tie',
			'cummerbund', 'robe', 'underpants', 'dungarees', 'undershirt', 'camisole', 'mantle', 'bodice', 'topless', 'moonboots',
			'cap', 'nightie', 'negligee', 'overalls', 'parka', 'poncho', 'bloomers', 'pumps', 'pajamas', 'farthingale',
			'sari', 'veil', 'apron', 'swimsuit', 'shorts', 'tuxedo', 'stocking', 'suspender', 'tanga', 'tankini',
			'toga', 'tunic', 'turban', 'jerkin', 'coat', 'suit', 'vest', 'gloves', 'bag', 'briefcase',
			'shoes', 'sandals', 'flip-flops', 'ballerinas', 'slingbacks', 'clogs', 'moccasins', 'sneakers', 'boots', 'slippers',
		);
		$size = array(
			'(9XS)', '(8XS)', '(7XS)', '(6XS)', '(5XS)', '(4XS)', '(3XS)', '(2XS)', '(XS)', '(S)',
			'(SS-M)', '(S-M)', '(M)', '(M-L)', '(M-LL)',
			'(L)', '(XL)', '(2XL)', '(3XL)', '(4XL)', '(5XL)', '(6XL)', '(7XL)', '(8XL)', '(9XL)',
		);

		$search = $productManager->createSearch();


		$this->_txBegin();

		$start = 0;

		do
		{
			$result = $productManager->searchItems( $search );

			foreach ( $result as $id => $item )
			{
				$text = current( $colors ) . ' ' . current( $attribute ) . ' ' . current( $articles ) . ' ' . current( $size );


				$textItem->setId( null );
				$textItem->setTypeId( $textTypes['name'] );
				$textItem->setLabel( $text );
				$textItem->setContent( $text );
				$textManager->saveItem( $textItem );

				$listItem->setId( null );
				$listItem->setParentId( $id );
				$listItem->setRefId( $textItem->getId() );
				$productListManager->saveItem( $listItem, false );


				$textItem->setId( null );
				$textItem->setTypeId( $textTypes['short'] );
				$textItem->setLabel( 'Short ' . $text );
				$textItem->setContent( 'Short description for ' . $text );
				$textManager->saveItem( $textItem );

				$listItem->setId( null );
				$listItem->setParentId( $id );
				$listItem->setRefId( $textItem->getId() );
				$productListManager->saveItem( $listItem, false );


				$textItem->setId( null );
				$textItem->setTypeId( $textTypes['long'] );
				$textItem->setLabel( 'Long ' . $text );
				$textItem->setContent( 'Long description for ' . $text . '. This may include some "lorem ipsum" text' );
				$textManager->saveItem( $textItem );

				$listItem->setId( null );
				$listItem->setParentId( $id );
				$listItem->setRefId( $textItem->getId() );
				$productListManager->saveItem( $listItem, false );


				if( next( $colors ) === false )
				{
					reset( $colors );
					next( $attribute );

					if( current( $attribute ) === false )
					{
						reset( $attribute );
						next( $articles );

						if( current( $articles ) === false ) {
							reset( $articles );
							next( $size );

							if( current( $size ) === false ) {
								reset( $size );
							}
						}
					}
				}
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count > 0 );

		$this->_txCommit();


		$this->_status( 'done' );
	}
}
