<?php
/**
 * Base Database Model Class
 * คลาสฐานสำหรับจัดการฐานข้อมูล
 * 
 * ใช้ PDO สำหรับเชื่อมต่อและจัดการฐานข้อมูล
 * รองรับ PHP 8.0+ features
 */

class Database
{
    /**
     * PDO Instance
     * @var PDO|null
     */
    protected ?PDO $pdo = null;

    /**
     * PDO Statement
     * @var PDOStatement|null
     */
    protected ?PDOStatement $stmt = null;

    /**
     * Constructor - สร้างการเชื่อมต่อฐานข้อมูล
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * เชื่อมต่อฐานข้อมูล
     * 
     * @return void
     */
    private function connect(): void
    {
        if ($this->pdo === null) {
            $this->pdo = getDatabaseConnection();
        }
    }

    /**
     * เตรียม SQL Query
     * 
     * @param string $sql
     * @return self
     */
    public function query(string $sql): self
    {
        try {
            $this->stmt = $this->pdo->prepare($sql);
            return $this;
        } catch (PDOException $e) {
            $this->handleError($e);
            throw $e;
        }
    }

    /**
     * Bind ค่าเข้ากับ Parameter
     * 
     * @param string|int $param
     * @param mixed $value
     * @param int|null $type
     * @return self
     */
    public function bind(string|int $param, mixed $value, ?int $type = null): self
    {
        // กำหนด type อัตโนมัติถ้าไม่ระบุ
        if ($type === null) {
            $type = match (true) {
                is_int($value) => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                is_null($value) => PDO::PARAM_NULL,
                default => PDO::PARAM_STR
            };
        }

        $this->stmt->bindValue($param, $value, $type);
        return $this;
    }

    /**
     * Execute SQL Query
     * 
     * @param array $params (optional) - Array ของ parameters สำหรับ bind
     * @return bool
     */
    public function execute(array $params = []): bool
    {
        try {
            if (!empty($params)) {
                return $this->stmt->execute($params);
            }
            return $this->stmt->execute();
        } catch (PDOException $e) {
            $this->handleError($e);
            return false;
        }
    }

    /**
     * ดึงข้อมูลแถวเดียว
     * 
     * @return array|false
     */
    public function fetch(): array|false
    {
        $this->execute();
        return $this->stmt->fetch();
    }

    /**
     * ดึงข้อมูลทั้งหมด
     * 
     * @return array
     */
    public function fetchAll(): array
    {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    /**
     * ดึงข้อมูลคอลัมน์เดียว
     * 
     * @param int $column (default: 0)
     * @return mixed
     */
    public function fetchColumn(int $column = 0): mixed
    {
        $this->execute();
        return $this->stmt->fetchColumn($column);
    }

    /**
     * นับจำนวนแถวที่ได้รับผลกระทบ
     * 
     * @return int
     */
    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    /**
     * ดึง ID ล่าสุดที่ Insert
     * 
     * @return string
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * เริ่ม Transaction
     * 
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit Transaction
     * 
     * @return bool
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback Transaction
     * 
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * ตรวจสอบว่าอยู่ใน Transaction หรือไม่
     * 
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    /**
     * Insert ข้อมูล
     * 
     * @param string $table
     * @param array $data
     * @return bool|string (false = fail, string = lastInsertId)
     */
    public function insert(string $table, array $data): bool|string
    {
        try {
            // สร้าง SQL
            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));

            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

            $this->query($sql);

            // Bind values
            foreach ($data as $key => $value) {
                $this->bind(":{$key}", $value);
            }

            if ($this->execute()) {
                return $this->lastInsertId();
            }

            return false;

        } catch (PDOException $e) {
            $this->handleError($e);
            return false;
        }
    }

    /**
     * Update ข้อมูล
     * 
     * @param string $table
     * @param array $data
     * @param array $where
     * @return bool
     */
    public function update(string $table, array $data, array $where): bool
    {
        try {
            // สร้าง SET clause
            $set = [];
            foreach ($data as $key => $value) {
                $set[] = "{$key} = :set_{$key}";
            }
            $setClause = implode(', ', $set);

            // สร้าง WHERE clause
            $whereConditions = [];
            foreach ($where as $key => $value) {
                $whereConditions[] = "{$key} = :where_{$key}";
            }
            $whereClause = implode(' AND ', $whereConditions);

            $sql = "UPDATE {$table} SET {$setClause} WHERE {$whereClause}";

            $this->query($sql);

            // Bind SET values
            foreach ($data as $key => $value) {
                $this->bind(":set_{$key}", $value);
            }

            // Bind WHERE values
            foreach ($where as $key => $value) {
                $this->bind(":where_{$key}", $value);
            }

            return $this->execute();

        } catch (PDOException $e) {
            $this->handleError($e);
            return false;
        }
    }

    /**
     * Delete ข้อมูล
     * 
     * @param string $table
     * @param array $where
     * @return bool
     */
    public function delete(string $table, array $where): bool
    {
        try {
            // สร้าง WHERE clause
            $whereConditions = [];
            foreach ($where as $key => $value) {
                $whereConditions[] = "{$key} = :{$key}";
            }
            $whereClause = implode(' AND ', $whereConditions);

            $sql = "DELETE FROM {$table} WHERE {$whereClause}";

            $this->query($sql);

            // Bind values
            foreach ($where as $key => $value) {
                $this->bind(":{$key}", $value);
            }

            return $this->execute();

        } catch (PDOException $e) {
            $this->handleError($e);
            return false;
        }
    }

    /**
     * Debug SQL Query
     * 
     * @return void
     */
    public function debugDumpParams(): void
    {
        if ($this->stmt) {
            $this->stmt->debugDumpParams();
        }
    }

    /**
     * จัดการ Error
     * 
     * @param PDOException $e
     * @return void
     */
    private function handleError(PDOException $e): void
    {
        // Log error
        $errorMessage = date('Y-m-d H:i:s') . ' - ' . $e->getMessage() . PHP_EOL;
        $errorMessage .= 'File: ' . $e->getFile() . PHP_EOL;
        $errorMessage .= 'Line: ' . $e->getLine() . PHP_EOL;
        $errorMessage .= 'SQL State: ' . $e->getCode() . PHP_EOL;
        $errorMessage .= '-----------------------------------' . PHP_EOL;

        // เขียน log ลงไฟล์
        if (defined('ERROR_LOG_FILE')) {
            error_log($errorMessage, 3, ERROR_LOG_FILE);
        } else {
            error_log($errorMessage);
        }

        // แสดง error ในโหมด development
        if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
            echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 5px;">';
            echo '<strong>Database Error:</strong><br>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<strong>File:</strong> ' . $e->getFile() . '<br>';
            echo '<strong>Line:</strong> ' . $e->getLine() . '<br>';
            echo '</div>';
        }
    }

    /**
     * ปิดการเชื่อมต่อ
     * 
     * @return void
     */
    public function close(): void
    {
        $this->stmt = null;
        $this->pdo = null;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->close();
    }
}