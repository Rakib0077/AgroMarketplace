<?php
require_once "base.php";

class Buyer extends Base {

    function __construct($id, $fullname, $email, $phone, $address)
    {
        parent::__construct($id, $fullname, $email, $phone, $address);
    }
}
