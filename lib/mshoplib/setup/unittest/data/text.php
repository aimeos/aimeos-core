<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */

return array(
	'text/type' => array(
		'attribute/name' => array( 'domain' => 'attribute', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'attribute/short' => array( 'domain' => 'attribute', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'attribute/long' => array( 'domain' => 'attribute', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		'attribute/url' => array( 'domain' => 'attribute', 'code' => 'url', 'label' => 'URL segment', 'status' => 1 ),
		'catalog/name' => array( 'domain' => 'catalog', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'catalog/short' => array( 'domain' => 'catalog', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'catalog/long' => array( 'domain' => 'catalog', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		'catalog/url' => array( 'domain' => 'catalog', 'code' => 'url', 'label' => 'URL segment', 'status' => 1 ),
		'catalog/metatitle' => array( 'domain' => 'catalog', 'code' => 'metatitle', 'label' => 'Meta title', 'status' => 1 ),
		'catalog/meta-keyword' => array( 'domain' => 'catalog', 'code' => 'meta-keyword', 'label' => 'Meta keywords', 'status' => 1 ),
		'catalog/meta-description' => array( 'domain' => 'catalog', 'code' => 'meta-description', 'label' => 'Meta description', 'status' => 1 ),
		'catalog/deliveryinformation' => array( 'domain' => 'catalog', 'code' => 'deliveryinformation', 'label' => 'Delivery information', 'status' => 1 ),
		'catalog/paymentinformation' => array( 'domain' => 'catalog', 'code' => 'paymentinformation', 'label' => 'Payment information', 'status' => 1 ),
		'catalog/quote' => array( 'domain' => 'catalog', 'code' => 'quote', 'label' => 'Quote', 'status' => 1 ),
		'text/name' => array( 'domain' => 'text', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'media/name' => array( 'domain' => 'media', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'media/short' => array( 'domain' => 'media', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'media/long' => array( 'domain' => 'media', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		'media/img-description' => array( 'domain' => 'media', 'code' => 'img-description', 'label' => 'Image description', 'status' => 0 ),
		'service/name' => array( 'domain' => 'service', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'service/short' => array( 'domain' => 'service', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'service/long' => array( 'domain' => 'service', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		'product/name' => array( 'domain' => 'product', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'product/short' => array( 'domain' => 'product', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'product/long' => array( 'domain' => 'product', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		'product/url' => array( 'domain' => 'product', 'code' => 'url', 'label' => 'URL segment', 'status' => 1 ),
		'product/basket' => array( 'domain' => 'product', 'code' => 'basket', 'label' => 'basket description', 'status' => 1 ),
		'product/metatitle' => array( 'domain' => 'product', 'code' => 'metatitle', 'label' => 'Meta title', 'status' => 1 ),
		'product/meta-keyword' => array( 'domain' => 'product', 'code' => 'meta-keyword', 'label' => 'Meta keywords', 'status' => 1 ),
		'product/meta-description' => array( 'domain' => 'product', 'code' => 'meta-description', 'label' => 'Meta description', 'status' => 1 ),
		'customer/information' => array( 'domain' => 'customer', 'code' => 'information', 'label' => 'Customer information', 'status' => 1 ),
		'customer/notify' => array( 'domain' => 'customer', 'code' => 'notify', 'label' => 'Customer notify', 'status' => 1 ),
		'customer/newsletter' => array( 'domain' => 'customer', 'code' => 'newsletter', 'label' => 'Customer newsletter', 'status' => 1 ),
		'service/serviceinformation' => array( 'domain' => 'service', 'code' => 'serviceinformation', 'label' => 'Service information', 'status' => 1 ),
		'supplier/description' => array( 'domain' => 'supplier', 'code' => 'description', 'label' => 'Supplier description', 'status' => 1 ),
		'supplier/name' => array( 'domain' => 'supplier', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'supplier/short' => array( 'domain' => 'supplier', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'supplier/long' => array( 'domain' => 'supplier', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
	),

	'text' => array(
//MEDIA
		'text/media' => array( 'langid' => 'de', 'type' => 'name', 'domain' => 'media', 'label' => 'media', 'content' => 'Media Name', 'status' => 1 ),
// service text
		'text/service' => array( 'langid' => 'de', 'type' => 'name', 'domain' => 'service', 'label' => 'service', 'content' => 'Service Name', 'status' => 1 ),
//dummy default
		'text/text' => array( 'langid' => 'de', 'type' => 'name', 'domain' => 'text', 'label' => 'text', 'content' => 'Text Name', 'status' => 1 ),
	),
);
