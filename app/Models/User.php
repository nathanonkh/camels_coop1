<?php
/**
 * User Model
 * จัดการข้อมูลผู้ใช้งานและการ Authentication
 * 
 * PHP 8.0+
 */

class User extends Database
{
    /**
     * Role mapping
     */
    const ROLE_ADMIN = 1;
    const ROLE_USER = 2;
    const ROLE_VIEWER = 3;

    /**
     * แปลง role number เป็น string
     */
    private function getRoleString(int $role): string
    {
        return match ($role) {
            self::ROLE_ADMIN => 'admin',
            self::ROLE_VIEWER => 'viewer',
            default => 'user'
        };
    }

    /**
     * แปลง role string เป็น number
     */
    private function getRoleNumber(string $role): int
    {
        return match ($role) {
            'admin' => self::ROLE_ADMIN,
            'viewer' => self::ROLE_VIEWER,
            default => self::ROLE_USER
        };
    }

    /**
     * ตรวจสอบการ Login
     * 
     * @param string $username
     * @param string $password
     * @return array|false - คืนค่าข้อมูล user ถ้า login สำเร็จ
     */
    public function login(string $username, string $password): array|false
    {
        try {
            // ดึงข้อมูล user จากฐานข้อมูล
            $sql = "SELECT user_id, username, password, full_name, email, role, status 
                    FROM users 
                    WHERE username = :username 
                    AND status = 1 
                    LIMIT 1";

            $this->query($sql);
            $this->bind(':username', $username);
            $user = $this->fetch();

            // ตรวจสอบว่าพบ user หรือไม่
            if (!$user) {
                return false;
            }

            // ตรวจสอบรหัสผ่าน (รองรับทั้ง SHA2 และ password_hash)
            $passwordMatch = false;
            if (hash('sha256', $password) === $user['password']) {
                $passwordMatch = true;
            } elseif (password_verify($password, $user['password'])) {
                $passwordMatch = true;
            }

            if ($passwordMatch) {
                // ลบ password ออกจาก array ก่อน return
                unset($user['password']);

                // แปลง role เป็น string
                $user['role'] = $this->getRoleString((int) $user['role']);

                // อัปเดตเวลา login ล่าสุด
                $this->updateLastLogin($user['user_id']);

                // บันทึก Activity Log
                $this->logActivity($user['user_id'], 'login', 'เข้าสู่ระบบ');

                return $user;
            }

            return false;

        } catch (Exception $e) {
            error_log("Login Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงข้อมูล User ตาม ID
     * 
     * @param int $userId
     * @return array|false
     */
    public function getUserById(int $userId): array|false
    {
        try {
            $sql = "SELECT user_id, username, full_name, email, role, status, 
                           last_login, created_date
                    FROM users 
                    WHERE user_id = :user_id 
                    LIMIT 1";

            $this->query($sql);
            $this->bind(':user_id', $userId);
            $result = $this->fetch();

            if ($result) {
                $result['role'] = $this->getRoleString((int) $result['role']);
            }

            return $result;

        } catch (Exception $e) {
            error_log("Get User Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงข้อมูล User ตาม Username
     * 
     * @param string $username
     * @return array|false
     */
    public function getUserByUsername(string $username): array|false
    {
        try {
            $sql = "SELECT user_id, username, full_name, email, role, status
                    FROM users 
                    WHERE username = :username 
                    LIMIT 1";

            $this->query($sql);
            $this->bind(':username', $username);
            $result = $this->fetch();

            if ($result) {
                $result['role'] = $this->getRoleString((int) $result['role']);
            }

            return $result;

        } catch (Exception $e) {
            error_log("Get User by Username Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบว่าเป็น Admin หรือไม่
     * 
     * @param int $userId
     * @return bool
     */
    public function isAdmin(int $userId): bool
    {
        $user = $this->getUserById($userId);
        return $user && $user['role'] === 'admin';
    }

    /**
     * ตรวจสอบ Role ของ User
     * 
     * @param int $userId
     * @param string $role
     * @return bool
     */
    public function hasRole(int $userId, string $role): bool
    {
        $user = $this->getUserById($userId);
        return $user && $user['role'] === $role;
    }

    /**
     * อัปเดตเวลา Login ล่าสุด
     * 
     * @param int $userId
     * @return bool
     */
    private function updateLastLogin(int $userId): bool
    {
        try {
            $sql = "UPDATE users 
                    SET last_login = NOW() 
                    WHERE user_id = :user_id";

            $this->query($sql);
            $this->bind(':user_id', $userId);

            return $this->execute();

        } catch (Exception $e) {
            error_log("Update Last Login Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * สร้าง User ใหม่
     * 
     * @param array $data
     * @return int|false - คืนค่า user_id ถ้าสำเร็จ
     */
    public function createUser(array $data): int|false
    {
        try {
            // Hash รหัสผ่าน
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            // แปลง role เป็น number
            if (isset($data['role']) && is_string($data['role'])) {
                $data['role'] = $this->getRoleNumber($data['role']);
            }

            $insertId = $this->insert('users', $data);

            if ($insertId) {
                // บันทึก Activity Log
                $this->logActivity($insertId, 'register', 'สร้างบัญชีผู้ใช้ใหม่');
                return (int) $insertId;
            }

            return false;

        } catch (Exception $e) {
            error_log("Create User Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * อัปเดตข้อมูล User
     * 
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function updateUser(int $userId, array $data): bool
    {
        try {
            // ถ้ามีการเปลี่ยนรหัสผ่าน ให้ hash ใหม่
            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            // แปลง role เป็น number
            if (isset($data['role']) && is_string($data['role'])) {
                $data['role'] = $this->getRoleNumber($data['role']);
            }

            $success = $this->update('users', $data, ['user_id' => $userId]);

            if ($success) {
                // บันทึก Activity Log
                $this->logActivity($userId, 'update_profile', 'อัปเดตข้อมูลผู้ใช้');
            }

            return $success;

        } catch (Exception $e) {
            error_log("Update User Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ลบ User (Soft Delete)
     * 
     * @param int $userId
     * @return bool
     */
    public function deleteUser(int $userId): bool
    {
        try {
            // Soft delete - เปลี่ยน status เป็น 0
            $success = $this->update('users', ['status' => 0], ['user_id' => $userId]);

            if ($success) {
                // บันทึก Activity Log
                $this->logActivity($userId, 'delete', 'ลบบัญชีผู้ใช้');
            }

            return $success;

        } catch (Exception $e) {
            error_log("Delete User Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงรายการ Users ทั้งหมด
     * 
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllUsers(int $limit = 50, int $offset = 0): array
    {
        try {
            $sql = "SELECT user_id, username, full_name, email, role, status, 
                           last_login, created_date
                    FROM users 
                    ORDER BY created_date DESC
                    LIMIT :limit OFFSET :offset";

            $this->query($sql);
            $this->bind(':limit', $limit, PDO::PARAM_INT);
            $this->bind(':offset', $offset, PDO::PARAM_INT);

            $users = $this->fetchAll();

            // แปลง role เป็น string
            foreach ($users as &$user) {
                $user['role'] = $this->getRoleString((int) $user['role']);
            }

            return $users;

        } catch (Exception $e) {
            error_log("Get All Users Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * นับจำนวน Users
     * 
     * @return int
     */
    public function countUsers(): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM users WHERE status = 1";
            $this->query($sql);
            $result = $this->fetch();

            return (int) ($result['total'] ?? 0);

        } catch (Exception $e) {
            error_log("Count Users Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * เปลี่ยนรหัสผ่าน
     * 
     * @param int $userId
     * @param string $oldPassword
     * @param string $newPassword
     * @return bool
     */
    public function changePassword(int $userId, string $oldPassword, string $newPassword): bool
    {
        try {
            // ดึงข้อมูล user พร้อมรหัสผ่าน
            $sql = "SELECT password FROM users WHERE user_id = :user_id LIMIT 1";
            $this->query($sql);
            $this->bind(':user_id', $userId);
            $user = $this->fetch();

            if (!$user) {
                return false;
            }

            // ตรวจสอบรหัสผ่านเดิม
            $passwordMatch = false;
            if (hash('sha256', $oldPassword) === $user['password']) {
                $passwordMatch = true;
            } elseif (password_verify($oldPassword, $user['password'])) {
                $passwordMatch = true;
            }

            if (!$passwordMatch) {
                return false;
            }

            // อัปเดตรหัสผ่านใหม่
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $success = $this->update(
                'users',
                ['password' => $hashedPassword],
                ['user_id' => $userId]
            );

            if ($success) {
                // บันทึก Activity Log
                $this->logActivity($userId, 'change_password', 'เปลี่ยนรหัสผ่าน');
            }

            return $success;

        } catch (Exception $e) {
            error_log("Change Password Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * บันทึก Activity Log
     * 
     * @param int $userId
     * @param string $action
     * @param string $description
     * @return bool
     */
    private function logActivity(int $userId, string $action, string $description): bool
    {
        try {
            $data = [
                'user_id' => $userId,
                'action' => $action,
                'description' => $description,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                'created_date' => date('Y-m-d H:i:s')
            ];

            return (bool) $this->insert('activity_logs', $data);

        } catch (Exception $e) {
            error_log("Log Activity Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบว่า Username ซ้ำหรือไม่
     * 
     * @param string $username
     * @param int|null $excludeUserId
     * @return bool
     */
    public function isUsernameExists(string $username, ?int $excludeUserId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM users WHERE username = :username";

            if ($excludeUserId) {
                $sql .= " AND user_id != :exclude_id";
            }

            $this->query($sql);
            $this->bind(':username', $username);

            if ($excludeUserId) {
                $this->bind(':exclude_id', $excludeUserId);
            }

            $result = $this->fetch();
            return (int) $result['count'] > 0;

        } catch (Exception $e) {
            error_log("Check Username Exists Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบว่า Email ซ้ำหรือไม่
     * 
     * @param string $email
     * @param int|null $excludeUserId
     * @return bool
     */
    public function isEmailExists(string $email, ?int $excludeUserId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";

            if ($excludeUserId) {
                $sql .= " AND user_id != :exclude_id";
            }

            $this->query($sql);
            $this->bind(':email', $email);

            if ($excludeUserId) {
                $this->bind(':exclude_id', $excludeUserId);
            }

            $result = $this->fetch();
            return (int) $result['count'] > 0;

        } catch (Exception $e) {
            error_log("Check Email Exists Error: " . $e->getMessage());
            return false;
        }
    }
}