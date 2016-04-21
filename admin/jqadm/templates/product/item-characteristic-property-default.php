<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */

$selected = function( $key, $code ) {
error_log( 'key: ' . $key . ': ' . $code . '<br/>' );
	return ( $key == $code ? 'selected="selected"' : '' );
};

$enc = $this->encoder();

?>
<div class="col-lg-6 product-item-characteristic-property">
	<table class="property-list table table-default">
		<thead>
			<tr>
				<th colspan="3"><?php echo $enc->html( $this->translate( 'admin', 'Properties' ) ); ?></th>
				<th class="actions"><div class="btn btn-primary fa fa-plus"></div></th>
			</tr>
		</thead>
		<tbody>
<?php foreach( $this->get( 'propertyData/product.property.id', array() ) as $idx => $id ) : ?>
			<tr>
				<td class="property-type">
					<input class="item-id" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'characteristic', 'property', 'product.property.id', '' ) ) ); ?>" value="<?php echo $enc->attr( $id ); ?>" />
					<select class="c-select item-typeid" name="<?php echo $enc->attr( $this->formparam( array( 'characteristic', 'property', 'product.property.typeid', '' ) ) ); ?>">
<?php foreach( $this->get( 'propertyTypes', array() ) as $typeid => $item ) : ?>
						<option value="<?php echo $enc->attr( $typeid ); ?>" <?php echo $selected( $this->get( 'propertyData/product.property.typeid/' . $idx ), $typeid ); ?> ><?php echo $enc->html( $item->getLabel() ); ?></option>
<?php endforeach; ?>
					</select>
				</td>
				<td class="property-language">
					<select class="combobox item-languageid" name="<?php echo $enc->attr( $this->formparam( array( 'characteristic', 'property', 'product.property.languageid', '' ) ) ); ?>">
						<option value="<?php echo $enc->attr( $this->get( 'propertyData/product.property.languageid/' . $idx ) ); ?>" selected="selected"><?php echo $enc->html( $this->get( 'propertyData/product.property.languageid/' . $idx ) ) ?></option>
					</select>
				</td>
				<td class="property-value">
					<input class="form-control item-value" type="text" name="<?php echo $enc->attr( $this->formparam( array( 'characteristic', 'property', 'product.property.value', '' ) ) ); ?>"
						placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Property value (required)' ) ); ?>"
						value="<?php echo $enc->attr( $this->get( 'propertyData/product.property.value/' . $idx ) ); ?>" />
				</td>
				<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
			</tr>
<?php endforeach; ?>
			<tr class="prototype">
				<td class="property-type">
					<input class="item-id" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'characteristic', 'property', 'product.property.id', '' ) ) ); ?>" value="" disabled="disabled" />
					<select class="c-select item-typeid" name="<?php echo $enc->attr( $this->formparam( array( 'characteristic', 'property', 'product.property.typeid', '' ) ) ); ?>" disabled="disabled">
<?php foreach( $this->get( 'propertyTypes', array() ) as $typeid => $item ) : ?>
						<option value="<?php echo $enc->attr( $typeid ); ?>" ><?php echo $enc->html( $item->getLabel() ); ?></option>
<?php endforeach; ?>
					</select>
				</td>
				<td class="property-language">
					<select class="combobox-prototype item-languageid" name="<?php echo $enc->attr( $this->formparam( array( 'characteristic', 'property', 'product.property.languageid', '' ) ) ); ?>" disabled="disabled">
					</select>
				</td>
				<td class="property-value">
					<input class="form-control item-value" type="text" name="<?php echo $enc->attr( $this->formparam( array( 'characteristic', 'property', 'product.property.value', '' ) ) ); ?>"
						placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Property value (required)' ) ); ?>" value="" disabled="disabled" />
				</td>
				<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
			</tr>
		</tbody>
	</table>
<?php echo $this->get( 'propertyBody' ); ?>
</div>
