<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */

return array (
	'attribute/property/type' => array(
		'attribute/property/type/size' => array( 'domain' => 'attribute', 'code' => 'size', 'label' => 'Size', 'position' => 0, 'status' => 1 ),
		'attribute/property/type/mtime' => array( 'domain' => 'attribute', 'code' => 'mtime', 'label' => 'Modification time', 'position' => 1, 'status' => 1 ),
		'attribute/property/type/htmlcolor' => array( 'domain' => 'attribute', 'code' => 'htmlcolor', 'label' => 'HTML color code', 'position' => 2, 'status' => 1 ),
	),

	'attribute/property' => array(
		'attribute/product/download/testurl/size' => array( 'parentid' => 'attribute/product/download/testurl', 'typeid' => 'attribute/property/type/size', 'langid' => null, 'value' => '1024' ),
		'attribute/product/download/testurl/mtime' => array( 'parentid' => 'attribute/product/download/testurl', 'typeid' => 'attribute/property/type/mtime', 'langid' => null, 'value' => '2000-01-01 00:00:00' ),
		'attribute/product/color/black/htmlcolor' => array( 'parentid' => 'attribute/product/color/black', 'typeid' => 'attribute/property/type/htmlcolor', 'langid' => 'de', 'value' => '#000000' ),
	),
);