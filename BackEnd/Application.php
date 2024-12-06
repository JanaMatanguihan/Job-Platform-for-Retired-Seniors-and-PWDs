<?php

require_once 'Connection.php';
require_once 'operations.php';
require_once 'Validation.php';

class Application {
    private $operations;
    private $validation;

    public function __construct($config) {
        $this->operations = new Operations($config);
        $this->validation = new Validation();
    }

    public function submitApplication($name, $email, $phone, $dob, $gender, $address, $resume) {
        $this->validation->clearAllErrors();

        $this->validation->isValidFullName($name);
        $this->validation->isValidEmail($email);
        $this->validation->isValidContact($phone);
        $this->validation->isValidDate($dob, 'dob');
        $this->validation->isValidGender($gender);
        $this->validation->isValidAddress($address);
        $this->validation->isValidResume($resume);

        $stmt = $this->operations->getConnection()->prepare(
            "SELECT id FROM registration WHERE email = ?"
        );
        $stmt->execute([$email]);
        $registeredUser = $stmt->fetch();

        if (!$registeredUser) {
            $this->validation->addError("email", "Please register first before submitting an application.");
        } else {
            $stmt = $this->operations->getConnection()->prepare(
                "SELECT contact_id FROM contact_info WHERE email = ?"
            );
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $this->validation->addError("email", "You have already submitted an application with this email.");
            }
        }

        $stmt = $this->operations->getConnection()->prepare(
            "SELECT contact_id FROM contact_info WHERE phone_num = ?"
        );
        $stmt->execute([$phone]);
        if ($stmt->fetch()) {
            $this->validation->addError("phone", "This phone number is already registered.");
        }

        if ($this->validation->hasErrors()) {
            return $this->validation->getErrors();
        }

        try {
            $uploadDir = '../uploads/resumes/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileExtension = strtolower(pathinfo($resume['name'], PATHINFO_EXTENSION));
            $fileName = uniqid() . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;

            if (!move_uploaded_file($resume['tmp_name'], $filePath)) {
                return [
                    "success" => false,
                    "errors" => [
                        ["field" => "resume", "message" => "Failed to upload resume"]
                    ]
                ];
            }

            $result = $this->operations->submitApplication($name, $email, $phone, $dob, $gender, $address, $filePath);
            
            if ($result) {
                return [
                    "success" => true,
                    "message" => "Application submitted successfully!",
                    "redirect" => "../Frontend/homepage.html"
                ];
            }

        } catch (Exception $e) {
            return [
                "success" => false,
                "errors" => [
                    ["field" => "general", "message" => "An error occurred: " . $e->getMessage()]
                ]
            ];
        }
    }

    public function close() {
        $this->operations->close();
    }
}

?>
