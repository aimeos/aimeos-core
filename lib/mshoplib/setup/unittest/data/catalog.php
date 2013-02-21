<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array (
	'catalog' => array (
		//0 level
		'catalog/root' => array( 'label' => 'Root', 'config' => array( "listlimit" => "30", "sizelimit" => "10" ), 'code' => 'root', 'status' => 1, 'parent' => 'init' ),
		//first level
		'catalog/categories' => array( 'label' => 'Categories', 'config' => array( "listlimit" => "20", "sizelimit" => "10" ), 'code' => 'categories', 'status' => 1, 'parent' => 'catalog/root' ),
		'catalog/group' => array( 'label' => 'Groups', 'config' => array( "listlimit" => "20", "sizelimit" => "10" ), 'code' => 'group', 'status' => 1, 'parent' => 'catalog/root' ),
		//categories
		'catalog/cafe' => array( 'label' => 'Kaffee', 'config' => array( "listlimit" => "10", "sizelimit" => "10" ), 'code' => 'cafe', 'status' => 1, 'parent' => 'catalog/categories' ),
		'catalog/tea' => array( 'label' => 'Tee', 'config' => array( "listlimit" => "10", "sizelimit" => "10" ), 'code' => 'tea', 'status' => 1, 'parent' => 'catalog/categories' ),
		'catalog/misc' => array( 'label' => 'Misc', 'config' => array( "listlimit" => "10", "sizelimit" => "10" ), 'code' => 'misc', 'status' => 1, 'parent' => 'catalog/categories' ),
		//group
		'catalog/new' => array( 'label' => 'Neu', 'config' => array( "listlimit" => "10", "sizelimit" => "10" ), 'code' => 'new', 'status' => 1, 'parent' => 'catalog/group' ),
		'catalog/internet' => array( 'label' => 'Internet', 'config' => array( "listlimit" => "10", "sizelimit" => "10" ), 'code' => 'internet', 'status' => 1, 'parent' => 'catalog/group' ),
	),
);