<?php

class Custom_Lancaster_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
    * Index action
    */
    public function indexAction()
    {
        $this->_redirect('/');
    }

    public function changePathwayAction() {

        $pathway = $this->getRequest()->getParam('new');
        $oldObject = Mage::getModel('score/oggetto')->load($this->getRequest()->getParam('old'));
        $pupil = Mage::getModel('customer/customer')->load($oldObject->getPupilId());
        $questionnary = Mage::getModel('score/oggetto')->getCollection()->addAttributeToFilter('attribute_set_id',$oldObject->getData('questionaryid'))->getFirstItem();
//        print_r($oldObject->getData());

        $select = Mage::getModel('score/oggetto')->getCollection()->getConnection()->query("SELECT COUNT(*) as count from score_oggetto_entity where attribute_set_id = 24")->fetch();
        $maxId = $select['count']+1;

        $assegnee = $oldObject->getOwner();
        $appointmentData = array(
            'owner' => $assegnee,
            'attribute_set_id' => 24,
            'is_public' => '1',
            'visibility' => '1',
            'unique_id' => $maxId,
            'questionaryid' => $oldObject->getData('questionaryid'),
            'pupil_id' =>  $oldObject->getData('pupil_id'),
            'activity_type' => $pathway,
            'task_status' => 115,
            'type_id' => Shaurmalab_Score_Model_Oggetto_Type::DEFAULT_TYPE
        );
        $initialActivityData = array(
            'attribute_set_id' => 35,
            'is_public' => '1',
            'visibility' => '1',
            'type_id' => Shaurmalab_Score_Model_Oggetto_Type::DEFAULT_TYPE
        );

        $appointmentData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($appointmentData);
        $appointment = new Shaurmalab_Score_Model_Oggetto();
        $appointmentData['appointment_description'] = "Questionnaire name:  {$questionnary->getSetName()}  <br/> Care pathway Name: ";
        switch($pathway) {
            case 'holistic_health':
                $appointmentData['appointment_description'] .= 'Holistic Health Interview';
                $appointmentData['exp_date'] = date('Y-m-d 00:00:00',strtotime(now() . " +2 weeks"));
                break;
            case 'pre_assessment_caf':
                $appointmentData['appointment_description'] .= 'Pre-Assessment CAF';
                $appointmentData['exp_date'] = date('Y-m-d 00:00:00',strtotime(now() . " +1 week"));
                break;
            case 'emotional_health':
                $appointmentData['appointment_description'] .= 'Emotional Health';
                $appointmentData['exp_date'] = date('Y-m-d 00:00:00',strtotime(now() . " +2 weeks"));
                break;
            case 'followup_care':
                $appointmentData['appointment_description'] .= 'Follow Up';
                $appointmentData['exp_date'] = date('Y-m-d 00:00:00',strtotime(now() . " +4 weeks"));
                break;
            case 'information':
                $appointmentData['appointment_description'] .= 'Information';
                $appointmentData['exp_date'] = date('Y-m-d 00:00:00',strtotime(now() . " +6 weeks"));
                break;
            case 'vision_screening':
                $appointmentData['appointment_description'] .= 'Vision Screening';
                $appointmentData['exp_date'] = date('Y-m-d 00:00:00',strtotime(now() . " +4 weeks"));
                break;
            case 'hearing_screening':
                $appointmentData['appointment_description'] .= 'Hearing Screening';
                $appointmentData['exp_date'] = date('Y-m-d 00:00:00',strtotime(now() . " +4 weeks"));
                break;
            default:
                break;
        }
        $appointmentData['appointment_description'] .= '<br/> Pupil ID: '.$pupil->getUsername();

        Mage::getModel('lancaster/observer')->createAppointment($pathway,null,$appointmentData,$initialActivityData);
        $oldObject->setTaskStatus(118)->save();
        $this->_getSession()->addSuccess($this->__('Appointment converted'));
        $this->_redirectReferer();
    }

    private function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function reportsAction() {

        $this->loadLayout();

        $this->renderLayout();
    }

    public function saveAppointmentAction() {
        $post = $this->getRequest()->getPost();

        foreach($post['activity_comment'] as $key => $comment) {
            $swift = $key; break;
        }
        if($post['task_status'] == 116 && !isset($post['activity_status'][$swift]))  {
            $this->_getSession()->addError('Can not complete appointment without completing Swift response');
            return false;
        }
      

        $app = Mage::getModel('score/oggetto')->load($post['id']);

        $app->setOwner($post['owner'])->setTaskStatus($post['task_status'])->save();

        foreach($post['activity_comment'] as $key => $comment) {
            if($comment) {
                $act = Mage::getModel('score/oggetto')->load($key);
                $act->setData('activity_comment',$comment)->save();
            }
        }

        foreach($post['activity_status'] as $key => $status) {
            $act = Mage::getModel('score/oggetto')->load($key);
            if(!$act->getExpDate()) $act->setExpDate(date('Y-m-d 00:00:00'));
            $act->setData('activity_status',1)->save();
        }
        $this->_getSession()->addSuccess('Appointment and activities are saved');

    }
}
