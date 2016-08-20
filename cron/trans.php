<?php
/**
 *  * Error reporting
 *   */
error_reporting(E_ALL | E_STRICT);

require_once '/Users/pavelivanov/www/shaurmalab.local/app/Mage.php';

if (!Mage::isInstalled()) {
          echo "Application is not installed yet, please complete install wizard first.";
                  exit;
}

$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', @$_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', @$_SERVER['SCRIPT_FILENAME']);

Mage::app('admin')->setUseSessionInUrl(false);

if ($_GET['f']) {
      $formatCode = $_GET['f'];
} else {
      $options = getopt('f:');
          $formatCode = @$options['f'];
}

if (!$formatCode){
      echo 'Set format name, please! Ex: ../dev-transformer.php?f=test_format';
          exit;
}

try {
      $storeId = 0;
          $xmlformatModel = Mage::getModel('cpwms/xmlformat_format_transformer')
                    ->getModelformatByName($formatCode,$storeId);

          if(!$xmlformatModel || !$xmlformatModel->getId()){
                    echo 'Format with name "'.$formatCode.'" - not found!';
                            exit;
                        }

      //    $xmlformatModel->addVariable('merchandise_id', $product->getCpPtn());
      //
echo $xmlformatModel->getData('xmlformat[url_request]');
      $xmlformatModel->processRequest();
                $xmlResult = $xmlformatModel->getServerResponse();
      //
                      echo 'Ressponse:';
                          Zend_Debug::dump($xmlResult);
      //
                              $result = $xmlformatModel->processResponse();
      //
                                  echo 'Result:';
                                      Zend_Debug::dump($result);
      //
                                      } catch (Exception $e) {
                                      //    Mage::log($e->getMessage(),null,'exception.log');
                                          echo 'Exception:'.$e->getMessage();
                                          }
