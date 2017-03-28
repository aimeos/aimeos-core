<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'catalog' => array(
		//0 level
		'catalog/root' => array( 'label' => 'Root', 'config' => array( 'css-class' => 'home' ), 'code' => 'root', 'status' => 1, 'parent' => 'init' ),
		//first level
		'catalog/categories' => array( 'label' => 'Categories', 'config' => array( 'css-class' => 'categories' ), 'code' => 'categories', 'status' => 1, 'parent' => 'catalog/root' ),
		'catalog/group' => array( 'label' => 'Groups', 'config' => [], 'code' => 'group', 'status' => 1, 'parent' => 'catalog/root' ),
		//categories
		'catalog/cafe' => array( 'label' => 'Kaffee', 'config' => array( 'css-class' => 'coffee' ), 'code' => 'cafe', 'status' => 1, 'parent' => 'catalog/categories' ),
		'catalog/tea' => array( 'label' => 'Tee', 'config' => [], 'code' => 'tea', 'status' => 1, 'parent' => 'catalog/categories' ),
		'catalog/misc' => array( 'label' => 'Misc', 'config' => [], 'code' => 'misc', 'status' => 1, 'parent' => 'catalog/categories' ),
		//group
		'catalog/new' => array( 'label' => 'Neu', 'config' => [], 'code' => 'new', 'status' => 1, 'parent' => 'catalog/group' ),
		'catalog/internet' => array( 'label' => 'Internet', 'config' => [], 'code' => 'internet', 'status' => 1, 'parent' => 'catalog/group' ),
	),
);