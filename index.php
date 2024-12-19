<?php
// phpinfo();
try {
    $dsn = "mysql:host=sql12.freesqldatabase.com;port=3306;dbname=sql12752944";
    $username = "sql12752944";
    $password = "9w5Eq9LF97";

    // Create a PDO instance
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $res = $conn->prepare("SELECT * FROM user");
    $res->execute();
    $data = $res->fetchAll(PDO::FETCH_ASSOC);
    print_r($data);
    echo "Connection successful!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
