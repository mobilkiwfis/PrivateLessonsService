<?php

class ErrorMessage {
    public $error_code = "E100";
    public $caused_by = "server";

    public function __construct($error_code, $caused_by) {
        $this->error_code = $error_code;
        $this->caused_by = $caused_by;
    }
}

?>