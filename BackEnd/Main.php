<?php

require_once 'Connection.php';
require_once 'Validation.php';
require_once 'Registration.php';
require_once 'operations.php';
require_once 'Application.php';

// Database configuration
$config = [
    'host' => 'localhost',
    'dbname' => 'Gabay',
    'username' => 'root',
    'password' => 'root'
];

// Handle all POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    if (empty($action)) {
        echo json_encode([
            'success' => false,
            'errors' => [
                ['field' => 'general', 'message' => 'No action specified']
            ]
        ]);
        exit;
    }
    
    try {
        switch ($action) {
            case 'register':
                $fullName = $_POST['fullName'] ?? '';
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                $userType = $_POST['userType'] ?? '';

                $registration = new Registration($config);
                $response = $registration->register($fullName, $email, $password, $userType);
                $registration->close();
                
                echo json_encode($response);
                break;

            case 'application':
                $name = $_POST['name'] ?? '';
                $email = $_POST['email'] ?? '';
                $phone = $_POST['phone'] ?? '';
                $dob = $_POST['dob'] ?? '';
                $gender = $_POST['gender'] ?? '';
                $address = $_POST['address'] ?? '';
                $resume = $_FILES['resume'] ?? null;

                $application = new Application($config);
                $response = $application->submitApplication($name, $email, $phone, $dob, $gender, $address, $resume);
                $application->close();
                
                echo json_encode($response);
                break;

            default:
                echo json_encode([
                    'success' => false,
                    'errors' => [
                        ['field' => 'general', 'message' => 'Invalid action']
                    ]
                ]);
                break;
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'errors' => [
                ['field' => 'general', 'message' => 'An error occurred: ' . $e->getMessage()]
            ]
        ]);
    }
    exit;
}
?>