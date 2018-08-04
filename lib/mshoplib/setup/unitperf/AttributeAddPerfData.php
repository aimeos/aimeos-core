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
		return ['MShopAddTypeDataUnitperf', 'LocaleAddPerfData'];
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
			'modifier' => [
				'plain', 'checked', 'striped', 'curled', 'colored', 'bubbled', 'geometric', 'quilted', 'pimpled', 'dotted',
				'light', 'heavy', 'simple', 'clear', 'cool', 'thin', 'thick', 'airy', 'breezy', 'blowy',
				'dark', 'gloomy', 'shiny', 'soft', 'fluffy', 'warm', 'elastic', 'dry', 'pliable', 'lustrous',
				'durable', 'rough', 'tough', 'strong', 'resistant', 'weak', 'knitted', 'comfortable', 'wrinkled', 'woven',
			],
			'material' => [
				'alpaca', 'horsehair', 'viscose', 'polyester', 'mohair', 'azlon', 'byssus', 'camelhair', 'chiengora', 'yak',
				'brocade', 'chiffon', 'cotton', 'damask', 'denim', 'flannel', 'jersey', 'satin', 'seersucker', 'tweed',
				'linen', 'silk', 'sheen', 'velvet', 'wool', 'sisal', 'jute', 'angora', 'cashmere', 'wire',
				'lambswool', 'llama', 'qiviut', 'rabbit', 'vicuña', 'abacá', 'acetate', 'bamboo', 'banana', 'kapok',
				'coir', 'flax', 'hemp', 'kenaf', 'lyocell', 'modal', 'piña', 'raffia', 'ramie', 'taffeta',
				'rayon', 'acrylic', 'kevlar', 'nomex', 'nylon', 'spandex', 'modacrylic', 'leather', 'steel', 'glass',
			],
		];


		$attrManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'attribute' );

		foreach( $characteristics as $type => $list )
		{
			$pos = 0;
			$attrItem = $attrManager->createItem()
				->setTypeId( $this->getTypeId( 'attribute/type', 'product', $type ) )
				->setDomain( 'product' )
				->setStatus( 1 );

			foreach( $list as $value )
			{
				$item = (clone $attrItem)
					->setPosition( $pos++ )
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

		$mediaItem = $mediaManager->createItem( 'default', 'attribute')
			->setMimeType( 'image/svg+xml' )
			->setStatus( 1 );

		$listItem = $listManager->createItem( 'default', 'media' );
		$pos = 0;

		foreach( $colors as $code => $name )
		{
			$list = str_split( ltrim( $code, '#' ), 2 );
			$triple = $list[0] . ',' . $list[1]. ',' . $list[2];
			$uri = 'data:image/svg+xml;utf8,<svg width="1" height="1"><rect width="1" height="1" style="fill:rgb(' . $triple . ')" /></svg>';

			$item = (clone $attrItem)
				->setPosition( $pos++ )
				->setLabel( $name )
				->setCode( $code );

			$refItem = (clone $mediaItem)
				->setPreview( $uri )
				->setUrl( $uri );

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
			$item = (clone $attrItem)
				->setPosition( $pos++ )
				->setLabel( $option )
				->setCode( $option );

			$refItem = (clone $priceItem)
				->setLabel( $option )
				->setValue( $price );

			$item->addListItem( 'price', clone $listItem, $refItem );

			$attrManager->saveItem( $item );
		}

	}


	protected function addVariants()
	{
		$sizes = [
			'length' => ['short', 'normal length', 'long', 'semi-short', 'semi-long', 'extra short', 'extra long'],
			'size' => [
				'M', 'S', 'L', 'XS', 'XL', 'S-MM', 'MM-L', '2XS', '2XL', 'SS-M', 'M-LL',
				'3XS', '3XL', 'S-M', 'M-L', '4XS', '4XL', '5XS', '5XL', '6XS', '6XL',
				'7XS', '7XL', '8XS', '8XL', '9XS', '9XL', '10XS', '10XL', '11XS', '11XL',
			],
			'width' => ['tight', 'standard width', 'wide', 'semi-tight', 'semi-wide', 'extra tight', 'extra wide'],
		];


		$attrManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'attribute' );

		foreach( $sizes as $type => $list )
		{
			$pos = 0;
			$attrItem = $attrManager->createItem()
				->setTypeId( $this->getTypeId( 'attribute/type', 'product', $type ) )
				->setDomain( 'product' )
				->setStatus( 1 );

			foreach( $list as $value )
			{
				$item = (clone $attrItem)
					->setPosition( $pos++ )
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
