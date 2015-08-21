<?php

namespace UWDOEM\CSRF;


class CSRF {

    /**
     * A list of the "unsafe" HTTP methods for which we shall require a valid CSRF token
     */
    protected static $unsafe_methods = ["POST", "PUT", "PATCH", "DELETE"];

    /**
     * Initialize CSRF token security.
     *
     * Checks if a CSRF token is required for this request, and if one has been provided. Inserts a CSRF token into
     * any form, and inserts a javascript CSRF_TOKEN variable.
     *
     * @throws \Exception if no CSRF token is provided when one is required
     */
    static function init() {

        // Insert a CSRF token into the session, if none is present
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(16));
        }

        // Checks if a CSRF token is required for this request, and, if so, whether the correct one is present
        static::checkCSRF();

        // Create an output buffering callback, to insert our CSRF tokens into the page
        $callback = function ($page) {
            if (isset($_SESSION) && array_key_exists("csrf_token", $_SESSION)) {
                $CSRFToken = $_SESSION['csrf_token'];
                $tokenField = "\n<input type=hidden name=csrf_token value=$CSRFToken>\n";
                $tokenJS = "\n<script>var CSRFTOKEN = '$CSRFToken';</script>\n";

                if (strpos(strtolower($page), "<head>") !== False) {
                    $page = substr_replace($page, "<head>" . $tokenJS, strpos(strtolower($page), "<head>"), 6);
                }

                $lastPosition = strlen($page) - 1;
                while ($lastPosition = strrpos(strtolower($page), "<form", $lastPosition - strlen($page) - 1)) {
                    $formClose = strpos($page, ">", $lastPosition);
                    $page = substr_replace($page, $tokenField, $formClose + 1, 0);
                }
            }

            return $page;
        };

        // Begin output buffering
        ob_start($callback);
    }

    static private function checkCSRF() {

        if (!isset($_SESSION['csrf_token'])) {
            throw new \Exception('No CSRF Token set in $_SESSION. Invoke \OSFAFramework\Etc\Utils::CSRFSetup before ::checkCSRF');
        }

        if (in_array($_SERVER['REQUEST_METHOD'], static::$unsafe_methods)) {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
                if (!headers_sent()) { header("HTTP/1.0 403 Forbidden"); }

                echo "Page error: CSRF token missing or incorrect. If this problem persists, please contact the page administrator.\n";

                throw new \Exception("CSRF token missing or incorrect. Ensure that you are using UWDOEM\\CSRF\\CSRF::init() to insert the CSRF token into submitted forms, and that any AJAX submission methods include the CSRF javascript variable.");
            }
        }
    }
}