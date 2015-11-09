<?php

$options = 0;
if( defined( 'JSON_PRETTY_PRINT' ) ) {
	$options = JSON_PRETTY_PRINT;
}


$target = $this->config( 'controller/jsonapi/url/target' );
$cntl = $this->config( 'controller/jsonapi/url/controller', 'jsonapi' );
$action = $this->config( 'controller/jsonapi/url/action', 'get' );
$config = $this->config( 'controller/jsonapi/url/config', array() );


$response = array();
$data = $this->get( 'data', array() );
$items = ( !is_array( $data ) ? array( $data ) : $data );
$fields = $this->param( 'fields', array() );

foreach( (array) $fields as $resource => $list ) {
	$fields[$resource] = array_flip( explode( ',', $list ) );
}

foreach( $items as $item )
{
	if( $item instanceof \Aimeos\MShop\Common\Item\ListRef\Iface )
	{
		foreach( $item->getRefItems() as $domain => $refItems )
		{
			foreach( $refItems as $id => $refItem )
			{
				$attributes = $refItem->toArray();
				$type = $refItem->getResourceType();

				if( isset( $fields[$type] ) ) {
					$attributes = array_intersect_key( $attributes, $fields[$type] );
				}

				$response[] = array(
					'id' => $id,
					'type' => $type,
					'attributes' => $attributes,
					'links' => array(
						'self' => $this->url( $target, $cntl, $action, array( 'resource' => $type, 'id' => $id ), array(), $config ),
						'related' => array(
							'href' => $this->url( $target, $cntl, $action, array( 'resource' => $type ), array(), $config )
						)
					)
				);
			}
		}
	}
}


echo json_encode( $response, $options );