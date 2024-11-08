<?php
class User {
    // Properties (private fields)
    private $username;
    private $email;
    private $password;
    private $id;
    private $role;
    private $storyPage;

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
        $this->password = $password;
    }

    //Gets the role (access level) that the account has
    public function getRole(){
        return $this->role;
    }

    //Sets the role (access level) that the account has
    public function setRole($role){
        $this->role = $role;
    }

    //Gets the story page that the player is on
    public function getStoryPage(){
        return $this->storyPage;
    }

    //Sets the story page that the player is on
    public function setStoryPage($storyPage){
        $this->storyPage = $storyPage;
    }

    // Method to display user info (for testing purposes)
    public function displayUserInfo() {
        echo "Username: " . $this->getUsername() . "<br>";
        echo "Email: " . $this->getEmail() . "<br>";
    }
}
?>