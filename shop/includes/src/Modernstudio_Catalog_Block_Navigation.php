<?php
/**
 * Catalog navigation
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Modernstudio_Catalog_Block_Navigation extends Mage_Catalog_Block_Navigation
{
	public function _sanitize_title_with_dashes($title) {
		$classTitle = strip_tags($title);
		// Preserve escaped octets.
		$classTitle = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $classTitle);
		// Remove percent signs that are not part of an octet.
		$classTitle = str_replace('%', '', $classTitle);
		// Restore octets.
		$classTitle = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $classTitle);
	
		$classTitle = strtolower($classTitle);
		$classTitle = preg_replace('/&.+?;/', '', $classTitle); // kill entities
		$classTitle = str_replace('.', '-', $classTitle);
		$classTitle = preg_replace('/[^%a-z0-9 _-]/', '', $classTitle);
		$classTitle = preg_replace('/\s+/', '-', $classTitle);
		$classTitle = preg_replace('|-+|', '-', $classTitle);
		$classTitle = trim($classTitle, '-');
	
		return $classTitle;
	}

    /**
     * Render category to html - first level only
     *
     * @param Mage_Catalog_Model_Category $category
     * @param int Nesting level number
     * @param boolean Whether ot not this item is last, affects list item class
     * @param boolean Whether ot not this item is first, affects list item class
     * @param boolean Whether ot not this item is outermost, affects list item class
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @param boolean Whether ot not to add on* attributes to list item
     * @return string
     */
    protected function _renderFirstLevelCategoryMenuItemHtml($category, $level = 0, $isLast = false, $isFirst = false,
        $isOutermost = false, $outermostItemClass = '', $childrenWrapClass = '', $noEventAttributes = false)
    {
        if (!$category->getIsActive()) {
            return '';
        }
        $html = array();

        // prepare list item html classes
        $classes = array();
		$classes[] = $this->_sanitize_title_with_dashes($category->getName());
        if ($this->isCategoryActive($category)) {
            $classes[] = 'active';
        }
        if ($isFirst) {
            $classes[] = 'first';
        }
        if ($isLast) {
            $classes[] = 'last';
        }

        // prepare list item attributes
        $attributes = array();
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }
        if ($hasActiveChildren && !$noEventAttributes) {
             $attributes['onmouseover'] = 'toggleMenu(this,1)';
             $attributes['onmouseout'] = 'toggleMenu(this,0)';
        }

        // assemble list item with attributes
        $htmlLi = '<li';
        foreach ($attributes as $attrName => $attrValue) {
            $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $htmlLi .= '>';
        $html[] = $htmlLi;

        $html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
        $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
        $html[] = '</a></li>';

        $html = implode("\n", $html);
        return $html;
    }
    /**
     * Render categories menu in HTML for left column
     *
     * @param int Level number for list item class to start from
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @return string
     */
    public function renderCategoriesHomeMenuHtml($level = 0, $outermostItemClass = '', $childrenWrapClass = '')
    {
        $activeCategories = array();
        foreach ($this->getStoreCategories() as $child) {
            if ($child->getIsActive()) {
                $activeCategories[] = $child;
            }
        }
        $activeCategoriesCount = count($activeCategories);
        $hasActiveCategoriesCount = ($activeCategoriesCount > 0);

        if (!$hasActiveCategoriesCount) {
            return '';
        }

        $html = '';
        $j = 0;
        foreach ($activeCategories as $category) {
            $html .= $this->_renderFirstLevelCategoryMenuItemHtml(
                $category,
                $level,
                ($j == $activeCategoriesCount - 1),
                ($j == 0),
                true,
                $outermostItemClass,
                $childrenWrapClass,
                true
            );
            $j++;
        }

        return $html;
    }
    /**
     * Render brands list for featured brands
     *
     * @param int Level number for list item class to start from
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @return string
     */
    public function renderBrandsMenuHtml($level = 0, $outermostItemClass = '', $childrenWrapClass = '')
    {
        $activeCategories = array();
        foreach ($this->getStoreCategories() as $child) {
            if ($child->getIsActive()) {
				if($child->getName() == "Brands"){
					$children = $child->getChildren();
            		$childrenCount = $children->count();
					
					foreach ($children as $category) {
						$category = Mage::getModel('catalog/category')->load($category['entity_id']);
						$class = $this->_sanitize_title_with_dashes($category->getName());
						$catName = $category->getName();
						$catImage = $category->getThumbnail();
						$catLink = $category->getUrl();
						
						$html .= '<li class="' . $class . '"><a href="' . $catLink . '">';
						if($catImage){
							$html .= '<img src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'media/catalog/category/' . $catImage . '" alt="' . $catName . '"/>';
						} else {
							$html .= $catName;
						}
						$html .= '</a></li>';
					}
					return $html;
				}
            }
        }
    }

}
