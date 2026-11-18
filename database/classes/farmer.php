<?php

class Farmer extends Base {
    private $business_name;

    function __construct($id, $fullname, $email, $phone, $address, $business_name) {
        parent::__construct($id, $fullname, $email, $phone, $address);
        $this->business_name = $business_name;
    }
}
