<?php

class Validation {
    private $errors = [];

    // Validation constants
    private const NAME_MIN_LENGTH = 2;
    private const NAME_MAX_LENGTH = 50;
    private const NAME_PATTERN = '/^[a-zA-ZÀ-ÿ\s\'-]+$/';
    private const PHONE_PATTERN = '/^[0-9]{11}$/';
    private const ADDRESS_MIN_LENGTH = 10;
    private const ADDRESS_PATTERN = '/^[a-zA-Z0-9\s\.,#-]+$/';

    public function clearAllErrors() {
        $this->errors = [];
    }

    public function addError($field, $message) {
        $this->errors[] = ["field" => $field, "message" => $message];
    }

    public function hasErrors() {
        return !empty($this->errors);
    }

    public function getErrors() {
        return [
            "success" => false,
            "errors" => $this->errors
        ];
    }

    public function isValidFullName($name) {
        if (empty($name)) {
            $this->addError("name", "Full name is required");
        } elseif (strlen($name) < self::NAME_MIN_LENGTH) {
            $this->addError("name", "Name is too short");
        } elseif (strlen($name) > self::NAME_MAX_LENGTH) {
            $this->addError("name", "Name is too long");
        } elseif (!preg_match(self::NAME_PATTERN, $name)) {
            $this->addError("name", "Name contains invalid characters");
        }
        return empty($this->errors);
    }

    public function isValidEmail($email) {
        if (empty($email)) {
            $this->addError("email", "Email is required");
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addError("email", "Invalid email format");
        }
        return empty($this->errors);
    }

    public function isValidContact($contact) {
        if (empty($contact)) {
            $this->addError("phone", "Contact number is required");
        } elseif (!preg_match(self::PHONE_PATTERN, $contact)) {
            $this->addError("phone", "Contact number must be exactly 11 digits");
        }
        return empty($this->errors);
    }

    public function isValidDate($date, $field = 'date') {
        if (empty($date)) {
            $this->addError($field, "Date is required");
        } else {
            $d = DateTime::createFromFormat('Y-m-d', $date);
            if (!$d || $d->format('Y-m-d') !== $date) {
                $this->addError($field, "Invalid date format");
            } else if ($field === 'dob') {
                // Check if date is not in future
                $today = new DateTime();
                if ($d > $today) {
                    $this->addError($field, "Date of birth cannot be in future");
                }
                // Check minimum age if it's a date of birth
                $minAge = new DateTime('-18 years');
                if ($d > $minAge) {
                    $this->addError($field, "Must be at least 18 years old");
                }
            }
        }
        return empty($this->errors);
    }

    public function isValidGender($gender) {
        $validGenders = ['Male', 'Female', 'Other'];
        if (empty($gender)) {
            $this->addError("gender", "Gender is required");
        } elseif (!in_array($gender, $validGenders)) {
            $this->addError("gender", "Invalid gender selection");
        }
        return empty($this->errors);
    }

    public function isValidAddress($address) {
        if (empty($address)) {
            $this->addError("address", "Address is required");
        } elseif (strlen($address) < self::ADDRESS_MIN_LENGTH) {
            $this->addError("address", "Address must be at least 10 characters long");
        } elseif (!preg_match(self::ADDRESS_PATTERN, $address)) {
            $this->addError("address", "Address contains invalid characters");
        }
        return empty($this->errors);
    }

    public function isValidResume($file) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $this->addError("resume", "Resume is required");
            return false;
        }

        $maxSize = 5242880; // 5MB
        if ($file['size'] > $maxSize) {
            $this->addError("resume", "Resume size must be less than 5MB");
            return false;
        }

        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($file['type'], $allowedTypes)) {
            $this->addError("resume", "Only PDF and Word documents are allowed");
            return false;
        }

        return true;
    }
}

?>
