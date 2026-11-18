<?php

require_once "base.php";
require_once "buyer.php";
require_once "farmer.php";
require_once "delivery.php";
require_once "admin.php";

class User {
    private string $role;
    private ?Base $person = null;
    private ?Admin $admin = null;

    /**
     * @param string $role  'farmer', 'buyer', 'delivery', or 'admin'
     * @param array  $data  Data array containing constructor arguments
     */
    public function __construct(string $role, array $data) {
        $this->role = strtolower($role);

        switch ($this->role) {
            case 'farmer':
                $this->person = new Farmer(
                    $data['id'],
                    $data['fullname'],
                    $data['email'],
                    $data['phone'],
                    $data['address'],
                    $data['business_name']
                );
                break;

            case 'buyer':
                $this->person = new Buyer(
                    $data['id'],
                    $data['fullname'],
                    $data['email'],
                    $data['phone'],
                    $data['address']
                );
                break;

            case 'delivery':
                $this->person = new Delivery(
                    $data['id'],
                    $data['fullname'],
                    $data['email'],
                    $data['phone'],
                    $data['address'],
                    $data['vehicle_capacity'],
                    $data['vehicle_license_plate']
                );
                break;

            case 'admin':
                $this->admin = new Admin(
                    $data['id'],
                    $data['username'],
                    $data['email']
                );
                break;

            default:
                throw new InvalidArgumentException("Invalid user role: {$role}");
        }
    }

    public function getRole(): string {
        return $this->role;
    }

    /**
     * Returns the Base instance (Farmer, Buyer, or Delivery).
     */
    public function getPerson(): ?Base {
        return $this->person;
    }

    /**
     * Returns the Admin instance.
     */
    public function getAdmin(): ?Admin {
        return $this->admin;
    }
}