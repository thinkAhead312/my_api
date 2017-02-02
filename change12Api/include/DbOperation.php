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

    /**
     * method userLogin for Disciple to login
     * @params $username, usnername of disciple
     * @params $pass, password of disciple
     */
    public function userLogin($username, $pass) {
        //generating hash sha256
        $password = hash('sha256', $pass);
        //creating query
         $stmt = $this->con->prepare("SELECT * FROM users WHERE username=? and password=?");
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
     * method getUser will return the particular disciple detail
     * @params $username, username of the disciple
     */
    public function getUser($username) {
        //create sql statement
        $stmt = $this->con->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s",$username); //bind param $id
        $stmt->execute();
        //Getting the student result array
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        //returning the student
        return $user;
    }

    /*
     * method getUserbyId will return user detail by ID
     * @params $id, id of the disciple
     */
    public function getUserbyId($id) {
        //create sql statement
        $stmt = $this->con->prepare("SELECT * FROM users WHERE id=?");
        $stmt->bind_param("i",$id); //bind param $id
        $stmt->execute();
        //Getting the student result array
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        //returning the student
        return $user;
    }

    /*
     * method getUsers will return all the row in users tbl
     */
    public function getUsers() {
        $health_status = "HEALTH_INACTIVE";
        $stmt = $this->con->prepare("SELECT * FROM users WHERE health_status != ?");
        $stmt->bind_param("s", $health_status); //bind string username to params
        $stmt->execute();
        $users = $stmt->get_result();
        $stmt->close();
        return $users;
    }

    /*
     * method getWaveChangees will return all "changees" on a particular wave
     * @params $wave_num, wave number
     */
    public function getWaveChangees($wave_num) {
        $stmt = $this->con->prepare("SELECT * FROM changees INNER JOIN users ON changees.changee = users.id WHERE change_12 = ?");
        $stmt->bind_param("i", $wave_num); //bind string username to params
        $stmt->execute();
        $changees = $stmt->get_result();
        $stmt->close();
        return $changees;
    }





   
}