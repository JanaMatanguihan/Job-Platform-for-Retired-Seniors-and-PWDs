<?php

require_once 'Connection.php';

class Operations {
    private $db;

    public function __construct($config) {
        $this->db = new DBConn($config);
    }

    public function getConnection() {
        return $this->db->getConnection();
    }

    public function checkExistingEmail($email) {
        $stmt = $this->db->getConnection()->prepare(
            "SELECT id FROM registration WHERE email = ?"
        );
        $stmt->execute([$email]);
        $registrationExists = $stmt->fetch();

        $stmt = $this->db->getConnection()->prepare(
            "SELECT contact_id FROM contact_info WHERE email = ?"
        );
        $stmt->execute([$email]);
        $contactExists = $stmt->fetch();

        return ($registrationExists || $contactExists) ? true : false;
    }

    public function createUser($fullName, $email, $hashedPassword, $userType) {
        try {
            $stmt = $this->db->getConnection()->prepare(
                "INSERT INTO registration (fullName, email, password, userType) 
                 VALUES (?, ?, ?, ?)"
            );
            
            $stmt->execute([$fullName, $email, $hashedPassword, $userType]);
            return $this->db->getConnection()->lastInsertId();

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                throw new Exception("This email is already registered.");
            }
            throw new Exception("Database error while creating user: " . $e->getMessage());
        }
    }

    public function submitApplication($name, $email, $phone, $dob, $gender, $address, $resumePath) {
        try {
            $this->db->getConnection()->beginTransaction();

            $stmt = $this->db->getConnection()->prepare(
                "SELECT id FROM registration WHERE email = ?"
            );
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                $this->db->getConnection()->rollBack();
                throw new Exception("Please register first before submitting an application.");
            }

            $userId = $user['id'];

            try {
                $stmt = $this->db->getConnection()->prepare(
                    "INSERT INTO user_details (user_id, name, dob, gender) 
                     VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([$userId, $name, $dob, $gender]);

                $stmt = $this->db->getConnection()->prepare(
                    "INSERT INTO contact_info (user_id, phone_num, email, address) 
                     VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([$userId, $phone, $email, $address]);

                $stmt = $this->db->getConnection()->prepare(
                    "INSERT INTO application_form (user_id, file) 
                     VALUES (?, ?)"
                );
                $stmt->execute([$userId, $resumePath]);

                $this->db->getConnection()->commit();
                return true;

            } catch (Exception $e) {
                $this->db->getConnection()->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            if ($this->db->getConnection()->inTransaction()) {
                $this->db->getConnection()->rollBack();
            }
            throw $e;
        }
    }

    public function close() {
        $this->db->close();
    }
}

?>