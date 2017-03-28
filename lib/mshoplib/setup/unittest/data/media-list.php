<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'media/lists/type' => array(
		'product/default' => array( 'domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'attribute/default' => array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'catalog/default' => array( 'domain' => 'catalog', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'media/default' => array( 'domain' => 'media', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'price/default' => array( 'domain' => 'price', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'service/default' => array( 'domain' => 'service', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'text/default' => array( 'domain' => 'text', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),

		'attribute/option' => array( 'domain' => 'attribute', 'code' => 'option', 'label' => 'Option', 'status' => 1 ),
		'attribute/front' => array( 'domain' => 'attribute', 'code' => 'front', 'label' => 'Frontside', 'status' => 1 ),
		'attribute/back' => array( 'domain' => 'attribute', 'code' => 'back', 'label' => 'Backside', 'status' => 1 ),
	),

	'media/lists' => array(
		array( 'parentid' => 'media/prod_266x221/198_prod_266x221.jpg', 'typeid' => 'attribute/option', 'domain' => 'attribute', 'refid' => 'attribute/media/color/olive', 'start' => null, 'end' => null, 'config' => [], 'pos' => 1, 'status' => 1 ),
		array( 'parentid' => 'media/prod_266x221/198_prod_266x221.jpg', 'typeid' => 'attribute/option', 'domain' => 'attribute', 'refid' => 'attribute/media/color/blue', 'start' => null, 'end' => null, 'config' => [], 'pos' => 2, 'status' => 1 ),
		array( 'parentid' => 'media/prod_266x221/198_prod_266x221.jpg', 'typeid' => 'attribute/option', 'domain' => 'attribute', 'refid' => 'attribute/media/color/red', 'start' => null, 'end' => null, 'config' => [], 'pos' => 3, 'status' => 1 ),
		array( 'parentid' => 'media/prod_114x95/194_prod_114x95.jpg', 'typeid' => 'attribute/option', 'domain' => 'attribute', 'refid' => 'attribute/media/color/blue', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'media/prod_179x178/196_prod_179x178.jpg', 'typeid' => 'attribute/option', 'domain' => 'attribute', 'refid' => 'attribute/media/color/red', 'start' => '2002-01-01 00:00:00', 'end' => '2006-12-31 23:59:59', 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'media/prod_266x221/198_prod_266x221.jpg', 'typeid' => 'attribute/option', 'domain' => 'text', 'refid' => 'text/img_desc', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'media/path/to/folder/example5.jpg', 'typeid' => 'attribute/default', 'domain' => 'attribute', 'refid' => 'attribute/media/color/white', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'media/path/to/folder/example6.jpg', 'typeid' => 'attribute/default', 'domain' => 'attribute', 'refid' => 'attribute/media/color/blue', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
	)
);