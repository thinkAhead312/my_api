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
        verifyRequiredParams(array('username','password'));

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

            //Generating response
            $response['error'] = false;
            $response['id'] = $user['id'];
            $response['first_name'] = $user['first_name'];
            $response['nick_name'] = $user['nick_name'];
            $response['middle_name'] = $user['middle_name'];
            $response['last_name'] = $user['last_name'];
            $response['gender'] = $user['gender'];
            $response['birth_date'] = $user['birth_date'];
            $response['nationality'] = $user['nationality'];
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