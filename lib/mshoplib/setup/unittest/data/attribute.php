<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

return array(
	'attribute/type' => array(
		'product/color' => array( 'domain' => 'product', 'code' => 'color', 'label' => 'Color', 'status' => 1 ),
		'product/size' => array( 'domain' => 'product', 'code' => 'size', 'label' => 'Size', 'status' => 1 ),
		'product/width' => array( 'domain' => 'product', 'code' => 'width', 'label' => 'Width', 'status' => 1 ),
		'product/length' => array( 'domain' => 'product', 'code' => 'length', 'label' => 'Length', 'status' => 1 ),
		'product/download' => array( 'domain' => 'product', 'code' => 'download', 'label' => 'Download', 'status' => 1 ),
		'product/date' => array( 'domain' => 'product', 'code' => 'date', 'label' => 'Date', 'status' => 1 ),
		'product/price' => array( 'domain' => 'product', 'code' => 'price', 'label' => 'Price', 'status' => 1 ),
		'product/interval' => array( 'domain' => 'product', 'code' => 'interval', 'label' => 'Interval', 'status' => 1 ),
		'media/color' => array( 'domain' => 'media', 'code' => 'color', 'label' => 'Color', 'status' => 1 ),
	),

	'attribute' => array(
		//size
		'attribute/product/size/xs' => array( 'domain' => 'product', 'typeid' => 'product/size', 'code' => 'xs', 'label' => 'product/size/xs', 'status' => 1, 'pos' => 0 ),
		'attribute/product/size/s' => array( 'domain' => 'product', 'typeid' => 'product/size', 'code' => 's', 'label' => 'product/size/s', 'status' => 1, 'pos' => 1 ),
		'attribute/product/size/m' => array( 'domain' => 'product', 'typeid' => 'product/size', 'code' => 'm', 'label' => 'product/size/m', 'status' => 1, 'pos' => 2 ),
		'attribute/product/size/l' => array( 'domain' => 'product', 'typeid' => 'product/size', 'code' => 'l', 'label' => 'product/size/l', 'status' => 1, 'pos' => 3 ),
		'attribute/product/size/xl' => array( 'domain' => 'product', 'typeid' => 'product/size', 'code' => 'xl', 'label' => 'product/size/xl', 'status' => 1, 'pos' => 4 ),
		'attribute/product/size/xxl' => array( 'domain' => 'product', 'typeid' => 'product/size', 'code' => 'xxl', 'label' => 'product/size/xxl', 'status' => 1, 'pos' => 5 ),
		//length
		'attribute/product/length/30' => array( 'domain' => 'product', 'typeid' => 'product/length', 'code' => '30', 'label' => 'product/length/30', 'status' => 1, 'pos' => 0 ),
		'attribute/product/length/32' => array( 'domain' => 'product', 'typeid' => 'product/length', 'code' => '32', 'label' => 'product/length/32', 'status' => 1, 'pos' => 1 ),
		'attribute/product/length/34' => array( 'domain' => 'product', 'typeid' => 'product/length', 'code' => '34', 'label' => 'product/length/34', 'status' => 1, 'pos' => 2 ),
		'attribute/product/length/36' => array( 'domain' => 'product', 'typeid' => 'product/length', 'code' => '36', 'label' => 'product/length/36', 'status' => 1, 'pos' => 3 ),
		'attribute/product/length/38' => array( 'domain' => 'product', 'typeid' => 'product/length', 'code' => '38', 'label' => 'product/length/38', 'status' => 1, 'pos' => 3 ),
		//width
		'attribute/product/width/29' => array( 'domain' => 'product', 'typeid' => 'product/width', 'code' => '29', 'label' => 'product/width/29', 'status' => 1, 'pos' => 0 ),
		'attribute/product/width/30' => array( 'domain' => 'product', 'typeid' => 'product/width', 'code' => '30', 'label' => 'product/width/30', 'status' => 1, 'pos' => 1 ),
		'attribute/product/width/32' => array( 'domain' => 'product', 'typeid' => 'product/width', 'code' => '32', 'label' => 'product/width/32', 'status' => 1, 'pos' => 2 ),
		'attribute/product/width/33' => array( 'domain' => 'product', 'typeid' => 'product/width', 'code' => '33', 'label' => 'product/width/33', 'status' => 1, 'pos' => 3 ),
		'attribute/product/width/34' => array( 'domain' => 'product', 'typeid' => 'product/width', 'code' => '34', 'label' => 'product/width/34', 'status' => 1, 'pos' => 4 ),
		'attribute/product/width/36' => array( 'domain' => 'product', 'typeid' => 'product/width', 'code' => '36', 'label' => 'product/width/36', 'status' => 1, 'pos' => 5 ),
		//color
		'attribute/product/color/white' => array( 'domain' => 'product', 'typeid' => 'product/color', 'code' => 'white', 'label' => 'product/color/white', 'status' => 1, 'pos' => 0 ),
		'attribute/product/color/gray' => array( 'domain' => 'product', 'typeid' => 'product/color', 'code' => 'gray', 'label' => 'product/color/gray', 'status' => 1, 'pos' => 1 ),
		'attribute/product/color/olive' => array( 'domain' => 'product', 'typeid' => 'product/color', 'code' => 'olive', 'label' => 'product/color/olive', 'status' => 1, 'pos' => 2 ),
		'attribute/product/color/blue' => array( 'domain' => 'product', 'typeid' => 'product/color', 'code' => 'blue', 'label' => 'product/color/blue', 'status' => 1, 'pos' => 3 ),
		'attribute/product/color/red' => array( 'domain' => 'product', 'typeid' => 'product/color', 'code' => 'red', 'label' => 'product/color/red', 'status' => 1, 'pos' => 4 ),
		'attribute/product/color/black' => array( 'domain' => 'product', 'typeid' => 'product/color', 'code' => 'black', 'label' => 'product/color/black', 'status' => 0, 'pos' => 5 ),
		'attribute/product/color/pink' => array( 'domain' => 'product', 'typeid' => 'product/color', 'code' => 'pink', 'label' => 'product/color/pink', 'status' => 0, 'pos' => 6 ),
		'attribute/media/color/white' => array( 'domain' => 'media', 'typeid' => 'media/color', 'code' => 'white', 'label' => 'media/color/white', 'status' => 1, 'pos' => 0 ),
		'attribute/media/color/gray' => array( 'domain' => 'media', 'typeid' => 'media/color', 'code' => 'gray', 'label' => 'media/color/gray', 'status' => 1, 'pos' => 1 ),
		'attribute/media/color/olive' => array( 'domain' => 'media', 'typeid' => 'media/color', 'code' => 'olive', 'label' => 'media/color/olive', 'status' => 1, 'pos' => 2 ),
		'attribute/media/color/blue' => array( 'domain' => 'media', 'typeid' => 'media/color', 'code' => 'blue', 'label' => 'media/color/blue', 'status' => 1, 'pos' => 3 ),
		'attribute/media/color/red' => array( 'domain' => 'media', 'typeid' => 'media/color', 'code' => 'red', 'label' => 'media/color/red', 'status' => 1, 'pos' => 4 ),
		'attribute/media/color/black' => array( 'domain' => 'media', 'typeid' => 'media/color', 'code' => 'black', 'label' => 'media/color/black', 'status' => 0, 'pos' => 5 ),
		'attribute/media/color/pink' => array( 'domain' => 'media', 'typeid' => 'media/color', 'code' => 'pink', 'label' => 'media/color/pink', 'status' => 0, 'pos' => 6 ),

		'attribute/product/interval/P1Y0M0W0D' => array( 'domain' => 'product', 'typeid' => 'product/interval', 'code' => 'P1Y0M0W0D', 'label' => 'product/interval/P1Y0M0W0D', 'status' => 1, 'pos' => 0 ),
		'attribute/product/date/custom' => array( 'domain' => 'product', 'typeid' => 'product/date', 'code' => 'custom', 'label' => 'product/date/custom', 'status' => 1, 'pos' => 0 ),
		'attribute/product/price/custom' => array( 'domain' => 'product', 'typeid' => 'product/price', 'code' => 'custom', 'label' => 'product/price/custom', 'status' => 1, 'pos' => 1 ),
		'attribute/product/download/testurl' => array( 'domain' => 'product', 'typeid' => 'product/download', 'code' => 'testurl', 'label' => 'product/download/testurl', 'status' => 1, 'pos' => 0 ),
	),
);