<?php

require_once "../configuration.php";
require_once "Response.php";
require_once "ResponseElement.php";

class User {

    public $is_logged = false;

    public $user_id;
    public $firstname;
    public $surname;
    public $password;
    public $email;
    public $photo;
    public $phone_number;
    public $creation_timestamp;
    public $is_activated;
    public $activation_key;
    public $is_banned;



    public function __construct() 
    {
        
    }


    public function logout() : Response 
    {
        $response = new Response();

        if ($this->is_logged) 
        {
            $this->is_logged = false;
            $response->set_status("OK");
        } 
        else 
        {
            $response->set_status("NO_OK");
            $response->data_add(new ResponseElement("E201", "user"));
        }

        return $response;
    }


    public function login($db, $email, $password) : Response
    {
        $response = new Response();

        // User already logged in
        if ($this->is_logged === true) 
        {
            $response->set_status("NO_OK");
            $response->data_add(new ResponseElement("E202", "user"));
            
            return $response;
        } 


        $good_email = true;
        $good_password = true;


        // Email validation
        if ($email !== null)
        {
            $email = trim($email); // Remove spaces from begining and ending
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);

            if ($email === false)
            {
                $response->data_add(new ResponseElement("E310", "email"));
                $good_email = false;
            }
        } 
        else 
        {
            $response->data_add(new ResponseElement("E301", "email"));
            $good_email = false;
        }



        // Password validation
        if ($password !== null)
        {
            // Encode
            $password = hash($GLOBALS["db_password_encoding"], $password);
        } 
        else 
        {
            $response->data_add(new ResponseElement("E301", "password"));
            $good_password = false;
        }


        if (!$good_email ||
            !$good_password) 
        {
            $response->set_status("NO_OK");
            return $response;
        }


        // search in database for user
        $db_table_users = $GLOBALS["db_table_users"];
        $query = "SELECT * FROM $db_table_users WHERE email=:email AND password=:password";
        $statement = $db->prepare($query);
        $statement->bindParam(":email", $email, PDO::PARAM_STR);
        $statement->bindParam(":password", $password, PDO::PARAM_STR);
        $statement->execute();

        if ($statement->rowCount() > 0) 
        {
            $result = $statement->fetchAll(PDO::FETCH_OBJ);
            $result = $result[0];

            $can_log_in = true;

            if ($result->is_activated != true)
            {
                $can_log_in = false;
                $response->data_add(new ResponseElement("E201", "not_activated"));
            }
                
            if ($result->is_banned == true)
            {
                $can_log_in = false;
                $response->data_add(new ResponseElement("E201", "banned"));
            }

            if ($can_log_in)
            {
                $this->merge_data($result);

                $this->is_logged = true;
                $response->set_status("OK");

                return $response;
            } 
            else 
            {
                $response->set_status("NO_OK");

                return $response;
            }
        }
        else
        {
            $response->data_add(new ResponseElement("E300", "user"));
            $response->set_status("NO_OK");

            return $response;
        } 
    }


    public function pull_data($db) : bool
    {
        if ($this->is_logged === false) return false;

        $db_table_users = $GLOBALS["db_table_users"];
        $query = "SELECT * FROM $db_table_users WHERE user_id=:user_id";
        $statement = $db->prepare($query);
        $statement->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
        $statement->execute();

        if ($statement->rowCount() > 0) 
        {
            $result = $statement->fetchAll(PDO::FETCH_OBJ);
            $result = $result[0];

            $this->merge_data($result);

            return true;
        }
        else
        {
            return false;
        }
    }


    public function merge_data($data) : bool
    {
        $this->user_id = intval($data->user_id);
        $this->firstname = $data->firstname;
        $this->surname = $data->surname;
        $this->password = $data->password;
        $this->email = $data->email;
        $this->photo = $data->photo;
        $this->phone_number = $data->phone_number;
        $this->creation_timestamp = $data->creation_timestamp;
        $this->is_activated = !!$data->is_activated;
        $this->activation_key = $data->activation_key;
        $this->is_banned = !!$data->is_banned;

        return true;
    }
}

?>