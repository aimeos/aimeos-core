<?php

$options = 0;
if( defined( 'JSON_PRETTY_PRINT' ) ) {
	$options = JSON_PRETTY_PRINT;
}


$build = function( \Aimeos\MW\View\Iface $view, array $items, array $fields )
{
	$list = array();

	$target = $view->config( 'admin/jsonadm/url/target' );
	$cntl = $view->config( 'admin/jsonadm/url/controller', 'jsonadm' );
	$action = $view->config( 'admin/jsonadm/url/action', 'get' );
	$config = $view->config( 'admin/jsonadm/url/config', array() );

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
				'self' => $view->url( $target, $cntl, $action, array( 'resource' => $type, 'id' => $id ), array(), $config ),
				'related' => array(
					'href' => $view->url( $target, $cntl, $action, array( 'resource' => $type, 'id' => null ), array(), $config )
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

$response = $build( $this, $this->get( 'childItems', array() ), $fields );
$response = array_merge( $response, $build( $this, $this->get( 'refItems', array() ), $fields ) );


echo json_encode( $response, $options );