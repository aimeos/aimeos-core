<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

?>
<div class="product-item-stock card panel">
	<div id="product-item-stock" class="header card-header" role="tab" data-toggle="collapse" data-parent="#accordion" href="#product-item-stock-data" aria-expanded="true" aria-controls="product-item-stock-data">
		<?php echo $enc->html( $this->translate( 'admin', 'Stock level' ) ); ?>
	</div>
	<div id="product-item-stock-data" class="item-stock card-block panel-collapse collapse" role="tabpanel" aria-labelledby="product-item-stock">
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
<?php foreach( $this->get( 'stockItems', array() ) as $whid => $item ) : ?>
				<tr>
			  		<td class="stock-warehouse">
						<select class="form-control" name="stock[product.stock.warehouseid][]">
<?php	foreach( $this->get( 'stockWarehouses', array() ) as $warehouse ) : ?>
<?php		if( $whid == $warehouse->getId() ) : ?>
							<option value="<?php echo $enc->attr( $warehouse->getId() ); ?>" selected="selected"><?php echo $enc->html( $warehouse->getLabel() ) ?></option>
<?php		else : ?>
							<option value="<?php echo $enc->attr( $warehouse->getId() ); ?>"><?php echo $enc->html( $warehouse->getLabel() ) ?></option>
<?php		endif; ?>
<?php	endforeach; ?>
						</select>
					</td>
			  		<td class="stock-stocklevel"><input type="text" class="form-control" name="stock[product.stock.stocklevel][]" value="<?php echo $enc->attr( $item->getStockLevel() ); ?>" /></td>
					<td class="stock-databack"><input type="text" class="form-control" name="stock[product.stock.dateback][]" value="<?php echo $enc->attr( $item->getDateBack() ); ?>" /></td>
					<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
				</tr>
<?php endforeach; ?>
				<tr class="prototype">
			  		<td class="stock-warehouse">
						<select class="form-control" name="stock[product.stock.warehouseid][]">
<?php foreach( $this->get( 'stockWarehouses', array() ) as $warehouse ) : ?>
							<option value="<?php echo $enc->attr( $warehouse->getId() ); ?>"><?php echo $enc->html( $warehouse->getLabel() ) ?></option>
<?php endforeach; ?>
						</select>
					</td>
			  		<td class="stock-stocklevel"><input type="text" class="form-control" name="stock[product.stock.stocklevel][]" disabled="disabled" /></td>
					<td class="stock-databack"><input type="text" class="form-control" name="stock[product.stock.dateback][]" disabled="disabled" /></td>
					<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
