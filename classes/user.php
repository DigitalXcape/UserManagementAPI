<?php
class User {
    // Properties (private fields)
    private $username;
    private $email;
    private $password;
    private $id;
    private $role;

    // Constructor to initialize the object
    public function __construct($username, $email, $password, $id, $role) {
        $this->setUsername($username);
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setId($id);
        $this->setRole($role);
    }

    // Getter for username
    public function getUsername() {
        return $this->username;
    }

    // Setter for username
    public function setUsername($username) {
        // You can add validation for username here
        if (!empty($username)) {
            $this->username = $username;
        } else {
            throw new Exception("Username cannot be empty.");
        }
    }

    // Setter for username
    public function setId($id) {
        // You can add validation for username here
        if (!empty($id)) {
            $this->id = $id;
        } else {
            throw new Exception("Id cannot be empty.");
        }
    }

    // Getter for email
    public function getId() {
        return $this->id;
    }

    // Getter for email
    public function getEmail() {
        return $this->email;
    }

    // Setter for email
    public function setEmail($email) {
        $this->email = $email;
    }

    // Getter for password (Note: typically, you shouldn't have a getter for passwords)
    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        // Define regex patterns for validation
        $lengthPattern = '/^.{8,20}$/';
        $numberPattern = '/[0-9]/';
        $lowercasePattern = '/[a-z]/';
        $uppercasePattern = '/[A-Z]/';
    
        // Initialize an array to hold the validation errors
        $requirements = [];
    
        // Check if the password meets all the requirements
        if (!preg_match($lengthPattern, $password)) {
            $requirements[] = "Password must be between 8 and 20 characters long.";
        }
        if (!preg_match($numberPattern, $password)) {
            $requirements[] = "Password must contain at least one number.";
        }
        if (!preg_match($lowercasePattern, $password)) {
            $requirements[] = "Password must contain at least one lowercase letter.";
        }
        if (!preg_match($uppercasePattern, $password)) {
            $requirements[] = "Password must contain at least one uppercase letter.";
        }
    
        // Throw an exception if there are validation errors
        if (count($requirements) > 0) {
            throw new Exception(implode("\n", $requirements));
        } else {
            // Hash the password and set it if validation is successful
            $this->password = $password;
        }
    }

    public function getRole(){
        return $this->role;
    }

    public function setRole($role){
        $this->role = $role;
    }

    // Method to display user info (for testing purposes)
    public function displayUserInfo() {
        echo "Username: " . $this->getUsername() . "<br>";
        echo "Email: " . $this->getEmail() . "<br>";
    }
}
?>