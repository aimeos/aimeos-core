<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array (
	'product/tag/type' => array(
		'product/tag/sort' => array( 'domain' => 'product/tag', 'code' => 'sort', 'label' => 'Brand', 'status' => 1 ),
		'product/tag/taste' => array( 'domain' => 'product/tag', 'code' => 'taste', 'label' => 'Taste', 'status' => 1 ),
	),

	'product/tag' => array(
		'product/tag/Expresso' => array( 'typeid' => 'product/tag/sort', 'langid' => 'de', 'label' => 'Expresso' ),
		'product/tag/Kaffee' => array( 'typeid' => 'product/tag/sort', 'langid' => 'de', 'label' => 'Kaffee' ),
		'product/tag/Cappuccino' => array( 'typeid' => 'product/tag/sort', 'langid' => 'de', 'label' => 'Cappuccino' ),
		'product/tag/herb' => array( 'typeid' => 'product/tag/taste', 'langid' => 'de', 'label' => 'herb' ),
		'product/tag/mild' => array( 'typeid' => 'product/tag/taste', 'langid' => 'de', 'label' => 'mild' ),
		'product/tag/cremig' => array( 'typeid' => 'product/tag/taste', 'langid' => 'de', 'label' => 'cremig' ),
	)
);