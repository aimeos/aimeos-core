<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2015
 */

return array (
	'product/property/type' => array(
		'product/property/type/packheight' => array( 'domain' => 'product', 'code' => 'package-height', 'label' => 'Package height', 'status' => 1 ),
		'product/property/type/packlength' => array( 'domain' => 'product', 'code' => 'package-length', 'label' => 'Package lenght', 'status' => 1 ),
		'product/property/type/packwidth' => array( 'domain' => 'product', 'code' => 'package-width', 'label' => 'Package width', 'status' => 1 ),
		'product/property/type/packweight' => array( 'domain' => 'product', 'code' => 'package-weight', 'label' => 'Package Weight', 'status' => 1 ),
	),

	'product/property' => array(
		'product/property/CNC/height' => array( 'parentid' => 'product/CNC', 'typeid' => 'product/property/type/packheight', 'langid' => null, 'value' => '10.0' ),
		'product/property/CNC/length' => array( 'parentid' => 'product/CNC', 'typeid' => 'product/property/type/packlength', 'langid' => null, 'value' => '20.0' ),
		'product/property/CNC/width' => array( 'parentid' => 'product/CNC', 'typeid' => 'product/property/type/packwidth', 'langid' => null, 'value' => '15.0' ),
		'product/property/CNC/weight' => array( 'parentid' => 'product/CNC', 'typeid' => 'product/property/type/packweight', 'langid' => null, 'value' => '1.25' ),
		'product/property/CNE/height' => array( 'parentid' => 'product/CNE', 'typeid' => 'product/property/type/packheight', 'langid' => null, 'value' => '10.0' ),
		'product/property/CNE/length' => array( 'parentid' => 'product/CNE', 'typeid' => 'product/property/type/packlength', 'langid' => null, 'value' => '25.00' ),
		'product/property/CNE/width' => array( 'parentid' => 'product/CNE', 'typeid' => 'product/property/type/packwidth', 'langid' => null, 'value' => '17.5' ),
		'product/property/CNE/weight' => array( 'parentid' => 'product/CNE', 'typeid' => 'product/property/type/packweight', 'langid' => null, 'value' => '1' ),
	),
);