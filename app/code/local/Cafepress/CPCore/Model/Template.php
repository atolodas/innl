<?php

class Cafepress_CPCore_Model_Template extends Mage_Core_Model_Template
{

    protected $_templateFilter;
    protected $_preprocessFlag = false;
    protected $_format;
    protected $_xmlformat;
    protected $_storeId = null;
    static protected $_defaultTemplates;

    /**
     * Initialize email template model
     *
     */
    protected function _construct()
    {
        $this->_init('cpcore/xmlformat');
    }


    /**
     * Declare template processing filter
     *
     * @param   Varien_Filter_Template $filter
     * @return  Mage_Core_Model_Email_Template
     */
    public function setTemplateFilter(Varien_Filter_Template $filter)
    {
        $this->_templateFilter = $filter;
        return $this;
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

    public function getTemplateFilter()
    {
        if (empty($this->_templateFilter)) {
            $this->_templateFilter = Mage::getModel('cpcore/template_filter');
            $this->_templateFilter->setUseAbsoluteLinks($this->getUseAbsoluteLinks())
                ->setStoreId($this->getStoreId());
        }
        return $this->_templateFilter;
    }

    /**
     * Retrive default templates as options array
     *
     * @return array
     */
    static public function getDefaultTemplatesAsOptionsArray()
    {
        $options = array(
            array('value'=>'', 'label'=> '')
        );

        $idLabel = array();
        foreach (self::getDefaultTemplates() as $templateId => $row) {
            if (isset($row['@']) && isset($row['@']['module'])) {
                $module = $row['@']['module'];
            } else {
                $module = 'adminhtml';
            }
            $idLabel[$templateId] = Mage::helper($module)->__($row['label']);
        }
        asort($idLabel);
        foreach ($idLabel as $templateId => $label) {
            $options[] = array('value' => $templateId, 'label' => $label);
        }

        return $options;
    }

    /**
     * Return template id
     * return int|null
     */
    public function getId()
    {
        return $this->getTemplateId();
    }

    /**
     * Set id of template
     * @param int $value
     */
    public function setId($value)
    {
        return $this->setTemplateId($value);
    }

    /**
     * Getter for template type
     *
     * @return int|string
     */
    public function getType(){
        return $this->getTemplateType();
    }

    /**
     * Process email template code
     *
     * @param   array $variables
     * @return  string
     */
    public function getProcessedTemplate(array $variables = array())
    {
        $processor = $this->getTemplateFilter();
        $processor->setUseSessionInUrl(false)
            ->setPlainTemplateMode($this->isPlain());

        $variables['this'] = $this->_xmlformat;

        $processor->setIncludeProcessor(array($this->_xmlformat, 'getInclude'))
            ->setVariables($variables);

        $this->_applyDesignConfig();
        try {
            $processedResult = $processor->filter($this->_xmlformat->getOutXml());
        }
        catch (Exception $e)   {
            $this->_cancelDesignConfig();
            throw $e;
        }
        $this->_cancelDesignConfig();
        return $processedResult;
    }

    /**
     * Makes additional text preparations for HTML templates
     *
     * @return string
     */
    public function getPreparedTemplateText()
    {
        if ($this->isPlain() || !$this->getTemplateStyles()) {
            return $this->getTemplateText();
        }
        // wrap styles into style tag
        return $this->getTemplateText();
    }

    /**
     * Get template code for include directive
     *
     * @param   string $template
     * @param   array $variables
     * @return  string
     */
    public function getInclude($template, array $variables)
    {
        $thisClass = __CLASS__;
        $includeTemplate = new $thisClass();

        $includeTemplate->loadByCode($template);

        return $includeTemplate->getProcessedTemplate($variables);
    }


    /**
     * Parse variables string into array of variables
     *
     * @param string $variablesString
     * @return array
     */
    protected function _parseVariablesString($variablesString)
    {
        $variables = array();
        if ($variablesString && is_string($variablesString)) {
            $variablesString = str_replace("\n", '', $variablesString);
            $variables = Zend_Json::decode($variablesString);
        }
        return $variables;
    }

    /**
     * Retrieve option array of variables
     *
     * @param boolean $withGroup if true wrap variable options in group
     * @return array
     */
    public function getVariablesOptionArray($withGroup = false)
    {
        $optionArray = array();
        $variables = $this->_parseVariablesString($this->getData('orig_template_variables'));
        if ($variables) {
            foreach ($variables as $value => $label) {
                $optionArray[] = array(
                    'value' => '{{' . $value . '}}',
                    'label' => Mage::helper('core')->__('%s', $label)
                );
            }
            if ($withGroup) {
                $optionArray = array(
                    'label' => Mage::helper('core')->__('Template Variables'),
                    'value' => $optionArray
                );
            }
        }
        return $optionArray;
    }

    public function setXmlformat($xmlformat)
    {
        $this->_xmlformat = $xmlformat;
        return $this;
    }
}
