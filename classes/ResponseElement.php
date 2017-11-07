<?php

class ResponseElement {
    public $status_code = "E100";
    public $caused_by = "server";

    public function __construct($status_code = null, $caused_by = null) {
        if ($status_code !== null)
            $this->status_code = $status_code;
        
        if ($caused_by !== null)
            $this->caused_by = $caused_by;
    }
}

?>