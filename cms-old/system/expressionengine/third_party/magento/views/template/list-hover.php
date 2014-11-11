<?php
/**
 * Magento FieldType Template List-Hover view
 *
 * @package			Magento
 * @version			1.0
 * @author          Nicolas Guerin-Verbialle <nicolas@submodal.com>
 */
$EE =&get_instance();
?>

<div class="category-products">
        <ul class="products-grid">
            <?php foreach($products AS $product): ?>
            <li class="item">
                <?php
                echo $EE->magento_helper->getBlock('catalog/product', array(
                                                                                 'product_id' => $product,
                                                                                 'template' => 'catalog/product/view/ee/' . $block . '.phtml')
                )->toHtml();
                ?>
            </li>
            <?php endforeach; ?>
        </ul>
</div>
<script type="text/javascript">decorateGeneric($$("ul.products-grid"), ["odd","even","first","last"])</script>