<?php

require_once 'Connection.php';
require_once 'operations.php';

class Registration {
    private $operations;

    public function __construct($config) {
        $this->operations = new Operations($config);
    }

    public function register($fullName, $email, $password, $userType) {
        try {
            if ($this->operations->checkExistingEmail($email)) {
                return [
                    "success" => false,
                    "errors" => [
                        ["field" => "email", "message" => "This email is already registered"]
                    ]
                ];
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userId = $this->operations->createUser($fullName, $email, $hashedPassword, $userType);

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $userId;

            return [
                "success" => true,
                "message" => "Registration successful!",
                "redirect" => "../Frontend/SignUp.html"
            ];

        } catch (Exception $e) {
            return [
                "success" => false,
                "errors" => [
                    ["field" => "general", "message" => $e->getMessage()]
                ]
            ];
        }
    }

    public function close() {
        $this->operations->close();
    }
}

?>
