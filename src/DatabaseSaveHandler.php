<?php
declare(strict_types=1);
namespace Soatok\AnthroKit\Session;

use ParagonIE\EasyDB\EasyDB;

/**
 * Class DatabaseSaveHandler
 * @package Soatok\AnthroKit\Session
 */
class DatabaseSaveHandler implements \SessionHandlerInterface
{
    private $db;

    private $table;

    public function __construct(EasyDB $db, string $table = 'anthrokit_session')
    {
        $this->db = $db;
        $this->table = $table;
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
        $this->db->beginTransaction();
        $this->db->delete($this->table, ['sessionid' => $session_id]);
        return $this->db->commit();
    }

    /**
     * @param int $maxlifetime
     * @return bool
     * @throws \Exception
     */
    public function gc($maxlifetime)
    {
        $interval = new \DateInterval('PT' . $maxlifetime . 'S');
        $cutoff = (new \DateTime())
            ->sub($interval)
            ->format(\DateTime::ISO8601);

        $this->db->cell(
            "DELETE FROM {$this->table} WHERE updated < ? AND created < ?",
            $cutoff,
            $cutoff
        );
        return $this->db->commit();
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
        $data = $this->db->cell(
            "SELECT sessiondata FROM {$this->table} WHERE sessionid = ?",
            $session_id
        );
        if (!$data) {
            return '';
        }
        return $data;
    }

    /**
     * @param string $session_id
     * @param string $session_data
     * @return bool
     * @throws \Exception
     */
    public function write($session_id, $session_data)
    {
        $this->db->beginTransaction();
        if ($this->db->exists(
            'SELECT count(*) FROM ' . $this->table . ' WHERE sessionid = ?',
            $session_id
        )) {
            $this->db->update(
                $this->table,
                [
                    'session_data' => $session_data,
                    'modified' => (new \DateTime())->format(\DateTime::ISO8601)
                ],
                [
                    'sessionid' => $session_id
                ]
            );
        } else {
            $this->db->insert(
                $this->table,
                [
                    'sessionid' => $session_id,
                    'session_data' => $session_data,
                    'modified' => (new \DateTime())->format(\DateTime::ISO8601)
                ]
            );
        }
        return $this->db->commit();
    }
}
