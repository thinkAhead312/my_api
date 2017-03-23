<?php

//including the required files
require_once '../include/DbOperation.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();
//creating a slim instance
$app = new \Slim\Slim();
    
    /*
     * /userlogin to make the login request of the disciple
     * METHOD POST
     * POST @params username, password
     * http://localhost:8081/change12Api/userlogin
     */
    $app->post('/userlogin', function() use ($app) {
        //verifying required parameters
        // verifyRequiredParams(array('username','password'));

        //getting post values
        $username = $app->request->post('username');
        $password = $app->request->post('password');

        //Creating DbOperation object
        $db = new DbOperation();

        //Creating a response array
        $response = array();

        //If username password is correct
        if($db->userLogin($username,$password)){
     
            //Getting user detail
            $user = $db->getUser($username);

            $is_admin = strpos($user['roles'], 'ROLE_ADMIN') !== false ? true : false; //check if user isAdmin

            //Generating response
            $response['error'] = false;
            $response['user']['disciple_id'] = $user['id'];
            $response['user']['first_name'] = utf8_encode($user['first_name']);
            $response['user']['nick_name'] = utf8_encode($user['nick_name']);
            $response['user']['middle_name'] = utf8_encode($user['middle_name']);
            $response['user']['last_name'] = utf8_encode($user['last_name']);
            $response['user']['full_name'] = utf8_encode($user['full_name']);
            $response['user']['username'] = $user['username'];
            $response['user']['is_admin'] = $is_admin;
        }
        else{
            //Generating response
            $response['error'] = true;
            $response['message'] = "Invalid username or password";
        }
         //Displaying the response
        echoResponse(200,$response);
    });

    /*
     * /users to fetch all users from db
    |* http://localhost:8081/change12Api/userlogin
     * METHOD GET
     */
     $app->get('/users', function() use ($app){
        $db = new DbOperation();
        $result = $db->getUsers();

        $response = array();
        $response['row_count'] = $result->num_rows;
        $response['error'] = false;
        $response['users'] = array();
        while($row = $result->fetch_assoc()){
            $temp = array();
            $temp['disciple_id'] = $row['id'];
            $temp['slug'] =  utf8_encode($row['slug']);
            $temp['first_name'] =  utf8_encode($row['first_name']);
            $temp['middle_name'] =  utf8_encode($row['middle_name']);
            $temp['last_name'] =  utf8_encode($row['last_name']);
            $temp['full_name'] =  utf8_encode($row['full_name']);
            $temp['nick_name'] =  utf8_encode($row['nick_name']);
            $temp['gender'] = $row['gender'];
            $temp['birth_date'] = $row['birth_date'];
            $temp['nationality'] = $row['nationality'];
            $temp['home_address'] = $row['home_address'];
            $temp['city_address'] = $row['city_address'];
            $temp['contact_number'] = $row['contact_number'];
            $temp['email_address'] = utf8_encode($row['email_address']);
            $temp['school'] = $row['school'];
            $temp['degree'] = $row['degree'];
            $temp['marital_status'] = $row['marital_status'];
            $temp['company'] = $row['company'];
            $temp['job_position'] = $row['job_position'];
            $temp['date_won'] = $row['date_won'];
            $temp['invited_by'] = $row['invited_by'];
            $temp['discipler'] = $row['discipler'];
            $temp['health_status'] = $row['health_status'];
            $temp['username'] = utf8_encode($row['username']);
            $temp['password'] = $row['password'];
            $is_admin = strpos($row['roles'], 'ROLE_ADMIN') !== false ? true : false; //check if user isAdmin
            $temp['roles'] = $is_admin;

            array_push($response['users'],$temp);
        }
        echoResponse(200,$response);
    });

      /*
     * /users to fetch all users from db
    |* http://localhost:8081/change12Api/userlogin
     * METHOD GET
     */
     $app->get('/user/:id', function($disciple_id) use ($app){
      //Creating DbOperation object
        $db = new DbOperation();

        //Getting user detail
        $user = $db->getUserbyId($disciple_id);
            
        $response['error'] = false;
        $response['id'] = $user['id'];
        $response['first_name'] = $user['first_name'];
        $response['nick_name'] = $user['nick_name'];
        $response['middle_name'] = $user['middle_name'];
        $response['last_name'] = $user['last_name'];
        $response['gender'] = $user['gender'];
        $response['birth_date'] = $user['birth_date'];
        $response['nationality'] = $user['nationality'];
       
        echoResponse(200,$response);

    });

     /*
         * /change12 to fetch all change12 from db
        |* http://localhost/change12Api/change12
         * METHOD GET
     */


     $app->get('/change12', function() use ($app){
        $db = new DbOperation();
        $result = $db->getChange12();

        $response = array();
        $response['change12_row_count'] = $result->num_rows;
        $response['error'] = false;
        $response['change12'] = array();
        while($row = $result->fetch_assoc()){
            $temp = array();
            $temp['id'] = $row['id'];
            $temp['wave_num'] =  $row['wave_num'];
            $temp['start_date'] =  $row['start_date'];
            $temp['end_date'] =  $row['end_date'];
            array_push($response['change12'],$temp);
        }

        $changees_result = $db->getChangee();
        $response['changees_row_count'] = $changees_result->num_rows;
        $response['changees'] = array();

       while($row = $changees_result->fetch_assoc()){
            $temp = array();
            $temp['id'] = $row['id'];
            $temp['change_12'] =  $row['change_12'];
            $temp['changee'] =  $row['changee'];
            $temp['change_1_ok'] =  $row['change_1_ok'];
            $temp['change_1_date'] =  $row['change_1_date'];
            $temp['change_2_ok'] =  $row['change_2_ok'];
            $temp['change_2_date'] =  $row['change_2_date'];
            $temp['change_3_ok'] =  $row['change_3_ok'];
            $temp['change_3_date'] =  $row['change_3_date'];
            $temp['change_4_ok'] =  $row['change_4_ok'];
            $temp['change_4_date'] =  $row['change_4_date'];
            $temp['change_5_ok'] =  $row['change_5_ok'];
            $temp['change_5_date'] =  $row['change_5_date'];
            array_push($response['changees'],$temp);
        }


        echoResponse(200,$response);
     });

    /*
     * /wavechangees to fetch all changees on particular wave
     * METHOD GET
     * http://localhost:8081/change12Api/v1/wavechangees/:id
     */ 
     $app->get('/wavechangees/:id', function($wave_num) use ($app){
        $db = new DbOperation();
        $result = $db->getWaveChangees($wave_num);
        $response = array();
        $response['error'] = false;
        $response['total_changees'] = $result->num_rows;
        $response['changees'] = array(); 
         while($row = $result->fetch_assoc()){
            $temp = array();
            $temp['name'] = $row['change_12'];
            $temp['changee'] = $row['changee'];
            // $temp['user_item'] = $db->getUserbyId($row['changee']);
            $temp['first_name'] = $row['first_name'];
            $temp['last_name'] = $row['last_name'];
            array_push($response['changees'],$temp);
        }
        echoResponse(200,$response);
     });

     /*
      * /wavelesson1changees/:wave_num/:lesson_num to fetch all changee currently on particular change12 lesson
      * METHOD GET
      */
     $app->get('/wavelessonchangees/:wave_num/:lesson_num', function($wave_num, $lesson_num) use ($app){
        $db = new DbOperation(); //creating Dboperation object
        $result = $db->getWaveChangees($wave_num); //fetch all wave changees
        $response = array();
        $response['error'] = false;
        $changee_count = 0;
        $response['changees'] = array(); 
        while($row = $result->fetch_assoc()){
            $change_status = false;
            for($i=1; $i <= 5; $i++) {
                if($row['change_'.$i.'_ok'] == 'on' && $i == $lesson_num) {
                    $change_status = true; 
                } elseif($row['change_'.$i.'_ok'] == 'on') {
                    $change_status = false;
                }
            }
            if($change_status) {
                $temp = array();
                $changee_count++;
                $temp['wave_num'] = $row['change_12'];
                $temp['changee'] = $row['changee'];
                $temp['first_name'] = $row['first_name'];
                $temp['last_name'] = $row['last_name'];
                $temp['change_'.$lesson_num.'_date'] = $row['change_'.$lesson_num.'_date'];
                array_push($response['changees'],$temp);
            }
        }
        $response['changee_count'] = $changee_count;
        echoResponse(200,$response);
     });


     /*
     * /wavechangees to fetch all changees on particular wave
     * METHOD GET
     * http://localhost:8081/change12Api/v1/wavechangees/:id
     */ 
     $app->get('/searchbynames/:name', function($name) use ($app){
        //Creating DbOperation object
        $db = new DbOperation();
        //Getting user detail
        $result = $db->getDiscipleNameByName($name);

        $response = array();
        $response['row_count'] = $result->num_rows;
        $response['error'] = false;
        $response['users'] = array();
        while($row = $result->fetch_assoc()){
            $temp = array();
            $temp['id'] = $row['id'];
            $temp['first_name'] = $row['first_name'];
            $temp['last_name'] = $row['last_name'];
            $temp['discipler'] = $db->getDisciplerNameById($row['discipler']);
            array_push($response['users'],$temp);
        }
        echoResponse(200,$response);
     });


    /*
     * method echoResponse to display response
     * @params $status_code, http status code
     * @params $response, response message/body
     */
    function echoResponse($status_code, $response) {
        //Getting app instance
        $app = \Slim\Slim::getInstance();

        //Setting Http response code
        $app->status($status_code);

        //setting response content type to json
        $app->contentType('application/json');

        //displaying the response in json format
        echo json_encode($response);
    }

    /*
     * method verifyRequiredParams 
     * To verify the required parameters in the request
     */
    function verifyRequiredParams($required_fields) {
        //Assuming there is no error
        $error = false;

        //Error fields are blank
        $error_fields = "";

        //Getting the request parameters
        $request_params = $_REQUEST;

        //Handling PUT request params
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            //Getting the app instance
            $app = \Slim\Slim::getInstance();
            //Getting put parameters in request params variable
            parse_str($app->request()->getBody(), $request_params);
        }
        //Looping through all the parameters
        foreach ($required_fields as $field) {
     
            //if any requred parameter is missing
            if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
                //error is true
                $error = true;
     
                //Concatnating the missing parameters in error fields
                $error_fields .= $field . ', ';
            }
        }
        //if there is a parameter missing then error is true
        if ($error) {
            //Creating response array
            $response = array();
     
            //Getting app instance
            $app = \Slim\Slim::getInstance();
     
            //Adding values to response array
            $response["error"] = true;
            $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
     
            //Displaying response with error code 400
            echoResponse(400, $response);
     
            //Stopping the app
            $app->stop();
        }
    }

    /*
     * method to aunthecate the student
     * To authenticate the student with the api key.
     */
    function authenticateStudent(\Slim\Route $route) {
        //Getting request headers
        $headers = apache_request_headers();
        $response = array();
        $app = \Slim\Slim::getInstance();
        //Verifying the headers
        if (isset($headers['Authorization'])) {
     
            //Creating a DatabaseOperation boject
            $db = new DbOperation();
     
            //Getting api key from header
            $api_key = $headers['Authorization'];
     
            //Validating apikey from database
            if (!$db->isValidStudent($api_key)) { 
                $response["error"] = true;
                $response["message"] = "Access Denied. Invalid Api key";
                echoResponse(401, $response);
                $app->stop();
            }
        } else {
            // api key is missing in header
            $response["error"] = true;
            $response["message"] = "Api key is misssing";
            echoResponse(400, $response);
            $app->stop();
        }
    }

    function authenticateFaculty(\Slim\Route $route)
    {
        $headers = apache_request_headers();
        $response = array();
        $app = \Slim\Slim::getInstance();
        if (isset($headers['Authorization'])) {
            $db = new DbOperation();
            $api_key = $headers['Authorization'];
            if (!$db->isValidFaculty($api_key)) {
                $response["error"] = true;
                $response["message"] = "Access Denied. Invalid Api key";
                echoResponse(401, $response);
                $app->stop();
            }
        } else {
            $response["error"] = true;
            $response["message"] = "Api key is misssing";
            echoResponse(400, $response);
            $app->stop();
        }
    }
$app->run();