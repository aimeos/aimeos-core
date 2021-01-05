<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

return [
	'attribute/type' => [
		['attribute.type.domain' => 'product', 'attribute.type.code' => 'color', 'attribute.type.label' => 'Color'],
		['attribute.type.domain' => 'product', 'attribute.type.code' => 'size', 'attribute.type.label' => 'Size'],
		['attribute.type.domain' => 'product', 'attribute.type.code' => 'width', 'attribute.type.label' => 'Width'],
		['attribute.type.domain' => 'product', 'attribute.type.code' => 'length', 'attribute.type.label' => 'Length'],
		['attribute.type.domain' => 'product', 'attribute.type.code' => 'download', 'attribute.type.label' => 'Download'],
		['attribute.type.domain' => 'product', 'attribute.type.code' => 'date', 'attribute.type.label' => 'Date'],
		['attribute.type.domain' => 'product', 'attribute.type.code' => 'price', 'attribute.type.label' => 'Price'],
		['attribute.type.domain' => 'product', 'attribute.type.code' => 'interval', 'attribute.type.label' => 'Interval'],
		['attribute.type.domain' => 'media', 'attribute.type.code' => 'color', 'attribute.type.label' => 'Color'],
	],

	'attribute/lists/type' => [
		['attribute.lists.type.domain' => 'attribute', 'attribute.lists.type.code' => 'default', 'attribute.lists.type.label' => 'Standard'],
		['attribute.lists.type.domain' => 'catalog', 'attribute.lists.type.code' => 'default', 'attribute.lists.type.label' => 'Standard'],
		['attribute.lists.type.domain' => 'media', 'attribute.lists.type.code' => 'default', 'attribute.lists.type.label' => 'Standard'],
		['attribute.lists.type.domain' => 'price', 'attribute.lists.type.code' => 'default', 'attribute.lists.type.label' => 'Standard'],
		['attribute.lists.type.domain' => 'product', 'attribute.lists.type.code' => 'default', 'attribute.lists.type.label' => 'Standard'],
		['attribute.lists.type.domain' => 'service', 'attribute.lists.type.code' => 'default', 'attribute.lists.type.label' => 'Standard'],
		['attribute.lists.type.domain' => 'text', 'attribute.lists.type.code' => 'default', 'attribute.lists.type.label' => 'Standard'],
	],

	'attribute/property/type' => [
		['attribute.property.type.domain' => 'attribute', 'attribute.property.type.code' => 'size', 'attribute.property.type.label' => 'Size', 'attribute.property.type.position' => 0],
		['attribute.property.type.domain' => 'attribute', 'attribute.property.type.code' => 'mtime', 'attribute.property.type.label' => 'Modification time', 'attribute.property.type.position' => 1],
		['attribute.property.type.domain' => 'attribute', 'attribute.property.type.code' => 'htmlcolor', 'attribute.property.type.label' => 'HTML color code', 'attribute.property.type.position' => 2],
	],

	'attribute' => [
		'attribute/product/size/xs' => [
			'attribute.domain' => 'product', 'attribute.type' => 'size', 'attribute.code' => 'xs',
			'attribute.label' => 'product/size/xs', 'attribute.position' => 0,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text', 'attribute.lists.position' => 1,
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'size/XS', 'text.content' => 'XS',
				], [
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text', 'attribute.lists.position' => 2,
					'text.languageid' => 'de', 'text.type' => 'long', 'text.domain' => 'attribute',
					'text.label' => 'small_items', 'text.content' => 'Artikel in dieser Größe fallen unter Umständen sehr klein aus.',
				], [
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text', 'attribute.lists.position' => 3,
					'text.languageid' => 'de', 'text.type' => 'short', 'text.domain' => 'attribute',
					'text.label' => 'small_size', 'text.content' => 'kleine Größe',
				]],
				'media' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'media',
					'media.languageid' => 'de', 'media.type' => 'default', 'media.domain' => 'attribute',
					'media.label' => 'prod_97x93/199_prod_97x93.jpg', 'media.link' => 'prod_97x93/199_prod_97x93.jpg',
					'media.previews' => [1 => 'prod_97x93/199_prod_97x93.jpg'], 'media.mimetype' => 'image/jpeg',
				]],
				'price' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'price',
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'attribute',
					'price.label'=>'attribute/default/12.95/1.99', 'price.quantity' => 1, 'price.value' => '12.95',
					'price.costs' => '1.99', 'price.rebate' => '1.05', 'price.taxrate' => '19.00',
				]],
			],
		],
		'attribute/product/size/s' => [
			'attribute.domain' => 'product', 'attribute.type' => 'size', 'attribute.code' => 's',
			'attribute.label' => 'product/size/s', 'attribute.position' => 1,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'size/S', 'text.content' => 'S',
				]],
			],
		],
		'attribute/product/size/m' => [
			'attribute.domain' => 'product', 'attribute.type' => 'size', 'attribute.code' => 'm',
			'attribute.label' => 'product/size/m', 'attribute.position' => 2,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'size/M', 'text.content' => 'M',
				]],
			],
		],
		'attribute/product/size/l' => [
			'attribute.domain' => 'product', 'attribute.type' => 'size', 'attribute.code' => 'l',
			'attribute.label' => 'product/size/l', 'attribute.position' => 3,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'size/L', 'text.content' => 'L',
				]],
			],
		],
		'attribute/product/size/xl' => [
			'attribute.domain' => 'product', 'attribute.type' => 'size', 'attribute.code' => 'xl',
			'attribute.label' => 'product/size/xl', 'attribute.position' => 4,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'size/XL', 'text.content' => 'XL',
				]],
				'price' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'price',
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'attribute',
					'price.label'=>'attribute/default/15.00/1.00', 'price.quantity' => 1, 'price.value' => '15.00',
					'price.costs' => '1.00', 'price.rebate' => '0.00', 'price.taxrate' => '19.00',
				]],
			],
		],
		'attribute/product/size/xxl' => [
			'attribute.domain' => 'product', 'attribute.type' => 'size', 'attribute.code' => 'xxl',
			'attribute.label' => 'product/size/xxl', 'attribute.position' => 5,
			'lists' => [
				'price' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'price',
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'attribute',
					'price.label'=>'attribute/default/99.99/9.99', 'price.quantity' => 1, 'price.value' => '99.99',
					'price.costs' => '9.99', 'price.rebate' => '0.00', 'price.taxrate' => '19.00',
				]],
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'size/XXL', 'text.content' => 'XXL',
				]],
			],
		],
		'attribute/product/length/30' => [
			'attribute.domain' => 'product', 'attribute.type' => 'length', 'attribute.code' => '30',
			'attribute.label' => 'product/length/30', 'attribute.position' => 0,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'lenth/30', 'text.content' => '30',
				]],
			],
		],
		'attribute/product/length/32' => [
			'attribute.domain' => 'product', 'attribute.type' => 'length', 'attribute.code' => '32',
			'attribute.label' => 'product/length/32', 'attribute.position' => 1,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'lenth/32', 'text.content' => '32',
				]],
			],
		],
		'attribute/product/length/34' => [
			'attribute.domain' => 'product', 'attribute.type' => 'length', 'attribute.code' => '34',
			'attribute.label' => 'product/length/34', 'attribute.position' => 2,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'lenth/34', 'text.content' => '34',
				]],
			],
		],
		'attribute/product/length/36' => [
			'attribute.domain' => 'product', 'attribute.type' => 'length', 'attribute.code' => '36',
			'attribute.label' => 'product/length/36', 'attribute.position' => 3,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'lenth/36', 'text.content' => '36',
				]],
			],
		],
		'attribute/product/length/38' => [
			'attribute.domain' => 'product', 'attribute.type' => 'length', 'attribute.code' => '38',
			'attribute.label' => 'product/length/38', 'attribute.position' => 3,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'lenth/38', 'text.content' => '38',
				]],
			],
		],

		'attribute/product/width/29' => [
			'attribute.domain' => 'product', 'attribute.type' => 'width', 'attribute.code' => '29',
			'attribute.label' => 'product/width/29', 'attribute.position' => 0,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'width/29', 'text.content' => '29',
				]],
			],
		],
		'attribute/product/width/30' => [
			'attribute.domain' => 'product', 'attribute.type' => 'width', 'attribute.code' => '30',
			'attribute.label' => 'product/width/30', 'attribute.position' => 1,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'width/30', 'text.content' => '30',
				]],
			],
		],
		'attribute/product/width/32' => [
			'attribute.domain' => 'product', 'attribute.type' => 'width', 'attribute.code' => '32',
			'attribute.label' => 'product/width/32', 'attribute.position' => 2,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'width/32', 'text.content' => '32',
				]],
			],
		],
		'attribute/product/width/33' => [
			'attribute.domain' => 'product', 'attribute.type' => 'width', 'attribute.code' => '33',
			'attribute.label' => 'product/width/33', 'attribute.position' => 3,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'width/33', 'text.content' => '33',
				]],
			],
		],
		'attribute/product/width/34' => [
			'attribute.domain' => 'product', 'attribute.type' => 'width', 'attribute.code' => '34',
			'attribute.label' => 'product/width/34', 'attribute.position' => 4,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'width/34', 'text.content' => '34',
				]],
			],
		],
		'attribute/product/width/36' => [
			'attribute.domain' => 'product', 'attribute.type' => 'width', 'attribute.code' => '36',
			'attribute.label' => 'product/width/36', 'attribute.position' => 5,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => null, 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'width/36', 'text.content' => '36',
				]],
			],
		],

		'attribute/product/color/white' => [
			'attribute.domain' => 'product', 'attribute.type' => 'color', 'attribute.code' => 'white',
			'attribute.label' => 'product/color/white', 'attribute.position' => 0,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'color/white', 'text.content' => 'weiß',
				]],
			],
		],
		'attribute/product/color/gray' => [
			'attribute.domain' => 'product', 'attribute.type' => 'color', 'attribute.code' => 'gray',
			'attribute.label' => 'product/color/gray', 'attribute.position' => 1,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'color/gray', 'text.content' => 'grau',
				]],
			],
		],
		'attribute/product/color/olive' => [
			'attribute.domain' => 'product', 'attribute.type' => 'color', 'attribute.code' => 'olive',
			'attribute.label' => 'product/color/olive', 'attribute.position' => 2,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'color/olive', 'text.content' => 'oliv',
				]],
			],
		],
		'attribute/product/color/blue' => [
			'attribute.domain' => 'product', 'attribute.type' => 'color', 'attribute.code' => 'blue',
			'attribute.label' => 'product/color/blue', 'attribute.position' => 3,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'color/blue', 'text.content' => 'blau',
				]],
			],
		],
		'attribute/product/color/red' => [
			'attribute.domain' => 'product', 'attribute.type' => 'color', 'attribute.code' => 'red',
			'attribute.label' => 'product/color/red', 'attribute.position' => 4,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'color/red', 'text.content' => 'rot',
				]],
			],
		],
		'attribute/product/color/black' => [
			'attribute.domain' => 'product', 'attribute.type' => 'color', 'attribute.code' => 'black',
			'attribute.label' => 'product/color/black', 'attribute.status' => 0, 'attribute.position' => 5,
			'lists' => [
				'text' => [[
					'attribute.lists.type' => 'default', 'attribute.lists.domain' => 'text',
					'attribute.lists.datestart' => '2000-01-01 00:00:00', 'attribute.lists.dateend' => '2001-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'attribute',
					'text.label' => 'color/black', 'text.content' => 'schwarz',
				]],
			],
			'property' => [[
				'attribute.property.type' => 'htmlcolor', 'attribute.property.languageid' => 'de', 'attribute.property.value' => '#000000',
			]],
		],
		'attribute/product/color/pink' => [
			'attribute.domain' => 'product', 'attribute.type' => 'color', 'attribute.code' => 'pink',
			'attribute.label' => 'product/color/pink', 'attribute.status' => 0, 'attribute.position' => 6,
		],

		'attribute/media/color/white' => [
			'attribute.domain' => 'media', 'attribute.type' => 'color', 'attribute.code' => 'white',
			'attribute.label' => 'media/color/white', 'attribute.position' => 0,
		],
		'attribute/media/color/gray' => [
			'attribute.domain' => 'media', 'attribute.type' => 'color', 'attribute.code' => 'gray',
			'attribute.label' => 'media/color/gray', 'attribute.position' => 1,
		],
		'attribute/media/color/olive' => [
			'attribute.domain' => 'media', 'attribute.type' => 'color', 'attribute.code' => 'olive',
			'attribute.label' => 'media/color/olive', 'attribute.position' => 2,
		],
		'attribute/media/color/blue' => [
			'attribute.domain' => 'media', 'attribute.type' => 'color', 'attribute.code' => 'blue',
			'attribute.label' => 'media/color/blue', 'attribute.position' => 3,
		],
		'attribute/media/color/red' => [
			'attribute.domain' => 'media', 'attribute.type' => 'color', 'attribute.code' => 'red',
			'attribute.label' => 'media/color/red', 'attribute.position' => 4,
		],
		'attribute/media/color/black' => [
			'attribute.domain' => 'media', 'attribute.type' => 'color', 'attribute.code' => 'black',
			'attribute.label' => 'media/color/black', 'attribute.status' => 0, 'attribute.position' => 5,
		],
		'attribute/media/color/pink' => [
			'attribute.domain' => 'media', 'attribute.type' => 'color', 'attribute.code' => 'pink',
			'attribute.label' => 'media/color/pink', 'attribute.status' => 0, 'attribute.position' => 6,
		],

		'attribute/product/interval/P1Y0M0W0D' => [
			'attribute.domain' => 'product', 'attribute.type' => 'interval', 'attribute.code' => 'P1Y0M0W0D',
			'attribute.label' => 'product/interval/P1Y0M0W0D', 'attribute.position' => 0,
		],
		'attribute/product/date/custom' => [
			'attribute.domain' => 'product', 'attribute.type' => 'date', 'attribute.code' => 'custom',
			'attribute.label' => 'product/date/custom', 'attribute.position' => 0,
		],
		'attribute/product/price/custom' => [
			'attribute.domain' => 'product', 'attribute.type' => 'price', 'attribute.code' => 'custom',
			'attribute.label' => 'product/price/custom', 'attribute.position' => 1,
		],
		'attribute/product/download/testurl' => [
			'attribute.domain' => 'product', 'attribute.type' => 'download', 'attribute.code' => 'testurl',
			'attribute.label' => 'product/download/testurl', 'attribute.position' => 0,
			'property' => [[
				'attribute.property.type' => 'size', 'attribute.property.languageid' => null, 'attribute.property.value' => '1024',
			], [
				'attribute.property.type' => 'mtime', 'attribute.property.languageid' => null, 'attribute.property.value' => '2000-01-01 00:00:00',
			]],
		],
	],
];
