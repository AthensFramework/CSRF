<?php

use UWDOEM\CSRF\CSRF;

$in = <<<HTML
<html>
    <head>

    </head>
    <body>
        <form>

        </form>
    </body>
</html>
HTML;

$expected = <<<HTML
<html>
    <head>
<script>var CSRFTOKEN = '2e658f027995c797e13f623f3308cf9c';</script>


    </head>
    <body>
        <form>
<input type=hidden name=csrf_token value=2e658f027995c797e13f623f3308cf9c>


        </form>
    </body>
</html>
HTML;

class CSRFTest extends PHPUnit_Framework_TestCase {

    public function testInsertsCSRF() {
        throw new \Exception("Test not implemented.");
    }

    public function testCheckCSRFNoTokenRequiredNoTokenProvided() {
        // Test no CSRF token required, none provided
        $_SERVER['REQUEST_METHOD'] = "GET";
        CSRF::init();
        ob_end_flush();

        // If we get here without an exception, then the test has passed
        $this->assertTrue(True);
    }

    /**
     * @expectedException \Exception
     */
    public function testCheckCSRFTokenRequiredNoTokenProvided() {
        // Test CSRF token required, but none provided
        $_SERVER['REQUEST_METHOD'] = "POST";
        CSRF::init();
        ob_end_clean();
    }

    public function testCheckCSRFTokenRequiredCorrectTokenProvided() {
        // Test CSRF token required, and token provided
        $_SERVER['REQUEST_METHOD'] = "POST";

        // Set the expected CSRF token
        $_SESSION['csrf_token'] = "testoken";

        // Set the correct CSRF token
        $_POST['csrf_token'] = $_SESSION['csrf_token'];

        CSRF::init();

        ob_end_clean();

        // If we got here without an exception, then the test has passed
        $this->assertTrue(True);
    }

    /**
     * @expectedException \Exception
     */
    public function testCheckCSRFTokenRequiredIncorrectTokenProvided() {
        // Test CSRF token required, and token provided
        $_SERVER['REQUEST_METHOD'] = "POST";

        // Set the expected CSRF token
        $_SESSION['csrf_token'] = "testoken";

        // Set the correct CSRF token
        $_POST['csrf_token'] = "incorecttoken";

        CSRF::init();

        ob_end_clean();

        // If we got here without an exception, then the test has passed
        $this->assertTrue(True);
    }


    public function testCreateSessionCSRF() {
        $_SERVER['REQUEST_METHOD'] = "GET";

        $this->assertFalse(isset($_SESSION['csrf_token']));

        CSRF::init();

        $this->assertTrue(isset($_SESSION['csrf_token']));

        ob_end_clean();
    }
}