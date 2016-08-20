<?php
/**
* Copyright © Pulsestorm LLC: All rights reserved
*/

class Shaurmalab_Commercebug_Helper_Cacheclearer
{
    public function clearCache()
    {			
        Mage::app()->cleanCache();
    }
}