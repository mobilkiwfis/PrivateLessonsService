<?php

class Response {

    public $status = "OK";
    public $data = array();


    public function set_status($status) : bool {
        $this->status = $status;
        return true;
    }


    public function data_add($data) : bool {
        if (is_array($this->data)) {
            array_push($this->data, $data);
            return true;
        }
        return false;
    }


    public function data_set($data) : bool {
        $this->data = $data;
        return true;
    }
}

?>