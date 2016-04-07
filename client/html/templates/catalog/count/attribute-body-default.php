<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

?>
<?php $this->block()->start( 'catalog/count/attribute' ); ?>
// <!--
var attributeCounts = <?php echo json_encode( $this->get( 'attributeCountList', array() ), JSON_FORCE_OBJECT ); ?>;

$( ".catalog-filter-attribute .attribute-lists li.attr-item" ).each( function( index, item ) {
	$(item).append( function() {
		var itemId = $(this).data( "id" );

		if( attributeCounts[itemId] ) {
			var node = document.createElement( 'span' );
			node.appendChild( document.createTextNode( attributeCounts[itemId] ) );
			$(node).addClass( 'attr-count' );
			return node;
		}

		$(this).addClass( 'disabled' );
	});
});

<?php echo $this->get( 'attributeBody' ); ?>
// -->
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'catalog/count/attribute' ); ?>
