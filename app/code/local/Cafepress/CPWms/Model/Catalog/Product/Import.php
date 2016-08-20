<?php

class Cafepress_CPWms_Model_Catalog_Product_Import extends Mage_Eav_Model_Convert_Adapter_Entity
{
 public $parser;

 public function importProductsData() {
  try {
   $importFolder  = Mage::getBaseDir('media').'/xmls/products/';
   $folders = scandir($importFolder);
   foreach($folders as $folder) {
    $fullPath = $importFolder.$folder;
    if(is_dir($fullPath) and !in_array($folder,array('','.','..','.svn'))) {
     $files = scandir($fullPath);
     foreach($files as $file) {
      $filePath = $fullPath.'/'.$file;
      if(!is_dir($filePath)) {
       $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
       if($fileExtension=='xml' && substr_count($file,'_CHECKED')==0) {
        echo $filePath.'<br/>';
        $this->processFile($filePath,$folder);
       }
      }
     }
    }
   }
  } catch (Exception $e) {
   echo $e->getMessage();
  }
 }

 public function cronJob() {
  //   		Mage::getModel('cpwms/catalog_product_import')->importProductsData();
  //		Mage::getModel('cpwms/catalog_product_import')->getInventoryAdvice();
  echo "Start cron";
  echo "Process acknowledgment";
  $files = array();
  $files = Mage::getModel('cpwms/order_observer')->getFiles($files,'Orderck','acknowledgment');
  Mage::getModel('cpwms/order_observer')->parseAndDeleteAknowledgment($files);

  echo "process shipment";
  $files = array();
  $files = Mage::getModel('cpwms/order_observer')->getFiles($files,'SN','shipment');
  Mage::getModel('cpwms/order_observer')->parseAndDeleteShipping($files);

  return true;
 }

 public function printError() {
  Mage::log(
  sprintf(	"XML Error: %s at line %d",xml_error_string(xml_get_error_code($this->parser)), xml_get_current_line_number($this->parser)),
  null,
		'productImport.log'
		);
 }

 public function parseXml($file) {
  //createxml parser object
  $this->parser= xml_parser_create();
  //this option ensures that unneccessary white spaces
  //between successive elements would be removed
  xml_parser_set_option($this->parser,XML_OPTION_SKIP_WHITE,1);
  //to use XML code correctly we have to turn case folding
  //(uppercasing) off. XML is case sensitive and upper
  //casing is in reality XML standards violation
  xml_parser_set_option($this->parser,XML_OPTION_CASE_FOLDING,0);
  //read XMLfile into $data
  $data= implode("",file($file));
  //parse XML input $data into two arrays:
  //$i_ar - pointers to the locations of appropriate values in
  //$d_ar - data value array
  $data = str_replace('&','amp;',$data);
  xml_parse_into_struct($this->parser,$data,$d_ar,$i_ar) or $this->printError();
  return array($d_ar,$i_ar);
 }

