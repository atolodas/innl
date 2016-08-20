<?php

class Shaurmalab_Events_Model_Observer
{


    public function oggettoCreatedEvents(Varien_Event_Observer $observer) {
     	$mainOggetto  = $observer->getEvent()->getData('oggetto');

      $events = Mage::getModel('events/events')->getCollection()
      ->addFieldToFilter('event_type','oggetto_created')
      ->addFieldToFilter('oggetto_type',$mainOggetto->getAttributeSetId());

      foreach($events as $event) {

        $data = $event->getData();

        switch($data['todo']) {
            case 'create_bulk':

              $newOggettos = array();
              $attribute = $data['changed_attribute'];
              $newOggettoSet = $data['new_oggetto_type'];
              $attributes = explode(';',$data['attributes_values']);

              $oggettoData = array(
                'attribute_set_id' => $newOggettoSet,
                'is_public' => '1',
                'visibility' => '1',
                'type_id' => Shaurmalab_Score_Model_Oggetto_Type::DEFAULT_TYPE
              );

              $createNewForEach = array();
              foreach($attributes as $attr) {
                list($old,$new) = explode('-',$attr);
                if(substr_count($new,'each|')!=0) {

                  /* get attribute labels */
                  $attr = Mage::getResourceSingleton('score/oggetto')->getAttribute($old);
                  $labels = array();
                  if($attr->usesSource()) {
                    $options = $attr->getSource()->getAllOptions();
                    $labels = array();
                    foreach ($options as $option):
                      $labels[$option['value']] = $option['label'];
                    endforeach;
                  }
                  /* end get attribute labels */
                  list($each,$new) = explode('|',$new);
                  foreach(explode(',',$mainOggetto->getData($new)) as $eachVal) {
                      if($new && $eachVal) $createNewForEach[$new][$eachVal] = $eachVal;
                  }
                } else {

                if(substr_count($new,"'")>0) { $oggettoData[$new] = $old; }
                else {
                  $oggettoData[$new] = $mainOggetto->getData($old);
                  }
                }
                }
              $newOggettos = array();

              foreach($createNewForEach as $attribute => $val) {
               foreach($val as $k=>$v) {
                $oggettoData['name'] = $labels[$v]; unset($oggettoData['sku']); // doing it to create different skus fast. TODO: find more inteligent way.
                $oggettoData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($oggettoData);

                  $oggettoData[$attribute] = $v;

                  $oggetto = new Shaurmalab_Score_Model_Oggetto();
                  $oggetto->setStoreId(0)
                  ->setId(0)
                  ->setTypeId('simple')
                  ->addData($oggettoData)
                  ->save();
                 $newOggettos[] = $oggetto->getId().'=';
                  $oggetto = null;

               }
              }


              $newOggettos = implode('&',$newOggettos);
              $mainOggetto->setRelatedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($newOggettos))->save();
              //die;
             //  die;
            break;
            default:
            break;
        }

        //die;
      }
    }

    public function oggettoUpdatedEvents(Varien_Event_Observer $observer) {
     	$oggetto  = $observer->getEvent()->getData('oggetto');

       if($oggetto->getId()) { // means that not new oggetto

       }
    }


}
