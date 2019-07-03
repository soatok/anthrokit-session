<?php
declare(strict_types=1);
namespace Soatok\AnthroKit\Session\Tests;

use PHPUnit\Framework\TestCase;
use Soatok\AnthroKit\Session\VolatileSaveHandler;

/**
 * Class VolatileSaveHandlerTest
 * @package Soatok\AnthroKit\Session\Tests
 */
class VolatileSaveHandlerTest extends TestCase
{
    private $buf = [];

    private $session_id;

    public function setUp(): void
    {
        $GLOBALS['session_buffer'] = [];
        session_set_save_handler(
            new VolatileSaveHandler($this->buf)
        );
        $this->session_id = bin2hex(random_bytes(16));
        session_id($this->session_id);
    }

    public function testStoreInArray()
    {
        session_start();
        $_SESSION['foo'] = 'bar';
        session_write_close();
        $this->assertNotEmpty($this->buf);
        $this->assertNotEmpty($this->buf[$this->session_id]);

        session_start();
        $_SESSION['foo'] = 'baz';
        session_write_close();
        $this->assertSame($this->buf[$this->session_id], 'foo|s:3:"baz";');
    }
}
