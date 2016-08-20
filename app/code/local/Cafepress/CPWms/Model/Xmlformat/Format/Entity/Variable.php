<?php

class Cafepress_CPWms_Model_Xmlformat_Format_Entity_Variable extends Cafepress_CPWms_Model_Abstract
{
    private $variables = array();
    private $_arrays = array();
    private $_arraysKey = array();
    
    public function setVar($name, $value)
    {
        $this->variables[$name] = $value;
        return $this;
    }
    
    public function getVar($name)
    {
        $parts = explode('.', $name, 2);

        if (2 === count($parts)) {
            list($arrayName, $key) = $parts;
            if (isset($this->variables[$arrayName][$key])){
                return $this->variables[$arrayName][$key];
            }
        } else {
            if (isset($this->variables[$name])){
                return $this->variables[$name];
            }
        }
        return false; 
    }
    
    public function issetVar($name)
    {
        if ((isset($this->variables[$name])) && ($this->variables[$name]!='')){
            return true;
        }
        return false;
    }

    public function issetValue()
    {
        if ((isset($this->variables['val'])) && ($this->variables['val']!='')){
            return true;
        }
        return false;
    }
    
    public function positiveValue()
    {
        if ((isset($this->variables['val'])) && ($this->variables['val']!='0.0000')&& ($this->variables['val']!=0)){
            return true;
        }
        return false;
    }

    public function getAll()
    {
       return $this->variables;
    }
    
    public function reset()
    {
        $this->variables = array();
        return $this;
    }
    
    public function setSessionVar($key, $value)
    {
        Mage::register($key, $value);
        return $this;
    }
    
    public function getSessionVar($key)
    {
        return Mage::registry($key);
    }
    
    public function newArray($name)
    {
        $this->_arrays[$name] = array();
        $this->_arraysKey[$name] = 0;
        return $this;
    }

    public function setDataArray($nameArray, $key, $value)
    {
        if (is_string($value)){
            $value = trim($value);
        }
        $keys = explode('.', $key);
        if (count($keys)>1){
            $keyStr = '';
            foreach($keys as $key){
                $keyStr .= "['$key']";
            }
            eval ("\$this->_arrays['$nameArray']['".$this->_arraysKey[$nameArray]."']$keyStr = \$value;");
            
        } else {
            $this->_arrays[$nameArray][$this->_arraysKey[$nameArray]][$key] = $value;
        }
        return $this;
    }
    
    public function getArray($name)
    {
        $name = trim($name);
        $keys = explode('.', $name);
        if (count($keys)>1){
            $keyStr = '';
            foreach($keys as $key){
                $keyStr .= "['$key']";
            }
            $result = array();
            eval ("if (isset(\$this->_arrays$keyStr))\$result = \$this->_arrays$keyStr;");
            return $result;
            
        } else {
            if (isset($this->_arrays[$name])){
                return $this->_arrays[$name];
            }
            return false;
        }
    }
    
    public function getArrays($arrays)
    {
        $result = array();
        $arraysName = explode(':', $arrays);
        foreach($arraysName as $name){
            $result[$name] = $this->_arrays[$name];
        }
        return $result;
    }
    
    public function incArray($name)
    {
        $this->_arraysKey[$name] = $this->_arraysKey[$name]+1;
    }
    
}