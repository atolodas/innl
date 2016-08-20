<?php
  include './wa_wrapper/WolframAlphaEngine.php';
  $queryIsSet = isset($_POST['q']);
  if ($queryIsSet) {
      $appID = 'JXU5GL-5KJW69QR22';

      $qArgs = array();
      if (isset($_REQUEST['assumption'])) {
          $qArgs['assumption'] = $_REQUEST['assumption'];
      }

      // instantiate an engine object with your app id
      $engine = new WolframAlphaEngine($appID);

      // we will construct a basic query to the api with the input 'pi'
      // only the bare minimum will be used
      $response = $engine->getResults($_REQUEST['q'], $qArgs);

      // getResults will send back a WAResponse object
      // this object has a parsed version of the wolfram alpha response
      // as well as the raw xml ($response->rawXML)

      // we can check if there was an error from the response object
      if ($response->isError()) {
          return false;
      } else {
          if (count($response->getPods()) > 0) {
              $responseArr = array();
              foreach ($response->getPods() as $pod) {
                  if(!in_array($pod->attributes['title'], array('Input interpretation', 'Wikipedia page hits history', 'Nicknames'))) {
                      foreach ($pod->getSubpods() as $subpod) {
                          $responseArr[] = array('title' => $pod->attributes['title'], 'image' => $subpod->image->attributes['src']);
                      }
                   }
              }
              echo json_encode($responseArr);
          }
      }
  }
