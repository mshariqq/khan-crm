<?php
/**
 * Global helper functions for the CRM/ERP system
 * Loads all helper files from the Helpers directory
 */

foreach (glob(__DIR__ . '/*.php') as $filename) {
    if ($filename !== __FILE__) {
        require_once $filename;
    }
}