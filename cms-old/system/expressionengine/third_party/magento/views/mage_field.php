<?php
/**
 * Magento FieldType view
 *
 * @package			Magento
 * @version			1.0
 * @author          Nicolas Guerin-Verbialle <nicolas@submodal.com>
 */
 ?>
<div class="MagentoProductsField" id="MagentoProducts<?=$mage_field_id?>">
	<div class="TopBar">
		<h4>Search for Magento Products</h4>
	</div>

	<div class="SearchMagentoBlock">
		<div class="InputBlock">
			<input rel="keywords" id="MageAutocomplete" type="text" size="40"/>
            <div class="button">
                <a href="javascript:void(0)" class="addProduct submit">Add product</a>
            </div>
            <span class="LoadingProduct hidden"><?=lang('magento:loading_product')?></span>
		</div>
		<br clear="all" />
	</div>

	<div class="AssignedProducts">
		<table class="mainTable">
			<thead>
				<tr>
					<th><?=lang('magento:image')?></th>
					<th><?=lang('magento:name')?></th>
                    <th><?=lang('magento:sku')?></th>
					<th><?=lang('magento:actions')?></th>
				</tr>
			</thead>
			<tbody class="RelGroupDrop">
            <?php $i = 1 ?>
            <?php foreach ($products as $product):?>
				<tr class="ProductItem <?php ($i % 2 != 0) ? print 'odd' : false ?>">
					<td> <img src="<?=$product->image?>"></a></td>
					<td><?=$product->name?></td>
                    <td><?=$product->sku?></td>
					<td>
						<a href="javascript:void(0)" class="MoveProduct">&nbsp;</a>
						<?php // @TODO rel is important, gotta specify it with product SKU ?>
                        <a href="javascript:void(0)" class="DelProduct" rel="<?=$product->product_id?>">&nbsp;</a>
						<input name="field_id_<?=$mage_field_id?>[products][<?=$product->product_order?>][product_id]" type="hidden" value="<?=$product->product_id?>" >
					</td>
				</tr>
                <?php $i++ ?>
			<?php endforeach;?>
			<?php if ($total_products < 1):?><tr class="NoProducts"><td colspan="7"><?=lang('magento:no_products_added')?></td></tr><?php endif;?>
			</tbody>
		</table>
	</div>

</div>