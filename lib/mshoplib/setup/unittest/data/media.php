<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'media/type' => array(
		'catalog/prod_123x103' => array( 'domain' => 'catalog', 'code' => 'prod_123x103', 'label' => 'Product 123x103', 'status' => 1 ),
		'product/prod_266x221' => array( 'domain' => 'product', 'code' => 'prod_266x221', 'label' => 'Product 266x221', 'status' => 1 ),
		'product/prod_114x95' => array( 'domain' => 'product', 'code' => 'prod_114x95', 'label' => 'Product 114x95', 'status' => 1 ),
		'product/prod_179x178' => array( 'domain' => 'product', 'code' => 'prod_179x178', 'label' => 'Product 179x178', 'status' => 1 ),
		'attribute/prod_242x416' => array( 'domain' => 'attribute', 'code' => 'prod_242x416', 'label' => 'Product 242x416', 'status' => 1 ),
		'attribute/prod_97x93' => array( 'domain' => 'attribute', 'code' => 'prod_97x93', 'label' => 'Product 97x93', 'status' => 1 ),
		'product/download' => array( 'domain' => 'product', 'code' => 'download', 'label' => '', 'status' => null ),
		'catalog/stage' => array( 'domain' => 'catalog', 'code' => 'stage', 'label' => 'Stage', 'status' => 1 ),

		'product/default' => array( 'domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'attribute/default' => array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'catalog/default' => array( 'domain' => 'catalog', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'media/default' => array( 'domain' => 'media', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'price/default' => array( 'domain' => 'price', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'service/default' => array( 'domain' => 'service', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'text/default' => array( 'domain' => 'text', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'media' => array(
		'media/prod_97x93/199_prod_97x93.jpg' => array( 'langid' => 'de', 'typeid' => 'attribute/prod_97x93', 'domain' => 'attribute', 'label' => 'cn_colombie_97x93', 'link' => 'prod_97x93/199_prod_97x93.jpg', 'preview' => 'prod_97x93/199_prod_97x93.jpg', 'status' => 1, 'mimetype' => null ),
		'media/prod_123x103/195_prod_123x103.jpg' => array( 'langid' => 'de', 'typeid' => 'catalog/prod_123x103', 'domain' => 'catalog', 'label' => 'cn_colombie_123x103', 'link' => 'prod_123x103/195_prod_123x103.jpg', 'preview' => 'prod_123x103/195_prod_123x103.jpg', 'status' => 1, 'mimetype' => null ),
		'media/prod_266x221/198_prod_266x221.jpg' => array( 'langid' => 'de', 'typeid' => 'product/prod_266x221', 'domain' => 'product', 'label' => 'cn_colombie_266x221', 'link' => 'prod_266x221/198_prod_266x221.jpg', 'preview' => 'prod_266x221/198_prod_266x221.jpg', 'status' => 1, 'mimetype' => null ),
		'media/prod_114x95/194_prod_114x95.jpg' => array( 'langid' => 'de', 'typeid' => 'product/prod_114x95', 'domain' => 'product', 'label' => 'cn_colombie_114x95', 'link' => 'prod_114x95/194_prod_114x95.jpg', 'preview' => 'prod_114x95/194_prod_114x95.jpg', 'status' => 1, 'mimetype' => null ),
		'media/prod_179x178/196_prod_179x178.jpg' => array( 'langid' => 'de', 'typeid' => 'product/prod_179x178', 'domain' => 'product', 'label' => 'cn_colombie_179x178', 'link' => 'prod_179x178/196_prod_179x178.jpg', 'preview' => 'prod_179x178/196_prod_179x178.jpg', 'status' => 1, 'mimetype' => null ),
		'media/prod_242x416/197_CafeNoire_Colombia_242x416.jpg' => array( 'langid' => 'de', 'typeid' => 'attribute/prod_242x416', 'domain' => 'attribute', 'label' => 'cn_colombie_242x416', 'link' => 'prod_242x416/197_CafeNoire_Colombia_242x416.jpg', 'preview' => 'prod_242x416/197_CafeNoire_Colombia_242x416.jpg', 'status' => 1, 'mimetype' => null ),
		'media/path/to/folder/example1.jpg' => array( 'langid' => 'de', 'typeid' => 'catalog/prod_123x103', 'domain' => 'catalog', 'label' => 'example image 1', 'link' => 'path/to/folder/example1.jpg', 'preview' => 'path/to/folder/example1.jpg', 'status' => 0, 'mimetype' => 'image/jpeg' ),
		'media/path/to/folder/example2.jpg' => array( 'langid' => 'de', 'typeid' => 'catalog/prod_123x103', 'domain' => 'catalog', 'label' => 'example image 2', 'link' => 'path/to/folder/example2.jpg', 'preview' => 'path/to/folder/example2.jpg', 'status' => 0, 'mimetype' => 'image/jpeg' ),
		'media/path/to/folder/example3.jpg' => array( 'langid' => 'de', 'typeid' => 'catalog/prod_123x103', 'domain' => 'catalog', 'label' => 'example image 3', 'link' => 'path/to/folder/example3.jpg', 'preview' => 'path/to/folder/example3.jpg', 'status' => 0, 'mimetype' => 'image/jpeg' ),
		'media/path/to/folder/example4.jpg' => array( 'langid' => 'de', 'typeid' => 'catalog/default', 'domain' => 'catalog', 'label' => 'example image 4', 'link' => 'path/to/folder/example4.jpg', 'preview' => 'path/to/folder/example4.jpg', 'preview' => 'example4.jpg', 'status' => 1, 'mimetype' => 'image/jpeg' ),
		'media/path/to/folder/example5.jpg' => array( 'langid' => 'de', 'typeid' => 'product/download', 'domain' => 'product', 'label' => 'example image 1', 'link' => 'path/to/folder/example5.jpg', 'preview' => 'path/to/folder/example5.jpg', 'status' => 1, 'mimetype' => 'image/jpeg' ),
		'media/path/to/folder/example6.jpg' => array( 'langid' => 'de', 'typeid' => 'product/download', 'domain' => 'product', 'label' => 'example image 2', 'link' => 'path/to/folder/example6.jpg', 'preview' => 'path/to/folder/example6.jpg', 'status' => 1, 'mimetype' => 'image/jpeg' ),
		'media/service_image1' => array( 'langid' => null, 'typeid' => 'service/default', 'domain' => 'service', 'label' => 'service_image1', 'link' => 'path/to/service.png', 'preview' => 'path/to/service.png', 'status' => 1, 'mimetype' => 'image/png' ),
		'media/path/to/folder/cafe/stage.jpg' => array( 'langid' => 'de', 'typeid' => 'catalog/default', 'domain' => 'catalog', 'label' => 'Cafe Stage image', 'link' => 'path/to/folder/cafe/stage.jpg', 'preview' => 'path/to/folder/cafe/stage.jpg', 'status' => 1, 'mimetype' => 'image/jpeg' ),
	),


);