<?php

class Shaurmalab_Score_Block_Oggetto_Allgrid extends Mage_Adminhtml_Block_Widget_Grid
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

        $filters = $this->getPrefilter();
        $filters = explode(',',$filters);
        $predefined = array();

        if($this->getSet()=='Customer') {
            $oggettos = Mage::helper('score/oggetto')->getUsersCollection($this->getGroups())->addAttributeToSelect('*');
        } else {
            $oggettos = Mage::getModel('score/oggetto')->getCollection()
                ->addAttributeToFilter('attribute_set_id',$this->getSetId())
                ->addAttributeToSelect('*')

                ;

            if($this->getOnlyPublic()) {
                $oggettos->addAttributeToFilter('is_public','1');
            }
        }
        foreach($filters as $filter) {
            if($filter) {
                list($code,$value) = explode('=',$filter);
                if($code == 'owner' && $value == 'customer_id' && is_object(Mage::getSingleton('customer/session'))) {
                    $value = Mage::getSingleton('customer/session')->getCustomerId();
                }
                $oggettos->addAttributeToFilter($code,$value);
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

        if(isset($_GET['dob']) && $_GET['dob']) {
            if(isset($_GET['dob']['from'])) {

                    $collection->addAttributeToFilter('dob',array(
                    'gteq'       => date('Y-m-d 00:00:00',strtotime($_GET['dob']['from']))));
            }
            if(isset($_GET['dob']['to'])) {
                $collection->addAttributeToFilter('dob',array(
                    'lteq'       => date('Y-m-d 00:00:00',strtotime($_GET['dob']['to']))
                ));
            }
        }

        if(isset($_GET['filter']) && $_GET['filter'] && $_GET['filter']!='undefined') {
            $filters = array();
            if($this->getSet()=='Customer') {
                $filters[] = array(
                    'attribute' => 'name',
                    'like'       => '%'.$_GET['filter'].'%'
                );
                $filters[] = array(
                    'attribute' => 'email',
                    'like'       => '%'.$_GET['filter'].'%'
                );



            } else {
                foreach ($this->getAttributesForColumns() as $attribute) {
                    if(!Mage::helper('score/oggetto')->isRelatedAttribute($attribute->getAttributeCode()) && !Mage::helper('score/oggetto')->isUserAttribute($attribute->getAttributeCode()))  {
                           $filters[] = array(
                               'attribute' => $attribute->getAttributeCode(),
                               'like'       => '%'.$_GET['filter'].'%'
                           );
                    }
                }
            }
            if($filters) {
                $collection->addAttributeToFilter($filters);
            }

        }

        foreach($_GET as $attr => $get) {
            if(!$get) continue;
            if($attr == 'usersgroup_id') $attr = 'entity_id';

            if($this->getSet()=='Customer') {
                if(in_array($attr,explode(',',$this->getAddColumns()))) {


                        if(Mage::helper('score/oggetto')->isRelatedAttribute($attr) || $attr == 'entity_id') {
                            $collection->addAttributeToFilter('entity_id',array('in'=>explode(',',$get)));
                        } else {
                            $collection->addAttributeToFilter($attr,$get);
                        }

                }
            } else {
                $codes = array();
                foreach ($this->getAttributesForColumns() as $attribute) {
                    $codes[] = $attribute->getAttributeCode();
                }
                $codes[] = 'entity_id';
                if(in_array($attr,$codes)) {

                    if(Mage::helper('score/oggetto')->isRelatedAttribute($attr) || $attr == 'entity_id') {
                        $collection->addAttributeToFilter($attr,array('in'=>explode(',',$get)));
                    } else {
                        $collection->addAttributeToFilter($attr,$get);
                    }
                }
            }

        }

        if(isset($_GET['p']) && $_GET['p']) {
            $this->getRequest()->setParam('page',$_GET['p']);

        }
        if(!$this->getRequest()->getParam('page')) {
            $this->getRequest()->setParam('page',1);
        }
        if(!$this->getRequest()->getParam('limit')) {
           $this->getRequest()->setParam('limit',5);
        }
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
        if($this->getSet()=='Customer') {
            if($this->getWithId()) {
                $this->addColumn('entity_id', array(
                    'header'    => 'ID',
                    'width'     =>  '10%',
                    'align'     => 'center',
                    'index'     => 'entity_id',
                    'sortable'  => false,
                    'filter' => false,
                ));

            }




//            $id = Mage::helper('score/oggetto')->isRelatedAttribute('usersgroup');
//                $oggettos = Mage::getModel('score/oggetto')->getAvailableObjects($id);
//                $options = array();
//                foreach($oggettos as $oggetto) {
//                    $options[$oggetto->getAssignedUid()] = $oggetto->getTitle().$oggetto->getName();
//                }
//            $this->addColumn('usersgroup_uid', array(
//                'header'    => 'Groups',
//                'index'     => 'entity_id',
//                'align'     => 'center',
//                'type'      => 'options',
//                'options'   => $options,
//                'sortable'  => false
//            ));

            foreach(explode(',',$this->getAddColumns()) as $attr) {
                if($attr == 'group_id') {
                    if($this->getGroups()) {
                        $groups = Mage::helper('score/oggetto')->getAvailableGroupsByNames($this->getGroups())
                            ->load()
                            ->toOptionHash();
                    } else {
                        $groups = Mage::helper('score/oggetto')->getAvailableGroups()
                            ->load()
                            ->toOptionHash();
                    }

                    $this->addColumn('group_id', array(
                        'header'    =>  Mage::helper('customer')->__('Role'),
                        'index'     =>  'group_id',
                        'type'      =>  'options',
                        'width'     =>  '20%',
                        'align'     => 'center',
                        'options'   =>  $groups,
                        'sortable'  => false
                    ));
                } elseif(Mage::helper('score/oggetto')->isRelatedAttribute($attr)) {

                    $id = Mage::helper('score/oggetto')->isRelatedAttribute(str_replace('_uid','',$attr));
                        $oggettos = Mage::getModel('score/oggetto')->getAvailableObjects($id);
                        $options = array();

                        foreach($oggettos as $oggetto) {
                            $options[$oggetto->getAssignedUid()] = $oggetto->getTitle().$oggetto->getName(); //
                        }
                    $this->addColumn($attr, array(
                        'header'    => $this->getRequest()->getParam('page_id').$oggettos->getFirstItem()->getSetName(),
                        'index'     => 'entity_id',
                        'align'     => 'center',
                        'width'     =>  '30%',
                        'type'      => 'options',
                        'options'   => $options,
                        'sortable'  => false
                    ));
                }  else {

                    $attribute = Mage::getModel('customer/attribute')->getCollection()->addFieldToFilter('attribute_code',$attr)->getFirstItem();

                    if($attribute->getFrontendInput()== 'select') {
                        $options = $attribute->getSource()->getAllOptions();
                        $opts = array();
                        foreach($options as $opt) {
                            if($opt['label']) $opts[$opt['value']] = $opt['label'];
                        }
                        $columnData = array(
                            'header'    =>  $attribute->getFrontendLabel(),
                            'index'     =>  $attr,
                            'type'      =>  'options',
                            'width'     =>  '20%',
                            'align'     => 'center',
                            'options'   =>  $opts,
                            'sortable'  => false
                        );
                        if(in_array($attr, array('username','questionnary_owner','is_active'))) {
                            $columnData['renderer'] =   'score/oggetto_renderer';
                        }
                        $this->addColumn($attr, $columnData);
                    } else {



                        $columnData = array(
                            'header'    => $attribute->getFrontendLabel()?$attribute->getFrontendLabel():$attr,
                            'width'     =>  '20%',
                            'align'     => 'center',
                            'index'     => $attr,
                            'sortable'  => false,
                            'filter' => false
                        );
                        if(in_array($attr, array('username','questionnary_owner','is_active'))) {
                            $columnData['renderer'] =   'score/oggetto_renderer';
                        }
                        if($attribute->getData('backend_type')=='datetime')  {  $columnData['type'] = 'datetime'; }

                        $this->addColumn($attr, $columnData);
                    }
                }
            }

            } else {

            if($this->getWithId()) {
                $this->addColumn('entity_id', array(
                    'header'    => $this->getSet().' ID',
                    'index'     => 'entity_id',
                    'width'     =>  '10%',
                    'align'     => 'center',
                    'sortable'  => false,
                    'filter' => false,
                ));

            }
            foreach ($this->getAttributesForColumns() as $attribute) {
                $codes[] = $attribute->getAttributeCode();
                if($this->getAddColumns() && !in_array($attribute->getAttributeCode(),explode(',',$this->getAddColumns()))) continue;


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
                        'width'     =>  '25%',
                        'align'     => 'center',
                        'options'   => $options,
                        'sortable'  => false
                    ));
                } else
                    if($isUser = Mage::helper('score/oggetto')->isUserAttribute($attribute->getAttributeCode()))  {
                    $users = Mage::helper('score/oggetto')->getUsersCollection();
                    $options = array();

                    foreach($users as $user) {
                        $options[$user->getId()] = $user->getName();
                    }

                    $this->addColumn($attribute->getAttributeCode(), array(
                        'header'    => $attribute->getStoreLabel(),
                        'index'     => $attribute->getAttributeCode(),
                        'type'      => 'options',
                         'width'     =>  '25%',
                        'align'     => 'center',
                        'options'   => $options,
                        'sortable'  => false
                    ));
                } else {

                    if($attribute->getFrontendInput()== 'select') {
                        $options = $attribute->getSource()->getAllOptions();
                        $opts = array();
                        foreach($options as $opt) {
                            if($opt['label']) $opts[$opt['value']] = $opt['label'];
                        }
                        $this->addColumn($attribute->getAttributeCode(), array(
                            'header'    =>  $attribute->getFrontendLabel(),
                            'index'     =>  $attribute->getAttributeCode(),
                            'type'      =>  'options',
                            'width'     =>  '25%',
                            'align'     => 'center',
                            'options'   =>  $opts,
                            'sortable'  => false
                        ));
                    } else {
                        $columnData = array(
                            'header'    => $attribute->getStoreLabel(),
                            'index'     => $attribute->getAttributeCode(),
                            'sortable'  => false,
                            'align'     => 'center',
                            'width'     =>  '25%',
                            'filter' => false
                            );
                        if($attribute->getData('backend_type')=='datetime')  {  $columnData['type'] = 'date';  }
                        $this->addColumn($attribute->getAttributeCode(), $columnData);

                    }
                }
            }

            foreach(explode(',',$this->getAddColumns()) as $column) {
                if(in_array($column,$codes)) continue;
                if($id = Mage::helper('score/oggetto')->isRelatedAttribute($column)) {

                    $oggettos = Mage::getModel('score/oggetto')->getAvailableObjects($id);
                    $options = array();
                    foreach($oggettos as $oggetto) {
                        $options[trim($oggetto->getSchoolByRegionId(),',')] = $oggetto->getTitle().$oggetto->getName();
                    }

                    $this->addColumn($column, array(
                        'header'    => $column,
                        'index'     => 'entity_id',
                        'type'      => 'options',
                        'width'     =>  '25%',
                        'align'     => 'center',
                        'options'   => $options,
                        'sortable'  => false
                    ));
                } else
                    if($value = Mage::helper('score/oggetto')->isNumberAttribute($column)) {

                    $index = str_replace('_num','',$column);
                    $this->addColumn($index, array(
                        'header'    => Mage::helper('core')->__($column),
                        'index'     => $index,
                        'type'      => 'counter',
                        'align'     => 'center',
                        'sortable'  => false,
                        'filter'  => false
                    ));
                } elseif($id = Mage::helper('score/oggetto')->isCounterAttribute($column,$this->getSet())) {

                    $this->addColumn($column, array(
                        'header'    => Mage::helper('core')->__($column).' '.$id,
                        'index'     => $id,
                        'set'       => $this->getSet(),
                        'type'      => 'label',
                        'align'     => 'center',
                        'sortable'  => false,
                        'filter'  => false
                    ));
                }

            }
        }
        $actionStyle = " width: 108px;
            border-right: 0;
            border-left: 5px solid #fff;
            border-spacing: 0;
            padding: 0;
            line-height: auto !important;";
        if($this->getView()) {
            $this->addColumn('view', array(
                'header'    => Mage::helper('core')->__('View'),
                'width'     => '25%',
                'type'      => 'action',
                'class'     => 'td-action',
                'style'     => $actionStyle,
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('core')->__('View'),
                        'url'       =>  ($this->getSet()=='Customer')?array('base'=> 'score/user/view'):array('base'=> 'score/oggetto/view'),
                        'field'     => 'id',
                        'class'     => 'btn w100 activities-button '.((!$this->getViewPage())?'view-btn ':'')
                    )
                ),
                'getter'    => 'getId',
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',

            ));
        }

        if($this->getAssign()) {
            $this->addColumn('assign', array(
                'header'    => Mage::helper('core')->__('Assign'),
                'width'     => '25%',
                'type'      => 'action',
                'class'     => 'td-action',
                'style'     => $actionStyle,
                'actions'   => array(
                    array(
                        'caption'   => '<nobr>'.Mage::helper('core')->__('Add to '.$this->getAssign()).'</nobr>',
                        'url'       =>  ($this->getSet()=='Customer')?array('base'=> 'score/user/assign/to/'.$this->getAssign()):array('base'=> 'score/oggetto/assign/to/'.$this->getAssign()),
                        'field'     => 'id',
                        'class'     => 'assign-btn btn btn-action',
                    )
                ),
                'getter'    => 'getId',
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',

            ));
        }

        if($this->getEdit()) {

            $this->addColumn('edit', array(
            'header'    => Mage::helper('core')->__('Manage'),
            'width'     => '40',
            'type'      => 'action',
            'class'     => 'td-action',
            'style'     => $actionStyle,
                'actions'   => array(
                array(
                    'caption'   => Mage::helper('core')->__('Edit'),
                    'url'       =>  ($this->getSet()=='Customer')?array('base'=> 'score/user/edit'.'/form_code/'.strtolower(str_replace(' ','',$this->getGroups())).'_reg'.'/filters/'.$this->getFilters()):array('base'=> 'score/oggetto/edit'), // TODO: remove hardcode
                    'field'     => 'id',
                    'class'     => 'edit-btn btn edit-user'
                )
            ),
            'getter'    => 'getId',
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',

        ));
        }

        if($this->getDelete()) {
        $this->addColumn('delete', array(
            'header'    => Mage::helper('core')->__('Delete'),
            'width'     => '40',
            'type'      => 'action',
            'class'     => 'td-action',
            'style'     => $actionStyle,
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('core')->__('Delete'),
                    'url'       => ($this->getSet()=='Customer')?array('base'=> 'score/user/delete'):array('base'=> 'score/oggetto/delete'),
                    'field'     => 'id',
                    'class'     => 'delete-btn btn btn-action',
                    'onclick' => "return confirm('".Mage::helper('core')->__('Are you sure?')."')"
                )
            ),
            'getter'    => 'getId',
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',

        ));
        }


        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {

       $data = array('_current'=> true);

        foreach($this->getData() as $key=>$param)  {
            if(!is_array($param) && !is_object($param) && in_array($key,array('remove_search','prefilter','filters','view_page','form_code','add_columns','editor','with_id','groups','add','edit','view','delete','assign','set'))) $data[$key] = $param;
        }
        return $this->getUrl('score/oggetto/grid', $data);
    }

    public function getRowUrl($row)
    {
        return '';//$this->getUrl('score/oggetto/edit', array('id'=>$row->getId()));
    }

    public function getTitle() {
        return '';
    }

    public function getAddTitle() {
//        if(Mage::app()->getRequest()->getParam('id')) {
//            return Mage::helper('core')->__('Edit').' '.(($this->getSet()=='Customer')?$this->getSet():'User');
//        } else {
        $arr = explode(',',$this->getGroups());
        if(count($arr)==1) {
            return ($this->getSet()=='Customer')?'Register New '.$arr[0]:Mage::helper('core')->__('Add').' '.$this->getSet();
        } else {
            return ($this->getSet()=='Customer')?'Register New User':Mage::helper('core')->__('Add').' '.$this->getSet();
        }
//       }
    }

    public function getAddUrl() {


        if($this->getSet()=='Customer') {
            return $this->getUrl('score/user/add').'form_code/'.strtolower(str_replace(' ','',$this->getGroups())).'_reg/filters/'.$this->getPrefilter();
        }
        return $this->getUrl('add-'.str_replace(' ','',strtolower($this->getSet())));
    }

}
