<?php

class JobDiagnostics_Job_Test extends Omeka_Job_AbstractJob
{
    private $_testId;
    
    /**
     * Create a new JobDiagnostics_Job_Test.
     *
     * @param array $options
     */
    public function __construct(array $options) 
    {
        parent::__construct($options);
        $this->_testId = $options['id'];
    }
    
    /**
     * Main runnable method.
     */
    public function perform() 
    {
        $test = get_db()->getTable('JobDiagnostics_Test')->find($this->_testId);
        if (!empty($test)) {
            $test->finished = date("Y-m-d H:i:s");
            $test->save();
        }
    }
}
