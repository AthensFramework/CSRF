<?php

namespace UWDOEM\CSRF\Test;

use PHPUnit_Framework_TestCase;

use UWDOEM\CSRF\CSRF;

/**
 * Class CSRFTest
 *
 * @package UWDOEM\CSRF\Test
 */
class CSRFTest extends PHPUnit_Framework_TestCase
{

    /**
     * Invoking CSRF::init() should cause any outputted page to include javascript and form input
     * CSRF tokens.
     *
     * @return void
     */
    public function testInsertsCSRF()
    {
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

    /**
     * Invoking CSRF::init() should NOT insert a CSRF token field into a GET form.
     *
     * @return void
     */
    public function testDoesntInsertCSRFToGETForm()
    {
        $inPOST = <<<HTML
<html>
    <body>
        <form>

        </form>
    </body>
</html>
HTML;
        $inGET = <<<HTML
<html>
    <body>
        <form method="GET">

        </form>
    </body>
</html>
HTML;

        // Test no CSRF token required, none provided
        $_SERVER['REQUEST_METHOD'] = "GET";

        ob_start();
        CSRF::init();
        echo $inPOST;
        ob_end_flush();
        $resultPOST = ob_get_clean();

        ob_start();
        CSRF::init();
        echo $inGET;
        ob_end_flush();
        $resultGET = ob_get_clean();

        $this->assertContains("value=" . $_SESSION['csrf_token'], $resultPOST);
        $this->assertNotContains("value=" . $_SESSION['csrf_token'], $resultGET);
    }

    /**
     * CSRF::init() should be able to insert to a form tag with multiple attributes and lines.
     *
     * @return void
     */
    public function testDoesntInsertComplicatedFormTag()
    {
        $inComplicated = <<<HTML
<html>
    <body>
        <form id="test-form"
              class="class-a class-b">

        </form>
    </body>
</html>
HTML;

        // Test no CSRF token required, none provided
        $_SERVER['REQUEST_METHOD'] = "GET";

        ob_start();
        CSRF::init();
        echo $inComplicated;
        ob_end_flush();
        $resultComplicated = ob_get_clean();

        $this->assertContains("value=" . $_SESSION['csrf_token'], $resultComplicated);
    }

    /**
     * Invoking the CSRF::init() method shall cause a CSRF token to be stored
     * in the visitor's session.
     *
     * @return void
     */
    public function testCreateSessionCSRF()
    {
        $_SERVER['REQUEST_METHOD'] = "GET";

        $this->assertFalse(isset($_SESSION['csrf_token']));

        CSRF::init();
        $this->assertTrue(isset($_SESSION['csrf_token']));
        ob_get_clean();
    }

    /**
     * If the method is safe, then no CSRF token shall be required.
     *
     * @return void
     */
    public function testCheckCSRFNoTokenRequiredNoTokenProvided()
    {
        // Test no CSRF token required, none provided
        $_SERVER['REQUEST_METHOD'] = "GET";

        CSRF::init();
        ob_get_clean();

        // If we get here without an exception, then the test has passed
        $this->assertTrue(true);
    }

    /**
     * If the method is unsafe and no CSRF token is provided, then an error shall be
     * raised.
     *
     * @return void
     * @expectedException \Exception
     */
    public function testCheckCSRFTokenRequiredNoTokenProvided()
    {
        // Test CSRF token required, but none provided
        $_SERVER['REQUEST_METHOD'] = "POST";

        CSRF::init();
        ob_get_clean();
    }

    /**
     * If the method is unsafe and the correct CSRF token is provided, then
     * no error shall be raised.
     *
     * @return void
     */
    public function testCheckCSRFTokenRequiredCorrectTokenProvided()
    {
        // Test CSRF token required, and token provided
        $_SERVER['REQUEST_METHOD'] = "POST";

        // Set the expected CSRF token
        $_SESSION['csrf_token'] = "testoken";

        // Set the correct CSRF token
        $_POST['csrf_token'] = $_SESSION['csrf_token'];

        CSRF::init();
        ob_get_clean();

        // If we got here without an exception, then the test has passed
        $this->assertTrue(true);
    }

    /**
     * If the method is not safe and a CSRF token is provided but incorrect, then
     * an error shall be raised.
     *
     * @return void
     * @expectedException \Exception
     */
    public function testCheckCSRFTokenRequiredIncorrectTokenProvided()
    {
        // Test CSRF token required, and token provided
        $_SERVER['REQUEST_METHOD'] = "POST";

        // Set the expected CSRF token
        $_SESSION['csrf_token'] = "testoken";

        // Set the correct CSRF token
        $_POST['csrf_token'] = "incorecttoken";

        CSRF::init();
    }
}
