<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: catalog.php 1316 2012-10-19 19:49:23Z nsendetzky $
 */

return array (
	'catalog' => array (
		//0 level
		'catalog/root' => array( 'label' => 'Root', 'code' => 'root', 'status' => 1, 'parent' => 'init' ),
		//first level
		'catalog/categories' => array( 'level' => 1, 'label' => 'Categories', 'code' => 'categories', 'status' => 1, 'parent' => 'catalog/root' ),
		'catalog/group' => array( 'level' => 1, 'label' => 'Groups', 'code' => 'group', 'status' => 1, 'parent' => 'catalog/root' ),
		//categories
		'catalog/cafe' => array( 'level' => 2, 'label' => 'Kaffee', 'code' => 'cafe', 'status' => 1, 'parent' => 'catalog/categories' ),
		'catalog/tea' => array( 'level' => 2, 'label' => 'Tee', 'code' => 'tea', 'status' => 1, 'parent' => 'catalog/categories' ),
		'catalog/misc' => array( 'level' => 2, 'label' => 'Misc', 'code' => 'misc', 'status' => 1, 'parent' => 'catalog/categories' ),
		//group
		'catalog/new' => array( 'level' => 2, 'label' => 'Neu', 'code' => 'new', 'status' => 1, 'parent' => 'catalog/group' ),
		'catalog/internet' => array( 'level' => 2, 'label' => 'Internet', 'code' => 'internet', 'status' => 1, 'parent' => 'catalog/group' ),
	),
);