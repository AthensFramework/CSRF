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
<script>var CSRFTOKEN = '{{ token }}';</script>


    </head>
    <body>
        <form>
<input type=hidden name=csrf_token value={{ token }}>


        </form>
    </body>
</html>
HTML;

class CSRFTest extends PHPUnit_Framework_TestCase {

    public function testInsertsCSRF() {
        global $in, $expected;

        // Test no CSRF token required, none provided
        $_SERVER['REQUEST_METHOD'] = "GET";

        ob_start();

        CSRF::init();
        echo $in;
        ob_end_flush();

        $result = ob_get_clean();

        $newExpected = str_replace("{{ token }}", $_SESSION['csrf_token'], $expected);

        $result = str_replace("\r\n", "\n", $result);
        $newExpected = str_replace("\r\n", "\n", $newExpected);

        // If we get here without an exception, then the test has passed
        $this->assertEquals($newExpected, $result);
    }

    public function testCheckCSRFNoTokenRequiredNoTokenProvided() {
        // Test no CSRF token required, none provided
        $_SERVER['REQUEST_METHOD'] = "GET";

        CSRF::init();
        ob_get_clean();

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
        ob_get_clean();
    }

    public function testCheckCSRFTokenRequiredCorrectTokenProvided() {
        // Test CSRF token required, and token provided
        $_SERVER['REQUEST_METHOD'] = "POST";

        // Set the expected CSRF token
        $_SESSION['csrf_token'] = "testoken";

        // Set the correct CSRF token
        $_POST['csrf_token'] = $_SESSION['csrf_token'];

        CSRF::init();
        ob_get_clean();

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
    }


    public function testCreateSessionCSRF() {
        $_SERVER['REQUEST_METHOD'] = "GET";

        $this->assertFalse(isset($_SESSION['csrf_token']));

        CSRF::init();
        $this->assertTrue(isset($_SESSION['csrf_token']));
        ob_get_clean();
    }
}