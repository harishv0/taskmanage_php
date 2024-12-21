<?php
    header("Access-Control-Allow-Origin: http://localhost:3000"); // Allow requests from your frontend
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allowed methods
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With"); // Custom headers
    header("Access-Control-Allow-Credentials: true"); // If you need cookies or authentication

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header("HTTP/1.1 200 OK");
        exit();
    }    

    

    try {
        $dsn = "mysql:host=sql12.freesqldatabase.com;port=3306;dbname=sql12752944";
        $username = "sql12752944";
        $password = "9w5Eq9LF97";

        // Create a PDO instance
        $conn = new PDO($dsn, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            $email = $_GET['mail'];
            $password = $_GET['password'];

            if(isset($email, $password)){
                $isvalid = $conn->prepare("SELECT mail FROM user WHERE mail = :email");
                $isvalid->bindParam(":email", $email);
                $isvalid->execute();

                if($isvalid->rowCount() > 0){
                    $isPass = $conn->prepare("SELECT * FROM user WHERE mail = :email");
                    $isPass->bindParam(":email", $email);
                    $isPass->execute();

                    $user = $isPass->fetch(PDO::FETCH_ASSOC);

                    if($password === $user['password']){
                        echo json_encode(["message" => "Login successful", "data" => $user]);
                    } else {
                        echo json_encode(["message" => "Invalid password"]);
                    }
                } else {
                    echo json_encode(["message" => "Email not found"]);
                }
            } else {
                echo json_encode(["message" => "Invalid input"]);
            }
        }
    } catch (\Throwable $th) {
        echo json_encode(["message" => "Error: " . $th->getMessage()]);
    }
?>
