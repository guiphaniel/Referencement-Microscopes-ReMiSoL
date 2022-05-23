<?php

include_once(__DIR__ . "/AbstractEntity.php");

class Person extends AbstractEntity
{
    function __construct(protected string $firstname, protected string $lastname, protected string $email, protected string $phoneCode, protected string $phoneNum)
    {
        parent::__construct();
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneCode()
    {
        return $this->phoneCode;
    }

    public function setPhoneCode($phoneCode)
    {
        $codes = ["+32", "+33", "+41"]; // Belgium, France, Switzerland

        $valid = false;
        foreach ($codes as $code) {
            if (strpos($phoneCode, $code)) {
                $valid = true;
                break;
            }
        }
        if (!$valid)
            throw new Exception("L'index téléphonique fourni n'est pas supporté");

        $this->phoneCode = $phoneCode;

        return $this;
    }

    public function getPhoneNum()
    {
        return $this->phoneNum;
    }

    public function setPhoneNum($phoneNum)
    {
        $this->phoneNum = $phoneNum;

        return $this;
    }
}