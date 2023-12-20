<?php

/**
 * Controller for viewing processes.
 * @package controllers
 */
class JobDiagnostics_ProcessesController extends Omeka_Controller_AbstractActionController
{
    /**
     * Number of processes to show per browsing page.
     * @var int
     */
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
        if (!$this->_helper->acl->isAllowed('browse', 'Process'))
        {
            throw new Omeka_Controller_Exception_403;
        }
        parent::browseAction();
    }

    /**
     * Show action. Inherit from parent.
     */
    public function showAction()
    {
        if (!$this->_helper->acl->isAllowed('show', 'Process'))
        {
            throw new Omeka_Controller_Exception_403;
        }
        parent::showAction();
        $this->view->process = $this->view->proces; // Patch for bad inflector
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
