<?php

class DbOperation
{
    //Database connection link
    private $con;

    // class constructor
    function __construct()
    {
        //Getting the Dbconnect.php file
        require_once dirname(__FILE__) . '/DbConnect.php';
        //creating a Dbconnect object to connect to the database
        $db = new DbConnect();
        /* Initializing our conneection link of this class
           by calling the method connect of the Dbconnect class*/
        $this->con = $db->connect();
    }

    /*
     * Method create student will create new student
     * @params $name, name of the student
     * @params $username, username of the student
     * @params $pass, password of the student
     */
    public function createStudent($name, $username, $pass) {
        //first we will check wether the student is already registered or not
         if (!$this->isStudentExists($username)) {
            //encrypting the password
            $password = md5($pass);

            //Generating an API key
            $apikey = $this->generateApiKey();

            //creating a sql statement
            $stmt = $this->con->prepare("INSERT INTO students(name, username, password, api_key) values(?, ?, ?, ?)");
            //Binding the parameters
            $stmt->bind_param("ssss", $name, $username, $password, $apikey);

            //Executing the statment
            $result = $stmt->execute();
            
            //Closing the statment
            $stmt->close();

            if ($result) {
                //Returning 0 means student created successfully
                return 0;
            } else {
                //Returning 1 means failed to create student
                return 1;
            }
 
         } else {
            //returning 2 means user already exist in the database
            return 2;
         }
    }

    /*
     * method studentLogin for student login
     * @params $username, username of the student
     * @params $pass, password of the student
     */
    public function studentLogin($username, $pass) {
        //Generating password hash
        $password = md5($pass);

        //creating query
         $stmt = $this->con->prepare("SELECT * FROM students WHERE username=? and password=?");
         //binding the parameters
         $stmt->bind_param("ss",$username,$password);
         //executing the query
         $stmt->execute();
         //Storing result
         $stmt->store_result();
         //Getting the result
         $num_rows = $stmt->num_rows;
         //closing the statment
         $stmt->close();
         /*If the result value is greater than 0 means user found in the database with given username and password So returning true */
         return $num_rows>0;
    }   

