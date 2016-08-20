<?php
class Shaurmalab_Score_Block_Oggetto_Renderer
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
//        'actions'   => array(
//        array(
//            'caption'   =>,
//            'url'       => ,
//            'field'     => 'id',
//            'class'     => 'delete-btn btn btn-action',
//            'onclick' => "return confirm('".Mage::helper('core')->__('Are you sure?')."')"
//        )
//    ),

        if($this->getColumn()->getAtype()=='delete') {
            $add = '';
            $setName = Mage::getModel('score/oggetto')->load($row->getId())->getSetName();
            if($setName=='School') {
                $add = 'If you will delete '.$setName.', all pupil profiles of that school and their questionaries results will be removed from the system';
            }
            $html = '<a class="delete-btn btn btn-action" onclick="confirmDialog = jQuery(\'#del-confirm-'.$row->getId().'\').dialog({
        autoOpen: false,
        modal: true,
        buttons: {},
        dialogClass: \'error-dialog\'}); confirmDialog.dialog(\'open\');" href="javascript:void(0)">'. Mage::helper('core')->__('Delete').'</a>
            <div class="error-dialog absolute  nml p20"  style="display: none" title="Please confirm" id="del-confirm-'.$row->getId().'">
                <h4>Do you want to delete '.(($row->getEmail() || $row->getUsername())?' user ':$setName).(($row->getEmail() || $row->getUsername())?($row->getUsername()?'"'.$row->getUsername().'"':'"'.$row->getName().'"'):' with name "'.$row->getTitle().'"').'? </h4><h5>'.$add.'</h5>
                <span class="btn btn-small" onclick="confirmDialog.dialog(\'close\')">Cancel</span><span><a class="btn btn-small btn-action pull-right" href="'.Mage::getBaseUrl().(($row->getEmail() || $row->getUsername())?'score/user/delete':'score/oggetto/delete').'/id/'.$row->getId().'/">Ok</a></span>
            </div>
            ';
            return $html;
        }
        switch($this->getColumn()->getIndex()) {
            case 'questionnary_owner':

            $user = Mage::getModel('customer/customer')->load($row->getId());
            $usernames = explode(',',$user->getData('username'));

            $attributesSets = Mage::helper('score/oggetto')->loadAttributeSets();

            $ids = array(25,29,30,31,32,33,34);
            $objects = Mage::getModel('score/oggetto')->getCollection()->addAttributeToFilter('attribute_set_id',array('in'=>$ids))->addAttributeToFilter('owner',$user->getId());

            $completed = 0;
            foreach($objects as $object) {
               $data = Mage::getModel('score/oggetto')->load($object->getId())->getData();

               if($object->getData('attribute_set_id')!= 25 || $data['completed']==1) {
                   $completed++;
               }
            }
            $sent = count($usernames);
            //if(!$user->getIsActive()) $sent--;
            return  $completed.'/'.$sent;
        break;
            case 'attr_set_name':
                return trim($row->getData($this->getColumn()->getIndex()),"The ");
                break;
            case 'attr_set_status':
                return $row->getData($this->getColumn()->getIndex());
                break;
            case 'view':
                if($row->getData('oid')) { return '<a class="btn w100 activities-button " href="'.Mage::getBaseUrl().'quest'.$row->getData('letter').'?oid='.$row->getData('oid').'&view=1">View</a>'; } else { return ''; }
                break;
            case 'username':
                $user = Mage::getModel('customer/customer')->load($row->getId());
                return $user->getUsername();
            case 'is_active':
                $user = Mage::getModel('customer/customer')->load($row->getId());

                if($user->getIsActive())  { return 'Active'; }
                elseif($user->getConfirmation()) { return 'Not activated'; }
                else { return 'Disabled'; }
                break;
            case 'owner':
                $user = Mage::getModel('customer/customer')->load($row->getData($this->getColumn()->getIndex()));
                return $user->getFirstname().'<br/>'.$user->getLastname();
            case 'created_at':
            case 'exp_date':
                $date = date('d M Y',strtotime($row->getData($this->getColumn()->getIndex())));
                $dateArr = explode(' ',$date);
                return $dateArr[0].' '.$dateArr[1].'<br/>'.$dateArr[2];
                break;
            default:
                return $row->getData($this->getColumn()->getIndex());
                break;
        }
    }


}
