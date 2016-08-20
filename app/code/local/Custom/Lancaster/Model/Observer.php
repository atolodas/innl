<?php
class Custom_Lancaster_Model_Observer
{

    public function validateObject(Varien_Event_Observer $observer) {
        //print_r(Mage::app()->getRequest()->getParams());
        if(Mage::app()->getRequest()->getParam('title') && Mage::app()->getRequest()->getParam('set') ) {
            $title = str_replace('+',' ',Mage::app()->getRequest()->getParam('title'));
            $oggettos = Mage::getModel('score/oggetto')->getCollection()->addAttributeToFilter('attribute_set_id',Mage::app()->getRequest()->getParam('set'))->addAttributeToFilter('title',$title);
            if(Mage::app()->getRequest()->getParam('id')) {
                $oggettos->addAttributeToFilter('entity_id',array('neq'=>Mage::app()->getRequest()->getParam('id')));
            }
            if(count($oggettos)) {
                throw new Exception($oggettos->getFirstItem()->getSetName().' with name "'.$title.'" already exists');
                return false;
            }
        }

        if(Mage::app()->getRequest()->getParam('region_id') && Mage::app()->getRequest()->getParam('school_by_region_id')) {
            $schools = Mage::getModel('score/oggetto')->getCollection()->addAttributeToFilter('entity_id',array('in'=>explode(',',Mage::app()->getRequest()->getParam('school_by_region_id') )))->addAttributeToSelect('region_id');
            $error = 0;
            foreach($schools as $school) {
                if($school->getRegionId() != Mage::app()->getRequest()->getParam('region_id')) {
                    $error++;
                }
            }
            if($error>0) {
                throw new Exception('One of schools have Region different than Region of Nurses group');
                return false;
            }

            $errors = array();
            foreach($schools as $school) {
                $id = $school->getEntityId();
                $otherGroups =  Mage::getModel('score/oggetto')->getCollection()
                    ->addAttributeToFilter('attribute_set_id',Mage::app()->getRequest()->getParam('set'))
                    ->addAttributeToFilter('entity_id',array('neq'=>Mage::app()->getRequest()->getParam('id')))
                    ->addAttributeToFilter('school_by_region_id',Mage::helper('score')->getLikeArray('school_by_region_id',$id))
                    ->addAttributeToSelect('title');
                $school = Mage::getModel('score/oggetto')->load($id);
                if(count($otherGroups)) {
                   $errors[] = 'School "'.$school->getTitle().'" already have group "'.$otherGroups->getFirstItem()->getTitle().'" assigned.';

                }
            }
            if(!empty($errors)) {
                throw new Exception(implode('<br/>',$errors));
                return false;
            }
        }

        if(Mage::app()->getRequest()->getParam('id') && Mage::app()->getRequest()->getParam('owner')) {
            $object = Mage::getModel('score/oggetto')->load(Mage::app()->getRequest()->getParam('id'));
            if($object->getAttributeSetId() == 24 && Mage::app()->getRequest()->getParam('owner') != $object->getOwner()) {
                $owner = Mage::getModel('customer/customer')->load(Mage::app()->getRequest()->getParam('owner'));
                try {
                Mage::getModel('score/oggetto')->sendMailByCode('Appointment assigned',$object,$owner);
                } catch(Exception $e) {}
            }
        }

}

//DELETE FROM  `score_oggetto_entity` WHERE attribute_set_id IN ( 24, 35)
    public function checkTriggers(Varien_Event_Observer $observer) {



        $object  = $observer->getEvent()->getData('oggetto');
        $setId = $object->getAttributeSetId();

        switch($setId) {
            case 25: // School entry
                if($object->getData('completed')==1) {
                    foreach($object->getData() as $key => $val) {
                    if(!is_array($val) && !is_object($val)) {
                        // echo $key.' = '.$val."<br/>";
                        $data[$key] = $val;
                    }
                }
                $pathways = array();

                $pathways['holistic_health'] = array('phisical_development','like_help_support','help_safety','help_emotional_health','help_about_diet','need_help_activity');
                $pathways['pre_assessment_caf'] = array('chronic_conditions','sensory_problems','common_problems','disabilities','unfamiliar_situation','return_to_pick','serios_injury','injury');
                $pathways['emotional_health'] = array('often_headaches','often_seems_worried','often_unhappy','nervious_in_new_situations','have_many_fears','often_tantrums','does_what_adults_request','often_fights','often_lies','steels_from_home','cannot_stay_for_long','squirming','easily_distracted','think_before_acting','good_attention_span','tends_to_play_alone','at_least_one_good_friend','generraly_liked_by_children','pickedon_by_others','better_adults_that_children');
                $pathways['followup_care'] = array('chronic_conditions','sensory_problems','common_problems','worries_safety','worries_or_concerns','any_disabilities','partner_disabilities','anyone_long_illness','worries_emotional_health','worries_about_diet','worries_about_activity');

            // missed first question in information
                $pathways['information'] = array('more_information','information_safety','information_emotional_health','information_about_diet','need_information_activity','any_following_information');

                $pathways['vision_screening'] = array('wear_glasses','sit_close','when_reading');
                $pathways['hearing_screening'] = array('hearing_problem','simple_verbal','phone_ringing');


                    foreach($pathways as $key => $attributes) {
                        $values = array();
                        foreach($attributes as $code) {
                            $values[$code] = @$data[$code];

                        }

                        $this->processTriggers($key,$values,$object);
                    }
                }
               break;
            case 29: // Year 6 Wellbeing Review
            case 30: // Year 6 Lifestyle Behaviour Review
                foreach($object->getData() as $key => $val) {
                    if(!is_array($val) && !is_object($val)) {
                  //      echo $key.' = '.$val."<br/>";
                        $data[$key] = $val;
                    }
                }
                $pathways = array();
            $pathways['holistic_health'] = array('phisical_development','concerns_bodychanging','like_help_support','help_safety','help_bullying','help_emotional_health','help_about_diet','need_help_activity','help_weight');
           $pathways['pre_assessment_caf'] = array('chronic_conditions','any_problems_child_six','disabilities','spendtimewithfamily','spendtimewithfriends','schooldayinterruped','becausecaringsomeoneathome','bullied_now','serios_injury','injury');
            $pathways['emotional_health'] = array('often_headaches','often_seems_worried','often_unhappy','nervious_in_new_situations','have_many_fears','often_tantrums','does_what_adults_request','often_fights','often_lies','steels_from_home','cannot_stay_for_long','squirming','easily_distracted','think_before_acting','good_attention_span','tends_to_play_alone','at_least_one_good_friend','generraly_liked_by_children','pickedon_by_others','better_adults_that_children');
            $pathways['followup_care'] = array('chronic_conditions','any_problems_child_six','worries_safety','worries_or_concerns','concerns_bullying','any_disabilities','anyone_long_illness','worries_emotional_health','worries_about_diet','worries_about_activity','concerns_weight','concerns_alcohol','worries_smoking','worries_drugs','worry_solvent','worry_moving_next_school');
//
            $pathways['information'] = array('more_information','information_safety','information_emotional_health','information_about_diet','need_information_activity','any_following_information','information_weight','worry_moving_next_school','information_bullying');


                foreach($pathways as $key => $attributes) {
                    $values = array();
                    foreach($attributes as $code) {
                        $values[$code] = $data[$code];

                    }
                    $this->processTriggers($key,$values,$object);
                }


                break;
            case 31: // Mid Teens Wellbeing Review
            case 32: // The Mid Teens Lifestyle Behaviour Review
            case 33: // The Further Education Wellbeing Review
            case 34: // Further Education Lifestyle Behaviour Review
                foreach($object->getData() as $key => $val) {
                    if(!is_array($val) && !is_object($val)) {
                 //       echo $key.' = '.$val."<br/>";
                        $data[$key] = $val;
                    }
                }
                $pathways = array();
            $pathways['holistic_health'] = array('student_number','student_number_activity','student_number_bullying','student_number_emotional','student_number_health_eating','student_number_safety','student_number_weight');
            foreach($pathways as $key => $attributes) {
                $values = array();
                foreach($attributes as $code) {
                    $values[$code] = $data[$code];

                }
                $this->processTriggers($key,$values,$object);
            }
                break;
            default:
                break;
        }

    }

