<?php

class SLab_Dcontent_Model_Filter extends Varien_Filter_Template
{
    /**
     * Use absolute links flag
     *
     * @var bool
     */
    protected $_useAbsoluteLinks = false;

    /**
     * Whether to allow SID in store directive: NO
     *
     * @var bool
     */
    protected $_useSessionInUrl = false;

    /**
     * @deprecated after 1.4.0.0-alpha2
     * @var Mage_Core_Model_Url
     */
    protected static $_urlInstance;

    /**
     * Modifier Callbacks
     *
     * @var array
     */
    protected $_modifiers = array(
        'nl2br'             => '',
        'round'             => 'round',
        'str_pad'           => 'str_pad',
        'decrypt'           => array('Mage_Core_Helper_Data','decrypt'),
    );

    protected $_storeId = null;

    protected $_plainTemplateMode = false;

    /**
     * Setup callbacks for filters
     *
     */
    public function __construct()
    {
        $this->_modifiers['escape'] = array($this, 'modifierEscape');

    }

    /**
     * Set use absolute links flag
     *
     * @param bool $flag
     * @return Mage_Core_Model_Email_Template_Filter
     */
    public function setUseAbsoluteLinks($flag)
    {
        $this->_useAbsoluteLinks = $flag;
        return $this;
    }

    /**
     * Setter whether SID is allowed in store directive
     * Doesn't set anything intentionally, since SID is not allowed in any kind of emails
     *
     * @param bool $flag
     * @return Mage_Core_Model_Email_Template_Filter
     */
    public function setUseSessionInUrl($flag)
    {
        $this->_useSessionInUrl = $flag;
        return $this;
    }

    /**
     * Setter
     *
     * @param boolean $plainTemplateMode
     * @return Mage_Core_Model_Email_Template_Filter
     */
    public function setPlainTemplateMode($plainTemplateMode)
    {
        $this->_plainTemplateMode = (bool)$plainTemplateMode;
        return $this;
    }

    /**
     * Getter
     *
     * @return boolean
     */
    public function getPlainTemplateMode()
    {
        return $this->_plainTemplateMode;
    }

    /**
     * Setter
     *
     * @param integer $storeId
     * @return Mage_Core_Model_Email_Template_Filter
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Getter
     * if $_storeId is null return Design store id
     *
     * @return integer
     */
    public function getStoreId()
    {
        if (null === $this->_storeId) {
            $this->_storeId = Mage::app()->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Retrieve Block html directive
     *
     * @param array $construction
     * @return string
     */
    public function blockMethodDirective($construction)
    {
        $skipParams = array('type', 'id', 'output','template');
        $blockParameters = $this->_getIncludeParameters($construction[2]);
        $layout = Mage::app()->getLayout();

	    if (isset($blockParameters['type'])) {
            $type = $blockParameters['type'];
            $block = $layout->createBlock($type, null, $blockParameters);
		} elseif (isset($blockParameters['id'])) {
            $block = $layout->createBlock('cms/block');
            if ($block) {
                $block->setBlockId($blockParameters['id']);
            }
        }

		if ($block) {
            $block->setBlockParams($blockParameters);
            foreach ($blockParameters as $k => $v) {
                if (in_array($k, $skipParams)) {
                    continue;
                }
                $block->setDataUsingMethod($k, $v);
            }
        }

        if (!$block) {
            return '';
        }
		if($blockParameters['template']) {
			$block->setTemplate($blockParameters['template']);
			return $construction;
		} elseif (isset($blockParameters['output'])) {
            $method = $blockParameters['output'];
        if (!isset($method) || !is_string($method) || !is_callable(array($block, $method))) {
            $method = 'toHtml';
        }
        	return $block->$method();
        }
    }

    /**
     * Retrieve layout html directive
     *
     * @param array $construction
     * @return string
     */
    public function layoutDirective($construction)
    {
        $skipParams = array('handle', 'area');

        $params = $this->_getIncludeParameters($construction[2]);
        $layout = Mage::getModel('core/layout');
        /* @var $layout Mage_Core_Model_Layout */
        if (isset($params['area'])) {
            $layout->setArea($params['area']);
        }
        else {
            $layout->setArea(Mage::app()->getLayout()->getArea());
        }

        $layout->getUpdate()->addHandle($params['handle']);
        $layout->getUpdate()->load();

        $layout->generateXml();
        $layout->generateBlocks();

        foreach ($layout->getAllBlocks() as $blockName => $block) {
            /* @var $block Mage_Core_Block_Abstract */
            foreach ($params as $k => $v) {
                if (in_array($k, $skipParams)) {
                    continue;
                }

                $block->setDataUsingMethod($k, $v);
            }
        }

        /**
         * Add output method for first block
         */
        $allBlocks = $layout->getAllBlocks();
        $firstBlock = reset($allBlocks);
        if ($firstBlock) {
            $layout->addOutputBlock($firstBlock->getNameInLayout());
        }

        $layout->setDirectOutput(false);
        return $layout->getOutput();
    }

    /**
     * Retrieve block parameters
     *
     * @param mixed $value
     * @return array
     */
    protected function _getBlockParameters($value)
    {
        $tokenizer = new Varien_Filter_Template_Tokenizer_Parameter();
        $tokenizer->setString($value);

        return $tokenizer->tokenize();
    }

    /**
     * Retrieve Skin URL directive
     *
     * @param array $construction
     * @return string
     */
    public function skinDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        $params['_absolute'] = $this->_useAbsoluteLinks;

        $url = Mage::getDesign()->getSkinUrl($params['url'], $params);

        return $url;
    }

    /**
     * Retrieve media file URL directive
     *
     * @param array $construction
     * @return string
     */
    public function mediaDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        return Mage::getBaseUrl('media') . $params['url'];
    }