 public function processFile($file, $vendorId) { // Folder name = id of vendor

 $parsed = $this->parseXml($file);
 $age_attribute =  Mage::getModel('eav/config')->getAttribute('catalog_product','age');
 $options = $age_attribute->getFrontend()->getSelectOptions();
 $d_ar = $parsed[0];
 $i_ar = $parsed[1];

 $brand_attribute =  Mage::getModel('eav/config')->getAttribute('catalog_product','manufacturer_brand');
 $options = $brand_attribute->getFrontend()->getSelectOptions();
  
 $data = array();
 $brands = array();
 foreach ($options as $option) {
  if (Mage::helper('core/string')->strlen($option['value'])) {
   $brands[$option['value']] = $option['label'];
  }
 }
  
 $age_attribute =  Mage::getModel('eav/config')->getAttribute('catalog_product','age');
 $options = $age_attribute->getFrontend()->getSelectOptions();
  
 $data = array();
 $ages = array();
 foreach ($options as $option) {
  if (Mage::helper('core/string')->strlen($option['value'])) {
   $ages[$option['value']] = $option['label'];
  }
 }
 $counter = 0;
 $categories = $this->parseCategories();
 //$i_ar['item'] contains all pointers to <item> tags
 for($i=0;$i<count($i_ar['Product']);$i++) {
  $importImages = array();
  $addToCategories = array();
  //we have no  <Product> nested inside another <Product> tag
  //we have to check if pointer is to open type tag.
  if($d_ar[$i_ar['Product'][$i]]['type']=='open') {
   //now for all content within single <Product> element
   //extract needed information
   $productSku = $d_ar[$i_ar['Product'][$i]]['attributes']['ItemNumber'];
   $vendorSku = $productSku;
   $productSku = $vendorId.'-'.$productSku;
   $productId = Mage::getModel('catalog/product')->getIdBySku($productSku);

   if(!$productId) {

    $counter++; if($counter==25) { break; break; }
    	
    $product = Mage::getModel('catalog/product')->load($productId);
    $product_data['sku'] = $productSku;
    $product_data['vendorSku'] = $vendorSku;
    for($j=$i_ar['Product'][$i];$j<$i_ar['Product'][$i+1];$j++) {
     $value = '';
     if(isset($d_ar[$j]['value'])) {
      $value =  str_replace('�','',$d_ar[$j]['value']);
     }
     $product->setPrice(0);

     switch($d_ar[$j]['tag']) {
      case 'Status':
       if($d_ar[$j]['type']=='open' && !isset($d_ar[$j]['attributes'])) {
        $product_data['status'] = $value;
        if(!$value) {
         $product_data['status'] = 'disabled';
        }
       }
       break;
      case 'UPC':
       $product_data['upc'] = $value;
       $product_data['barcode'] = $value;
       break;
      case 'UnitCost':

       $product_data['cost'] = $value;

       break;
      case 'MSRP':
      case 'MRSP':
       $product_data['mrsp'] = $value;
       break;
      case 'CountryOfOrigin':
       $countries = Mage::getResourceModel('directory/country_collection')
       ->addCountryIdFilter($value)
       ->load()
       ->getItems();
       $country = array_shift($countries);
       if(is_object($country)) {
        $countryName = $country->getName();
        $product_data['countryoforigin'] = $countryName;
       }
       break;
      case 'Category':
       $addToCategories[] = array('id'=>$value,'position'=>$d_ar[$j]['attributes']['Position']);
       break;
      case 'Description':
       if($d_ar[$j]['attributes']['Type']=='LongName') {
        if(!$value) {
         $value = $productSku;
        }
        $product_data['name'] = $value;
       } elseif($d_ar[$j]['attributes']['Type']=='Web Description') {
        $product_data['description'] = $value;
       } elseif($d_ar[$j]['attributes']['Type']=='Short Description') {
        $product_data['short_description'] = $value;
       }
       break;
      case 'ProductSpecSheet':
       if(substr_count(strtolower($value),'astm')) {
        $product_data['astm'] = $value;
       } elseif(substr_count(strtolower($value),'coc')) {
        $product_data['coc'] = $value;
       }
       break;
      case 'Age':
       if(!in_array($value,$ages) && !in_array($value,array_keys($ages))) {
        $o = Mage::getModel('eav/entity_attribute_option')->setAttributeId($age_attribute->getId())->setStoreId(0)->save();
        $optionId = $o->getId();
        $option = array();
        $option['attribute_id'] = $age_attribute->getId();
        $option['value'][$o->getId()][0] = $value;
        $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
        $setup->addAttributeOption($option);
        $ages[$value] = $value;
       }
       $product_data['age'] = $value;
       break;
      case 'Brand':
       if(!in_array($value,$brands) && !in_array($value,array_keys($brands))) {
        $o = Mage::getModel('eav/entity_attribute_option')->setAttributeId($brand_attribute->getId())->setStoreId(0)->save();
        $optionId = $o->getId();
        $option = array();
        $option['attribute_id'] = $brand_attribute->getId();
        $option['value'][$o->getId()][0] = $value;
        $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
        $setup->addAttributeOption($option);
        $brands[$value] = $value;
       }
       $product_data['manufacturer_brand'] = $value;
       break;
      case 'Color':
       $product_data['color'] = $value;
       break;
      case 'ReleaseDate':
       try  {
        $product_data['release_date'] = new Zend_Date($value);
       } catch (Exception $e) {
        Mage::log($e->getMessage());
       }
       break;
      case 'AssemblyReq':
       $product_data['assembly_req'] = $value;
       break;
      case 'CareAndInstructions':
       $product_data['care_and_instructions'] = $value;
       break;
      case 'Image':
       $importImages[] = $d_ar[$j]['attributes']['File'];
       break;
      case 'Dimension':
       if(isset($d_ar[$j]['attributes']) && isset($d_ar[$j]['attributes']['Type']) &&  $d_ar[$j]['attributes']['Type']=='Assembled') {
        foreach(array($d_ar[$j+1],$d_ar[$j+2],$d_ar[$j+3],$d_ar[$j+4]) as $dimention) {
         if(isset($dimention['value'])) {
          $tag = strtolower($dimention['tag']);
          $val = $dimention['value'];
          $product_data[$tag] = $val;
         }
        }
       }
       break;
      case 'ModelNumber':
       $product_data['vendor_item_no']= $value;
       break;
      case 'Manufacturer':
       if($d_ar[$j]['type']=='open') {
        foreach(array($d_ar[$j+1]) as $dimention) {
         if(isset($dimention['value'])) {
          $val = $dimention['value'];
          $product_data['manufacturer'] = $val;
         }
        }
       }
       break;
      default:
       break;
     }

    }
    $product->setData($product_data);
    $product->setTypeId('simple');
    $product->setAttributeSetId(4);
    try {
     $product->save();
     //$this->addImportImages($importImages,$product->getId());
     $this->addProductToCategories($addToCategories,$product,$categories);
     echo 'Product #'.$product->getId().' with sku  '.$product->getSku().' imported \n';
    } catch (Exception $e) {
     foreach($e->getTrace() as $t){
      var_dump($t);
     }
    }
   }
  }
  	
  $product = null; $addToCategories=null;$importImages=null;
  if($i==20 && !empty($product_data)) { die('T>T'); }
 }
 //unseting XML parser object
 xml_parser_free($this->parser);
 //rename($file,$file.'_CHECKED');
 }

