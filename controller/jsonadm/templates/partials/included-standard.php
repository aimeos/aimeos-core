<?php

$options = 0;
if( defined( 'JSON_PRETTY_PRINT' ) ) {
	$options = JSON_PRETTY_PRINT;
}


$build = function( array $items, array $fields )
{
	$list = array();

	$target = $this->config( 'controller/jsonadm/url/target' );
	$cntl = $this->config( 'controller/jsonadm/url/controller', 'jsonadm' );
	$action = $this->config( 'controller/jsonadm/url/action', 'get' );
	$config = $this->config( 'controller/jsonadm/url/config', array() );

	foreach( (array) $items as $item )
	{
		$id = $item->getId();
		$attributes = $item->toArray();
		$type = $item->getResourceType();

		if( isset( $fields[$type] ) ) {
			$attributes = array_intersect_key( $attributes, $fields[$type] );
		}

		$list[] = array(
			'id' => $id,
			'type' => $type,
			'attributes' => $attributes,
			'links' => array(
				'self' => $this->url( $target, $cntl, $action, array( 'resource' => $type, 'id' => $id ), array(), $config ),
				'related' => array(
					'href' => $this->url( $target, $cntl, $action, array( 'resource' => $type, 'id' => null ), array(), $config )
				)
			)
		);
	}

	return $list;
};


$response = array();
$fields = $this->param( 'fields', array() );

foreach( (array) $fields as $resource => $list ) {
	$fields[$resource] = array_flip( explode( ',', $list ) );
}

$response = $build( $this->get( 'childItems', array() ), $fields );
$response = array_merge( $response, $build( $this->get( 'refItems', array() ), $fields ) );


echo json_encode( $response, $options );