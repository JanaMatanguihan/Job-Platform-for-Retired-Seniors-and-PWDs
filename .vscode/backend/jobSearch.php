<?php
// Database configuration
$dsn = 'mysql:host=localhost;dbname=jobplatformdb';
$user = 'root';
$pass = '';

try {
    // Create a PDO connection
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get search term
    $searchTerm = isset($_GET['query']) ? trim($_GET['query']) : '';

    // Ensure the search term is safe for use in SQL
    $searchTerm = "%$searchTerm%";

    // Query the database
    $stmt = $conn->prepare("
        SELECT title, company, link
        FROM jobs 
        WHERE title LIKE :searchTerm OR company LIKE :searchTerm
    ");

    // Bind the search term to the placeholder
    $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);

    // Execute the query
    $stmt->execute();

    // Fetch all results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return results as JSON
    echo json_encode($results);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>