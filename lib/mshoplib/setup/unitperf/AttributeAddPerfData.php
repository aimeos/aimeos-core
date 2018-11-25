<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds attribute performance records
 */
class AttributeAddPerfData extends \Aimeos\MW\Setup\Task\Base
{
	private $typeIds = [];


	public function __construct( \Aimeos\MW\Setup\DBSchema\Iface $schema, \Aimeos\MW\DB\Connection\Iface $conn, $additional = null )
	{
		\Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\MShop\\Context\\Item\\Iface', $additional );

		parent::__construct( $schema, $conn, $additional );
	}


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return ['MShopAddTypeDataUnitperf', 'LocaleAddPerfData', 'MShopSetLocale'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Insert product data.
	 */
	public function migrate()
	{
		$this->msg( 'Adding attribute performance data', 0 );


		$this->init();

		$manager = \Aimeos\MShop\Factory::createManager( $this->additional, 'attribute' );
		$manager->begin();

		$this->addCharacteristics();
		$this->addColors();
		$this->addOptions();
		$this->addVariants();

		$manager->commit();


		$this->status( 'done' );
	}


	protected function addCharacteristics()
	{
		$characteristics = [
			'property' => [
				23 => 'plain', 4 => 'checked', 31 => 'striped', 9 => 'curled', 6 => 'colored', 3 => 'bubbled', 16 => 'geometric', 25 => 'quilted', 22 => 'pimpled', 11 => 'dotted',
				20 => 'light', 18 => 'heavy', 29 => 'simple', 5 => 'clear', 8 => 'cool', 34 => 'thin', 33 => 'thick', 0 => 'airy', 2 => 'breezy', 1 => 'blowy',
				10 => 'dark', 17 => 'gloomy', 28 => 'shiny', 30 => 'soft', 15 => 'fluffy', 36 => 'warm', 14 => 'elastic', 12 => 'dry', 24 => 'pliable', 21 => 'lustrous',
				13 => 'durable', 27 => 'rough', 35 => 'tough', 32 => 'strong', 26 => 'resistant', 37 => 'weak', 19 => 'knitted', 7 => 'comfortable', 39 => 'wrinkled', 38 => 'woven',
			],
			'material' => [
				3 => 'alpaca', 22 => 'horsehair', 56 => 'viscose', 39 => 'polyester', 35 => 'mohair', 5 => 'azlon', 9 => 'byssus', 10 => 'camelhair', 12 => 'chiengora', 59 => 'yak',
				8 => 'brocade', 13 => 'chiffon', 15 => 'cotton', 18 => 'flannel', 23 => 'jersey', 45 => 'satin', 16 => 'damask', 46 => 'seersucker', 17 => 'denim', 53 => 'tweed',
				30 => 'linen', 48 => 'silk', 47 => 'sheen', 54 => 'velvet', 58 => 'wool', 49 => 'sisal', 24 => 'jute', 4 => 'angora', 11 => 'cashmere', 57 => 'wire',
				28 => 'lambswool', 31 => 'llama', 40 => 'qiviut', 41 => 'rabbit', 55 => 'vicuña', 0 => 'abacá', 1 => 'acetate', 6 => 'bamboo', 7 => 'banana', 25 => 'kapok',
				14 => 'coir', 19 => 'flax', 21 => 'hemp', 26 => 'kenaf', 32 => 'lyocell', 34 => 'modal', 38 => 'piña', 42 => 'raffia', 43 => 'ramie', 52 => 'taffeta',
				44 => 'rayon', 2 => 'acrylic', 27 => 'kevlar', 36 => 'nomex', 37 => 'nylon', 50 => 'spandex', 33 => 'modacrylic', 29 => 'leather', 51 => 'steel', 20 => 'glass',
			],
		];


		$attrManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'attribute' );

		foreach( $characteristics as $type => $list )
		{
			$attrItem = $attrManager->createItem()
				->setTypeId( $this->getTypeId( 'attribute/type', 'product', $type ) )
				->setDomain( 'product' )
				->setStatus( 1 );

			foreach( $list as $pos => $value )
			{
				$item = clone $attrItem;
				$item->setPosition( $pos )
					->setLabel( $value )
					->setCode( $value );

				$attrManager->saveItem( $item );
			}
		}
	}


