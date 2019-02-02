<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

return array(
	'media/type' => [
		['media.type.domain' => 'catalog', 'media.type.code' => 'prod_123x103', 'media.type.label' => 'Product 123x103'],
		['media.type.domain' => 'product', 'media.type.code' => 'prod_266x221', 'media.type.label' => 'Product 266x221'],
		['media.type.domain' => 'product', 'media.type.code' => 'prod_114x95', 'media.type.label' => 'Product 114x95'],
		['media.type.domain' => 'product', 'media.type.code' => 'prod_179x178', 'media.type.label' => 'Product 179x178'],
		['media.type.domain' => 'attribute', 'media.type.code' => 'prod_242x416', 'media.type.label' => 'Product 242x416'],
		['media.type.domain' => 'attribute', 'media.type.code' => 'prod_97x93', 'media.type.label' => 'Product 97x93'],
		['media.type.domain' => 'product', 'media.type.code' => 'download', 'media.type.label' => 'Download'],
		['media.type.domain' => 'catalog', 'media.type.code' => 'stage', 'media.type.label' => 'Stage'],
		['media.type.domain' => 'catalog', 'media.type.code' => 'icon', 'media.type.label' => 'Stage'],

		['media.type.domain' => 'product', 'media.type.code' => 'default', 'media.type.label' => 'Standard'],
		['media.type.domain' => 'attribute', 'media.type.code' => 'default', 'media.type.label' => 'Standard'],
		['media.type.domain' => 'catalog', 'media.type.code' => 'default', 'media.type.label' => 'Standard'],
		['media.type.domain' => 'media', 'media.type.code' => 'default', 'media.type.label' => 'Standard'],
		['media.type.domain' => 'price', 'media.type.code' => 'default', 'media.type.label' => 'Standard'],
		['media.type.domain' => 'service', 'media.type.code' => 'default', 'media.type.label' => 'Standard'],
		['media.type.domain' => 'supplier', 'media.type.code' => 'default', 'media.type.label' => 'Standard'],
		['media.type.domain' => 'text', 'media.type.code' => 'default', 'media.type.label' => 'Standard'],
	],

	'media/lists/type' => [
		['media.lists.type.domain' => 'product', 'media.lists.type.code' => 'default', 'media.lists.type.label' => 'Standard'],
		['media.lists.type.domain' => 'attribute', 'media.lists.type.code' => 'default', 'media.lists.type.label' => 'Standard'],
		['media.lists.type.domain' => 'catalog', 'media.lists.type.code' => 'default', 'media.lists.type.label' => 'Standard'],
		['media.lists.type.domain' => 'media', 'media.lists.type.code' => 'default', 'media.lists.type.label' => 'Standard'],
		['media.lists.type.domain' => 'price', 'media.lists.type.code' => 'default', 'media.lists.type.label' => 'Standard'],
		['media.lists.type.domain' => 'service', 'media.lists.type.code' => 'default', 'media.lists.type.label' => 'Standard'],
		['media.lists.type.domain' => 'text', 'media.lists.type.code' => 'default', 'media.lists.type.label' => 'Standard'],

		['media.lists.type.domain' => 'attribute', 'media.lists.type.code' => 'variant', 'media.lists.type.label' => 'Variant'],
		['media.lists.type.domain' => 'attribute', 'media.lists.type.code' => 'option', 'media.lists.type.label' => 'Option'],
		['media.lists.type.domain' => 'attribute', 'media.lists.type.code' => 'front', 'media.lists.type.label' => 'Frontside'],
		['media.lists.type.domain' => 'attribute', 'media.lists.type.code' => 'back', 'media.lists.type.label' => 'Backside'],
	],

	'media' => array(
		'media/prod_97x93/199_prod_97x93.jpg' => array( 'langid' => 'de', 'type' => 'prod_97x93', 'domain' => 'attribute', 'label' => 'prod_97x93/199_prod_97x93.jpg', 'link' => 'prod_97x93/199_prod_97x93.jpg', 'preview' => 'prod_97x93/199_prod_97x93.jpg', 'status' => 1, 'mimetype' => 'image/jpeg' ),
		'media/prod_123x103/195_prod_123x103.jpg' => array( 'langid' => 'de', 'type' => 'prod_123x103', 'domain' => 'catalog', 'label' => 'prod_123x103/195_prod_123x103.jpg', 'link' => 'prod_123x103/195_prod_123x103.jpg', 'preview' => 'prod_123x103/195_prod_123x103.jpg', 'status' => 1, 'mimetype' => 'image/jpeg' ),
		'media/prod_242x416/197_CafeNoire_Colombia_242x416.jpg' => array( 'langid' => 'de', 'type' => 'prod_242x416', 'domain' => 'attribute', 'label' => 'prod_242x416/197_CafeNoire_Colombia_242x416.jpg', 'link' => 'prod_242x416/197_CafeNoire_Colombia_242x416.jpg', 'preview' => 'prod_242x416/197_CafeNoire_Colombia_242x416.jpg', 'status' => 1, 'mimetype' => 'image/jpeg' ),
		'media/path/to/folder/example1.jpg' => array( 'langid' => 'de', 'type' => 'default', 'domain' => 'catalog', 'label' => 'path/to/folder/example1.jpg', 'link' => 'path/to/folder/example1.jpg', 'preview' => 'path/to/folder/example1.jpg', 'status' => 1, 'mimetype' => 'image/jpeg' ),
		'media/path/to/folder/example2.jpg' => array( 'langid' => 'de', 'type' => 'prod_123x103', 'domain' => 'catalog', 'label' => 'path/to/folder/example2.jpg', 'link' => 'path/to/folder/example2.jpg', 'preview' => 'path/to/folder/example2.jpg', 'status' => 0, 'mimetype' => 'image/jpeg' ),
		'media/path/to/folder/example3.jpg' => array( 'langid' => 'de', 'type' => 'prod_123x103', 'domain' => 'catalog', 'label' => 'path/to/folder/example3.jpg', 'link' => 'path/to/folder/example3.jpg', 'preview' => 'path/to/folder/example3.jpg', 'status' => 0, 'mimetype' => 'image/jpeg' ),
		'media/path/to/folder/example4.jpg' => array( 'langid' => 'de', 'type' => 'icon', 'domain' => 'catalog', 'label' => 'path/to/folder/example4.jpg', 'link' => 'path/to/folder/example4.jpg', 'preview' => 'path/to/folder/example4.jpg', 'preview' => 'example4.jpg', 'status' => 1, 'mimetype' => 'image/jpeg' ),
		'media/service_image1' => array( 'langid' => null, 'type' => 'default', 'domain' => 'service', 'label' => 'service_image1', 'link' => 'path/to/service.png', 'preview' => 'path/to/service.png', 'status' => 1, 'mimetype' => 'image/png' ),
		'media/path/to/folder/cafe/stage.jpg' => array( 'langid' => 'de', 'type' => 'stage', 'domain' => 'catalog', 'label' => 'path/to/folder/cafe/stage.jpg', 'link' => 'path/to/folder/cafe/stage.jpg', 'preview' => 'path/to/folder/cafe/stage.jpg', 'status' => 1, 'mimetype' => 'image/jpeg' ),
		'media/path/to/supplier.jpg' => array( 'langid' => null, 'type' => 'default', 'domain' => 'supplier', 'label' => 'path/to/supplier.jpg', 'link' => 'path/to/supplier.jpg', 'preview' => 'path/to/supplier.jpg', 'status' => 1, 'mimetype' => 'image/jpeg' ),
	),


);