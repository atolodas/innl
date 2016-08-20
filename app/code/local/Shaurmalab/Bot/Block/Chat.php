<?php

class Shaurmalab_Bot_Block_Chat extends Shaurmalab_Bot_Block_Abstract
{
    public function getChatHistory($conversationId, $date) {
        return Mage::helper('bot')->getChatHistory($conversationId, $date);
    }

    public function getChatDates($conversationId) {
        $read =  Mage::getSingleton('core/resource')->getConnection('core/read');
        $query = "SELECT DATE_FORMAT(userdate, '%Y-%m-%d') as date FROM conversation_log where convo_id = '{$conversationId}' group by date order by userdate";
        return $read->query($query)->fetchAll();
    }
}
