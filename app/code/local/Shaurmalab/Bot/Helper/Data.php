<?php

class Shaurmalab_Bot_Helper_Data extends Mage_Core_Helper_Data {

    public function getChatHistory($conversationId, $date) {
        $read =  Mage::getSingleton('core/resource')->getConnection('core/read');
        $query = "SELECT * FROM conversation_log where convo_id = '{$conversationId}' and userdate like '{$date}%' order by userdate";
        return $read->query($query)->fetchAll();
    }

    public function buildCardResponse($dataArray) {
        $html = '';

        foreach ($dataArray as $key => $data) {
            $html .= Mage::app()->getLayout()->createBlock('core/template')->setData($data)->setTemplate('bot/card-response.phtml')->toHtml();
        }

        return $html;
    }

    public function buildGalleryResponse($dataArray) {
        $html = '<div class="w100p">' . $this->buildCardResponse($dataArray) . '</div>';

        return $html;
    }

    public function convertDate($date, $format = 'Y-m-d h:i:s') {
        if ($timezone = Mage::getModel('core/cookie')->get('tz'))
        {
            //at this point, you have the users timezone in your session
            $timestamp = strtotime($date);

            $dt = new DateTime();
            $dt->setTimestamp($timestamp);

            $dt->setTimezone(new DateTimeZone($timezone));
            $userdate = $dt->format($format);

            $date = $userdate;
        }

        return $date;
    }
}
