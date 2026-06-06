<?php
/**
 * Single-instance cron lock.
 *
 * Prevents overlapping runs of the same cron script. Previously, if a run took
 * longer than the 1-minute cron interval, a second run could start and process
 * the same orders again -> double refunds to client balances. This guard makes
 * each cron job run as a single instance at a time.
 *
 * Usage (place right after app/init.php is required):
 *   require __DIR__.'/_cron_lock.php';
 *   cron_lock(basename(__FILE__));
 */

if (!defined('BASEPATH')) {
    die('Direct access to the script is not allowed');
}

if (!function_exists('cron_lock')) {
    /**
     * Acquire an exclusive, non-blocking lock for the given cron job name.
     * If another instance is already running, the current run exits quietly.
     *
     * The lock is held for the lifetime of the request and is released
     * automatically when the script finishes (even on fatal error/timeout).
     *
     * @param string $name Unique cron name, usually basename(__FILE__).
     */
    function cron_lock($name)
    {
        $lockDir = __DIR__ . '/locks';
        if (!is_dir($lockDir)) {
            @mkdir($lockDir, 0775, true);
        }

        $safeName = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $name);
        $lockFile = $lockDir . '/' . $safeName . '.lock';

        $handle = fopen($lockFile, 'c');
        if ($handle === false) {
            // Fail open, but make the problem visible in the error log.
            error_log("[cron_lock] Unable to open lock file: $lockFile");
            return;
        }

        if (!flock($handle, LOCK_EX | LOCK_NB)) {
            // Another instance already holds the lock -> skip this run.
            error_log("[cron_lock] '$name' is already running; skipping this run.");
            exit(0);
        }

        // Keep the handle referenced for the whole request so the OS keeps
        // the lock until the process ends.
        $GLOBALS['__cron_lock_handles'][$name] = $handle;

        // Best-effort debug info (pid + start time) inside the lock file.
        @ftruncate($handle, 0);
        @fwrite($handle, getmypid() . ' ' . date('Y-m-d H:i:s'));
        @fflush($handle);
    }
}
