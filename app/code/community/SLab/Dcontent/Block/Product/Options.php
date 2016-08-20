<?php
class SLab_Dcontent_Block_Product_Options extends Mage_Catalog_Block_Product_View_Options
{
    protected $_product;

    protected $_optionRenders = array();

    public function __construct()
    {
        parent::__construct();

        $this->addOptionRenderer(
            "text",
            "catalog/product_view_options_type_text",
            "catalog/product/view/options/type/text.phtml"
        );

        $this->addOptionRenderer(
            "file",
            "catalog/product_view_options_type_file",
            "catalog/product/view/options/type/file.phtml"
        );

        $this->addOptionRenderer(
            "select",
            "catalog/product_view_options_type_select",
            "catalog/product/view/options/type/select.phtml"
        );

        $this->addOptionRenderer(
            "date",
            "catalog/product_view_options_type_date",
            "catalog/product/view/options/type/date.phtml"
        );

        $this->addOptionRenderer(
            'default',
            'catalog/product_view_options_type_default',
            'catalog/product/view/options/type/default.phtml'
        );
    }

    public function getOption()
    {
        $options = $this->getProduct()->getOptions();
        foreach ($options as $option)
        {
            if ($option->getTitle() == $this->getData('title'))
            {
                return array($option);
            }
        }
        return false;
    }

}
