<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $dsn = "mysql:host=sql12.freesqldatabase.com;port=3306;dbname=sql12752944";
    $username = "sql12752944";
    $password = "9w5Eq9LF97";

    // Create a PDO instance
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        
        error_log("Received Data: " . print_r($data, true));

        if (isset($data['name'],$data['mail'], $data['password'])) {
            $name = $data['name'];
            $email = $data['mail'];
            $password = $data['password'];

            $stmt = $conn->prepare("SELECT * FROM user WHERE mail = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo json_encode(["message" => "User already exists"]);
                exit;
            }

            $stmt = $conn->prepare("INSERT INTO user (name, mail, password) VALUES (:name, :mail, :password)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':mail', $email);
            $stmt->bindParam(':password', $password);
            if ($stmt->execute()) {
                echo json_encode(["message" => "User registered successfully"]);
            } else {
                echo json_encode(["message" => "Registration failed"]);
            }
        } else {
            echo json_encode(["message" => "Invalid input"]);
        }
    } else {
        echo json_encode(["message" => "Invalid request method"]);
    }
} catch (Exception $e) {
    echo json_encode(["message" => "Error: " . $e->getMessage()]);
}

?>
