<?php
error_reporting(0);

session_start();
$empid = $_SESSION["empid"];
date_default_timezone_set("Asia/Kolkata");

class CrudOperation {
    protected $db; // Database connection

    public function __construct($host, $username, $password, $database) {
        // Create a new database connection
        $this->db = new mysqli($host, $username, $password, $database);

        // Check connection
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function create($table, $data) {
        $keys = implode(', ', array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";
        $sql = "INSERT INTO $table ($keys) VALUES ($values)";
        
        if ($this->db->query($sql) === TRUE) {
           return $this->db->insert_id;
        } else {
            return false;
        }
    }

    public function read($table, $condition = '',$column='*') {
        $sql = "SELECT $column FROM $table";
        if (!empty($condition)) {
            $sql .= " WHERE $condition";
        }
        
        // return $sql;
        // exit;
        
        $result = $this->db->query($sql);
        $rows = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }else{
            return false;
        }
        return $rows;
    }

    public function update($table, $data, $condition) {
        $set = '';
        foreach ($data as $key => $value) {
            $set .= "$key='$value', ";
        }
        $set = rtrim($set, ', ');

        $sql = "UPDATE $table SET $set WHERE $condition";

        if ($this->db->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($table, $condition) {
        $sql = "DELETE FROM $table WHERE $condition";

        if ($this->db->query($sql) === TRUE) {
            return 1;
        } else {
            return 0;
        }
    }
    
     public function getcountdata($table,$maths,$colunm='*',$condition='') {
        $sql = "SELECT $maths($colunm) as sumdata FROM $table";
        if (!empty($condition)) {
            $sql .= " WHERE $condition";
        }

        $result = $this->db->query($sql);
        $rows = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }else{
            return false;
        }
        return $rows;
    }

    public function __destruct() {
        // Close the database connection when the object is destroyed
        $this->db->close();
    }
}

// Example usage:
include_once '../../pages/dbInfo.php';
$crud = new CrudOperation(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);


?>
