<?php

include_once(__DIR__ . "/AbstractEntity.php");
include_once(__DIR__ . "/../../utils/normalize_utf8_string.php");

class Person extends AbstractEntity
{
    protected string $lastname;
    protected string $normLastname;
    protected string $phoneCode;

    function __construct(protected string $firstname, string $lastname, protected string $email, string $phoneCode, protected string $phoneNum)
    {
        parent::__construct();

        $this->setLastname($lastname);
        $this->setPhoneCode($phoneCode);
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
        $this->normLastname = strNormalize($lastname);

        return $this;
    }

    public function getNormLastname(): string
    {
        return $this->normLastname;
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
            if (str_contains($phoneCode, $code)) {
                $phoneCode = $code;
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