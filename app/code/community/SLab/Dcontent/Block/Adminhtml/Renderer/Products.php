<?php
/**
 * Grid column renderer
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Adminhtml_Renderer_Products extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render column
     *
     * @param Varien_Object $row
     * @return string html
     */
    public function render(Varien_Object $row)
    {
    	$prod_str = $row->getProducts();
    	$prods = Mage::helper('dcontent')->decodeInput($prod_str);
    	$html = '';
    	
    	$visible = Mage::getModel('catalog/product_visibility')->getOptionArray(); 
    	$status =   Mage::getSingleton('catalog/product_status')->getOptionArray();
    	$html = '<table><thead>
    	<th> '. Mage::helper('catalog')->__('Name').' </th>
    	<th> '. Mage::helper('catalog')->__('Sku').' </th>
    	<th> '. Mage::helper('catalog')->__('Visibility').' </th>
    	<th> '. Mage::helper('catalog')->__('Status').' </th>
    	</thead>
    	<tbody>
    	';
    	foreach ($prods as $id=>$prod) {
    		$p = Mage::getModel('catalog/product')->load($id);
    		$html.='<tr><td>'.$p->getName().'</td><td>'.$p->getSku().'</td><td>'.$visible[$p->getVisibility()].'</td><td>'.$status[$p->getStatus()]."</td></tr>";
    	}
    	$html.="</tbody></table>";
    	return $html;
    }
}

?>