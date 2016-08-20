<?php

class Cafepress_CPWms_Adminhtml_ConditionsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction(){
        $html = "";
        $format_id = json_decode($this->getRequest()->getParam('format_id'));
        if($format_id){
            $conditions = Mage::getModel('cpwms/xmlformat_format_order')->divideCondition($format_id);
            if($conditions){
                $html .= "<table class='condition_table' cellpadding='0' cellspacing='0' border='1px'>";
                $html .= "<tr>";
                $html .= "<th>";
                $html .= Mage::helper('cpwms')->__('ID');
                $html .= "</th>";
                foreach($conditions as $header){
                    $html .= "<th>".$header."</th>";
                }
                $html .= "<th>".Mage::helper('cpwms')->__('Total')."</th>";
                $html .= "</tr>";
                foreach(Mage::getModel('cpwms/xmlformat_format_order')->getOrders() as $order){
                    $html .= "<tr>";
                    $html .= "<td>".$order->getIncrementId()."</td>";
                    foreach($conditions as $condition){
                        $html .= "<td>";
                        if(!in_array($condition, array('AND', 'and', 'OR', 'or', '||', '&&'))){
                            $html .= Mage::getModel('cpwms/xmlformat_format_order')->getConditionForOrder('{{cond '.$condition.'}}', $order, $format_id);
                        } else{
                            $html .= $condition;
                        }
                        $html .= "</td>";
                    }
                    $html .= "<td>".Mage::getModel('cpwms/xmlformat_format_order')->getTotalConditionForOrder(Mage::getModel('cpwms/xmlformat')->load($format_id)->getCondition(), $order, $format_id)."</td>";
                    $html .= "</tr>";
                }
                $html .= "</table>";
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('message' => $html)));
        return;
    }
}