    /*
     * method createFaculty to add new faculty
     * @params $name, Faculty's name
     * @params $username, Faculty's username
     * @params $pass, Faculty's password
     * @params $subject, Faculty's subject
     */
    public function createFaculty($name,$username,$pass,$subject) {
        //first we will check wether the Faculty is already registered or not
        if (!$this->isFacultyExists($username)) {
            $password = md5($pass);
            $apikey = $this->generateApiKey();
            //create sql stmt
            $stmt = $this->con->prepare("INSERT INTO faculties(name, username, password, subject, api_key) values(?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $username, $password, $subject, $apikey); //bind params
            $result = $stmt->execute(); //execute query
            $stmt->close(); //close stmt
            if ($result) {
                //Returning 0 means faculty created successfully
                return 0; 
            } else {
                //Returning 1 means failed to create faculty
                return 1;
            }
        } else {
            //returning 2 means user already exist in the database
            return 2;
        }
    }

    /*
     * method facultyLogin to let a faculty log in
     * @params $username, faculty's username
     * @params $pass, faculty's pass
     */
    public function facultyLogin($username, $pass) {
        //generate hash password
        $password = md5($pass);
        //create sql statement
        $stmt = $this->con->prepare("SELECT * FROM faculties WHERE username=? and password =?");
        $stmt->bind_param("ss",$username,$password); //bind params
        $stmt->execute(); //execute stmt
        $stmt->store_result(); //store results to stmt
        $num_rows = $stmt->num_rows; //count num of rows
        $stmt->close(); //close stmt
        /*If the result value is greater than 0 means user found in the database with given username and password So returning true */
        return $num_rows>0; 
    }   

    /*
     * method createAssignment to create assignment
     * @params $name, assignments name
     * @params $detail, detail of the assignment
     * @params $facultyid, id of the faculty
     * @params $studentid, id of the student
     */
     public function createAssignment($name,$detail,$facultyid,$studentid){
        //create sql statement
        $stmt = $this->con->prepare("INSERT INTO assignments (name,details,faculties_id,students_id) VALUES (?,?,?,?)");
        $stmt->bind_param("ssii",$name,$detail,$facultyid,$studentid); //bind params
        $result = $stmt->execute(); //execute stmt
        $stmt->close(); // close stmt
        if($result){
            return true; //successfully create assignment
        }
        return false; //error occured
    }

    //Method to update assignment status
    public function updateAssignment($id){
        $stmt = $this->con->prepare("UPDATE assignments SET completed = 1 WHERE id=?");
        $stmt->bind_param("i",$id);
        $result = $stmt->execute();
        $stmt->close();
        if($result){
            return true;
        }
        return false;
    }

    /*
     * method getAssignments to get all the assignments of a particular student
     * @params $studentid, id of the student
     */
     public function getAssignments($studentid){
        //create sql stmt
        $stmt = $this->con->prepare("SELECT * FROM assignments WHERE students_id=?");
        $stmt->bind_param("i",$studentid); //bind params
        $stmt->execute();
        $assignments = $stmt->get_result(); //get results
        $stmt->close();
        return $assignments;
    }



    /*
     * method getStudent will return particular student detail
     * @params $username, username of the student
     */
     public function getStudent($username){
        //create sql statement
        $stmt = $this->con->prepare("SELECT * FROM students WHERE username=?");
        $stmt->bind_param("s",$username); //bind param $username
        $stmt->execute();
        //Getting the student result array
        $student = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        //returning the student
        return $student;
     }

    /*
     * method getAllStudents to fetch all student from DB
     */
    public function getAllStudents(){
        $stmt = $this->con->prepare("SELECT * FROM students");
        $stmt->execute();
        $students = $stmt->get_result();
        $stmt->close();
        return $students;
    }

    /*
     * method getFaculty to get faculty details by username
     * @params $username, faculty's username
     */
    public function getFaculty($username){
        $stmt = $this->con->prepare("SELECT * FROM faculties WHERE username=?");
        $stmt->bind_param("s",$username);
        $stmt->execute();
        $faculty = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $faculty;
    }
    
    /*
     * method getFacultyName to get faculty name by id
     * @params $id, faculty's id
     */
    public function getFacultyName($id){
        $stmt = $this->con->prepare("SELECT name FROM faculties WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $faculty = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $faculty['name'];
    }



    /*
     * method isStudentExists to check wether a student already exist
     * @params $username, passed username value to check if already exist
     */
    private function isStudentExists($username) {
        //sql statement to query to check username is already exist
        $stmt = $this->con->prepare("SELECT id from students WHERE username = ?");
        $stmt->bind_param("s", $username); //bind string username to params
        $stmt->execute(); //execute sql stmt
        $stmt->store_result(); //store result to stmt
        $num_rows = $stmt->num_rows; //get number of rows returned
        $stmt->close(); //close stmt 
        return $num_rows > 0; // if $num_rows true, otherwise false
    }

    /*
     * method isFacultyExists to check wether a faculty already exist
     * @params $username, passed username value to check if already exist
     */
    private function isFacultyExists($username) {
        $stmt = $this->con->prepare("SELECT id from faculties WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }



    /*
     * Methods to check a user is valid or not using api key
     * I will not write comments to every method as the same thing is done in each method
     * */
    public function isValidStudent($api_key) {
        //Creating an statement
        $stmt = $this->con->prepare("SELECT id from students WHERE api_key = ?");
 
        //Binding parameters to statement with this
        //the question mark of queries will be replaced with the actual values
        $stmt->bind_param("s", $api_key);
 
        //Executing the statement
        $stmt->execute();
 
        //Storing the results
        $stmt->store_result();
 
        //Getting the rows from the database
        //As API Key is always unique so we will get either a row or no row
        $num_rows = $stmt->num_rows;
 
        //Closing the statment
        $stmt->close();
 
        //If the fetched row is greater than 0 returning  true means user is valid
        return $num_rows > 0;
    }


    //Checking the faculty is valid or not by api key
    public function isValidFaculty($api_key){
        $stmt = $this->con->prepare("SELECT id from faculties WHERE api_key=?");
        $stmt->bind_param("s",$api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows>0;
    }

    //This method will generate a unique api key
    private function generateApiKey(){
        return md5(uniqid(rand(), true));
    }
}