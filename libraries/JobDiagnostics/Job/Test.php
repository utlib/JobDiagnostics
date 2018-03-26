<?php

/**
 * Dummy background job representing a test.
 * @package Job
 */
class JobDiagnostics_Job_Test extends Omeka_Job_AbstractJob
{
    /**
     * The ID for the JobDiagnostics_Test record being run.
     * @var int
     */
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
