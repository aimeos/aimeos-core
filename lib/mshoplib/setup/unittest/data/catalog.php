<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

return array(
	'catalog' => array(
		//0 level
		'catalog/root' => array( 'label' => 'Root', 'config' => array( 'css-class' => 'home' ), 'code' => 'root', 'status' => 1, 'parent' => 'init' ),
		//first level
		'catalog/categories' => array( 'label' => 'Categories', 'config' => array( 'css-class' => 'categories' ), 'code' => 'categories', 'status' => 1, 'parent' => 'catalog/root' ),
		'catalog/group' => array( 'label' => 'Groups', 'config' => array(), 'code' => 'group', 'status' => 1, 'parent' => 'catalog/root' ),
		//categories
		'catalog/cafe' => array( 'label' => 'Kaffee', 'config' => array( 'css-class' => 'coffee' ), 'code' => 'cafe', 'status' => 1, 'parent' => 'catalog/categories' ),
		'catalog/tea' => array( 'label' => 'Tee', 'config' => array(), 'code' => 'tea', 'status' => 1, 'parent' => 'catalog/categories' ),
		'catalog/misc' => array( 'label' => 'Misc', 'config' => array(), 'code' => 'misc', 'status' => 1, 'parent' => 'catalog/categories' ),
		//group
		'catalog/new' => array( 'label' => 'Neu', 'config' => array(), 'code' => 'new', 'status' => 1, 'parent' => 'catalog/group' ),
		'catalog/internet' => array( 'label' => 'Internet', 'config' => array(), 'code' => 'internet', 'status' => 1, 'parent' => 'catalog/group' ),
	),
);