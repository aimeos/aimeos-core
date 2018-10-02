<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */

return array (
	'media/property/type' => array(
		'media/property/type/size' => array( 'domain' => 'media', 'code' => 'size', 'label' => 'Size', 'status' => 1 ),
		'media/property/type/mtime' => array( 'domain' => 'media', 'code' => 'mtime', 'label' => 'Modification time', 'status' => 1 ),
		'media/property/type/copyright' => array( 'domain' => 'media', 'code' => 'copyright', 'label' => 'HTML color code', 'status' => 1 ),
	),

	'media/property' => array(
		'media/path/to/folder/example1.jpg/size' => array( 'parentid' => 'media/path/to/folder/example1.jpg', 'typeid' => 'media/property/type/size', 'langid' => null, 'value' => '1024' ),
		'media/path/to/folder/example1.jpg/mtime' => array( 'parentid' => 'media/path/to/folder/example1.jpg', 'typeid' => 'media/property/type/mtime', 'langid' => null, 'value' => '2000-01-01 00:00:00' ),
		'media/path/to/folder/example2.jpg/copyright' => array( 'parentid' => 'media/path/to/folder/example2.jpg', 'typeid' => 'media/property/type/copyright', 'langid' => 'de', 'value' => 'ich, 2017' ),
	),
);