 public function addProductToCategories($addToCategories,$product,$categories) {
  try {
   $am = Mage::getModel('catalog/category_api');
   foreach($addToCategories as $category) {
    $id = $category['id'];
    $position = $category['position'];
    if($id) {
     $categoriesArr = explode('-',$id);
     foreach($categoriesArr as $categoryId) {
      $categoryId = trim($categoryId);
      if(isset($categories[$categoryId]) && $categories[$categoryId]!='') {
       try {
        if(!in_array($categories[$categoryId],$product->getCategoryIds())) {
         $am->assignProduct($categories[$categoryId], $product->getId());
        }
       } catch (Exception $e) {
        $root = 2; // id of main category
        if(!in_array($root,$product->getCategoryIds())) {
         $am->assignProduct($root, $product->getId());
        }
       }
      } else {
       $root = 2; // id of main category
       $am->assignProduct($root, $product->getId());
      }
     }
    }
   }
   $am = null; $addToCategories = null; $product=null; $categories = null;
  } catch (Exception $e) {
   Mage::log('Category add product error '.$e->getMessage(),null,'productsImport.log');
  }
 }

 public function addImportImages($importImages,$productId) {
  $product = Mage::getModel('catalog/product')->load($productId);
  if(!is_dir(Mage::getBaseDir().'/media/importedImages/')) {
   mkdir(Mage::getBaseDir().'/media/importedImages/');
   chmod(Mage::getBaseDir().'/media/importedImages/',0777);
   Mage::log('Dir created');
  }
  try {
   $i=0;
   $server = trim(Mage::getStoreConfig('ftp/options/address'));
   $login = trim(Mage::getStoreConfig('ftp/options/login'));
   $password = trim(Mage::getStoreConfig('ftp/options/password'));

   if(!$server || !$login || !$password) {
    Mage::log('Can\'t connect to FTP',null,'productsImport.log');
    return false;
   }
   foreach($importImages as $image) {
    if(trim($image)) {
     // path to remote file
     $image = str_replace('amp;','&',$image);
     $remote_file = str_replace($server.'/','',$image);
     $remote_file = str_replace($server,'',$remote_file);
     $remote_file = addslashes($remote_file);
     $filename = preg_replace('/[^a-z0-9-_]/i','',$remote_file);
     if(substr_count($remote_file,'videogames')>0) { $remote_file  = '/videogames/'.$product->getUpc().'.jpg'; }
     $local_file = Mage::getBaseDir('media').'/importedImages/'.$filename.'.jpg';

     $labels[] = $product->getImageLabel();
     foreach($product->getMediaGalleryImages() as $im) {
      $labels[] = $im->getLabel();
     }

     if(!in_array($filename,$labels)) {
      Mage::log('load '.$remote_file.' to '.$local_file,null,'productsImport.log');
      $c = curl_init("ftp://$login:$password@$server/$remote_file");
      // $local is the location to store file on local machine
      $fh = fopen($local_file, 'w') or die('Can\'t open file');
      curl_setopt($c, CURLOPT_FILE, $fh);
      curl_exec($c);
      curl_close($c);
      fclose($fh);
      $fh = null;
      $c = null;


      $newImage = array(
        'file' => array(
            'content' => base64_encode(file_get_contents($local_file)),
            'mime'    => 'image/jpeg'
            ),
            'label'    => $filename,
            'types' => array('image','small_image','thumbnail'),
            'position' => $i,
            'exclude'  => 0
        );

        Mage::getModel('catalog/product_attribute_media_api')->create($product->getId(),  $newImage);
        Mage::log('Image saved for product '.$product->getId(),null,'productsImport.log');
													         
     }
    }		else {
     Mage::log('Can\'t read file '.$remote_file.' from FTP',null,'productsImport.log');
    }
    // close the connection and the file handler

     
    $i++;
     
   }
   $product = null; $newImage=null; $filename = null; $labels = null;
   $importImages=null;
  } catch (Exception $e) {
   Mage::log($e->getMessage(),null,'productsImport.log');
  }
 }

