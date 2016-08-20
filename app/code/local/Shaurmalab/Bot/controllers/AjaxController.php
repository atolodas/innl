<?php

class Shaurmalab_Bot_AjaxController extends Mage_Core_Controller_Front_Action {

    protected $_isLayoutLoaded = true;
    /**
     * Predispatch: should set layout area
     *
     * @return Mage_Core_Controller_Front_Action
     */
    public function preDispatch()
    {
        $this->getLayout()->setArea($this->_currentArea);
        return $this;
    }

    /**
     * Postdispatch: should set last visited url
     *
     * @return Mage_Core_Controller_Front_Action
     */
    public function postDispatch()
    {
        return $this;
    }

    public function historyAction() {
        $date = Mage::app()->getRequest()->getParam('date');
        $convo_id =  Mage::app()->getRequest()->getParam('convo');
        if($date && $convo_id) {
            Mage::app()->getResponse()->setBody(json_encode(array('messages'=>Mage::helper('bot')->getChatHistory($convo_id, $date))));
            return;
        }

        Mage::app()->getResponse()->setBody(json_encode(array('messages'=>array())));
            $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
    }

    public function autocompleteAction() {
        $query = $this->getRequest()->getParam('query');
        $contains_cyrillic = (bool) preg_match('/[\p{Cyrillic}]/u', $query);
        $availableCommands = array();

        if($contains_cyrillic) {
            $availableCommands = array('/погода', '/создать', '/заметка');
        } else {
            $availableCommands = array('/search', '/weather', '/new', '/note');
        }

        $availableOptions = array();
        foreach ($availableCommands as $command) {
            if(substr_count($command, $query)) $availableOptions[] =  array('value' => $command . " ", 'data' => $command, 'note' => 'Hello bro!');
        }

        $result =   array(
                        "query" => $query,
                        'suggestions' => $availableOptions
                    );

        $JSONresponse =  Mage::helper('core')->jsonEncode($result);

        $this->getResponse()->setBody($JSONresponse);
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
    }

    public function messageAction() {
        $params = $this->getRequest()->getPost();
        $coreHelper = Mage::helper('core');
        $contains_cyrillic = (bool) preg_match('/[\p{Cyrillic}]/u', $params['say']);

        if(!$contains_cyrillic) {
            $accessToken = "6904327edca24fb9ba1f768c0fe4a58f";
            $lang = 'en';
        } else {
            $accessToken = "eceb55cabb7b4953853ffbacab0285d6";
            $lang = 'ru';
        }

        $headers = array(
            "authorization" => "Bearer " . $accessToken,
            'content-type' => 'application/json; charset=utf-8'
        );

        $url = Mage::getBaseUrl() . "/ibot/chatbot/conversation_start.php";
        $config = array(
            'timeout'         => 60
        );
        $client = new Zend_Http_Client($url, $config);
        $client->setMethod(Zend_Http_Client::POST);
        $params['tz'] = Mage::getModel('core/cookie')->get('tz');
        $client->setParameterPost($params);
        $JSONresponse = $client->request()->getBody();
        $arrayResponse = $coreHelper->jsonDecode($JSONresponse);

        if(preg_match('/\/(.*)/', $params['say'], $matches)) {
            $command = explode(' ', $matches[0])[0];
            $body = str_replace($command, '', $matches[0]);
            switch ($command) {
                case '/search':
                case '/weather':
                case '/погода':
		default:
        $client = new Zend_Http_Client('https://api.api.ai/v1/query?v=20150910', $config);
                    $client->setHeaders($headers);
                    $client->setMethod(Zend_Http_Client::POST);
                    $client->setRawData($coreHelper->jsonEncode(array('q'=>$body, 'lang' => $lang)));

                    $response = $client->request()->getBody();
                    $responseArray = $coreHelper->jsonDecode($response);


                    if(isset($responseArray['result']['fulfillment']['speech']) && $responseArray['result']['fulfillment']['speech']) {
                        $response = $responseArray['result']['fulfillment']['speech'];
                        $write = Mage::getSingleton('core/resource')->getConnection('core/write');
                        $query = "UPDATE conversation_log SET response = :response where convo_id = :convo_id and userdate = :timestamp";
                        $binds = array(
                                'response' => $response,
                                'convo_id' => $arrayResponse['convo_id'],
                                'timestamp' => $arrayResponse['timestamp']
                        );
                        $write->query($query, $binds);

                        $arrayResponse['botsay'] = $response;
                    }
                    break;
            }
        } else {



            if($arrayResponse['botsay'] == 'Sorry, I have nothing to say here.' && str_word_count($arrayResponse['usersay']) > 2  && strlen($arrayResponse['usersay']) > 5) {
                // if(str_word_count($arrayResponse['usersay']) > 2 && strlen($arrayResponse['usersay']) >= 10) {
                //     $client = new Zend_Http_Client(Mage::getBaseUrl().'wolfram/q.php', $config);
                //     $client->setMethod(Zend_Http_Client::POST);
                //     $client->setParameterPost(array('q' => $arrayResponse['usersay']));
                //     $response = $client->request()->getBody();
                //     if($response) {
                //         $html = Mage::helper('bot')->buildGalleryResponse(Mage::helper('core')->jsonDecode($response));
                //
                //         $html .= $this->__("I'm sorry, I can not find answer by myself. But here are some results from my bot-friends. Hope you'll find it useful. <br/> ") . $html;
                //
                //         $write = Mage::getSingleton('core/resource')->getConnection('core/write');
                //         $query = "UPDATE conversation_log SET response = :html where convo_id = :convo_id and timestamp = :timestamp";
                //         $binds = array(
                //                 'html' => $html,
                //                 'convo_id' => $arrayResponse['convo_id'],
                //                 'timestamp' => $arrayResponse['timestamp']
                //         );
                //         $write->query($query, $binds);
                //
                //         $arrayResponse['botsay'] = $html;
                //     }
                // }


                $client = new Zend_Http_Client('https://api.api.ai/v1/query?v=20150910', $config);
                $client->setHeaders($headers);
                $client->setMethod(Zend_Http_Client::POST);
                $client->setRawData($coreHelper->jsonEncode(array('q'=>$arrayResponse['usersay'], 'lang' => $lang)));

                $response = $client->request()->getBody();
                $responseArray = $coreHelper->jsonDecode($response);
                if(isset($responseArray['result']['fulfillment']['speech']) && $responseArray['result']['fulfillment']['speech']) {
                    $response = $responseArray['result']['fulfillment']['speech'];
                    $write = Mage::getSingleton('core/resource')->getConnection('core/write');
                    $query = "UPDATE conversation_log SET response = :response where convo_id = :convo_id and userdate = :timestamp";
                    $binds = array(
                            'response' => $response,
                            'convo_id' => $arrayResponse['convo_id'],
                            'timestamp' => $arrayResponse['timestamp']
                    );
                    $write->query($query, $binds);

                    $arrayResponse['botsay'] = $response;
                }

            }
        }
        $arrayResponse['botsay'] = $this->__($arrayResponse['botsay']); //Translation goes here
        $JSONresponse =  $coreHelper->jsonEncode($arrayResponse);

        $this->getResponse()->setBody($JSONresponse);
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
    }

    public function saveUserTimezoneAction() {
        Mage::getModel('core/cookie')->set('tz', Mage::app()->getRequest()->getParam('timezone'));
        return;
    }


}
