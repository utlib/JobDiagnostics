<?php

/**
 * Migration 1.0.1: Force "test started" column to be NOT NULL.
 * @package JobDiagnostics
 * @subpackage Migration
 */
class JobDiagnostics_Migration_1_0_1 extends JobDiagnostics_BaseMigration {
    public static $version = '1.0.1';
    
    /**
     * Migrate up
     */
    public function up() {
        $this->_db->query("ALTER TABLE {$this->_db->prefix}job_diagnostics_tests CHANGE COLUMN started started TIMESTAMP DEFAULT NOW() NOT NULL;");
    }
}
