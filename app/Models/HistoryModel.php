<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\BaseConnection;

class HistoryModel
{
    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    /**
     * Get monthly history table name
     */
    private function tableName(): string
    {
        return 'history_' . date('mY'); // history_012026
    }

    /**
     * Ensure history table exists
     */
    private function ensureTableExists()
    {
        $table = $this->tableName();

        if (!$this->db->tableExists($table)) {

            $sql = "
                CREATE TABLE `$table` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `TableName` VARCHAR(255),
                    `TransactionType` VARCHAR(50),
                    `ValuesBefore` LONGTEXT,
                    `ValuesAfter` LONGTEXT,
                    `TransactionDate` DATETIME,
                    `TransactedBy` INT(10),
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ";

            $this->db->query($sql);
        }
    }

    /**
     * Insert history log
     */
    public function log(
        string $tableName,
        string $transactionType,
        ?array $before,
        ?array $after,
        int $userId
    ) {
        $this->ensureTableExists();

        $this->db->table($this->tableName())->insert([
            'TableName'        => $tableName,
            'TransactionType'  => $transactionType,
            'ValuesBefore'     => $before ? json_encode($before) : null,
            'ValuesAfter'      => $after ? json_encode($after) : null,
            'TransactionDate'  => date('Y-m-d H:i:s'),
            'TransactedBy'     => $userId,
        ]);
    }
}
