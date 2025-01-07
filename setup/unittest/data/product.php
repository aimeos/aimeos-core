<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


return [
	'product' => [
		'product/ABCD' => [
			'product.type' => 'default', 'product.code' => 'ABCD', 'product.label' => 'ABCD/16 discs', 'product.instock' => 1,
			'lists' => [
				'attribute' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0, 'ref' => 'product/size/xl',
				]],
				'catalog' => [[
					'product.lists.type' => 'default', 'product.lists.domain' => 'catalog', 'product.lists.position' => 0,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2099-01-01 00:00:00',
					'ref' => 'Internet',
				]],
				'price' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/15.00/1.00', 'price.quantity' => 1, 'price.value' => '15.00',
					'price.costs' => '1.00', 'price.rebate' => '1.00', 'price.taxrate' => '19.00'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 1,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/12.00/1.50', 'price.quantity' => 1000, 'price.value' => '12.00',
					'price.costs' => '1.50', 'price.rebate' => '0.00', 'price.taxrate' => '19.00'
				]],
				'text' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'product',
					'text.label' => 'subproduct1', 'text.content' => 'Unterproduct 1'
				]],
			],
		],
		'product/EFGH' => [
			'product.type' => 'default', 'product.code' => 'EFGH', 'product.label' => 'EFGH/16 discs', 'product.instock' => 0,
			'lists' => [
				'attribute' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0, 'ref' => 'product/size/xxl'
				]],
				'price' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/25.00/2.00', 'price.quantity' => 1, 'price.value' => '25.00',
					'price.costs' => '2.00', 'price.rebate' => '2.00', 'price.taxrate' => '19.00'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 1,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/22.00/2.00', 'price.quantity' => 50, 'price.value' => '22.00',
					'price.costs' => '2.00', 'price.rebate' => '2.00', 'price.taxrate' => '19.00'
				]],
				'text' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'product',
					'text.label' => 'subproduct2', 'text.content' => 'Unterproduct 2'
				]],
			],
		],
		'product/IJKL' => [
			'product.type' => 'default', 'product.code' => 'IJKL', 'product.label' => 'IJKL/16 discs', 'product.instock' => 1,
			'lists' => [
				'attribute' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0, 'ref' => 'product/size/xl',
				]],
				'price' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/12.00/0.00', 'price.quantity' => 2, 'price.value' => '12.00',
					'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '19.00'
				]],
				'text' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'product',
					'text.label' => 'subproduct3', 'text.content' => 'Unterproduct 3'
				]],
			],
		],
		'product/MNOP' => [
			'product.type' => 'default', 'product.code' => 'MNOP', 'product.label' => 'MNOP/16 discs', 'product.instock' => 1,
			'lists' => [
				'attribute' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0, 'ref' => 'product/size/m'
				]],
				'catalog' => [[
					'product.lists.type' => 'default', 'product.lists.domain' => 'catalog', 'product.lists.position' => 1,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2099-01-01 00:00:00',
					'ref' => 'Internet',
				]],
			],
		],
		'product/QRST' => [
			'product.type' => 'default', 'product.code' => 'QRST', 'product.label' => 'QRST/16 discs',
			'product.status' => 0, 'product.instock' => 1,
			'lists' => [
				'attribute' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0, 'ref' => 'product/size/xl'
				]],
				'catalog' => [[
					'product.lists.type' => 'default', 'product.lists.domain' => 'catalog', 'product.lists.position' => 2,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2099-01-01 00:00:00',
					'ref' => 'Internet',
				]],
				'price' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/50.00/0.00', 'price.quantity' => 1, 'price.value' => '50.00',
					'price.costs' => '0.00', 'price.rebate' => '3.00', 'price.taxrate' => '7.00'
				]],
			],
		],
		'product/U:MD' => [
			'product.type' => 'default', 'product.code' => 'U:MD', 'product.label' => 'Unittest: Monetary rebate',
			'product.status' => 0, 'product.instock' => 1,
			'lists' => [
				'price' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/29.95/0.00', 'price.quantity' => 1, 'price.value' => '29.95',
					'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '7.00'
				]],
				'text' => [[
					'product.lists.type' => 'unittype25', 'product.lists.position' => 0,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'product',
					'text.label' => 'rebate', 'text.content' => 'Geldwerter Nachlass'
				], [
					'product.lists.type' => 'unittype26', 'product.lists.position' => 1,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'short', 'text.domain' => 'product',
					'text.label' => 'money_rebate', 'text.content' => 'Unittest: Monetary rebate.'
				]],
			],
		],
		'product/U:SD' => [
			'product.type' => 'default', 'product.code' => 'U:SD', 'product.label' => 'Unittest: Shipping rebate',
			'product.status' => 0, 'product.instock' => 1,
			'lists' => [
				'price' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/19.95/0.00', 'price.quantity' => 1, 'price.value' => '19.95',
					'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '7.00'
				]],
				'product' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0, 'ref' => 'MNOP/16 discs',
				]],
				'text' => [[
					'product.lists.type' => 'unittype27', 'product.lists.position' => 0,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'product',
					'text.label' => 'delivery_rebate', 'text.content' => 'Versandkosten Nachlass'
				], [
					'product.lists.type' => 'unittype28', 'product.lists.position' => 1,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'short', 'text.domain' => 'product',
					'text.label' => 'costs_rebate', 'text.content' => 'Unittest: Shipping rebate.'
				]],
			],
		],
		'product/U:PD' => [
			'product.type' => 'voucher', 'product.code' => 'U:PD', 'product.label' => 'Unittest: Present rebate',
			'product.status' => 0, 'product.instock' => 1,
			'lists' => [
				'price' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/199.95/0.00', 'price.quantity' => 1, 'price.value' => '199.95',
					'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '19.00', 'price.status' => 0
				]],
				'product' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0, 'ref' => 'QRST/16 discs',
				]],
				'text' => [[
					'product.lists.type' => 'unittype29', 'product.lists.position' => 0,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'product',
					'text.label' => 'gift_rebate', 'text.content' => 'Geschenk Nachlass'
				], [
					'product.lists.type' => 'unittype30', 'product.lists.position' => 1,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'short', 'text.domain' => 'product',
					'text.label' => 'present_rebate', 'text.content' => 'Unittest: Present rebate.'
				]],
			],
		],
		'product/U:WH' => [
			'product.type' => 'default', 'product.code' => 'U:WH', 'product.label' => 'Unittest: Present rebate',
			'product.status' => 0, 'product.instock' => 1,
		],
		'product/U:CF' => [
			'product.type' => 'default', 'product.code' => 'U:CF', 'product.label' => 'Unittest: Cheapest free rebate',
			'product.status' => 0, 'product.instock' => 1,
			'lists' => [
				'price' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/0.00/0.00', 'price.quantity' => 1, 'price.value' => '0.00',
					'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '0.00', 'price.status' => 0
				]],
				'text' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'product',
					'text.label' => 'productUT', 'text.content' => 'Produkt fuer UT:Cheapest free'
				]],
			],
		],
		'product/U:TESTSUB01' => [
			'product.type' => 'default', 'product.code' => 'U:TESTSUB01', 'product.label' => 'Unittest: Test Sub 1',
			'product.instock' => 1,
			'lists' => [
				'attribute' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0, 'ref' => 'product/color/white',
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 1, 'ref' => 'product/size/m',
				]],
			],
		],
		'product/U:TESTSUB02' => [
			'product.type' => 'default', 'product.code' => 'U:TESTSUB02', 'product.label' => 'Unittest: Test Sub 2',
			'product.instock' => 1,
			'lists' => [
				'attribute' => [[
					'product.lists.type' => 'variant', 'product.lists.position' => 0, 'ref' => 'product/length/30',
				], [
					'product.lists.type' => 'variant', 'product.lists.position' => 1, 'ref' => 'product/width/30',
				]],
			],
		],
		'product/U:TESTSUB03' => [
			'product.type' => 'default', 'product.code' => 'U:TESTSUB03', 'product.label' => 'Unittest: Test Sub 3',
			'product.instock' => 1,
			'lists' => [
				'attribute' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0, 'ref' => 'product/color/blue',
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 1, 'ref' => 'product/size/l',
				]],
			],
		],
		'product/U:TESTSUB04' => [
			'product.type' => 'default', 'product.code' => 'U:TESTSUB04', 'product.label' => 'Unittest: Test Sub 4',
			'product.instock' => 1,
			'lists' => [
				'attribute' => [[
					'product.lists.type' => 'variant', 'product.lists.position' => 0, 'ref' => 'product/length/32',
				], [
					'product.lists.type' => 'variant', 'product.lists.position' => 1, 'ref' => 'product/width/30',
				]],
				'price' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/28.00/0.00', 'price.quantity' => 1, 'price.value' => '28.00',
					'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '19.00'
				]],
			],
		],
		'product/U:TESTSUB05' => [
			'product.type' => 'default', 'product.code' => 'U:TESTSUB05', 'product.label' => 'Unittest: Test Sub 5',
			'product.instock' => 1,
			'lists' => [
				'price' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/28.00/0.00', 'price.quantity' => 1, 'price.value' => '28.00',
					'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '19.00'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 1,
					'price.type' => 'purchase', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/purchase/12.00/0.00', 'price.quantity' => 1, 'price.value' => '12.00',
					'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '19.00'
				]],
			],
		],
		'product/U:TEST' => [
			'product.type' => 'select', 'product.code' => 'U:TEST', 'product.label' => 'Unittest: Test Selection',
			'product.instock' => 1,
			'lists' => [
				'catalog' => [[
					'product.lists.type' => 'promotion', 'product.lists.domain' => 'catalog', 'product.lists.position' => 0,
					'ref' => 'Misc',
				]],
				'media' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'media.languageid' => 'de', 'media.type' => 'download', 'media.domain' => 'product',
					'media.label' => 'path/to/folder/example5.jpg', 'media.url' => 'path/to/folder/example5.jpg',
					'media.previews' => [1 => 'path/to/folder/example5.jpg'], 'media.mimetype' => 'image/jpeg',
					'lists' => [
						'attribute' => [[
							'media.lists.type' => 'default', 'media.lists.position' => 0, 'ref' => 'media/color/white'
						]],
					],
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 1,
					'media.languageid' => 'de', 'media.type' => 'download', 'media.domain' => 'product',
					'media.label' => 'path/to/folder/example6.jpg', 'media.url' => 'path/to/folder/example6.jpg',
					'media.previews' => [1 => 'path/to/folder/example6.jpg'], 'media.mimetype' => 'image/jpeg',
					'lists' => [
						'attribute' => [[
							'media.lists.type' => 'default', 'media.lists.position' => 0, 'ref' => 'media/color/blue'
						]],
					],
				]],
				'price' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/18.00/1.00', 'price.quantity' => 1, 'price.value' => '18.00',
					'price.costs' => '1.00', 'price.rebate' => '0.00', 'price.taxrate' => '19.00'
				]],
				'product' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 1, 'ref' => 'Unittest: Test Sub 1',
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 2, 'ref' => 'Unittest: Test Sub 2',
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 3, 'ref' => 'Unittest: Test Sub 3',
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 4, 'ref' => 'Unittest: Test Sub 4',
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 5, 'ref' => 'Unittest: Test Sub 5',
				]],
			],
		],
		'product/U:noSel' => [
			'product.type' => 'select', 'product.code' => 'U:noSel', 'product.label' => 'Unittest: Empty Selection',
			'product.instock' => 1,
			'lists' => [
				'catalog' => [[
					'product.lists.type' => 'default', 'product.lists.domain' => 'catalog', 'product.lists.position' => 5,
					'ref' => 'Misc',
				]],
				'price' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/12.00/0.00', 'price.quantity' => 2, 'price.value' => '12.00',
					'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '19.00'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 1,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/11.00/0.00', 'price.quantity' => 4, 'price.value' => '11.00',
					'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '19.00'
				]],
			],
		],
		'product/U:TESTPSUB01' => [
			'product.type' => 'default', 'product.code' => 'U:TESTPSUB01', 'product.label' => 'Unittest: Test priced Sub 1',
			'product.instock' => 1,
		],
		'product/U:TESTP' => [
			'product.type' => 'select', 'product.code' => 'U:TESTP', 'product.label' => 'Unittest: Test priced Selection',
			'product.instock' => 1,
			'lists' => [
				'attribute' => [[
					'product.lists.type' => 'config', 'product.lists.position' => 0, 'ref' => 'product/color/white',
				], [
					'product.lists.type' => 'config', 'product.lists.position' => 1, 'ref' => 'product/size/xs',
				], [
					'product.lists.type' => 'hidden', 'product.lists.position' => 0, 'ref' => 'product/width/29',
				], [
					'product.lists.type' => 'custom', 'product.lists.position' => 0, 'ref' => 'product/date/custom',
				], [
					'product.lists.type' => 'custom', 'product.lists.position' => 0, 'ref' => 'product/price/custom',
				]],
				'catalog' => [[
					'product.lists.type' => 'default', 'product.lists.domain' => 'catalog', 'product.lists.position' => 0,
					'ref' => 'Misc',
				]],
				'product' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0, 'ref' => 'Unittest: Test priced Sub 1',
				]],
				'price' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/18.00/1.00', 'price.quantity' => 1, 'price.value' => '18.00',
					'price.costs' => '1.00', 'price.rebate' => '0.00', 'price.taxrate' => '19.00'
				]],
			],
		],
		'product/bdl:zyx' => [
			'product.type' => 'bundle', 'product.code' => 'bdl:zyx', 'product.label' => 'Unittest: Bundle bdl:zyx',
			'product.instock' => 1,
		],
		'product/bdl:EFG' => [
			'product.type' => 'bundle', 'product.code' => 'bdl:EFG', 'product.label' => 'Unittest: Bundle bdl:EFG',
			'product.instock' => 1,
		],
		'product/bdl:HIJ' => [
			'product.type' => 'bundle', 'product.code' => 'bdl:HIJ', 'product.label' => 'Unittest: Bundle bdl:HIJ',
			'product.instock' => 1,
		],
		'product/bdl:hal' => [
			'product.type' => 'bundle', 'product.code' => 'bdl:hal', 'product.label' => 'Unittest: Bundle bdl:hal',
			'product.instock' => 1,
		],
		'product/bdl:EFX' => [
			'product.type' => 'bundle', 'product.code' => 'bdl:EFX', 'product.label' => 'Unittest: Bundle bdl:EFX',
			'product.instock' => 1,
		],
		'product/bdl:HKL' => [
			'product.type' => 'bundle', 'product.code' => 'bdl:HKL', 'product.label' => 'Unittest: Bundle bdl:HKL',
			'product.instock' => 1,
		],
		'product/CNC' => [
			'product.type' => 'default', 'product.code' => 'CNC', 'product.label' => 'Cafe Noire Cappuccino',
			'product.url' => 'cafe_noire_cappuccino', 'product.config' => ['css-class' => 'sale'],
			'product.dataset' => 'Coffee', 'product.scale' => 0.1, 'product.instock' => 1, 'product.boost' => 2,
			'lists' => [
				'attribute' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0, 'ref' => 'product/size/xs',
				]],
				'catalog' => [[
					'product.lists.type' => 'promotion', 'product.lists.domain' => 'catalog', 'product.lists.position' => 1,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2099-01-01 00:00:00',
					'ref' => 'Kaffee',
				], [
					'product.lists.type' => 'new', 'product.lists.domain' => 'catalog', 'product.lists.position' => 0,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2099-01-01 00:00:00',
					'ref' => 'Neu',
				], [
					'product.lists.type' => 'default', 'product.lists.domain' => 'catalog', 'product.lists.position' => 1,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2099-01-01 00:00:00',
					'ref' => 'Neu',
				], [
					'product.lists.type' => 'internet', 'product.lists.domain' => 'catalog', 'product.lists.position' => 0,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2099-01-01 00:00:00',
					'ref' => 'Internet',
				], [
					'product.lists.type' => 'default', 'product.lists.domain' => 'catalog', 'product.lists.position' => 0,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2099-01-01 00:00:00',
					'ref' => 'Internet',
				]],
				'media' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 1,
					'product.lists.datestart' => '2000-01-01 00:00:00', 'product.lists.dateend' => '2100-01-01 00:00:00',
					'media.languageid' => 'de', 'media.type' => 'slideshow', 'media.domain' => 'product',
					'media.label' => 'prod_266x221/198_prod_266x221.jpg', 'media.url' => 'prod_266x221/198_prod_266x221.jpg',
					'media.previews' => [1 => 'prod_266x221/198_prod_266x221.jpg'], 'media.mimetype' => 'image/jpeg',
					'lists' => [
						'attribute' => [[
							'media.lists.type' => 'option', 'media.lists.position' => 1, 'ref' => 'media/color/olive',
						], [
							'media.lists.type' => 'option', 'media.lists.position' => 2, 'ref' => 'media/color/blue',
						], [
							'media.lists.type' => 'option', 'media.lists.position' => 3, 'ref' => 'media/color/red',
						]],
						'text' => [[
							'media.lists.type' => 'default', 'media.lists.position' => 0,
							'text.languageid' => 'de', 'text.type' => 'img-description', 'text.domain' => 'media',
							'text.label' => 'img_desc', 'text.content' => 'Bildbeschreibung',
						]],
					],
					'property' => [[
						'media.property.type' => 'copyright', 'media.property.languageid' => 'de', 'media.property.value' => 'ich, 2019',
					], [
						'media.property.type' => '200', 'media.property.value' => 'prod_266x221/199_prod_200x180.jpg',
					]],
				], [
					'product.lists.type' => 'unittype9', 'product.lists.position' => 2,
					'product.lists.datestart' => '2000-01-01 00:00:00', 'product.lists.dateend' => '2100-01-01 00:00:00',
					'media.languageid' => 'de', 'media.type' => 'slideshow', 'media.domain' => 'product',
					'media.label' => 'prod_114x95/194_prod_114x95.jpg', 'media.url' => 'prod_114x95/194_prod_114x95.jpg',
					'media.previews' => [1 => 'prod_114x95/194_prod_114x95.jpg'], 'media.mimetype' => 'image/jpeg',
					'lists' => [
						'attribute' => [[
							'media.lists.type' => 'option', 'media.lists.position' => 1, 'ref' => 'media/color/blue',
						]],
					],
				], [
					'product.lists.type' => 'unittype10', 'product.lists.position' => 3,
					'product.lists.datestart' => '2000-01-01 00:00:00', 'product.lists.dateend' => '2010-01-01 00:00:00',
					'media.languageid' => 'de', 'media.type' => 'slideshow', 'media.domain' => 'product',
					'media.label' => 'prod_179x178/196_prod_179x178.jpg', 'media.url' => 'prod_179x178/196_prod_179x178.jpg',
					'media.previews' => [1 => 'prod_179x178/196_prod_179x178.jpg'], 'media.mimetype' => 'image/jpeg',
					'lists' => [
						'attribute' => [[
							'media.lists.type' => 'option', 'media.lists.position' => 1, 'ref' => 'media/color/red',
							'media.lists.datestart' => '2002-01-01 00:00:00', 'media.lists.dateend' => '2006-12-31 23:59:59'
						]],
					],
				]],
				'price' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/600.00/30.00', 'price.quantity' => 1, 'price.value' => '600.00',
					'price.costs' => '30.00', 'price.rebate' => '0.00', 'price.taxrate' => '19.00',
					'property' => [[
						'price.property.type' => 'zone', 'price.property.languageid' => null, 'price.property.value' => 'NY'
					]],
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 1,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/580.00/20.00', 'price.quantity' => 100, 'price.value' => '580.00',
					'price.costs' => '20.00', 'price.rebate' => '0.00', 'price.taxrate' => '19.00',
					'property' => [[
						'price.property.type' => 'zone', 'price.property.languageid' => null, 'price.property.value' => 'NY'
					]],
				]],
				'supplier' => [[
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2100-01-01 00:00:00',
					'product.lists.position' => 0, 'ref' => 'Unit Supplier 001'
				]],
				'tag' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0, 'ref' => 'Kaffee',
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 1, 'ref' => 'Cappuccino',
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 2, 'ref' => 'mild',
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 3, 'ref' => 'cremig',
				]],
				'text' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'product',
					'text.label' => 'cnc', 'text.content' => 'Cafe Noire Cappuccino'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 1,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'short', 'text.domain' => 'product',
					'text.label' => 'cnc_short_desc', 'text.content' => 'Der köstliche Cappuccino mit Espresso und feinem Milchschaum.'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 2,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'long', 'text.domain' => 'product',
					'text.label' => 'cnc_long_desc', 'text.content' => 'Cafe Noire gehört zu den beliebtesten Kaffeemarken in Frankreich.'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 3,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'url', 'text.domain' => 'product',
					'text.label' => 'cnc_url', 'text.content' => 'Cafe-Noire-Cappuccino'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 4,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => null, 'text.type' => 'meta-keyword', 'text.domain' => 'product',
					'text.label' => 'cnc_metakey', 'text.content' => 'Cappuccino keywords without language ID'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 5,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'meta-description', 'text.domain' => 'product',
					'text.label' => 'cnc_metadesc', 'text.content' => 'Cafe Noire Cappuccino online kaufen'
				]],
			],
			'property' => [[
				'product.property.type' => 'package-height', 'product.property.languageid' => null, 'product.property.value' => '10.0'
			], [
				'product.property.type' => 'package-length', 'product.property.languageid' => null, 'product.property.value' => '20.0'
			], [
				'product.property.type' => 'package-width', 'product.property.languageid' => null, 'product.property.value' => '15.0'
			], [
				'product.property.type' => 'package-weight', 'product.property.languageid' => null, 'product.property.value' => '1.25'
			]],
		],
		'product/CNE' => [
			'product.type' => 'default', 'product.code' => 'CNE', 'product.label' => 'Cafe Noire Expresso',
			'product.url' => 'cafe_noire_expresso', 'product.config' => ['css-class' => 'top', 'size' => 1],
			'product.dataset' => 'Coffee', 'product.scale' => 0.1, 'product.rating' => 4, 'product.ratings' => 1,
			'product.instock' => 1, 'product.boost' => 3.33,
			'lists' => [
				'attribute' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0, 'ref' => 'product/size/xs',
				], [
					'product.lists.type' => 'variant', 'product.lists.position' => 1, 'ref' => 'product/length/30',
				], [
					'product.lists.type' => 'variant', 'product.lists.position' => 2, 'ref' => 'product/width/29',
				], [
					'product.lists.type' => 'config', 'product.lists.position' => 3, 'ref' => 'product/color/white',
				], [
					'product.lists.type' => 'config', 'product.lists.position' => 4, 'ref' => 'product/interval/P1Y0M0W0D',
				], [
					'product.lists.type' => 'hidden', 'product.lists.position' => 5, 'ref' => 'product/download/testurl',
				]],
				'catalog' => [[
					'product.lists.type' => 'promotion', 'product.lists.domain' => 'catalog', 'product.lists.position' => 0,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2099-01-01 00:00:00',
					'ref' => 'Kaffee',
				], [
					'product.lists.type' => 'default', 'product.lists.domain' => 'catalog', 'product.lists.position' => 0,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2099-01-01 00:00:00',
					'ref' => 'Kaffee',
				], [
					'product.lists.type' => 'new', 'product.lists.domain' => 'catalog', 'product.lists.position' => 0,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2099-01-01 00:00:00',
					'ref' => 'Neu',
				], [
					'product.lists.type' => 'default', 'product.lists.domain' => 'catalog', 'product.lists.position' => 0,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2099-01-01 00:00:00',
					'ref' => 'Neu',
				], [
					'product.lists.type' => 'internet', 'product.lists.domain' => 'catalog', 'product.lists.position' => 0,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2099-01-01 00:00:00',
					'ref' => 'Internet',
				]],
				'media' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 1,
					'product.lists.datestart' => '2000-01-01 00:00:00', 'product.lists.dateend' => '2100-01-01 00:00:00',
					'media.languageid' => 'de', 'media.type' => 'slideshow', 'media.domain' => 'product',
					'media.label' => 'prod_266x221/198_prod_266x221.jpg', 'media.url' => 'prod_266x221/198_prod_266x221.jpg',
					'media.previews' => [1 => 'prod_266x221/198_prod_266x221.jpg'], 'media.mimetype' => 'image/jpeg',
					'lists' => [
						'attribute' => [[
							'media.lists.type' => 'option', 'media.lists.position' => 1, 'ref' => 'media/color/olive',
						], [
							'media.lists.type' => 'option', 'media.lists.position' => 2, 'ref' => 'media/color/blue',
						], [
							'media.lists.type' => 'option', 'media.lists.position' => 3, 'ref' => 'media/color/red',
						]],
						'text' => [[
							'media.lists.type' => 'default', 'media.lists.position' => 0,
							'text.languageid' => 'de', 'text.type' => 'img-description', 'text.domain' => 'media',
							'text.label' => 'img_desc', 'text.content' => 'Bildbeschreibung',
						]],
					],
				], [
					'product.lists.type' => 'unittype3', 'product.lists.position' => 2,
					'product.lists.datestart' => '2000-01-01 00:00:00', 'product.lists.dateend' => '2100-01-01 00:00:00',
					'media.languageid' => 'de', 'media.type' => 'slideshow', 'media.domain' => 'product',
					'media.label' => 'prod_114x95/194_prod_114x95.jpg', 'media.url' => 'prod_114x95/194_prod_114x95.jpg',
					'media.previews' => [1 => 'prod_114x95/194_prod_114x95.jpg'], 'media.mimetype' => 'image/jpeg',
					'lists' => [
						'attribute' => [[
							'media.lists.type' => 'option', 'media.lists.position' => 1, 'ref' => 'media/color/blue',
						]],
					],
				], [
					'product.lists.type' => 'unittype4', 'product.lists.position' => 3,
					'product.lists.datestart' => '2000-01-01 00:00:00', 'product.lists.dateend' => '2010-01-01 00:00:00',
					'media.languageid' => 'de', 'media.type' => 'slideshow', 'media.domain' => 'product',
					'media.label' => 'prod_179x178/196_prod_179x178.jpg', 'media.url' => 'prod_179x178/196_prod_179x178.jpg',
					'media.previews' => [1 => 'prod_179x178/196_prod_179x178.jpg'], 'media.mimetype' => 'image/jpeg',
					'lists' => [
						'attribute' => [[
							'media.lists.type' => 'option', 'media.lists.position' => 1, 'ref' => 'media/color/red',
							'media.lists.datestart' => '2002-01-01 00:00:00', 'media.lists.dateend' => '2006-12-31 23:59:59'
						]],
					],
				], [
					'product.lists.type' => 'download', 'product.lists.position' => 1,
					'product.lists.datestart' => '2000-01-01 00:00:00', 'product.lists.dateend' => null,
					'media.languageid' => 'de', 'media.type' => 'download', 'media.domain' => 'product',
					'media.label' => 'path/to/folder/example5.jpg', 'media.url' => 'path/to/folder/example5.jpg',
					'media.previews' => [1 => 'path/to/folder/example5.jpg'], 'media.mimetype' => 'image/jpeg',
					'lists' => [
						'attribute' => [[
							'media.lists.type' => 'default', 'media.lists.position' => 0, 'ref' => 'media/color/white'
						]],
					],
					'property' => [[
						'media.property.type' => 'title', 'media.property.languageid' => 'de', 'media.property.value' => 'Example image',
					]],
				]],
				'price' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/18.00/1.00', 'price.quantity' => 1, 'price.value' => '18.00',
					'price.costs' => '1.00', 'price.rebate' => '0.00', 'price.taxrate' => '19.00',
					'property' => [[
						'price.property.type' => 'zone', 'price.property.languageid' => null, 'price.property.value' => 'CA'
					]],
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 1,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label' => 'product/default/15.00/1.50', 'price.quantity' => 1000, 'price.value' => '15.00',
					'price.costs' => '1.50', 'price.rebate' => '0.00', 'price.taxrate' => '19.00',
					'property' => [[
						'price.property.type' => 'zone', 'price.property.languageid' => null, 'price.property.value' => 'CA'
					]],
				]],
				'product' => [[
					'product.lists.type' => 'bought-together', 'product.lists.position' => 0, 'ref' => 'Cafe Noire Cappuccino',
				], [
					'product.lists.type' => 'suggestion', 'product.lists.position' => 0, 'ref' => 'Cafe Noire Cappuccino',
				]],
				'supplier' => [[
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.position' => 1,
					'ref' => 'Unit Supplier 001',
				]],
				'tag' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0, 'ref' => 'Expresso'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 1, 'ref' => 'Kaffee'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 2, 'ref' => 'herb'
				]],
				'text' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'text.languageid' => 'de', 'text.type' => 'basket', 'text.domain' => 'product',
					'text.label' => 'cne_basket', 'text.content' => 'Cafe Noire Expresso for basket'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'product',
					'text.label' => 'cne', 'text.content' => 'Cafe Noire Expresso'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 1,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'short', 'text.domain' => 'product',
					'text.label' => 'cne_short_desc', 'text.content' => 'Ein vollaromatischer Espresso mit herrlich feinem Schaum'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 2,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'long', 'text.domain' => 'product',
					'text.label' => 'cne_long_desc', 'text.content' => 'Dieser kurze Kaffee mit seinem reichen Geschmack,
						delikaten Aroma und feinen Schaum ist das ultimative Getränk für jede Tageszeit.<br>
						Erhältlich in Packungen mit 16 T-DISCs (Hypermärkte) oder 8 T-DISCs (Supermärkte).'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 3,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'url', 'text.domain' => 'product',
					'text.label' => 'cne_url', 'text.content' => 'Cafe-Noire-Expresso'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 4,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => null, 'text.type' => 'meta-keyword', 'text.domain' => 'product',
					'text.label' => 'cne_metakey', 'text.content' => 'Kaffee keywords without language ID'
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 5,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => 'de', 'text.type' => 'meta-description', 'text.domain' => 'product',
					'text.label' => 'cne_metadesc', 'text.content' => 'Expresso'
				]],
			],
			'property' => [[
				'product.property.type' => 'package-height', 'product.property.languageid' => null, 'product.property.value' => '10.0'
			], [
				'product.property.type' => 'package-length', 'product.property.languageid' => null, 'product.property.value' => '25.00'
			], [
				'product.property.type' => 'package-width', 'product.property.languageid' => null, 'product.property.value' => '17.5'
			], [
				'product.property.type' => 'package-weight', 'product.property.languageid' => null, 'product.property.value' => '1'
			]],
		],
		'product/U:BUNDLE' => [
			'product.type' => 'bundle', 'product.code' => 'U:BUNDLE', 'product.label' => 'Unittest: Bundle',
			'product.instock' => 1,
			'lists' => [
				'catalog' => [[
					'product.lists.type' => 'default', 'product.lists.domain' => 'catalog', 'product.lists.position' => 2,
					'product.lists.datestart' => '2010-01-01 00:00:00', 'product.lists.dateend' => '2099-01-01 00:00:00',
					'ref' => 'Neu',
				]],
				'product' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0, 'ref' => 'Cafe Noire Cappuccino',
				], [
					'product.lists.type' => 'default', 'product.lists.position' => 1, 'ref' => 'Cafe Noire Expresso',
				]],
				'price' => [[
					'product.lists.type' => 'default', 'product.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'product',
					'price.label'=>'product/default/600.00/30.00', 'price.quantity' => 1, 'price.value' => '600.00',
					'price.costs' => '30.00', 'price.rebate' => '0.00', 'price.taxrate' => '19.00'
				]],
			],
		],
	],
];
