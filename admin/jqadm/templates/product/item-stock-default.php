<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

?>
<div class="product-item-stock card panel">
	<div id="product-item-stock" class="header card-header" role="tab"
		data-toggle="collapse" data-parent="#accordion" href="#product-item-stock-data"
		aria-expanded="true" aria-controls="product-item-stock-data">
		<?php echo $enc->html( $this->translate( 'admin', 'Stock level' ) ); ?>
	</div>
	<div id="product-item-stock-data" class="item-stock card-block panel-collapse collapse table-responsive" role="tabpanel" aria-labelledby="product-item-stock">
		<table class="stock-list table table-default">
			<thead>
				<tr>
			  		<th class="stock-warehouse"><?php echo $enc->html( $this->translate( 'admin', 'Warehouse' ) ); ?></th>
			  		<th class="stock-stocklevel"><?php echo $enc->html( $this->translate( 'admin', 'Stock level' ) ); ?></th>
			  		<th class="stock-databack"><?php echo $enc->html( $this->translate( 'admin', 'Back in stock' ) ); ?></th>
					<th class="actions"><div class="btn btn-primary fa fa-plus"></div></th>
				</tr>
			</thead>
			<tbody>
<?php foreach( $this->get( 'stockData/product.stock.id', array() ) as $idx => $id ) : ?>
				<tr>
			  		<td class="stock-warehouse">
						<input class="item-id" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'stock', 'product.stock.id', '' ) ) ); ?>" value="<?php echo $enc->attr( $id ); ?>" />
						<select class="form-control c-select item-warehouseid" name="<?php echo $enc->attr( $this->formparam( array( 'stock', 'product.stock.warehouseid', '' ) ) ); ?>">
<?php	foreach( $this->get( 'stockWarehouses', array() ) as $whid => $warehouse ) : ?>
<?php		if( $whid == $this->get( 'stockData/product.stock.warehouseid/' . $idx ) ) : ?>
							<option value="<?php echo $enc->attr( $whid ); ?>" selected="selected"><?php echo $enc->html( $warehouse->getLabel() ) ?></option>
<?php		else : ?>
							<option value="<?php echo $enc->attr( $whid ); ?>"><?php echo $enc->html( $warehouse->getLabel() ) ?></option>
<?php		endif; ?>
<?php	endforeach; ?>
						</select>
					</td>
					<td class="stock-stocklevel">
						<input class="form-control item-stocklevel" type="text" name="<?php echo $enc->attr( $this->formparam( array( 'stock', 'product.stock.stocklevel', '' ) ) ); ?>"
							value="<?php echo $enc->attr( $this->get( 'stockData/product.stock.stocklevel/' . $idx ) ); ?>" />
					</td>
					<td class="stock-databack">
						<input class="form-control item-dateback date" type="text" name="<?php echo $enc->attr( $this->formparam( array( 'stock', 'product.stock.dateback', '' ) ) ); ?>"
							value="<?php echo $enc->attr( $this->get( 'stockData/product.stock.dateback/' . $idx ) ); ?>"
							placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'YYYY-MM-DD hh:mm:ss (optional)' ) ); ?>"
							data-format="<?php echo $this->translate( 'admin', 'yy-mm-dd' ); ?>" />
					</td>
					<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
				</tr>
<?php endforeach; ?>
				<tr class="prototype">
			  		<td class="stock-warehouse">
						<input class="item-id" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'stock', 'product.stock.id', '' ) ) ); ?>" value="" disabled="disabled" />
						<select class="form-control c-select item-warehouseid" name="<?php echo $enc->attr( $this->formparam( array( 'stock', 'product.stock.warehouseid', '' ) ) ); ?>" disabled="disabled">
<?php foreach( $this->get( 'stockWarehouses', array() ) as $whid => $warehouse ) : ?>
							<option value="<?php echo $enc->attr( $whid ); ?>"><?php echo $enc->html( $warehouse->getLabel() ) ?></option>
<?php endforeach; ?>
						</select>
					</td>
					<td class="stock-stocklevel">
						<input class="form-control item-stocklevel" type="text" name="<?php echo $enc->attr( $this->formparam( array( 'stock', 'product.stock.stocklevel', '' ) ) ); ?>" disabled="disabled" />
					</td>
					<td class="stock-databack">
						<input class="form-control date-prototype item-dateback" type="text" name="<?php echo $enc->attr( $this->formparam( array( 'stock', 'product.stock.dateback', '' ) ) ); ?>" disabled="disabled"
							placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'YYYY-MM-DD hh:mm:ss (optional)' ) ); ?>"
							data-format="<?php echo $this->translate( 'admin', 'yy-mm-dd' ); ?>" />
					</td>
					<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
				</tr>
			</tbody>
		</table>
<?php echo $this->get( 'stockBody' ); ?>
	</div>
</div>
