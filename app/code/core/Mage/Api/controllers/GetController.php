<?php
class Mage_Api_GetController extends Mage_Api_Controller_Action
{
    public function randomAction()
    {
        $params = $this->getRequest()->getParams();
        $type = @$params['t'];
        $owner = @$params['o'];
        $limit = @$params['l'];
        $format = @$params['f'];
        $attr = @$params['attr'];

        if(!$format) $format = 'json';
        if(!$limit) $limit = 1;


        $oggettos = Mage::getModel('score/oggetto')->getCollection()
            ->addAttributeToSelect('*')
        ;

        if($owner) {
            $oggettos->addAttributeToFilter('owner',$owner);
        } else {
            $oggettos->addAttributeToFilter('is_public','1');
        }

        foreach(explode('_',$attr) as $attribute) {
            list($key,$value) = explode('-',$attribute);
            if($key && $value) {
                $oggettos->addAttributeToSelect($key, 'left');
                $nodeChildren = Mage::getResourceModel('score/oggetto_attribute_collection')
                    ->addFieldToFilter('attribute_code',$key)
                    ->addVisibleFilter()
                    ->load();
                $attribute = null;
                if ($nodeChildren->getSize() > 0) {
                    foreach ($nodeChildren->getItems() as $child) {
                        $attr = $child;
                        break;
                    }
                }
                $labels = array();
                if($attr->usesSource()) {
                    $options = $attr->getSource()->getAllOptions();
                    $labels = array();
                    foreach ($options as $option):
                       if($value == $option['label']) {
                           $oggettos->addAttributeToFilter($key, $option['value']);
                           break;
                       }
                    endforeach;

                } else {
                    $oggettos->addAttributeToFilter($key, $value);
                }
            }
        }

        if($type) {
            $set = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter(Mage::getModel('score/oggetto')->getResource()->getTypeId())
                ->addFieldToFilter('attribute_set_name',$type)
                ->getFirstItem();
            
            $oggettos->addAttributeToFilter('attribute_set_id',$set->getId());
        }
        $oggettos->getSelect()->order(new Zend_Db_Expr('RAND()')); 
        $oggettos->setPageSize($limit)->setCurPage(1);


        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
 if($format == 'json') {
            if($limit == 1) {
                  echo json_encode(array_map('strip_tags',$oggettos->getFirstItem()->getData()));
                //echo Mage::helper('core')->jsonEncode($oggettos->getFirstItem()->getData());
            } else {
                $data = array();
                foreach($oggettos as $ogg) {
                        $data[] = array_map('strip_tags',$ogg->getData());
                }
            echo Mage::helper('core')->jsonEncode(array('quotes'=>$data));
                }
        } elseif($format == 'text') {
            echo $oggettos->getFirstItem()->getText()."<i class='pull-right darkgrey'> ".$oggettos->getFirstItem()->getQuoteAuthor().'</i>';
        }

    }

    public function latestAction($type, $format = 'json')
    {
        $params = $this->getRequest()->getParams();
        $type = @$params['t'];
        $owner = @$params['o'];
        $limit = @$params['l'];
        $format = @$params['f'];

        if(!$format) $format = 'json';
        if(!$limit) $limit = 1;


        $oggettos = Mage::getModel('score/oggetto')->getCollection()
            ->addAttributeToSelect('*')
        ;

        if($owner) {
            $oggettos->addAttributeToFilter('owner',$owner);
        } else {
            $oggettos->addAttributeToFilter('is_public','1');
        }

        if($type) {
            $set = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter(Mage::getModel('score/oggetto')->getResource()->getTypeId())
                ->addFieldToFilter('attribute_set_name',$type)
                ->getFirstItem(); // TODO: add filter by owner when needed
            echo $set->getSelect();

            $oggettos->addAttributeToFilter('attribute_set_id',$set->getId());
        }
        $oggettos->setPageSize($limit)->setCurPage(1);
        $oggettos->getSelect()->order('e.entity_id DESC');

        if($format == 'json') {
            if($limit == 1) {
                echo Mage::helper('core')->jsonEncode(array_map('strip_tags',$oggettos->getFirstItem()->getData()));
            } else {
                 $data = array();
                foreach($oggettos as $ogg) {
                        $data[] = array_map('strip_tags',$ogg->getData());
                }
                
echo Mage::helper('core')->jsonEncode(array('quotes'=>$data));
            }
        }
    }




}
