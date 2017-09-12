<?php

class Table_JobDiagnostics_Test extends Omeka_Db_Table
{
    /**
     * Delete all test records with the given dispatch type
     *
     * @param string $dispatchType The dispatch type.
     */
    public function clearByDispatchType($dispatchType) 
    {
        $this->_db->query("DELETE FROM `{$this->_db->prefix}job_diagnostics_tests` WHERE `dispatch_type` = ?", array($dispatchType));
    }
}
