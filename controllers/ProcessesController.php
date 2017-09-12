<?php

class JobDiagnostics_ProcessesController extends Omeka_Controller_AbstractActionController
{
    protected $_browseRecordsPerPage = self::RECORDS_PER_PAGE_SETTING;
    
    /**
     * Initialize controller settings.
     */
    public function init()
    {
        $this->_helper->db->setDefaultModelName('Process');
    }
    
    /**
     * Browse action. Inherit from parent.
     */
    public function browseAction() 
    {
        parent::browseAction();
    }
    
    /**
     * Return the default sort type.
     *
     * @return array
     */
    protected function _getBrowseDefaultSort()
    {
        return array('id', 'd');
    }
}
