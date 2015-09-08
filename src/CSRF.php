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

        // Insert a CSRF token into the session
        static::generateToken();

        // Checks if a CSRF token is required for this request, and, if so, whether the correct one is present
        static::checkCSRF();

        // Begin output buffering, with callback to insert our CSRF tokens into the page
        ob_start(static::generateCallback(static::getToken()));
    }

    private static function generateToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(16));
        }
    }

    private static function getToken() {
        return $_SESSION['csrf_token'];
    }

    static protected function generateCallback($token) {
        return function ($page) use ($token) {

            $tokenField = "\n<input type=hidden name=csrf_token value=$token>\n";
            $tokenJS = "\n<script>var CSRFTOKEN = '$token';</script>\n";

            if (strpos(strtolower($page), "<head>") !== False) {
                $page = substr_replace($page, "<head>" . $tokenJS, strpos(strtolower($page), "<head>"), 6);
            }

            $lastPosition = strlen($page) - 1;
            while ($lastPosition = strrpos(strtolower($page), "<form", $lastPosition - strlen($page) - 1)) {
                $formClose = strpos($page, ">", $lastPosition);
                $page = substr_replace($page, $tokenField, $formClose + 1, 0);
            }

            return $page;
        };
    }

    static protected function checkCSRF() {

        if (!array_key_exists("csrf_token", $_SESSION)) {
            throw new \Exception('No CSRF Token set in $_SESSION. Invoke \UWDOEM\CSRF\CSRF::init before ::checkCSRF');
        }

        if (in_array($_SERVER['REQUEST_METHOD'], static::$unsafe_methods)) {
            if (!array_key_exists("csrf_token", $_POST) || $_POST['csrf_token'] != static::getToken()) {
                if (!headers_sent()) { header("HTTP/1.0 403 Forbidden"); }

                echo "Page error: CSRF token missing or incorrect. If this problem persists, please contact the page administrator.\n";

                throw new \Exception("CSRF token missing or incorrect. Ensure that you are using UWDOEM\\CSRF\\CSRF::init() to insert the CSRF token into submitted forms, and that any AJAX submission methods include the CSRF javascript variable.");
            }
        }
    }
}