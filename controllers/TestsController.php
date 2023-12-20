<?php

/**
 * Controller for viewing test results.
 * @package controllers
 */
class JobDiagnostics_TestsController extends Omeka_Controller_AbstractActionController
{
    /**
     * Label slug for short-running job tests.
     */
    const SHORT_DISPATCH = 'short_running';

    /**
     * Label slug for long-running job tests.
     */
    const LONG_DISPATCH = 'long_running';

    /**
     * Time limit in seconds for a job queue to be considered slow.
     */
    const SLOW_LIMIT = 20;

    /**
     * Time limit in seconds for a job queue to be considered jammed.
     */
    const TIMEOUT_LIMIT = 60;

    /**
     * Interval in seconds to poll for updates.
     */
    const POLL_INTERVAL = 2;

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
        if (!$this->_helper->acl->isAllowed('add', 'JobDiagnostics_Test'))
        {
            throw new Omeka_Controller_Exception_403;
        }
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
        $this->_helper->redirector('wait', null, null, array('id' => $record->id));
    }

    /**
     * Main page.
     */
    public function indexAction()
    {
        if (!$this->_helper->acl->isAllowed('browse', 'JobDiagnostics_Test'))
        {
            throw new Omeka_Controller_Exception_403;
        }
        $this->view->short_running_result = $this->_resultForDispatchType(self::SHORT_DISPATCH, $test);
        $this->_killTestIfDead($test);
        $this->view->latest_short_running_test = $test;
        $this->view->short_running_allow_test = empty($test) || !empty($test->finished);
        $this->view->short_running_allow_history = !empty($test);
        $this->view->long_running_result = $this->_resultForDispatchType(self::LONG_DISPATCH, $test);
        $this->_killTestIfDead($test);
        $this->view->latest_long_running_test = $test;
        $this->view->long_running_allow_test = empty($test) || !empty($test->finished);
        $this->view->long_running_allow_history = !empty($test);
    }
    
    /**
     * Time out the given test record if its starting time is longer ago than the timeout limit.
     * @param JobDiagnostics_Test $test
     */
    private function _killTestIfDead($test)
    {
        if (!empty($test))
        {
            if (empty($test->finished) && time()-strtotime($test->started) >= self::TIMEOUT_LIMIT)
            {
                $test->finished = date("Y-m-d H:i:s");
                $test->error = __("[Timed out]");
                $test->save();
            }
        }
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
        if (!$this->_helper->acl->isAllowed('browse', 'JobDiagnostics_Test'))
        {
            throw new Omeka_Controller_Exception_403;
        }
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
        if (!$this->_helper->acl->isAllowed('clear', 'JobDiagnostics_Test'))
        {
            throw new Omeka_Controller_Exception_403;
        }
        if ($this->getRequest()->isPost()) {
            $dispatchType = $this->getParam('dispatch_type');
            $localizedDispatchType = __($dispatchType);
            if (empty($dispatchType)) {
                $this->_helper->flashMessenger(__("Bad form submission."), 'error');
            } else {
                try {
                    get_db()->getTable('JobDiagnostics_Test')->clearByDispatchType($dispatchType);
                    switch ($dispatchType) {
                        case 'short_running':
                            $localizedDispatchType = __("Short-Running Job");
                            break;
                        case 'long_running':
                            $localizedDispatchType = __("Long-Running Job");
                            break;
                    }
                    $this->_helper->flashMessenger(__("%s test records successfully cleared!", $localizedDispatchType), 'success');
                } catch (Exception $ex) {
                    $this->_helper->flashMessenger(__("An error occurred: %s", $ex->getMessage()), 'error');
                }
            }
            $this->_helper->redirector('index', null, null, array());
        }
    }

    /**
     * Poll the test record until it finishes running
     * @throws Omeka_Controller_Exception_404
     */
    public function waitAction()
    {
        if (!$this->_helper->acl->isAllowed('show', 'JobDiagnostics_Test'))
        {
            throw new Omeka_Controller_Exception_403;
        }
        if (empty($testRecord = get_db()->getTable('JobDiagnostics_Test')->find($this->getParam('id')))) {
            throw new Omeka_Controller_Exception_404;
        }
        if (!empty($testRecord->finished) || !empty($testRecord->error)) {
            $this->_helper->redirector('index', null, null, array());
        }
        queue_js_file('test_wait');
        $this->view->job_diagnostics_test = $testRecord;
    }

    /**
     * Ajax back-end for wait action.
     * @throws Omeka_Controller_Exception_404
     */
    public function waitAjaxAction()
    {
        if (!$this->_helper->acl->isAllowed('show', 'JobDiagnostics_Test'))
        {
            throw new Omeka_Controller_Exception_403;
        }
        $id = $this->getParam('id');
        if (empty($testRecord = get_db()->getTable('JobDiagnostics_Test')->find($this->getParam('id')))) {
            throw new Omeka_Controller_Exception_404;
        }
        $response = $this->getResponse();
        $this->_helper->viewRenderer->setNoRender();
        $response->setHeader('Content-Type', 'application/json');
        $response->clearBody();
        $this->_killTestIfDead($testRecord);
        $response->setBody(json_encode(array(
            'id' => $testRecord->id,
            'dispatch_type' => $testRecord->dispatch_type,
            'started' => $testRecord->started,
            'finished' => $testRecord->finished,
            'error' => $testRecord->error,
        )));
    }
}
