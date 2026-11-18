<?php

class Delivery extends Base {
    private $vehicle_capacity, $vehicle_license_plate;

    function __construct($id, $fullname, $email, $phone, $address, $vehicle_capacity, $vehicle_licence_plate) {
        parent::__construct($id, $fullname, $email, $phone, $address);
        $this->vehicle_capacity = $vehicle_capacity;
        $this->vehicle_license_plate = $vehicle_licence_plate;
    }

    /**
     * @return mixed
     */
    public function getVehicleCapacity()
    {
        return $this->vehicle_capacity;
    }

    /**
     * @return mixed
     */
    public function getVehicleLicensePlate()
    {
        return $this->vehicle_license_plate;
    }
}
