<?php
declare(strict_types=1);
namespace Soatok\AnthroKit\Session;

/**
 * Class VolatileSaveHandler
 * @package Soatok\AnthroKit\Session
 */
class VolatileSaveHandler implements \SessionHandlerInterface
{
    private $buf;

    public function __construct(array &$buf)
    {
        $this->buf =& $buf;
    }

    /**
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * @param string $session_id
     * @return bool
     */
    public function destroy($session_id)
    {
        if (!isset($this->buf[$session_id])) {
            return false;
        }
        unset($this->buf[$session_id]);
        return true;
    }

    /**
     * @param int $maxlifetime
     * @return bool
     * @throws \Exception
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * @param string $save_path
     * @param string $name
     * @return bool
     */
    public function open($save_path, $name)
    {
        return true;
    }

    /**
     * @param string $session_id
     * @return string
     */
    public function read($session_id)
    {
        if (!isset($this->buf[$session_id])) {
            $this->buf[$session_id] = '';
        }
        return $this->buf[$session_id];
    }

    /**
     * @param string $session_id
     * @param string $session_data
     * @return bool
     * @throws \Exception
     */
    public function write($session_id, $session_data)
    {
        $this->buf[$session_id] = $session_data;
        return true;
    }
}
