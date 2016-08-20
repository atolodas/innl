<?php 
class Shaurmalab_Score_Adminhtml_Score_DictionaryController extends Mage_Adminhtml_Controller_Action
{
    
    public function indexAction()
    {
        $this->_title($this->__(''))
             ->_title($this->__('Manage dictionaries'));

        $this->loadLayout();
        $this->renderLayout();
    }


    public function editAction()
    {
        $dictId  = (int) $this->getRequest()->getParam('id');
       // $dictionarie = 
        $this->loadLayout();
        $this->_setActiveMenu('score/oggettos');
		$this->renderLayout();
    }

    public function saveAction() { 

        try { 
        $post = $this->getRequest()->getParams('post');
        $sql = '';
        $table = $post['table'];
        foreach ($post['elements'] as $id => $data) {
            if($data['delete'] == 1) { 
                $sql.= "DELETE FROM {$table} WHERE id = {$id}; ";
            } else { 
                if(isset($data['code'])) $additional = "{$data['code']}";
                if(isset($data['country_id'])) $additional = "{$data['country_id']}";

                
                if(isset($data['lat'])) { 
                    $sql.= "REPLACE INTO {$table} values ({$id}, '{$data['title']}', {$data['store_id']}, '{$additional}',{$data['lat']},{$data['lng']}); ";
                } else {
                    $sql.= "REPLACE INTO {$table} values ({$id}, '{$data['title']}', {$data['store_id']}, '{$additional}'); ";
                }
                //if($update) $sql.= "UPDATE {$table} SET {$update}  WHERE id = {$id}; ";
            
            }
        }

        if(Mage::getSingleton('core/resource')->getConnection('core_read')->query($sql)) { 
             $this->_getSession()->addSuccess($this->__('Dictionary has been saved.'));
        } else { 
             $this->_getSession()->addError('Dictionary was not saved.');
        }

         } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
         }
        
        $this->_redirect('*/*/edit', array(
                'id'    => $table,
                '_current'=>true
        ));
    }

}