 public function parseCategories() {
  $file = Mage::getBaseDir('media').'/xmls/categories.csv';
  $csv = new Varien_File_Csv();
  $data = $csv->getData($file);
  $categories = array();
  foreach($data as $category) {
   if(isset($category[4])) {
    if($category[2]) {
     $categories[$category[2]] = $category[4];
    } else {
     $categories[$category[0]] = $category[4];
    }
   }
  }
  return $categories;
 }
  
 public function getInventoryAdvice() {
  $dateObj = Mage::app()->getLocale()->date();
  $hours = $dateObj->get('HH')*1;


   Mage::getModel('core/config')->saveConfig('imports', intval(Mage::getStoreConfig('imports'))+1);
   if((trim(Mage::getStoreConfig('inventory_advice/options/check'))==1 && $hours==trim(Mage::getStoreConfig('inventory_advice/options/hour')) )
   || trim(Mage::getStoreConfig('inventory_advice/options/check'))==0
   || Mage::app()->getRequest()->getParam('force')==1) {
    try {
     $server = trim(Mage::getStoreConfig('ftp/orders/address'));
     $login = trim(Mage::getStoreConfig('ftp/orders/login'));
     $password = trim(Mage::getStoreConfig('ftp/orders/password'));
     $folder = trim(Mage::getStoreConfig('ftp/orders/outbound'));
     $c = curl_init();
     curl_setopt($c, CURLOPT_URL, "ftp://$server/$folder/");
     curl_setopt($c, CURLOPT_USERPWD, "$login:$password");
     curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
     curl_error($c);
$return = curl_exec($c);

        foreach(explode(PHP_EOL,$return) as $line) {
            foreach(explode(' ',$line) as $item) {
                if(substr_count($item,'.xml') && substr_count(strtolower($item),'inv')) {
                    $items[] = $item;
                }
            }
        }

        curl_close ($c);


        if(empty($items)) {
                die('No files to import');
        }

        $local_dir = Mage::getBaseDir('media').'/xmls/inbound/';
        $local_files = scandir($local_dir);

        $download = array_diff($items,$local_files);

        foreach($items as $item) {
            $local_file = $local_dir.$item;
            $c = curl_init("ftp://$login:$password@$server/$folder/$item");
            $fh = fopen($local_file, 'w') or die('Can\'t open file');
            curl_setopt($c, CURLOPT_FILE, $fh);
            curl_exec($c);
            curl_close($c);
            fclose($fh);

            if($this->updateInventory($local_file)) {
                $file = "$folder/$item";
                // set up basic connection
                $conn_id = ftp_connect($server);

                // login with username and password
                $login_result = ftp_login($conn_id, $login, $password);

                // try to delete $file
                if (ftp_delete($conn_id, $file)) {
                echo "$file deleted successfully";

                Mage::log("$file deleted successfully  from FTP",null,'inventoryAdvice.log');
                } else {
                echo "Could not delete $file  from FTP";
                Mage::log("Could not delete $file",null,'inventoryAdvice.log');
                }

                // close the connection
                ftp_close($conn_id);
            } else {
                echo "Can't parse inventory advice";
                Mage::log("Can't parse inventory advice",null,'inventoryAdvice.log');
            }
            break; // we are importing only one file
        }
    } catch (Exception $e) {
     echo $e->getMessage();
    }
    //sleep(5);
   } else {
    echo 'File will not be imported now. Check the settings';
   }
   if(Mage::getStoreConfig('imports')>0) {
    Mage::getModel('core/config')->saveConfig('imports', intval(Mage::getStoreConfig('imports'))-1);
   }

 }

