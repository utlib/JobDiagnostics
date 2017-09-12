<?php

class JobDiagnostics_TestsController extends Omeka_Controller_AbstractActionController
{
    const SHORT_DISPATCH = 'short_running';
    const LONG_DISPATCH = 'long_running';
    const SLOW_LIMIT = 20;
    const TIMEOUT_LIMIT = 60;
    
    /**
     * Initialize controller settings.
     */
    public function init() 
    {
        $this->_helper->db->setDefaultModelName('JobDiagnostics_Test');
    }
    
    /**
     * Alias for main page.
     */
    public function homeAction() 
    {
        $this->_helper->redirector('index', null, null, array());
    }
    
    /**
     * POST-only create test records
     */
    public function addAction() 
    {
        if ($this->getRequest()->isPost()) {
            $dispatchType = $this->getParam('dispatch_type');
            if (empty($dispatchType)) {
                $this->_helper->flashMessenger(__("Bad form submission."), 'error');
            } else {
                try {
                    switch ($dispatchType) {
                    case 'short_running':
                        parent::addAction();
                        Zend_Registry::get('bootstrap')->getResource('jobs')->send('JobDiagnostics_Job_Test', array('id' => $this->view->job_diagnostics_test->id));
                        break;
                    case 'long_running':
                        parent::addAction();
                        Zend_Registry::get('bootstrap')->getResource('jobs')->sendLongRunning('JobDiagnostics_Job_Test', array('id' => $this->view->job_diagnostics_test->id));
                        break;
                    default:
                        $this->_helper->flashMessenger(__("Bad form submission."), 'error');
                        break;
                    }
                    
                } catch (Exception $ex) {
                    $this->_helper->flashMessenger(__("An error occurred: %s", $ex->getMessage()), 'error');
                }
            }
            $this->_helper->redirector('index', null, null, array());
        }
    }
    
    /**
     * Redirect back to index after add.
     *
     * @param Omeka_Record_AbstractRecord $record
     */
    protected function _redirectAfterAdd($record) 
    {
        try {
            switch ($record->dispatch_type) {
            case 'short_running':
                Zend_Registry::get('bootstrap')->getResource('jobs')->send('JobDiagnostics_Job_Test', array('id' => $record->id));
                break;
            case 'long_running':
                Zend_Registry::get('bootstrap')->getResource('jobs')->sendLongRunning('JobDiagnostics_Job_Test', array('id' => $record->id));
                break;
            default:
                $record->delete();
                $this->_helper->flashMessenger(__("Bad form submission."), 'error');
                break;
            }
        } catch (Exception $ex) {
            $record->error = $ex->getMessage();
        }
        $this->_helper->redirector('index', null, null, array());
    }
    
    /**
     * Main page.
     */
    public function indexAction() 
    {
        $this->view->short_running_result = $this->_resultForDispatchType(self::SHORT_DISPATCH, $test);
        $this->view->latest_short_running_test = $test;
        $this->view->short_running_allow_test = empty($test) || !empty($test->finished);
        $this->view->short_running_allow_history = !empty($test);
        $this->view->long_running_result = $this->_resultForDispatchType(self::LONG_DISPATCH, $test);
        $this->view->latest_long_running_test = $test;
        $this->view->long_running_allow_test = empty($test) || !empty($test->finished);
        $this->view->long_running_allow_history = !empty($test);
    }
    
    /**
     * Return the latest result for the dispatch type
     *
     * @param string $dispatchType
     */
    private function _resultForDispatchType($dispatchType, &$test) 
    {
        $table = $this->_helper->_db->getTable('JobDiagnostics_Test');
        $select = $table->getSelectForFindBy(array('dispatch_type' => $dispatchType))->order('started desc');
        $test = $table->fetchObject($select);
        if (empty($test)) {
            return __("No tests run yet.");
        } else {
            if (empty($test->finished)) {
                $timeDifference = time() - strtotime($test->started);
                if (!empty($test->error)) {
                    return __("Error during dispatch: %s", $test->error);
                }
                elseif ($timeDifference <= self::TIMEOUT_LIMIT) {
                    return __("Test in progress...");
                } else {
                    return __("No response after %d seconds. Jammed queue suspected.", self::TIMEOUT_LIMIT);
                }
            } else {
                $timeDifference = strtotime($test->finished) - strtotime($test->started);
                if (!empty($test->error)) {
                    return __("Error during dispatch: %s", $test->error);
                } elseif ($timeDifference <= self::SLOW_LIMIT) {
                    return __("Response in %d seconds. Queue OK.", $timeDifference);
                } elseif ($timeDifference <= self::TIMEOUT_LIMIT) {
                    return __("Response in %d seconds. Queue busy but still OK.", $timeDifference);
                } else {
                    return __("Response in %d seconds. Queue nearing capacity, or re-test required.", $timeDifference);
                }
            }
        }
    }
    
    /**
     * Browse old test records.
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
    
    /**
     * Clear test records by dispatch type.
     */
    public function clearAction() 
    {
        if ($this->getRequest()->isPost()) {
            $dispatchType = $this->getParam('dispatch_type');
            if (empty($dispatchType)) {
                $this->_helper->flashMessenger(__("Bad form submission."), 'error');
            } else {
                try {
                    get_db()->getTable('JobDiagnostics_Test')->clearByDispatchType($dispatchType);
                    $this->_helper->flashMessenger(__("%s test records successfully cleared!", __($dispatchType)), 'success');
                } catch (Exception $ex) {
                    $this->_helper->flashMessenger(__("An error occurred: %s", $ex->getMessage()), 'error');
                }
            }
            $this->_helper->redirector('index', null, null, array());
        }
    }
}
