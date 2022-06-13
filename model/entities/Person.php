<?php

include_once(__DIR__ . "/AbstractEntity.php");
include_once(__DIR__ . "/../../utils/normalize_utf8_string.php");

class Person extends AbstractEntity
{
    protected string $firstname;
    protected string $lastname;
    protected string $normLastname;
    protected string $email;
    protected string $phoneCode;
    protected string $phoneNum;

    function __construct(string $firstname, string $lastname, string $email, string $phoneCode, string $phoneNum)
    {
        parent::__construct();

        $this->setFirstname($firstname);
        $this->setLastname($lastname);
        $this->setEmail($email);
        $this->setPhoneCode($phoneCode);
        $this->setPhoneNum($phoneNum);
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname)
    {
        $this->firstname = ucwords($firstname);

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname)
    {
        $this->lastname = strtoupper($lastname);
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
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new Exception("Veuillez saisir un courriel valide.");
            
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
            throw new Exception("L'index téléphonique fourni n'est pas supporté.");

        $this->phoneCode = $phoneCode;

        return $this;
    }

    public function getPhoneNum()
    {
        return $this->phoneNum;
    }

    public function setPhoneNum($phoneNum)
    {
        if(!preg_match("/^\d{9}$/", $phoneNum))
            throw new Exception("Veuillez saisir un numéro de téléphone valide.");

        $this->phoneNum = $phoneNum;

        return $this;
    }
}