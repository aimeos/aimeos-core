<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

$subAttrDeps = $this->get( 'subAttributeDependencies', array() );
$attrMap = $this->get( 'attributeMap', array() );
$enc = $this->encoder();

?>
<div class="additional-box">
<?php if( count( $attrMap ) > 0 ) : ?>
	<h2 class="header attributes"><?php echo $enc->html( $this->translate( 'client', 'Characteristics' ), $enc::TRUST ); ?></h2>
	<div class="content attributes">
		<table class="attributes">
			<tbody>
<?php foreach( $attrMap as $type => $attrItems ) : ?>
<?php	foreach( $attrItems as $attrItem ) : $classes = ""; ?>
<?php 		if( isset( $subAttrDeps[ $attrItem->getId() ] ) ) : ?>
<?php			$classes .= ' subproduct'; ?>
<?php			foreach( $subAttrDeps[ $attrItem->getId() ] as $prodid ) { $classes .= ' subproduct-' . $prodid; } ?>
<?php		endif; ?>
				<tr class="item<?php echo $classes; ?>">
					<td class="name"><?php echo $enc->html( $this->translate( 'client/html/code', $type ), $enc::TRUST ); ?></td>
					<td class="value">
						<div class="media-list">
<?php		foreach( $attrItem->getListItems( 'media', 'default' ) as $listItem ) : ?>
<?php			if( ( $item = $listItem->getRefItem() ) !== null ) : ?>
<?php				echo $this->partial( $this->config( 'client/html/common/partials/media', 'common/partials/media-default.php' ), array( 'item' => $item, 'boxAttributes' => array( 'class' => 'media-item' ) ) ); ?>
<?php			endif; ?>
<?php		endforeach; ?>
						</div>
						<span class="attr-name"><?php echo $enc->html( $attrItem->getName() ); ?></span>
					</td>
				</tr>
<?php	endforeach; ?>
<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>
<?php echo $this->get( 'attributeBody' ); ?>
</div>
