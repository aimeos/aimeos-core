<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds catalog and product performance records
 */
class CatalogAddPerfData extends \Aimeos\MW\Setup\Task\Base
{
	private $maxBatch;
	private $numCatLevels;
	private $numCategories;
	private $numCatProducts;
	private $numProdVariants;
	private $attributes = [];


	/**
	 * Initializes the task object.
	 *
	 * @param \Aimeos\MW\Setup\DBSchema\Iface $schema Database schema object
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param mixed $additional Additionally provided information for the setup tasks if required
	 * @param array $paths List of paths of the setup tasks ordered by dependencies
	 */
	public function __construct( \Aimeos\MW\Setup\DBSchema\Iface $schema, \Aimeos\MW\DB\Connection\Iface $conn,
		$additional = null, array $paths = [] )
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $additional );

		parent::__construct( $schema, $conn, $additional, $paths );
	}


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['MShopAddCodeDataUnitperf', 'AttributeAddPerfData', 'LocaleAddPerfData', 'MShopSetLocale'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['IndexRebuildPerf'];
	}


	/**
	 * Insert catalog nodes and product/catalog relations.
	 */
	public function migrate()
	{
		$this->msg( 'Adding catalog performance data', 0 );


		$treeFcn = function( array $parents, $catParentId, $numCatPerLevel, $level, $catLabel, $catIdx ) use ( &$treeFcn ) {

			$catItem = $this->addCatalogItem( $catParentId, $catLabel, $catIdx );
			array_unshift( $parents, $catItem );

			if( $level > 0 )
			{
				for( $i = 0; $i < $numCatPerLevel; $i++ ) {
					$treeFcn( $parents, $catItem->getId(), $numCatPerLevel, $level - 1, $catLabel . '-' . ( $i + 1 ), $i );
				}
			}
			else
			{
				$fcn = function( array $parents, $catLabel ) {

					srand( getmypid() ); mt_srand( getmypid() );
					$this->addProductItems( $parents, $catLabel );

					foreach( $parents as $catItem ) {
						$this->save( 'catalog', $catItem );
					}
				};

				$this->additional->__sleep();
				$this->additional->getProcess()->start( $fcn, [$parents, $catLabel] );
			}
		};


		$this->init();

		$config = $this->additional->getConfig();
		$treeidx = $config->get( 'setup/unitperf/treeindex' );
		$this->maxBatch = $config->get( 'setup/unitperf/max-batch', 10000 );
		$this->numCatLevels = $config->get( 'setup/unitperf/num-catlevels', 1 );
		$this->numCategories = $config->get( 'setup/unitperf/num-categories', 10 );
		$this->numCatProducts = $config->get( 'setup/unitperf/num-catproducts', 1000 );
		$this->numProdVariants = $config->get( 'setup/unitperf/num-prodvariants', 100 );

		$catRootItem = $this->addCatalogItem( null, 'home', 0 );
		$end = $numCatPerLevel = round( pow( $this->numCategories, 1 / $this->numCatLevels ) );
		$begin = 0;

		if( $treeidx !== null ) {
			$begin = $treeidx; $end = $treeidx + 1;
		}

		for( $i = $begin; $i < $end; $i++ ) {
			$treeFcn( [$catRootItem], $catRootItem->getId(), $numCatPerLevel, $this->numCatLevels - 1, $i + 1, $i );
		}

		$this->additional->getProcess()->wait();
		$this->status( 'done' );
	}


	protected function addCatalogItem( $parentId, $catLabel, $catIdx )
	{
		$catalogManager = \Aimeos\MShop::create( $this->additional, 'catalog' );

		$item = $catalogManager->createItem()
			->setLabel( 'category-' . $catLabel )
			->setCode( 'cat-' . $catLabel )
			->setStatus( 1 );
		$item->pos = $catIdx;

		$item = $this->addCatalogTexts( $item, $catLabel );

		while( true )
		{
			try {
				return $catalogManager->insertItem( $item, $parentId );
			} catch( \Aimeos\MW\DB\Exception $e ) {
				if( $e->getCode() !== 40001 ) { throw $e; } // transaction deadlock
			}
		}
	}


	protected function addCatalogProducts( array $catItems, array $items, $num )
	{
		$catalogListManager = \Aimeos\MShop::create( $this->additional, 'catalog/lists' );
		$defListItem = $catalogListManager->createItem()->setType( 'default' );
		$start = 0;

		foreach( $catItems as $idx => $catItem )
		{
			$catItem = clone $catItem; // forget stored product references afterwards
			$fraction = pow( 10, $idx );

			foreach( $items as $item )
			{
				if( $item->pos % $fraction === 0 )
				{
					$litem = ( clone $defListItem )->setRefId( $item->getId() )->setPosition( $start + round( $item->pos / $fraction ) );
					$catItem->addListItem( 'product', $litem );
				}
			}

			$start += $num * $catItem->pos * round( count( $items ) / pow( 10, $idx + 1 ) );
			$this->save( 'catalog', $catItem );
		}
	}


	protected function addCatalogTexts( \Aimeos\MShop\Catalog\Item\Iface $catItem, $catLabel )
	{
		$textManager = \Aimeos\MShop::create( $this->additional, 'text' );
		$catalogListManager = \Aimeos\MShop::create( $this->additional, 'catalog/lists' );

		$textItem = $textManager->createItem()
			->setContent( 'Category ' . $catLabel )
			->setLabel( 'cat-' . $catLabel )
			->setLanguageId( 'en' )
			->setType( 'name' )
			->setStatus( 1 );

		$listItem = $catalogListManager->createItem()->setType( 'default' );

		return $catItem->addListItem( 'text', $listItem, $textItem );
	}


	protected function addProductAttributes( \Aimeos\MShop\Product\Item\Iface $prodItem, array $attrIds )
	{
		$productListManager = \Aimeos\MShop::create( $this->additional, 'product/lists' );

		$listItem = $productListManager->createItem()->setType( 'default' );

		foreach( $attrIds as $attrId ) {
			$prodItem->addListItem( 'attribute', ( clone $listItem )->setRefId( $attrId ) );
		}

		$listItem = $productListManager->createItem()->setType( 'config' );

		foreach( $this->attributes['sticker'] as $attrId => $label ) {
			$prodItem->addListItem( 'attribute', ( clone $listItem )->setRefId( $attrId ) );
		}

		return $prodItem;
	}


	protected function addProductItems( array $catItems, $catLabel )
	{
		$articles = $this->shuffle( [
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
		] );

		$productManager = \Aimeos\MShop::create( $this->additional, 'product' );
		$productManager->begin();

		$newItem = $productManager->createItem()->setType( $this->numProdVariants > 0 ? 'select' : 'default' );
		$slice = (int) ceil( $this->maxBatch / ( $this->numProdVariants ?: 1 ) );

		$property = $this->shuffle( $this->attributes['property'] );
		$material = $this->shuffle( $this->attributes['material'] );
		$color = $this->shuffle( $this->attributes['color'] );

		$items = [];
		$num = 1;

		for( $i = 1; $i <= $this->numCatProducts; $i++ )
		{
			$text = current( $color ) . ' ' . current( $property ) . ' ' . current( $material ) . ' ' . current( $articles );

			$item = ( clone $newItem )
				->setLabel( $text . ' (' . $catLabel . ')' )
				->setCode( 'p-' . $i . ':' . $catLabel )
				->setStatus( 1 );

			$item = $this->addProductAttributes( $item, [key( $color ), key( $property ), key( $material )] );
			$item = $this->addProductTexts( $item, $text, $catLabel );
			$item = $this->addProductMedia( $item, $i );
			$item = $this->addProductPrices( $item, $i );
			$item = $this->addProductVariants( $item, $i );
			$item = $this->addProductSuggestions( $item, $catItems );

			$item->pos = $i - 1; // 0 based category position
			$items[] = $item;

			next( $color );
			if( current( $color ) === false )
			{
				reset( $color ); next( $property );

				if( current( $property ) === false )
				{
					reset( $property ); next( $material );

					if( current( $material ) === false )
					{
						reset( $material ); next( $articles );

						if( current( $articles ) === false ) {
							reset( $articles );
						}
					}
				}
			}

			if( $i % $slice === 0 )
			{
				$productManager->saveItems( $items );
				$this->addCatalogProducts( $catItems, $items, $num++ );
				$this->addStock( $items );
				$items = [];
			}
		}

		$productManager->saveItems( $items );
		$this->addCatalogProducts( $catItems, $items, $num++ );
		$this->addStock( $items );

		$productManager->commit();
	}


	protected function addProductMedia( \Aimeos\MShop\Product\Item\Iface $prodItem, $idx )
	{
		$prefix = 'https://demo.aimeos.org/media/';

		$mediaManager = \Aimeos\MShop::create( $this->additional, 'media' );
		$productListManager = \Aimeos\MShop::create( $this->additional, 'product/lists' );

		$litem = $productListManager->createItem()->setType( 'default' );
		$newItem = $mediaManager->createItem()->setType( 'default' );

		foreach( array_values( $this->shuffle( range( 0, 3 ) ) ) as $pos => $i )
		{
			$num = ( ( $idx + $i ) % 4 ) + 1;
			$mediaItem = ( clone $newItem )
				->setLabel( ( $pos + 1 ) . '. picture for ' . $prodItem->getLabel() )
				->setPreviews( [1 => $prefix . 'unitperf/' . $num . '.jpg'] )
				->setUrl( $prefix . 'unitperf/' . $num . '-big.jpg' )
				->setMimeType( 'image/jpeg' )
				->setStatus( 1 );

			$prodItem->addListItem( 'media', ( clone $litem )->setPosition( $pos ), $mediaItem );
		}

		$mediaItem = ( clone $newItem )
			->setPreviews( [1 => $prefix . 'unitperf/download-preview.jpg'] )
			->setUrl( $prefix . 'unitperf/download.pdf' )
			->setMimeType( 'application/pdf' )
			->setLabel( 'PDF download' )
			->setStatus( 1 );

		$litem = $productListManager->createItem()->setType( 'download' );

		return $prodItem->addListItem( 'media', ( clone $litem ), $mediaItem );
	}


	protected function addProductPrices( \Aimeos\MShop\Product\Item\Iface $prodItem, $idx )
	{
		$priceManager = \Aimeos\MShop::create( $this->additional, 'price' );
		$productListManager = \Aimeos\MShop::create( $this->additional, 'product/lists' );

		$litem = $productListManager->createItem()->setType( 'default' );
		$newItem = $priceManager->createItem()->setType( 'default' );
		$base = rand( 0, 896 );

		for( $i = 0; $i < 3; $i++ )
		{
			$priceItem = ( clone $newItem )
				->setLabel( $prodItem->getLabel() . ': from ' . ( 1 + $i * 5 ) )
				->setValue( 100 + ( ( $base + $idx ) % 900 ) - $i * 10 )
				->setQuantity( 1 + $i * 10 )
				->setCurrencyId( 'EUR' )
				->setRebate( $i * 10 )
				->setStatus( 1 );

			$prodItem->addListItem( 'price', ( clone $litem )->setPosition( $i ), $priceItem );
		}

		return $prodItem;
	}


	protected function addProductSuggestions( \Aimeos\MShop\Product\Item\Iface $prodItem, array $catItems )
	{
		if( ( $catItem = reset( $catItems ) ) !== false )
		{
			$productListManager = \Aimeos\MShop::create( $this->additional, 'product/lists' );

			$listItem = $productListManager->createItem()->setType( 'suggestion' );
			$listItems = $catItem->getListItems( 'product' );
			$ids = []; $num = 5;

			while( ( $litem = $listItems->pop() ) !== null && $num > 0 )
			{
				if( !in_array( $litem->getRefId(), $ids ) )
				{
					$prodItem->addListItem( 'product', ( clone $listItem )->setRefId( $litem->getRefId() ) );
					$ids[] = $litem->getRefId();
					$num--;
				}
			}
		}

		return $prodItem;
	}


	protected function addProductTexts( \Aimeos\MShop\Product\Item\Iface $prodItem, $label, $catLabel )
	{
		$textManager = \Aimeos\MShop::create( $this->additional, 'text' );
		$productListManager = \Aimeos\MShop::create( $this->additional, 'product/lists' );

		$listItem = $productListManager->createItem()->setType( 'default' );

		$textItem = $textManager->createItem()
			->setContent( str_replace( ' ', '_', $label . '_' . $catLabel ) )
			->setLabel( $label . '(' . $catLabel . ')' )
			->setLanguageId( 'en' )
			->setType( 'url' )
			->setStatus( 1 );

		$prodItem->addListItem( 'text', ( clone $listItem ), $textItem );

		$textItem = $textManager->createItem()
			->setLanguageId( 'en' )
			->setContent( $label )
			->setLabel( $label )
			->setType( 'name' )
			->setStatus( 1 );

		$prodItem->addListItem( 'text', ( clone $listItem )->setPosition( 0 ), $textItem );

		$textItem = $textManager->createItem()
			->setContent( 'Short description for ' . $label )
			->setLabel( $label . ' (short)' )
			->setLanguageId( 'en' )
			->setType( 'short' )
			->setStatus( 1 );

		$prodItem->addListItem( 'text', ( clone $listItem )->setPosition( 1 ), $textItem );

		$textItem = $textManager->createItem()
			->setContent( 'Long description for ' . $label . '. This may include some "lorem ipsum" text' )
			->setLabel( $label . ' (long)' )
			->setLanguageId( 'en' )
			->setType( 'long' )
			->setStatus( 1 );

		$prodItem->addListItem( 'text', ( clone $listItem )->setPosition( 2 ), $textItem );

		return $prodItem;
	}


	protected function addProductVariants( \Aimeos\MShop\Product\Item\Iface $prodItem, $idx )
	{
		$productManager = \Aimeos\MShop::create( $this->additional, 'product' );
		$productListManager = \Aimeos\MShop::create( $this->additional, 'product/lists' );

		$defListItem = $productListManager->createItem()->setType( 'default' );
		$varListItem = $productListManager->createItem()->setType( 'variant' );
		$newItem = $productManager->createItem()->setType( 'default' );

		$length = $this->attributes['length'];
		$width = $this->attributes['width'];
		$size = $this->attributes['size'];

		for( $i = 0; $i < $this->numProdVariants; $i++ )
		{
			$text = current( $length ) . ', ' . current( $width ) . ', ' . $prodItem->getLabel() . ' (' . current( $size ) . ')';

			$item = ( clone $newItem )
				->setCode( 'v-' . $idx . '/' . $i . ':' . $prodItem->getCode() )
				->setLabel( $text )
				->setStatus( 1 );

			$item->addListItem( 'attribute', ( clone $varListItem )->setRefId( key( $length ) ) );
			$item->addListItem( 'attribute', ( clone $varListItem )->setRefId( key( $width ) ) );
			$item->addListItem( 'attribute', ( clone $varListItem )->setRefId( key( $size ) ) );

			$prodItem->addListItem( 'product', clone $defListItem, $item );

			next( $width );
			if( current( $width ) === false )
			{
				reset( $width ); next( $length );

				if( current( $length ) === false )
				{
					reset( $length ); next( $size );

					if( current( $size ) === false ) {
						reset( $size );
					}
				}
			}
		}

		return $prodItem;
	}


	public function addStock( array $items )
	{
		$stockManager = \Aimeos\MShop::create( $this->additional, 'stock' );

		$stockItem = $stockManager->createItem()->setType( 'default' );
		$stocklevels = $this->shuffle( [null, 100, 80, 60, 40, 20, 10, 5, 2, 0] );
		$list = [];

		foreach( $items as $item )
		{
			foreach( $item->getRefItems( 'product', 'default', 'default' ) as $refItem )
			{
				$sitem = clone $stockItem;
				$sitem->setProductCode( $refItem->getCode() );
				$sitem->setStockLevel( current( $stocklevels ) );

				if( next( $stocklevels ) === false ) {
					reset( $stocklevels );
				}

				$list[] = $sitem;
			}

			$sitem = clone $stockItem;
			$list[] = $sitem->setProductCode( $item->getCode() );
		}

		$stockManager->begin();
		$stockManager->saveItems( $list, false );
		$stockManager->commit();
	}


	protected function init()
	{
		$manager = \Aimeos\MShop::create( $this->additional, 'attribute' );

		$search = $manager->createSearch()->setSlice( 0, 0x7fffffff );
		$search->setSortations( [$search->sort( '+', 'attribute.position' )] );

		foreach( $manager->searchItems( $search ) as $id => $item ) {
			$this->attributes[$item->getType()][$id] = $item->getLabel();
		}
	}


	protected function save( $domain, $item )
	{
		$manager = \Aimeos\MShop::create( $this->additional, $domain );

		$manager->begin();
		$item = $manager->saveItem( $item );
		$manager->commit();

		return $item;
	}


	protected function shuffle( array $list )
	{
		$keys = array_keys( $list );
		shuffle( $keys );
		$result = [];

		foreach( $keys as $key ) {
			$result[$key] = $list[$key];
		}

		return $result;
	}
}
