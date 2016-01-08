<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

?>
<div class="product-item-image card panel">
	<div id="product-item-image" class="header card-header" role="tab" data-toggle="collapse" data-parent="#accordion" href="#product-item-image-data" aria-expanded="true" aria-controls="product-item-image-data">
		<?php echo $enc->html( $this->translate( 'admin', 'Images' ) ); ?>
	</div>
	<div id="product-item-image-data" class="item-image card-block panel-collapse collapse" role="tabpanel" aria-labelledby="product-item-image">
		<table class="image-list table table-default">
			<thead>
				<tr>
			  		<th class="image-preview"><?php echo $enc->html( $this->translate( 'admin', 'Preview' ) ); ?></th>
			  		<th class="image-language"><?php echo $enc->html( $this->translate( 'admin', 'Language' ) ); ?></th>
			  		<th class="image-label"><?php echo $enc->html( $this->translate( 'admin', 'Title' ) ); ?></th>
					<th class="actions"><div class="btn btn-primary fa fa-plus"><input class="fileupload" type="file" name="image[files]" multiple /></div></th>
				</tr>
			</thead>
			<tbody>
<?php foreach( $this->get( 'imageData/media.id', array() ) as $idx => $id ) : ?>
				<tr>
			  		<td class="image-preview"><img src="<?php echo $enc->attr( $this->get( 'imageData/media.preview/' . $idx ) ); ?>" /></td>
			  		<td class="image-language">
			  			<input name="image[product.lists.id][]" value="<?php echo $enc->attr( $this->get( 'imageData/product.lists.id/' . $idx ) ); ?>" />
						<select  type="hidden" class="combobox" name="image[media.languageid][]">
						  <option value="<?php echo $enc->attr( $this->get( 'imageData/media.languageid/' . $idx ) ); ?>" selected="selected">
						  	<?php echo $enc->html( $this->get( 'imageData/media.languageid/' . $idx ) ) ?>
						  </option>
						</select>
					</td>
			  		<td class="image-label"><input type="text" class="form-control" name="image[media.label][]" value="<?php echo $enc->attr( $this->get( 'imageData/media.label/' . $idx ) ); ?>" /></td>
					<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
				</tr>
<?php endforeach; ?>
				<tr class="prototype">
			  		<td class="image-preview"></td>
			  		<td class="image-language">
			  			<input name="image[product.lists.id][]" value="" disabled="disabled" />
			  			<select class="combobox-prototype" name="image[media.languageid][]" disabled="disabled"></select>
					</td>
			  		<td class="image-label"><input type="text" class="form-control" name="image[media.label][]" value="" disabled="disabled" /></td>
					<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