	protected function addColors()
	{
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


		$mediaManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'media' );
		$attrManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'attribute' );
		$listManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'attribute/lists' );

		$attrItem = $attrManager->createItem( 'color', 'product' )
			->setDomain( 'product' )
			->setStatus( 1 );

		$mediaItem = $mediaManager->createItem( 'icon', 'attribute')
			->setMimeType( 'image/svg+xml' )
			->setStatus( 1 );

		$listItem = $listManager->createItem( 'default', 'media' );
		$pos = 0;

		foreach( $colors as $code => $name )
		{
			$uri = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='1px' height='1px'><style>svg{background-color:" . $code . "}</style></svg>";

			$item = clone $attrItem;
			$item->setPosition( $pos++ )
				->setLabel( $name )
				->setCode( $code );

			$refItem = clone $mediaItem;
			$refItem->setPreview( $uri )->setUrl( $uri );

			$item->addListItem( 'media', clone $listItem, $refItem );

			$attrManager->saveItem( $item );
		}
	}


	protected function addOptions()
	{
		$priceManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'price' );
		$attrManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'attribute' );
		$listManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'attribute/lists' );

		$priceItem = $priceManager->createItem( 'default', 'attribute' )
			->setTaxRate( '20.00' )
			->setStatus( 1 );

		$attrItem = $attrManager->createItem( 'sticker', 'product' )
			->setDomain( 'product' )
			->setStatus( 1 );

		$listItem = $listManager->createItem( 'default', 'price' );
		$pos = 0;

		foreach( ['small sticker' => '+2.50', 'large sticker' => '+7.50'] as $option => $price )
		{
			$item = clone $attrItem;
			$item->setPosition( $pos++ )
				->setLabel( $option )
				->setCode( $option );

			$refItem = clone $priceItem;
			$refItem->setLabel( $option )->setValue( $price );

			$item->addListItem( 'price', clone $listItem, $refItem );

			$attrManager->saveItem( $item );
		}

	}


	protected function addVariants()
	{
		$sizes = [
			'size' => [
				15 => 'M', 11 => 'S', 19 => 'L', 10 => 'XS', 20 => 'XL', 14 => 'S-MM', 16 => 'MM-L', 9 => '2XS', 21 => '2XL', 13 => 'SS-M', 18 => 'M-LL',
				8 => '3XS', 22 => '3XL', 12 => 'S-M', 17 => 'M-L', 7 => '4XS', 23 => '4XL', 6 => '5XS', 24 => '5XL', 5 => '6XS', 25 => '6XL',
				4 => '7XS', 26 => '7XL', 3 => '8XS', 27 => '8XL', 2 => '9XS', 28 => '9XL', 1 => '10XS', 29 => '10XL', 0 => '11XS', 30 => '11XL',
			],
			'length' => [2 => 'short', 3 => 'normal length', 5 => 'long', 1 => 'semi-short', 4 => 'semi-long', 0 => 'extra short', 6 => 'extra long'],
			'width' => [2 => 'tight', 3 => 'standard width', 5 => 'wide', 1 => 'semi-tight', 4 => 'semi-wide', 0 => 'extra tight', 6 => 'extra wide'],
		];


		$attrManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'attribute' );

		foreach( $sizes as $type => $list )
		{
			$attrItem = $attrManager->createItem()
				->setTypeId( $this->getTypeId( 'attribute/type', 'product', $type ) )
				->setDomain( 'product' )
				->setStatus( 1 );

			foreach( $list as $pos => $value )
			{
				$item = clone $attrItem;
				$item->setPosition( $pos )
					->setLabel( $value )
					->setCode( $value );

				$attrManager->saveItem( $item );
			}
		}
	}


	protected function getTypeId( $path, $domain, $code )
	{
		if( !isset( $this->typeIds[$path][$domain][$code] ) )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->additional, $path );

			$item = $manager->createItem();
			$item->setDomain( $domain );
			$item->setLabel( $code );
			$item->setCode( $code );
			$item->setStatus( 1 );

			$this->typeIds[$path][$domain][$code] = $manager->saveItem( $item )->getId();
		}

		return $this->typeIds[$path][$domain][$code];
	}


	protected function init()
	{
		foreach( ['attribute/type', 'attribute/lists/type', 'media/type', 'price/type'] as $path )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->additional, $path );
			$search = $manager->createSearch()->setSlice( 0, 0x7fffffff );

			foreach( $manager->searchItems( $search ) as $id => $item ) {
				$this->typeIds[$path][$item->getDomain()][$item->getCode()] = $id;
			}
		}
	}
}
