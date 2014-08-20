<?php
/**
 * Magento FieldType Template List view
 *
 * @package			Magento
 * @version			1.0
 * @author          Nicolas Guerin-Verbialle <nicolas@submodal.com>
 */
$EE =&get_instance();
?>

<div class="category-products">
        <ul class="products-grid">
            <?php $i = 0 ?>
            <?php foreach($products AS $product): ?>
            <li class="item-nohover">
                <div class="product-container <?php if ($i % 5 == 0) echo ' first' ?>">
                    <?php
                    echo $EE->magento_helper->getBlock('catalog/product', array(
                                                                               'product_sku' => $product,
                                                                               'template' => 'catalog/product/view/ee/' . $block . '.phtml')
                    )->toHtml();
                    ?>
                </div>
            </li>
            <?php $i++ ?>
            <?php endforeach; ?>
        </ul>
</div>
<script type="text/javascript">decorateGeneric($$("ul.products-grid"), ["odd","even","first","last"])</script>
