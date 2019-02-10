<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

return array(
	'attribute/lists/type' => array(
		'attribute/default' => array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'catalog/default' => array( 'domain' => 'catalog', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'media/default' => array( 'domain' => 'media', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'price/default' => array( 'domain' => 'price', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'product/default' => array( 'domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'service/default' => array( 'domain' => 'service', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'text/default' => array( 'domain' => 'text', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'attribute/lists' => array(
		array( 'parentid' => 'product/size/xs', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/size/XS', 'start' => null, 'end' => null, 'config' => [], 'pos' => 1, 'status' => 1 ),
		array( 'parentid' => 'product/size/s', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/size/S', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/size/m', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/size/M', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/size/l', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/size/L', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/size/xl', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/size/XL', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/size/xxl', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/size/XXL', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),

		array( 'parentid' => 'product/length/30', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/lenth/30', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/length/32', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/lenth/32', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/length/34', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/lenth/34', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/length/36', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/lenth/36', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/length/38', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/lenth/38', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),

		array( 'parentid' => 'product/width/29', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/width/29', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/width/30', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/width/30', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/width/32', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/width/32', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/width/33', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/width/33', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/width/34', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/width/34', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/width/36', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/width/36', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),

		array( 'parentid' => 'product/color/white', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/color/white', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/color/gray', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/color/gray', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/color/olive', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/color/olive', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/color/blue', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/color/blue', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/color/red', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/color/red', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/color/black', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/color/black', 'start' => '2000-01-01 00:00:00', 'end' => '2001-01-01 00:00:00', 'config' => [], 'pos' => 0, 'status' => 1 ),

		array( 'parentid' => 'product/size/xs', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/small_items', 'start' => null, 'end' => null, 'config' => [], 'pos' => 2, 'status' => 1 ),
		array( 'parentid' => 'product/size/xs', 'type' => 'default', 'domain' => 'text', 'refid' => 'text/small_size', 'start' => null, 'end' => null, 'config' => [], 'pos' => 3, 'status' => 1 ),
		array( 'parentid' => 'product/size/xs', 'type' => 'default', 'domain' => 'media', 'refid' => 'media/prod_97x93/199_prod_97x93.jpg', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/size/xs', 'type' => 'default', 'domain' => 'price', 'refid' => 'price/attribute/default/12.95/1.99', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'product/size/xl', 'type' => 'default', 'domain' => 'price', 'refid' => 'price/attribute/default/15.00/1.00', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
	)

);