    public function processTriggers($pathway,$attributes,$object) {

        $triggetWorks = 0;
        $score = 0;
        foreach($attributes as $key=>$value) {
            switch($key) {
                case 'information_weight':
                case 'worry_moving_next_school':
                case 'information_bullying':
                case 'concerns_alcohol':
                case 'worries_smoking':
                case 'worries_drugs':
                case 'worry_solvent':
                case 'worry_moving_next_school':
                case 'concerns_weight':
                case 'concerns_bodychanging':
                case 'help_bullying':
                case 'help_weight':
                case 'phisical_development':
                case 'like_help_support':
                case 'help_safety':
                case 'help_emotional_health':
                case 'help_about_diet':
                case 'need_help_activity':
                case 'serios_injury':
                case 'injury':
                case 'worries_safety':
                case 'worries_or_concerns':
                case 'any_disabilities':
                case 'disabilities':
                case 'partner_disabilities':
                case 'anyone_long_illness':
                case 'worries_emotional_health':
                case 'worries_about_diet':
                case 'worries_about_activity':
                case 'more_information':
                case 'information_safety':
                case 'concerns_bullying':
                case 'information_emotional_health':
                case 'information_about_diet':
                case 'need_information_activity':
                case 'bullied_now':
                    if($value == 1) $triggetWorks = 1;
                    break;
                case 'chronic_conditions':
                case 'any_problems_child_six':
                    if($object->getAttributeText('chronic_conditions')) { $count1 = count(explode(',',$object->getAttributeText('chronic_conditions'))); } else { $count1 = 0; }
                    if($object->getAttributeText('chronic_conditions_2')) { $count2 = count(explode(',',$object->getAttributeText('chronic_conditions_2'))); } else { $count2 = 0; }
                    if($object->getAttributeText('any_problems_child_six')) { $count3 = count(explode(',',$object->getAttributeText('any_problems_child_six'))); } else { $count3 = 0; }
                    if($count1+$count2 >= 1) $triggetWorks = 1;
                    if($count3 >= 1) $triggetWorks = 1;
                    break;
                case 'sensory_problems':
                case 'common_problems':

                case 'any_disabilities':
                    if(count(explode(',',$value))>=2) $triggetWorks = 1;
                    break;
                case 'any_following_information':
                    if($value) { if(count(explode(',',$value))>=1) $triggetWorks = 1; }
                    break;
                case 'unfamiliar_situation':
                case 'return_to_pick':
                     $pick = strtolower($object->getAttributeText('return_to_pick'));
                     $unf_sit = strtolower($object->getAttributeText('unfamiliar_situation'));
                     $unf_situation_points = array('highly distressed'=> 2, 'slightly upset'=> 1, 'very upset'=> 1, 'not upset'=> 2);
                    $pick_points = array('happy'=> 1, 'not bothered'=> 2, 'unsure/hesitant'=> 2, 'upset'=> 1);
                    $sum = $unf_situation_points[$unf_sit] + $pick_points[$pick];
                    if($sum>=4) { $triggetWorks = 1; }
                break;
                    case 'often_headaches':
                    case 'often_seems_worried':
                    case 'often_unhappy':
                    case 'nervious_in_new_situations':
                    case 'have_many_fears':
                    case 'often_tantrums':
                    case 'often_fights':
                    case 'often_lies':
                    case 'steels_from_home':
                    case 'cannot_stay_for_long':
                    case 'squirming':
                    case 'easily_distracted':
                    case 'tends_to_play_alone':
                    case 'pickedon_by_others':
                    case 'better_adults_that_children':
                    case 'others_feelings':
                    case 'shares_with_other_children':
                    case 'helpful_if_someone_is_hurt':
                    case 'kind_to_younger':
                    case 'often_volunteers_to_help':
                        if($value) $score+= $value;
                    break;
                    case 'does_what_adults_request':
                    case 'think_before_acting':
                    case 'good_attention_span':
                    case 'at_least_one_good_friend':
                    case 'generraly_liked_by_children':
                        if($value == 2) $value = 0;
                        elseif($value === 0) $value = 2;
                        if($value) $score+= $value;
                    break;
                case 'wear_glasses':
                case 'sit_close':
                case 'when_reading':
                    $wearGlasses = $object->getData('wear_glasses');
                    $sitClose =  $object->getData('sit_close');
                    $whenReading =  $object->getData('when_reading');
                    $points = 0;
                    if($wearGlasses) $points+=1;
                    if($sitClose)  { $points+=4; } elseif($sitClose===0) { $points+=1; }
                    if($whenReading)  { $points+=4; } elseif($whenReading===0) { $points+=1; }
                    if($points>=4) $triggetWorks = 1;
                    break;
                case 'hearing_problem':
                case 'simple_verbal':
                case 'phone_ringing':
                $hearing_problem = $object->getData('hearing_problem');
                $simple_verbal =  $object->getData('simple_verbal');
                $phone_ringing =  $object->getData('phone_ringing');
                $points = 0;
                if($hearing_problem) $points+=1;
                if($simple_verbal)  { $points+=1; } elseif($simple_verbal===0) { $points+=4; }
                if($phone_ringing)  { $points+=1; } elseif($phone_ringing===0) { $points+=4; }
                if($points>=4) $triggetWorks = 1;
                break;
                case 'spendtimewithfriends':
                case 'spendtimewithfamily':
                    $spendtimewithfriends = $object->getData('spendtimewithfriends');
                    $spendtimewithfamily =  $object->getData('spendtimewithfamily');
                    if($spendtimewithfamily) { $score+=1; } elseif($spendtimewithfamily===0) { $score+=2; }
                    if($spendtimewithfriends) { $score+=1; } elseif($spendtimewithfriends===0) { $score+=2; }
                    if($score==4) $triggetWorks = 1;
                    break;
                case 'schooldayinterruped':
                case 'becausecaringsomeoneathome':
                    $schooldayinterruped = $object->getData('schooldayinterruped');
                    $becausecaringsomeoneathome =  $object->getData('becausecaringsomeoneathome');
                    $points = 0;
                    if($schooldayinterruped) { $points+=2; } elseif($schooldayinterruped===0) { $points+=1; }
                    if($becausecaringsomeoneathome) { $points+=2; } elseif($schooldayinterruped===0) { $points+=1; }
                    if($points==4) $triggetWorks = 1;
                    break;
                case 'student_number':
                case 'student_number_activity':
                case 'student_number_bullying':
                case 'student_number_emotional':
                case 'student_number_health_eating':
                case 'student_number_safety':
                case 'student_number_weight':
                    if($value) $triggetWorks = 1;
                    break;
                default:
                    break;
            }
        }

        //        if($object->getData('set')==25) if($score >= 14) $triggetWorks = 1;
        //        else if($score >= 16) $triggetWorks = 1;

        if($score > 0) $triggetWorks = 1; // any bad answer cause trigger;

        if($triggetWorks) {
            $this->createAppointment($pathway,$object);
        }

    }

