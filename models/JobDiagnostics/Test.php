<?php

/**
 * A test run record for the job queue.
 * @package models
 */
class JobDiagnostics_Test extends Omeka_Record_AbstractRecord
{
    /**
     * The ID key.
     * @var int
     */
    public $id;
    
    /**
     * The type of dispatching used for this test.
     * @var string
     */
    public $dispatch_type;
    
    /**
     * The time that the test began.
     * @var datetime
     */
    public $started;
    
    /**
     * The time that the test finished.
     * @var datetime
     */
    public $finished;
    
    /**
     * The error resulting from attempts to start this test, if any.
     * @var string|null
     */
    public $error;
}