    /**
     * Retrieve store URL directive
     * Support url and direct_url properties
     *
     * @param array $construction
     * @return string
     */
    public function storeDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        if (!isset($params['_query'])) {
            $params['_query'] = array();
        }
        foreach ($params as $k => $v) {
            if (strpos($k, '_query_') === 0) {
                $params['_query'][substr($k, 7)] = $v;
                unset($params[$k]);
            }
        }
        $params['_absolute'] = $this->_useAbsoluteLinks;

        if ($this->_useSessionInUrl === false) {
            $params['_nosid'] = true;
        }

        if (isset($params['direct_url'])) {
            $path = '';
            $params['_direct'] = $params['direct_url'];
            unset($params['direct_url']);
        }
        else {
            $path = isset($params['url']) ? $params['url'] : '';
            unset($params['url']);
        }

        return Mage::app()->getStore(Mage::getDesign()->getStore())->getUrl($path, $params);
    }

    /**
     * Directive for converting special characters to HTML entities
     * Supported options:
     *     allowed_tags - Comma separated html tags that have not to be converted
     *
     * @param array $construction
     * @return string
     */
    public function htmlescapeDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        if (!isset($params['var'])) {
            return '';
        }

        $allowedTags = null;
        if (isset($params['allowed_tags'])) {
            $allowedTags = preg_split('/\s*\,\s*/', $params['allowed_tags'], 0, PREG_SPLIT_NO_EMPTY);
        }

        return Mage::helper('core')->htmlEscape($params['var'], $allowedTags);
    }

    /**
     * Escape specified string
     *
     * @param string $value
     * @param string $type
     * @return string
     */
    public function modifierEscape($value, $type = 'html')
    {
        switch ($type) {
            case 'html':
                return htmlspecialchars($value, ENT_QUOTES);

            case 'htmlentities':
                return htmlentities($value, ENT_QUOTES);

            case 'url':
                return rawurlencode($value);
        }
        return $value;
    }

    /**
     * HTTP Protocol directive
     *
     * Using:
     * {{protocol}} - current protocol http or https
     * {{protocol url="www.domain.com/"}} domain URL with current protocol
     * {{protocol http="http://url" https="https://url"}
     * also allow additional parameter "store"
     *
     * @param array $construction
     * @return string
     */
    public function protocolDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        $store = null;
        if (isset($params['store'])) {
            $store = Mage::app()->getSafeStore($params['store']);
        }
        $isSecure = Mage::app()->getStore($store)->isCurrentlySecure();
        $protocol = $isSecure ? 'https' : 'http';
        if (isset($params['url'])) {
            return $protocol . '://' . $params['url'];
        }
        elseif (isset($params['http']) && isset($params['https'])) {
            if ($isSecure) {
                return $params['https'];
            }
            return $params['http'];
        }

        return $protocol;
    }

    /**
     * Store config directive
     *
     * @param array $construction
     * @return string
     */
    public function configDirective($construction)
    {
        $configValue = '';
        $params = $this->_getIncludeParameters($construction[2]);
        $storeId = $this->getStoreId();
        if (isset($params['path'])) {
            $configValue = Mage::getStoreConfig($params['path'], $storeId);
        }
        return $configValue;
    }

    /*=====REWRITED IN WMSPRO=====*/
    /**
     * Custom Variable directive
     *
     * @param array $construction
     * @return string
     */
    public function customvarDirective($construction)
    {
        $customVarValue = '';
        $params = $this->_getIncludeParameters($construction[2]);
        if (isset($params['code'])) {
            $variable = Mage::getModel('core/variable')
                ->setStoreId($this->getStoreId())
                ->loadByCode($params['code']);
            $mode = $this->getPlainTemplateMode()?Mage_Core_Model_Variable::TYPE_TEXT:Mage_Core_Model_Variable::TYPE_HTML;
            if ($value = $variable->getValue($mode)) {
                $customVarValue = $value;
            }
        }
        return $customVarValue;
    }

    /**
     * Filter the string as template.
     * Rewrited for logging exceptions
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        try {
            $value = parent::filter($value);
        } catch (Exception $e) {
            $value = '';
            Mage::logException($e);
        }
        return $value;
    }

    public function mathDirective($construction)
    {
        $parts = explode('|', $construction[2], 2);
        $variableName = $construction[2];
        $modifiersString = '';

        if (2 === count($parts)) {
            list($variableName, $modifiersString) = $parts;
        }
        preg_match_all("/\[\[([^]]*)]]/i", $variableName, $matches);

        foreach($matches['1'] as $variable){
            $res = $this->filter('{{'.$variable.'}}');
            if (is_numeric($res)){
                $variableName = str_replace(array('[['.$variable.']]'),array($res),$variableName);
            } else {
                $variableName = str_replace(array('[['.$variable.']]'),array('0'),$variableName);
            }
        }
        $expression = $variableName;

        $result = '';
        eval ("\$result = (float)($expression);");

        if ($modifiersString!=''){
            return $this->_amplifyModifiers($result, trim($modifiersString));
        }
        return $result;
    }

	 public function phpDirective($construction)
    {
        $variableName = $construction[2];
        $modifiersString = '';
        preg_match_all("/\[\[([^]]*)]]/i", $variableName, $matches);

        foreach($matches['1'] as $variable) {
            $res = $this->filter('{{'.$variable.'}}');
            if($res) {
              $variableName = str_replace(array('[['.$variable.']]'),array($res),$variableName);
              }
        }
        $expression = $variableName;

        $result = '';

        eval("\$result = $expression;");
        if ($modifiersString!=''){
            return $this->_amplifyModifiers($result, trim($modifiersString));
        }
        return $result;
    }

    public function dateDirective($construction)
    {
        $parts = explode('|', $construction[2], 2);
        $format = '';
        $dataIn = $construction[2];;

        if (2 === count($parts)) {
            list($dataIn,$format) = $parts;
        } else {
            $time = Mage::getModel('core/date')->timestamp();
            return trim(date($construction[2],$time));
        }
        $dataIn = $this->filterFromSquare($dataIn);
        $time = Mage::getModel('core/date')->timestamp($dataIn);
        return trim(date($format, $time));
    }

    public function filterFromSquare($squareData)
    {
        $dataOut = str_replace(array('[[',']]'),array('{{','}}'),$squareData);
        return $this->filter($dataOut);
    }

	public function condDirective($construction)
    {
        $parts = explode('|', $construction[2], 2);
		$variableName = $construction[2];
		$modifiersString = '';

		if (2 === count($parts)) {
			list($variableName, $modifiersString) = $parts;
		}
		preg_match_all("/\[\[([^]]*)]]/i", $variableName, $matches);

		foreach($matches['1'] as $variable) {
			$res = $this->filter('{{'.$variable.'}}');
			if (is_numeric($res)) {
				$variableName = str_replace(array('[['.$variable.']]'),array($res),$variableName);
			} else {
				$variableName = str_replace(array('[['.$variable.']]'),array('0'),$variableName);
			}
		}
		$expression = $variableName;

        Zend_Debug::dump($expression);

		$result = '';
		eval ("\$result = (float)($expression);");

		if ($modifiersString!='') {
			return $this->_amplifyModifiers($result, trim($modifiersString));
		}
		return $result;
    }

    public function functionDirective($construction)
    {
        $variableName = $construction[2];
		$modifiersString = '';
        $parts = explode('|', $variableName,2);
		if (2 === count($parts)) {
			list($method,$variableName) = $parts;
                        $method=trim($method);
		}

		preg_match_all("/\[\[([^]]*)]]/i", $variableName, $matches);

		foreach($matches['1'] as $variable){
			$res = $this->filter('{{'.$variable.'}}');
				$variableName = str_replace(array('[['.$variable.']]'),array($res),$variableName);

		}
        return Mage::helper('dcontent')->$method($variableName);
    }

    public function varDirective($construction)
    {
        if (count($this->_templateVars)==0) {
            // If template preprocessing
            return $construction[0];
        }

        $parts = explode('|', $construction[2], 2);

        if (2 === count($parts)) {
            list($variableName, $modifiersString) = $parts;
            return $this->_amplifyModifiers($this->_getVariable($variableName, ''), $modifiersString);
        }
        $var = $this->_getVariable($construction[2], '');
        return $var;
    }

    public function dependDirective($construction)
    {
        if (count($this->_templateVars)==0) {
            // If template preprocessing
            return $construction[0];
        }

        if($this->_getVariable($construction[1], '')=='' || (int)$this->_getVariable($construction[1], '')==0) {
            return '';
        } else {
            return $construction[2];
        }
    }


    public function issetDirective($construction)
    {
        if($construction[1]=='') {
            return '';
        } else {
            return $construction[2];
        }
    }

   public function fiDirective($construction)
    {
        $var = '';
        if($construction[1]) $var = $this->_getVariable($construction[1], '');
        $var2 = $construction[1];
        if (count($this->_templateVars) == 0) {
            return $construction[0];
        }

        if((!$var) && (!$var2)) {
            if (isset($construction[3]) && isset($construction[4])) {
                return $construction[4];
            }
            return '';
        } elseif($var2 !== '' && $var2 !== false && $var2 !== 0 && !$var) { 
            if($var2 == 'customerSession.getId()' && !$var) return $construction[4];  // Exception for missed customer session
            elseif($var2) return $construction[2];
            elseif(isset($construction[4])) return $construction[4];
            else return '';
        } else {
            return $construction[2];
        }
    }

    public function ifDirective($construction)
    {
        $var = '';
        if($construction[1]) $var = $this->_getVariable($construction[1], '');
        $var2 = $construction[1];
        if (count($this->_templateVars) == 0) {
            return $construction[0];
        }

        if((!$var) && (!$var2)) {
            if (isset($construction[3]) && isset($construction[4])) {
                return $construction[4];
            }
            return '';
        } elseif($var2 !== '' && $var2 !== false && $var2 !== 0 && !$var) { 
            if($var2 == 'customerSession.getId()' && !$var) return $construction[4];  // Exception for missed customer session
            elseif($var2) return $construction[2];
            elseif(isset($construction[4])) return $construction[4];
            else return '';
        } else {
            return $construction[2];
        }
    }


    protected function _amplifyModifiers($value, $modifiers)
    {
        foreach (explode('|', $modifiers) as $part) {
            if (empty($part)) {
                continue;
            }
            $params   = explode(':', $part);
            $modifier = array_shift($params);
            if (isset($this->_modifiers[$modifier])) {
                $callback = $this->_modifiers[$modifier];
                if (!$callback) {
                    $callback = $modifier;
                }
                array_unshift($params, $value);
                $value = call_user_func_array($callback, $params);
            }
        }

        return $value;
    }
}
