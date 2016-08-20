<?php

class Shaurmalab_Score_Block_Oggetto_Selectgrid extends Mage_Adminhtml_Block_Widget_Grid
{

    public  $set;

    public function getSetId()
    {
        $set = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('score/oggetto')->getResource()->getTypeId())
            ->addFieldToFilter('attribute_set_name',$this->getSet())
            ->getFirstItem(); // TODO: add filter by owner when needed
        return $set->getId();
    }

    public function getOggettos()
    {
        $oggettos = array();

        if($this->getSet()=='Customer') {
            $oggettos = Mage::getModel('customer/customer')->getCollection()->addNameToSelect()->addAttributeToSelect('*');
        } else {
            $oggettos = Mage::getModel('score/oggetto')->getCollection()
                ->addAttributeToFilter('attribute_set_id',$this->getSetId())
                ->addAttributeToSelect('*')
                ;

            if($this->getOnlyPublic()) {
                $oggettos->addAttributeToFilter('is_public','1');
            }
        }
        return $oggettos;
    }


    protected function _getOggettoCollection()
    {

        if (is_null($this->_oggettoCollection)) {
            $layer = $this->getLayer();

            $this->_oggettoCollection = $this->getOggettos();

            if(is_object($layer)) $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

        }

        return $this->_oggettoCollection;
    }

    public function __construct()
    {
        parent::__construct();
        if(!Mage::registry('gridCounter')) {
            Mage::register('gridCounter',1);
        } else {
            $counter = Mage::registry('gridCounter');
            Mage::unregister('gridCounter');
            Mage::register('gridCounter',$counter+1);
        }
        $this->setId('objectGrid'.Mage::registry('gridCounter'));
        $this->setUseAjax(true);
        $this->setDefaultSort('title');
        $this->setSaveParametersInSession(false);
    }

    protected function _prepareCollection()
    {

        $params = $this->getRequest()->getParams();
        $collection = $this->_getOggettoCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function getAttributesForColumns() {


        $setId = $this->getSetId();
        /* @var $groups Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection */
        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($setId)
            ->setSortOrder()
            ->load();
        $attributes = array();


        /* @var $node Mage_Eav_Model_Entity_Attribute_Group */
        foreach ($groups as $node) {
            $nodeChildren = Mage::getResourceModel('score/oggetto_attribute_collection')
                ->setAttributeGroupFilter($node->getId())
                ->addVisibleFilter()
                // ->checkConfigurableOggettos()
                ->load();

            if ($nodeChildren->getSize() > 0) {
                foreach ($nodeChildren->getItems() as $child) {
                    /* @var $child Mage_Eav_Model_Entity_Attribute */

                    if($child->getIsVisibleOnFront()) {
                        $attributes[] = $child;
                    }
                }
            }
        }

        return $attributes;


    }

    protected function _prepareColumns()
    {

        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'checkbox',
                'index' => 'entity_id',
                'filterable' => false,
                'sortable' => false
            ));


        if($this->getSet()=='Customer') {
            $this->addColumn('name', array(
                'header'    => 'Name',
                'index'     => 'name',
                'sortable'  => false
            ));
            $this->addColumn('email', array(
                'header'    => 'Email',
                'index'     => 'email',
                'sortable'  => false
            ));
            $id = Mage::helper('score/oggetto')->isRelatedAttribute('usersgroup');
                $oggettos = Mage::getModel('score/oggetto')->getAvailableObjects($id);
                $options = array();
                foreach($oggettos as $oggetto) {
                    $options[$oggetto->getId()] = $oggetto->getTitle().$oggetto->getName();
                }
            $this->addColumn('usersgroup_uid', array(
                'header'    => 'Groups',
                'index'     => 'entity_id',
                'type'      => 'options',
                'options'   => $options,
                'sortable'  => false
            ));

            } else {
            foreach ($this->getAttributesForColumns() as $attribute) {

                if($id = Mage::helper('score/oggetto')->isRelatedAttribute($attribute->getAttributeCode())) {
                    $oggettos = Mage::getModel('score/oggetto')->getAvailableObjects($id);
                    $options = array();
                    foreach($oggettos as $oggetto) {
                        $options[$oggetto->getId()] = $oggetto->getTitle().$oggetto->getName();
                    }

                    $this->addColumn($attribute->getAttributeCode(), array(
                        'header'    => $attribute->getStoreLabel(),
                        'index'     => $attribute->getAttributeCode(),
                        'type'      => 'options',
                        'options'   => $options,
                        'sortable'  => false
                    ));
                } else {
                    $this->addColumn($attribute->getAttributeCode(), array(
                        'header'    => $attribute->getStoreLabel(),
                        'index'     => $attribute->getAttributeCode(),
                        'sortable'  => false
                    ));
                }
            }
        }


        $this->addColumn('edit', array(
            'header'    => Mage::helper('core')->__('Manage'),
            'width'     => '40',
            'type'      => 'action',
            'actions'   => array(
                array(
                    'caption'   => '<img src="'.$this->getSkinUrl('images/manage.gif').'" alt="'.Mage::helper('core')->__('Manage').'"/>',
                    'url'       => array('base'=> 'score/oggetto/edit'),
                    'field'     => 'id'
                )
            ),
            'getter'    => 'getId',
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
        ));

        $this->addColumn('delete', array(
            'header'    => Mage::helper('core')->__('Delete'),
            'width'     => '40',
            'type'      => 'action',
            'actions'   => array(
                array(
                    'caption'   => '<img src="'.$this->getSkinUrl('images/delete.gif').'" alt="'.Mage::helper('core')->__('Delete').'"/>',
                    'url'       => array('base'=> 'score/oggetto/delete'),
                    'field'     => 'id'
                )
            ),
            'getter'    => 'getId',
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
        ));


        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('score/oggetto/selectgrid', array('_current'=> true,'set'=>$this->getSet()));
    }

    public function getRowUrl($row)
    {
        return '';//$this->getUrl('score/oggetto/edit', array('id'=>$row->getId()));
    }

    public function getTitle() {
        return '';
    }

    public function getAddTitle() {
        if(Mage::app()->getRequest()->getParam('id')) {
            return Mage::helper('core')->__('Edit').' '.$this->getSet();
        } else {
            return Mage::helper('core')->__('Add').' '.$this->getSet();
        }
    }
}
