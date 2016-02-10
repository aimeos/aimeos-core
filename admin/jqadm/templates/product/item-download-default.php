<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */

$selected = function( $key, $code ) {
	return ( $key == $code ? 'selected="selected"' : '' );
};

$enc = $this->encoder();

?>
<div class="product-item-download card panel">
	<div id="product-item-download" class="header card-header" role="tab" data-toggle="collapse" data-parent="#accordion" href="#product-item-download-data" aria-expanded="true" aria-controls="product-item-download-data">
		<?php echo $enc->html( $this->translate( 'admin', 'Downloads' ) ); ?>
	</div>
	<div id="product-item-download-data" class="item-download card-block panel-collapse collapse table-responsive" role="tabpanel" aria-labelledby="product-item-download">
	<table class="download-list table table-default">
			<thead>
				<tr>
			  		<th class="download-status"><?php echo $enc->html( $this->translate( 'admin', 'Status' ) ); ?></th>
			  		<th class="download-label"><?php echo $enc->html( $this->translate( 'admin', 'Name' ) ); ?></th>
					<th class="actions"><div class="btn btn-primary fa fa-plus"><input class="fileupload" type="file" name="download[files][]" multiple /></div></th>
				</tr>
			</thead>
			<tbody>
<?php foreach( $this->get( 'downloadData/product.lists.id', array() ) as $idx => $id ) : ?>
				<tr>
					<td class="download-status">
						<input class="item-listid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'download', 'product.lists.id', '' ) ) ); ?>" value="<?php echo $enc->attr( $id ); ?>" />
						<input class="item-id" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'download', 'attribute.id', '' ) ) ); ?>" value="<?php echo $enc->attr( $this->get( 'downloadData/attribute.id/' . $idx ) ); ?>" />
						<select class="form-control c-select item-status" name="<?php echo $enc->attr( $this->formparam( array( 'download', 'attribute.status' ) ) ); ?>">
							<option value="1" <?php echo $selected( $this->get( 'downloadData/attribute.status', 1 ), 1 ); ?>><?php echo $enc->html( $this->translate( 'admin', 'status:enabled' ) ); ?></option>
							<option value="0" <?php echo $selected( $this->get( 'downloadData/attribute.status', 1 ), 0 ); ?>><?php echo $enc->html( $this->translate( 'admin', 'status:disabled' ) ); ?></option>
							<option value="-1" <?php echo $selected( $this->get( 'downloadData/attribute.status', 1 ), -1 ); ?>><?php echo $enc->html( $this->translate( 'admin', 'status:review' ) ); ?></option>
							<option value="-2" <?php echo $selected( $this->get( 'downloadData/attribute.status', 1 ), -2 ); ?>><?php echo $enc->html( $this->translate( 'admin', 'status:archive' ) ); ?></option>
						</select>
					</td>
					<td class="download-label">
						<input class="form-control item-label" type="text" required="required"
							name="<?php echo $enc->attr( $this->formparam( array( 'download', 'attribute.label', '' ) ) ); ?>"
							value="<?php echo $enc->attr( $this->get( 'downloadData/attribute.label/' . $idx ) ); ?>" />
					</td>
					<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
				</tr>
<?php endforeach; ?>
				<tr class="prototype">
			  		<td class="download-status">
						<input class="item-listid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'download', 'product.lists.id', '' ) ) ); ?>" value="" disabled="disabled" />
						<select class="form-control c-select item-status" name="<?php echo $enc->attr( $this->formparam( array( 'download', 'attribute.status' ) ) ); ?>">
							<option value="1"><?php echo $enc->html( $this->translate( 'admin', 'status:enabled' ) ); ?></option>
							<option value="0"><?php echo $enc->html( $this->translate( 'admin', 'status:disabled' ) ); ?></option>
							<option value="-1"><?php echo $enc->html( $this->translate( 'admin', 'status:review' ) ); ?></option>
							<option value="-2"><?php echo $enc->html( $this->translate( 'admin', 'status:archive' ) ); ?></option>
						</select>
					</td>
					<td class="download-label"><input class="form-control item-label" type="text" name="<?php echo $enc->attr( $this->formparam( array( 'download', 'attribute.label', '' ) ) ); ?>" value="" disabled="disabled" /></td>
					<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
				</tr>
			</tbody>
		</table>
<?php echo $this->get( 'downloadBody' ); ?>
	</div>
</div>
