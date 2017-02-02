<?php

//including the required files
require_once '../include/DbOperation.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();
//creating a slim instance
$app = new \Slim\Slim();
    
    /*
     * /createstudent to create new student
     * the first parameter is the URL address that will be added at last to the root url
     * http://localhost/StudentApp/v1/createstudent
     * METHOD POST
     * POST @params name, username, password
     */
    $app->post('/createstudent', function() use($app) {
        //Verifying the required parameters
        verifyRequiredParams(array('name', 'username', 'password'));

        //Creating a response array
        $response = array();

        //reading post parameters
        $name = $app->request->post('name');
        $username = $app->request->post('username');
        $password = $app->request->post('password');

        //Creating a DbOperation object
        $db = new DbOperation();
        //Calling the method createStudent to add student to the database
        $res = $db->createStudent($name,$username,$password);

         //If the result returned is 0 means success
        if ($res == 0) {
            //Making the response error false
            $response["error"] = false;
            //Adding a success message
            $response["message"] = "You are successfully registered";
            //Displaying response
            echoResponse(201, $response);
        //If the result returned is 1 means failure
        } else if ($res == 1) {
            $response["error"] = true;
            $response["message"] = "Oops! An error occurred while registereing";
            echoResponse(200, $response);
        //If the result returned is 2 means user already exist
        } else if ($res == 2) {
            $response["error"] = true;
            $response["message"] = "Sorry, this email already existed";
            echoResponse(200, $response);
        }
    });

    /*
     * /studentlogin to make login request
     * METHOD POST
     * POST @params username, password
     * http://localhost/StudentApp/v1/studentlogin
     */
    $app->post('/studentlogin',function() use ($app){
        //verifying required parameters
        verifyRequiredParams(array('username','password'));

        //getting post values
        $username = $app->request->post('username');
        $password = $app->request->post('password');

        //Creating DbOperation object
        $db = new DbOperation();

        //Creating a response array
        $response = array();

        //If username password is correct
        if($db->studentLogin($username,$password)){
     
            //Getting user detail
            $student = $db->getStudent($username);
     
            //Generating response
            $response['error'] = false;
            $response['id'] = $student['id'];
            $response['name'] = $student['name'];
            $response['username'] = $student['username'];
            $response['apikey'] = $student['api_key'];
     
        }else{
            //Generating response
            $response['error'] = true;
            $response['message'] = "Invalid username or password";
        }

        //Displaying the response
        echoResponse(200,$response);
    });


    /* 
     * URL: http://localhost/StudentApp/v1/createfaculty
     * Parameters: name, username, password, subject
     * Method: POST
     * */
    $app->post('/createfaculty', function () use ($app) {
        verifyRequiredParams(array('name', 'username', 'password', 'subject'));

        //reading post parameters
        $name = $app->request->post('name');
        $username = $app->request->post('username');
        $password = $app->request->post('password');
        $subject = $app->request->post('subject');
     
        //Creating a DbOperation object
        $db = new DbOperation();
        $response = array();
        //calling method createFaculty to register new faculty in DB 
        $res = $db->createFaculty($name, $username, $password, $subject);
        if ($res == 0) { //Successful
            $response["error"] = false;
            $response["message"] = "You are successfully registered";
            echoResponse(201, $response);
        } else if ($res == 1) {
            $response["error"] = true;
            $response["message"] = "Oops! An error occurred while registereing";
            echoResponse(200, $response);
        } else if ($res == 2) {
            $response["error"] = true;
            $response["message"] = "Sorry, this faculty already existed";
            echoResponse(200, $response);
        }
    });

    /* *
     * URL: http://localhost/StudentApp/v1/facultylogin
     * Parameters: username, password
     * Method: POST
     * */
     
    $app->post('/facultylogin', function() use ($app){
        verifyRequiredParams(array('username','password'));
        $username = $app->request->post('username');
        $password = $app->request->post('password');
     
        $db = new DbOperation();
     
        $response = array();
     
        if($db->facultyLogin($username,$password)){
            $faculty = $db->getFaculty($username);
            $response['error'] = false;
            $response['id'] = $faculty['id'];
            $response['name'] = $faculty['name'];
            $response['username'] = $faculty['username'];
            $response['subject'] = $faculty['subject'];
            $response['apikey'] = $faculty['api_key'];
        }else{
            $response['error'] = true;
            $response['message'] = "Invalid username or password";
        }
     
        echoResponse(200,$response);
    });

    /* 
     * URL: http://localhost/StudentApp/v1/createassignment
     * Parameters: name, details, facultyid, studentid
     * Method: POST
     * */
    $app->post('/createassignment',function() use ($app){
        verifyRequiredParams(array('name','details','facultyid','studentid'));
     
        $name = $app->request->post('name');
        $details = $app->request->post('details');
        $facultyid = $app->request->post('facultyid');
        $studentid = $app->request->post('studentid');
     
        $db = new DbOperation();
     
        $response = array();
     
        if($db->createAssignment($name,$details,$facultyid,$studentid)){
            $response['error'] = false;
            $response['message'] = "Assignment created successfully";
        }else{
            $response['error'] = true;
            $response['message'] = "Could not create assignment";
        }
     
        echoResponse(200,$response);
     
    });

    /* *
     * URL: http://localhost/StudentApp/v1/assignments/<student_id>
     * Parameters: none
     * Authorization: Put API Key in Request Header
     * Method: GET
     * */
    $app->get('/assignments/:id', 'authenticateStudent', function($student_id) use ($app){
        $db = new DbOperation();
        $result = $db->getAssignments($student_id);
        $response = array();
        $response['error'] = false;
        $response['assignments'] = array(); 
        while($row = $result->fetch_assoc()){
            $temp = array();
            $temp['id']=$row['id'];
            $temp['name'] = $row['name'];
            $temp['details'] = $row['details'];
            $temp['completed'] = $row['completed'];
            $temp['faculty']= $db->getFacultyName($row['faculties_id']);
            array_push($response['assignments'],$temp);
        }
        echoResponse(200,$response);
    });

    /* 
     * URL: http://localhost/StudentApp/v1/submitassignment/<assignment_id>
     * Parameters: none
     * Authorization: Put API Key in Request Header
     * Method: PUT
     * */
    $app->put('/submitassignment/:id', 'authenticateFaculty', function($assignment_id) use ($app){
        $db = new DbOperation();
        $result = $db->updateAssignment($assignment_id);
        $response = array();
        if($result){
            $response['error'] = false;
            $response['message'] = "Assignment submitted successfully";
        }else{
            $response['error'] = true;
            $response['message'] = "Could not submit assignment";
        }
        echoResponse(200,$response);
    });
     

    /* *
     * URL: http://localhost/StudentApp/v1/students
     * Parameters: none
     * Authorization: Put API Key in Request Header
     * Method: GET
     * */
    $app->get('/students', 'authenticateFaculty', function() use ($app){
        $db = new DbOperation();
        $result = $db->getAllStudents();
        $response = array();
        $response['error'] = false;
        $response['students'] = array();
     
        while($row = $result->fetch_assoc()){
            $temp = array();
            $temp['id'] = $row['id'];
            $temp['name'] = $row['name'];
            $temp['username'] = $row['username'];
            array_push($response['students'],$temp);
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