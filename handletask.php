<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    try {
        $dsn = "mysql:host=sql12.freesqldatabase.com;port=3306;dbname=sql12752944";
        $username = "sql12752944";
        $password = "9w5Eq9LF97";

        // Create a PDO instance
        $conn = new PDO($dsn, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents("php://input");
            $request = json_decode($input, true);
            if (isset($request['action'])) {
                $action = $request['action'];
                	
                switch ($action) {
                    case "addtask":
                        addtask($request['data'], $conn);
                        break;
                    case "taskstatusupdate":
                        $newstatus = $request['newstatus'];
                        $taskid = $request['taskid'];
                        handletaskstatus($conn, $taskid, $newstatus);
                        break;
                    default:
                        echo json_encode(["error" => "Invalid action specified"]);
                        break;
                }
            } else {
                echo json_encode(["error" => "No action specified"]);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $action = $_GET['action'];

            switch($action){
                case 'getalltask':
                    getalltask($conn);
                    break;
                case 'fetchuserbyid':
                    $userid = $_GET['userid'];
                    fetchuserbyid($conn, $userid );
                    break;
                case 'fetchusertaksbyname':
                    $username = $_GET['name'];
                    fetchusertaskbyname($username, $conn);
                    break;
                case 'getallusers':
                    fetchAllUsers($conn);
                    break;
                default:
                    echo json_encode(["error" => "Invalid action specified"]);
            }
        } else {
            echo json_encode(["error" => "Unsupported HTTP method"]);
        }
    } catch (\Throwable $th) {
        //throw $th;
    }

    function addtask($data, $conn) {
        try {
            $res = $conn->prepare("SELECT * FROM user WHERE name = :name");
            $res->bindParam(":name", $data['assignto']);
            $res->execute();
            
            if ($res->rowCount() <= 0) {
                echo json_encode(["message" => "User does not exist"]);
                exit;
            }

            $user = $res->fetch(PDO::FETCH_ASSOC);
            if ($user['role'] === 'manager') {
                echo json_encode(["message" => "Tasks cannot be assigned to managers"]);
                exit;
            }


            
            $stmt = $conn->prepare("INSERT INTO tasks (taskname, assignto, enddate, taskby) VALUES (:taskname, :assignto, :enddate, :taskby)");
            $stmt->bindParam(':taskname', $data['taskname']);
            $stmt->bindParam(':assignto', $data['assignto']);
            $stmt->bindParam(':enddate', $data['enddate']);
            $stmt->bindParam(':taskby', $data['taskby']);
    
            if ($stmt->execute()) {
                echo json_encode(["message" => "Task added successfully"]);
            } else {
                echo json_encode(["error" => "Failed to add task"]);
            }
        } catch (\Throwable $th) {
            echo json_encode(["error" => "An error occurred", "details" => $th->getMessage()]);
        }
    }
    

    function getalltask($conn){
        $res = $conn->prepare("SELECT * FROM tasks");
        if($res->execute()){
            $data = $res->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status"=> true, "message" => "All fetched data", "data" => $data]);
        }else{
            echo json_encode(["status"=> false, "message"=> "Data not found"]);
        }
    }

    function fetchuserbyid($conn, $userid){
        $res = $conn->prepare("SELECT * FROM user WHERE userid = :userid ");
        $res->bindParam(":userid", $userid);
        if($res->execute()){
            $data = $res->fetch(PDO::FETCH_ASSOC);
            echo json_encode(["status"=> true, "message" => "user fetched", "data" => $data]);
        }else{
            echo json_encode(["status"=> false, "message" => "user not fethced"]);
        }
    }
    
    function fetchusertaskbyname($username, $conn){
        try {
            $res = $conn->prepare("SELECT * FROM tasks WHERE assignto = :username");
            $res->bindParam(":username", $username);
            $res->execute();

            if($res->rowCount() > 0){
                $data = $res->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(["status"=> true, "message" => "user task fetched", "data" => $data]);
            }else{
                echo json_encode(["status"=> false, "message" => "user has no task"]);
            }
        } catch (\Throwable $th) {
            echo $th;
        }
    }

    function handletaskstatus($conn, $taskid, $newstatus){
        try {
            $validstatus = ['PENDING', 'COMPLETED'];
            if(!in_array($newstatus, $validstatus)){
                echo json_encode(["error"=>"Invalid status"]);
                return;
            }
            $res = $conn->prepare("UPDATE tasks SET status = :newstatus WHERE taskid = :taskid");
            $res->bindParam(":newstatus", $newstatus);
            $res->bindParam(":taskid", $taskid);
            if($res->execute()){
                echo json_encode(["status"=> true, "message" => "user task updated"]);
            }else{
                echo json_encode(["status"=> false, "message" => "user task not updated"]);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    function fetchAllUsers($conn){
        $res = $conn->prepare("SELECT * FROM user WHERE role = :role");
        $res->bindValue(":role", "employee", PDO::PARAM_STR);
        $res->execute();
    
        if($res->rowCount() > 0){
            $data = $res->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(["status" => true,"message" => "fetched all users","data" => $data]);
        } else {
            echo json_encode(["status" => false,"message" => "no users found"]);
        }
    }
?>