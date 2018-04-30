<?php

namespace testframework;

use PDO;

/**
 * @author albert
 * 
 * @property \PDO $pdo
 */
class Db extends Object {

    public $dsn;
    public $username;
    public $password;
    private $_pdo;

    public function init() {
        parent::init();

        $this->_pdo = new PDO($this->dsn, $this->username, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    /**
     * @return PDO
     */
    public function getPdo() {
        return $this->_pdo;
    }

    /**
     * @param string $table
     * @param string $condition
     * @param array $params
     * @return array
     */
    public function find(string $table, string $condition = '', array $params = []) {
        $where = $condition !== '' ? ('WHERE ' . $condition) : '';

        $stmt = $this->pdo->prepare("SELECT * FROM {$table} {$where} LIMIT 1");
        $stmt->execute($params);

        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        return $array;
    }

    /**
     * @param string $table
     * @param string $condition
     * @param array $params
     * @return array
     */
    public function findAll(string $table, string $condition = '', array $params = []) {
        $where = $condition !== '' ? ('WHERE ' . $condition) : '';

        $stmt = $this->pdo->prepare("SELECT * FROM {$table} {$where}");
        $stmt->execute($params);

        $array = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $array;
    }

    public function insert(string $table, array $row) {
        $fieldList = join(', ', array_keys($row));
        $psList = ':' . join(', :', array_keys($row));

        $query = $this->pdo->prepare("INSERT INTO {$table} ({$fieldList}) VALUES ({$psList});");
        $query->execute($row);
    }

    public function update(string $table, string $condition, array $row) {

        $setParts = [];
        foreach (array_keys($row) as $key) {
            $setParts[] = $key . ' = :' . $key;
        }

        $query = $this->pdo->prepare("UPDATE {$table} SET " . join(', ', $setParts) . " WHERE {$condition};");
        $query->execute($row);
    }
    
    public function delete(string $table, string $condition, array $params = []) {
        $query = $this->pdo->prepare("DELETE FROM {$table} WHERE {$condition};");
        $query->execute($params);
    }

    /**
     * @return string
     */
    public function getLastInsertId() {
        return $this->pdo->lastInsertId();
    }

}
