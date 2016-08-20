<?php

class Cafepress_CPCore_Block_Catalog_Shop_Copy_Tab_Selectstores extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'shop_copy_form',
            'action' => $this->getUrl('*/*/continue'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('select_shops', array('legend'=>Mage::helper('cpcore')->__('Select Shops')));

        $accounts_data = array();
        if(isset($_SESSION['cp_shop_accounts_data'])){
            $accounts_data = $_SESSION['cp_shop_accounts_data'];
        }

        $src_store_options = array();
        if(count($accounts_data) > 0){
            $stores = Mage::getModel('cpcore/cafepress_shops')
                ->getStoresList($accounts_data['src_shop_login'],
                $accounts_data['src_shop_password'],
                $accounts_data['src_shop_apikey']);

//            $_SESSION['cp_shop_src_stores_data'] = $stores;

            foreach($stores as $key => $store){
                $src_store_options[$key] = $store['name'];
            }
        }

        $dst_store_options = array();
//        $dst_store_options[''] = '<<< Please Select >>>';
        if(count($accounts_data) > 0){
            $stores = Mage::getModel('cpcore/cafepress_shops')
                ->getStoresList($accounts_data['dst_shop_login'],
                $accounts_data['dst_shop_password'],
                $accounts_data['dst_shop_apikey']);

//            $_SESSION['cp_shop_dst_stores_data'] = $stores;

            foreach($stores as $key => $store){
                $dst_store_options[$key] = $store['name'];
            }
        }

//        Mage::log($stores, null, 'lomantik.log');

        $fieldset->addField('src_shop_store', 'select', array(
            'label'     => $this->__('Source Shop Store'),
            'name'      => 'src_shop_store',
            'required'  => true,
            'options'     => $src_store_options,
        ));

        $fieldset->addField('dst_shop_store', 'select', array(
            'label'     => $this->__('Destination Shop Store'),
            'name'      => 'dst_shop_store',
            'required'  => true,
            'options'     => $dst_store_options,
        ));

//        $fieldset->addField('dst_shop_store_id', 'text', array(
//            'label'     => $this->__('Destination Shop Store ID'),
//            'name'      => 'dst_shop_store_id',
//        ));
//
//        $fieldset->addField('dst_shop_store_name', 'text', array(
//            'label'     => $this->__('Destination Shop Store Name'),
//            'name'      => 'dst_shop_store_name',
//        ));

        foreach($accounts_data as $key => $value){
            if($key != 'form_key'){
                $fieldset->addField($key, 'hidden', array(
                    'name'      => $key,
                    'value'      => $value,
                ));
            }
        }

        $this->setForm($form);
    }
}