<?php
// Constants

/* ===== ALERT ONLY CONSTANTS ===== */
define("ALERT_ERROR", "danger");
define("ALERT_SUCCESS", "success");
define("ALERT_INFO", "info");
define("ALERT_WARNING", "warning");
define("ALERT_SECONDARY", "secondary");

define('ALERT_TYPES', [
    ALERT_ERROR,
    ALERT_SUCCESS,
    ALERT_INFO,
    ALERT_WARNING,
    ALERT_SECONDARY
]);

define('ALERT_LABELS', [
    ALERT_ERROR => "Error",
    ALERT_SUCCESS => "Success",
    ALERT_INFO => "Info",
    ALERT_WARNING => "Warning",
    ALERT_SECONDARY => "Secondary",
]);

/* ===== PAGINATION ===== */
define("PAGE_LIMIT", 6);

/* ===== REGEX PATTERNS ===== */
define("USERNAME_PATTERN", '/^[a-zA-Z0-9_]{4,12}$/');

?>