 public function updateInventory($file) {
   
  try {
   $parsed = $this->parseXml($file);
   $d_ar = $parsed[0];
   $i_ar = $parsed[1];
   for($i=0;$i<count($i_ar['product']);$i++) {
    if($d_ar[$i_ar['product'][$i]]['type']=='open') {
     //now for all content within single <Product> element
     //extract needed information
     for($j=$i_ar['product'][$i];$j<$i_ar['product'][$i+1];$j++) { 
      	
      $value = $d_ar[$j]['value'];
      switch($d_ar[$j]['tag']) {
       case 'vendorSKU':
        $data['vendor_sku'] = $value;
        break;
       case 'buyerSKU':
        $data['sku'] = $value;
        break;
       case 'qtyonhand':
        $data['qty'] = str_replace(',','',$value);
        break;
       case 'available':
        $data['status'] = ($value=='YES')?1:2;
        break;
       case 'unit_cost':
        $data['cost'] = $value*1;
        break;
       default:
        break;
      }

     }
     $ids = array(0);
    foreach(Mage::app()->getStores() as $store) {
     $ids[] = $store->getId();
    }
    $product = Mage::getModel('catalog/product')->load(Mage::getModel('catalog/product')->getIdBySku($data['sku']));
 if($product->getId()) {
    foreach($ids as $id) { 
    // $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('sku',$data['vendor_sku']);
    //unset($data['vendor_sku']);
      try {
       if(!$product->getTaxClassId()) {
        $product->setTaxClassId(0);
       }
       if(!$product->getWebsiteId()) {
        $product->setWebsiteId(1);
       }
//       $product->setAttributeSetId(1);
       $product->setMinSaleQty(0);
       $product->setMaxSaleQty(0);
       $product->setStatus($data['status']);
       $product->setCost($data['cost']);
       $product->setStoreId($id);
       $product->setVendorSku(@$data['vendor_sku']);
        $product->save();
       
        if($data['qty']>0) {
            $data['is_in_stock'] = 1;
               
        } else {
            $data['is_in_stock'] = 0;
        }
       Mage::getModel('cataloginventory/stock_item_api')->update($product->getId(), $data);
       Mage::log("Product ".$product->getSku().' stock updated with QTY='.$data['qty'].' and InStock = '.$data['is_in_stock'].' in store '.$id,null,'inventoryAdvice.log');
       echo "Product ".$product->getSku()." updated  with QTY=".$data['qty']." and InStock = ".$data['is_in_stock']." in store {$id} <br/>";
//       flush();
//       ob_flush();
//      
      
      } catch (Exception $e) {
       Mage::log("Product ".$product->getSku().' stock NOT updated in store '.$id.' '.$e->getMessage(),null,'inventoryAdvice.log');
       echo "Product ".$product->getSku()." stock NOT updated  in store {$id} {$e->getMessage()} <br/>";
//       flush();
//       ob_flush();
      }
    }
 }
     $product = null; 
     $collection = null;
    }
   }
   $collection = Mage::getModel('catalog/product')->getcollection();
   foreach($collection as $prod) {
       if($prod->getTypeId()=='grouped') {
           $prod = Mage::getModel('catalog/product')->load($prod->getId());
           $simples = $prod->getTypeInstance(true)
            ->getAssociatedProducts($prod);
           $parent['is_in_stock']=0;
           foreach($simples as $simple) {
            if($simple->isInStock()) {
                $parent['is_in_stock']=1; break;
            }
           }
            Mage::getModel('cataloginventory/stock_item_api')->update($prod->getId(), $parent);
            Mage::log("Product ".$prod->getSku().' stock updated (automatically as parent product) with InStock = '.$parent['is_in_stock'].' in store '.$id,null,'inventoryAdvice.log');
            echo "Product ".$prod->getSku()." updated (automatically as parent product) with InStock = ".$parent['is_in_stock']." in store {$id} <br/>";
       }
       elseif($prod->getTypeId()=='configurable') {
            $prod = Mage::getModel('catalog/product')->load($prod->getId());
           $simples = $prod->getTypeInstance(true)
                ->getUsedProducts(null, $prod);
            $parent['is_in_stock']=0;
            foreach ($simples as $simple) {
               
                    if($simple->isInStock()) {
                        $parent['is_in_stock']=1; break;
                    }
              
            }
            Mage::getModel('cataloginventory/stock_item_api')->update($prod->getId(), $parent);
            Mage::log("Product ".$prod->getSku().' stock updated (automatically as parent product) with InStock = '.$parent['is_in_stock'].' in store '.$id,null,'inventoryAdvice.log');
            echo "Product ".$prod->getSku()." updated (automatically as parent product) with InStock = ".$parent['is_in_stock']." in store {$id} <br/>";
       }
   }
   Mage::log(count($i_ar['product'])." records processed from inventory file ".$file,null,'inventoryAdvice.log');
   return true;
  } catch (Exception $e) {
   echo $e->getMessage();
   Mage::log("Inventory updating error: ".$e->getMessage(),null,'inventoryAdvice.log');
   return false;
  }
 }



}
