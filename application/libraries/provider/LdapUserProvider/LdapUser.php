<?php

class LdapUser extends User {

    protected $first_name;
    protected $last_name;
    protected $phone_number;
    protected $mobile_number;
    protected $address;
    protected $city;
    protected $state;
    protected $zip_code;

    public function __construct($data, $mapping)
    {
        $this->id = isset($data[$mapping['id']][0])? $data[$mapping['id']][0] : null;
        $this->username = isset($data[$mapping['username']][0])? $data[$mapping['username']][0] : null;
        $this->email = isset($data[$mapping['email']][0])? $data[$mapping['email']][0] : null;
        $this->first_name = isset($data[$mapping['first_name']][0])? $data[$mapping['first_name']][0] : null;
        $this->last_name = isset($data[$mapping['last_name']][0])? $data[$mapping['last_name']][0] : null;
        $this->phone_number = isset($data[$mapping['phone_number']][0])? $data[$mapping['phone_number']][0] : null;
        $this->mobile_number = isset($data[$mapping['mobile_number']][0])? $data[$mapping['mobile_number']][0] : null;
        $this->address = isset($data[$mapping['address']][0])? $data[$mapping['address']][0] : null;
        $this->state = isset($data[$mapping['state']][0])? $data[$mapping['state']][0] : null;
        $this->city = isset($data[$mapping['city']][0])? $data[$mapping['city']][0] : null;
        $this->zip_code = isset($data[$mapping['zip_code']][0])? $data[$mapping['zip_code']][0] : null;
        $this->role = $data['role_slug'];
    }

    /**
     * @param null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @return null
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @return null
     */
    public function getPhoneNumber()
    {
        return $this->phone_number;
    }

    /**
     * @return null
     */
    public function getMobileNumber()
    {
        return $this->mobile_number;
    }

    /**
     * @return null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return null
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return null
     */
    public function getZipCode()
    {
        return $this->zip_code;
    }
}