    public function createAppointment($pathway,$object,$appointmentData = 0,$initialActivityData = 0) {

if(!$appointmentData) {
        $select = Mage::getModel('score/oggetto')->getCollection()->getConnection()->query("SELECT COUNT(*) as count from score_oggetto_entity where attribute_set_id = 24")->fetch();
        $maxId = $select['count']+1;

        $assegnee = 0;
        $groups =  Mage::getModel('score/oggetto')->getCollection()
            ->addAttributeToFilter('attribute_set_id',20)->addAttributeToFilter(Mage::helper('score')->getLikeArray('school_by_region_id',Mage::getSingleton('customer/session')->getCustomer()->getSchoolId()))->addAttributeToSelect('assigned_uid');
        $nurses = array();
        foreach($groups as $group) {
            $group = Mage::getModel('score/oggetto')->load($group->getId());
            $users = explode(',',$group->getAssignedUid());
            foreach($users as $user) {
                if($user) {
                    $coll = Mage::getModel('customer/customer')->load($user);
                    $nurses[$coll->getGroupId()] = $coll->getId();
                }
            }
        }
       $assegnee = (isset($nurses[3]))?$nurses[3]:(isset($nurses[5])?$nurses[5]:(isset($nurses[2])?$nurses[2]:0));


        $appointmentData = array(
            'owner' => $assegnee,
            'attribute_set_id' => 24,
            'is_public' => '1',
            'visibility' => '1',
            'unique_id' => $maxId,
            'questionaryid' => $object->getAttributeSetId(),
            'pupil_id' =>  Mage::getSingleton('customer/session')->getCustomer()->getId(),
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

        $appointmentData['appointment_description'] = "Questionnaire name:  {$object->getSetName()}  <br/> Care pathway Name: ";

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
        $appointmentData['appointment_description'] .= '<br/> Pupil ID: '.Mage::getSingleton('customer/session')->getCustomer()->getUsername();
    }
        $appointment = new Shaurmalab_Score_Model_Oggetto();
        $appointment->setStoreId(0)->setId(0)->setTypeId('simple')->addData($appointmentData)->save();
                $owner = Mage::getModel('customer/customer')->load($appointment->getData('owner'));
          try {
                Mage::getModel('score/oggetto')->sendMailByCode('Appointment assigned',$appointment,$owner);
          } catch(Exception $e) {}
//                foreach($appointment->getData() as $key=>$val) {
//                    if(!is_array($val) && !is_object($val)) {
//                        echo $key.' = '.$val."<br/><br/>";
//                    }
//                }

                switch($pathway) {
            case 'holistic_health':
                $initialActivityData['appid'] = $appointment->getId();
                $swift = new Shaurmalab_Score_Model_Oggetto();
                $activityData = $initialActivityData;
                $activityData['activity_type'] = 'swift';
                $activityData['title'] = 'Swift Response (Within 2 Weeks) Revisit Questionnaire and explore data in all themes';
               // $activityData['exp_date'] = date('Y-m-d 00:00:00',strtotime(now() . " +2 weeks"));
                $activityData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($activityData);

        //        print_r($activityData);  echo "<br/><br/>";

                $swift->setStoreId(0)->setId(0)->setTypeId('simple')->addData($activityData)->save();
                $otherActivities = array(
                    'Holistic Health Interview (Face to Face Appointment)',
                    'Brief Intervention dependent on need/problem',
                    'Discharge back to universal services',
                    'Refer on to Specialist Services'
                );

                foreach($otherActivities as $title) {
                    $activityData = $initialActivityData;
                    $activity = new Shaurmalab_Score_Model_Oggetto();
                    $activityData['activity_type'] = '';
                    $activityData['title'] = $title;
                    $activityData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($activityData);
         //           print_r($activityData);  echo "<br/><br/>";

                    $activity->setStoreId(0)->setId(0)->setTypeId('simple')->addData($activityData)->save();
                }
                break;
            case 'pre_assessment_caf':

                $initialActivityData['appid'] = $appointment->getId();

                $swift = new Shaurmalab_Score_Model_Oggetto();
                $activityData = $initialActivityData;
                $activityData['activity_type'] = 'swift';
                $activityData['title'] = 'Swift Response (Within 1 Week) Revisit Questionnaire and explore data in all themes';

               // $activityData['exp_date'] = date('Y-m-d 00:00:00',strtotime(now() . " +1 week"));
                $activityData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($activityData);

            //    print_r($activityData);  echo "<br/><br/>";

                $swift->setStoreId(0)->setId(0)->setTypeId('simple')->addData($activityData)->save();

                $otherActivities = array(
                    'Pre-Assessment CAF or Risk Assessment (Face to Face Appointment)',
                    'Liaise with other agencies and collate further information',
                    'Brief Intervention dependent on need/problem',
                    'Discharge back to universal services',
                    'Complete Full CAF'
                );

                foreach($otherActivities as $key => $title) {
                    $activityData = $initialActivityData;
                    $activity = new Shaurmalab_Score_Model_Oggetto();
                    $activityData['activity_type'] = (!is_integer($key))?$key:'';
                    $activityData['title'] = $title;
                    $activityData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($activityData);
             //       print_r($activityData);  echo "<br/><br/>";

                    $activity->setStoreId(0)->setId(0)->setTypeId('simple')->addData($activityData)->save();
                }
                break;
            case 'emotional_health':
                $initialActivityData['appid'] = $appointment->getId();

                $swift = new Shaurmalab_Score_Model_Oggetto();
                $activityData = $initialActivityData;
                $activityData['activity_type'] = 'swift';
                $activityData['title'] = 'Swift Response (Within 2 Weeks) Revisit Questionnaire and explore data in all themes';

              //  $activityData['exp_date'] = date('Y-m-d 00:00:00',strtotime(now() . " +2 weeks"));
                $activityData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($activityData);

            //    print_r($activityData);  echo "<br/><br/>";

                $swift->setStoreId(0)->setId(0)->setTypeId('simple')->addData($activityData)->save();

                $otherActivities = array(
                    'Repeat SDQ (Face to Face Appointment)',
                    'Liaise with other agencies and collate further information',
                    'Brief Intervention dependent on need/problem',
                    'Discharge back to universal services',
                    'Refer to CAMHS'
                );

                foreach($otherActivities as $key => $title) {
                    $activityData = $initialActivityData;
                    $activity = new Shaurmalab_Score_Model_Oggetto();
                    $activityData['activity_type'] = (!is_integer($key))?$key:'';
                    $activityData['title'] = $title;
                    $activityData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($activityData);
            //        print_r($activityData);  echo "<br/><br/>";

                    $activity->setStoreId(0)->setId(0)->setTypeId('simple')->addData($activityData)->save();
                }
                break;
            case 'followup_care':
                $initialActivityData['appid'] = $appointment->getId();

                $swift = new Shaurmalab_Score_Model_Oggetto();
                $activityData = $initialActivityData;
                $activityData['activity_type'] = 'swift';
                $activityData['title'] = 'Swift Response (Within 4 Weeks)';

             //   $activityData['exp_date'] = date('Y-m-d 00:00:00',strtotime(now() . " +4 weeks"));
                $activityData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($activityData);

            //    print_r($activityData);  echo "<br/><br/>";

                $swift->setStoreId(0)->setId(0)->setTypeId('simple')->addData($activityData)->save();

                $otherActivities = array(
                    'Check Records',
                    'Speak to Parents',
                    'Liase With School',
                    'Liase With Professionals',
                    'Discharge Back to Universal Services',
                    'Brief Intervention dependent on need/problem',
                    'Refer on to Specialist Services'

                );

                foreach($otherActivities as $key => $title) {
                    $activityData = $initialActivityData;
                    $activity = new Shaurmalab_Score_Model_Oggetto();
                    $activityData['activity_type'] = (!is_integer($key))?$key:'';
                    $activityData['title'] = $title;
                    $activityData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($activityData);
             //       print_r($activityData);  echo "<br/><br/>";

                    $activity->setStoreId(0)->setId(0)->setTypeId('simple')->addData($activityData)->save();
                }
                break;
            case 'information':
                $initialActivityData['appid'] = $appointment->getId();

                $swift = new Shaurmalab_Score_Model_Oggetto();
                $activityData = $initialActivityData;
                $activityData['activity_type'] = 'swift';
                $activityData['title'] = 'Swift Response (Within 6 Weeks)';

              //  $activityData['exp_date'] = date('Y-m-d 00:00:00',strtotime(now() . " +6 weeks"));
                $activityData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($activityData);

             //   print_r($activityData);  echo "<br/><br/>";

                $swift->setStoreId(0)->setId(0)->setTypeId('simple')->addData($activityData)->save();

                $otherActivities = array(
                    'Send Out Leaflet',
                    'Signpost to Website',
                    'Liaise With School',
                    'Liase With Professionals',
                    'Alerts Feed in to Evidence Development of Public Health Plan'
                );

                foreach($otherActivities as $key => $title) {
                    $activityData = $initialActivityData;
                    $activity = new Shaurmalab_Score_Model_Oggetto();
                    $activityData['activity_type'] = (!is_integer($key))?$key:'';
                    $activityData['title'] = $title;
                    $activityData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($activityData);
            //        print_r($activityData);  echo "<br/><br/>";

                    $activity->setStoreId(0)->setId(0)->setTypeId('simple')->addData($activityData)->save();
                }
                break;
            case 'vision_screening':
                $initialActivityData['appid'] = $appointment->getId();

                $swift = new Shaurmalab_Score_Model_Oggetto();
                $activityData = $initialActivityData;
                $activityData['activity_type'] = 'swift';
                $activityData['title'] = 'Swift Response (Within 4 Weeks)';

             //   $activityData['exp_date'] = date('Y-m-d 00:00:00',strtotime(now() . " +4 weeks"));
                $activityData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($activityData);

              //  print_r($activityData);  echo "<br/><br/>";

                $swift->setStoreId(0)->setId(0)->setTypeId('simple')->addData($activityData)->save();

                $otherActivities = array(
                    'Carry out vision test',
                    'Send letter advising visit to Optician',
                    'Discharge back to Universal Services',
                );

                foreach($otherActivities as $key => $title) {
                    $activityData = $initialActivityData;
                    $activity = new Shaurmalab_Score_Model_Oggetto();
                    $activityData['activity_type'] = (!is_integer($key))?$key:'';
                    $activityData['title'] = $title;
                    $activityData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($activityData);
               //     print_r($activityData);  echo "<br/><br/>";

                    $activity->setStoreId(0)->setId(0)->setTypeId('simple')->addData($activityData)->save();
                }
                break;
            case 'hearing_screening':
                $initialActivityData['appid'] = $appointment->getId();

                $swift = new Shaurmalab_Score_Model_Oggetto();
                $activityData = $initialActivityData;
                $activityData['activity_type'] = 'swift';
                $activityData['title'] = 'Swift Response (Within 4 Weeks)';

              //  $activityData['exp_date'] = date('Y-m-d 00:00:00',strtotime(now() . " +4 weeks"));
                $activityData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($activityData);

            //    print_r($activityData);  echo "<br/><br/>";

                $swift->setStoreId(0)->setId(0)->setTypeId('simple')->addData($activityData)->save();

                $otherActivities = array(
                    'Carry out Hearing Screening Test',
                    'Send referral to Audiologist',
                    'Discharge back to Universal Services',
                );

                foreach($otherActivities as $key => $title) {
                    $activityData = $initialActivityData;
                    $activity = new Shaurmalab_Score_Model_Oggetto();
                    $activityData['activity_type'] = (!is_integer($key))?$key:'';
                    $activityData['title'] = $title;
                    $activityData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($activityData);
              //      print_r($activityData);  echo "<br/><br/>";

                    $activity->setStoreId(0)->setId(0)->setTypeId('simple')->addData($activityData)->save();
                }
                break;
            default:
                break;

        }
        $child = Mage::getModel('score/oggetto')->getCollection()->addAttributeToFilter('appid',$appointment->getId())->getColumnValues('entity_id');
       // print_r($child);
        foreach($child as $k=>$v) {
            $child[$k] = $v.'=';
        }
        $child =  implode('&',$child);
        $appointment->setRelatedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($child))->save();
    }
}
