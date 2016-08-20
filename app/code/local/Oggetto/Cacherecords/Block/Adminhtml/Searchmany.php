<?php
/**
 * Select grid column filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Oggetto_Cacherecords_Block_Adminhtml_Searchmany extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Text
{


//    public function getCondition()
//    {
//        if (is_null($this->getValue())) {
//            return null;
//        }
//
//
//        $value = $this->getValue();
//        $contain = $value[0];
//        $notcontain = $value[1];
//        $endsWith = $value[2];
//
//        if($contain) {
//            $contain = explode(',', $contain);
//            $searchArray1 = array();
//            foreach ($contain as $val) {
//                $searchArray1[] = array('like' => '%' . $val . '%');
//            }
//            $searchArray[] = array($searchArray1);
//        }
//
//        if($notcontain) {
//            $notcontain = explode(',', $notcontain);
//            $searchArray2 = array();
//            foreach ($notcontain as $val) {
//                $searchArray2[] = array('nlike' => '%' . $val . '%');
//            }
//            $searchArray[] = array($searchArray2);
//        }
//
//        if($endsWith) {
//            $endsWith = explode(',', $endsWith);
//            $searchArray3 = array();
//            foreach ($endsWith as $val) {
//                $searchArray3[] = array('like' => '%' . $val);
//            }
//            $searchArray[] = array($searchArray3);
//        }
//
//
//        //print_r($searchArray); echo "<br/>";
//        return $searchArray;
//
//    }

    public function getHtml()
    {
        $val = $this->getValue();
        if(!$val) { $val = array('skip','skip','skip'); }
        $html = '<div class="field-100">
    Contain <input type="text" name="'.$this->_getHtmlName().'[]" id="'.$this->_getHtmlId().'" value="'.(($val[0])?$val[0]:'skip').'" class="input-text no-changes"/> <br/>
       AND doesn\'s contain <input type="text" name="'.$this->_getHtmlName().'[]" id="'.$this->_getHtmlId().'" value="'.(($val[1])?$val[1]:'skip').'" class="input-text no-changes"/> <br/>
         AND Ends with <input type="text" name="'.$this->_getHtmlName().'[]" id="'.$this->_getHtmlId().'" value="'.(($val[2])?$val[2]:'skip').'" class="input-text no-changes"/> <br/>

    </div>';
        return $html;
    }

}

