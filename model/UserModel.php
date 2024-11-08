<?php
require_once '../classes/user.php';
require_once '../logger/Logger.php';

class UserModel {
    private $conn;
    private $logger;

    public function __construct() {
        try {
            $this->conn = new PDO('mysql:host=localhost;dbname=db_users', 'root', '');
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->logger = Logger::getInstance();
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getData() {
        if ($this->conn) {
            try {
                $stmt = $this->conn->query("SELECT * FROM users");
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $users = [];
                foreach ($results as $result) {
                    $user = new User(
                        $result['UserName'],
                        $result['Email'],
                        $result['Password'],
                        $result['UserID'],
                        $result['Role']
                    );

                    $user->setStoryPage($result['StoryPage']);

                    $users[] = [
                        'username' => $user->getUsername(),
                        'email' => $user->getEmail(),
                        'user_id' => $result['UserID'],
                        'role' => $result['Role'],
                        'story_page' => $user->getStoryPage()
                    ];
                    $this->logger->log($result['UserName']);
                }
                return $users; // Return an array of user data arrays
            } catch (PDOException $e) {
                $this->logger->log("Query failed: " . $e->getMessage());
                return [];
            }
        } else {
            $this->logger->log("No connection.");
            return [];
        }
    }

    public function __destruct() {
        $this->conn = null;
    }

    public function getUserById($userId) {
        if ($this->conn) {
            try {
                $stmt = $this->conn->prepare("SELECT UserID, UserName, Email, Password, Role FROM users WHERE UserID = :userId");
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result) {
                $this->logger->log('User: ' . $result['UserName'] . ' was found via email');
                return new User(
                    $result['UserName'],
                    $result['Email'],
                    $result['Password'],
                    $result['UserID'],
                    $result['Role']
                );
                    $this->logger->log($result['UserID'] . "Fetched");
                } else {
                    return null;
                }
            } catch (PDOException $e) {
                $this->logger->log("Query failed: " . $e->getMessage());
                return null;
            }
        } else {
            $this->logger->log("No connection.");
            return null;
        }
    }

    public function getUserByEmail($email) {
        if ($this->conn) {
            try {
                $stmt = $this->conn->prepare("SELECT UserID, UserName, Email, Password, Role FROM users WHERE Email = :email");
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result) {
                    $this->logger->log('User: ' . $result['UserName'] . ' was found via email');
                    return new User(
                        $result['UserName'],
                        $result['Email'],
                        $result['Password'],
                        $result['UserID'],
                        $result['Role']
                    );
                    $this->logger->log($result['UserID'] . "Fetched");
                } else {
                    return null;
                }
            } catch (PDOException $e) {
                $this->logger->log("Query failed: " . $e->getMessage());
                return null;
            }
        } else {
            $this->logger->log("No connection.");
            return null;
        }
    }

    public function deleteUser($userId) {
        if ($this->conn) {
            try {
                $stmt = $this->conn->prepare("DELETE FROM users WHERE UserID = :userId");
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $this->logger->log('User with ID: ' . $userId . ' deleted successfully.');  
                    return true;
                } else {
                    $this->logger->log('No user found with ID: ' . $userId . '. Deletion failed.'); 
                    return false;
                }
            } catch (PDOException $e) {
                $this->logger->log("Delete failed: " . $e->getMessage());
                return false;
            }
        } else {
            $this->logger->log("No database connection available.");
            return false;
        }
    }

    public function updateUser($userId, $userName, $email, $password) {
        if ($this->conn) {
            try {
                $stmt = $this->conn->prepare("
                    UPDATE users 
                    SET UserName = :userName, Email = :email, Password = :password
                    WHERE UserID = :userId
                ");
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':userName', $userName, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $this->logger->log('User ' . $userName . ' Updated Successfully');  
                    return true;
                } else {
                    return false;
                }
            } catch (PDOException $e) {
                $this->logger->log("Update failed: " . $e->getMessage());
                return false;
            }
        } else {
            $this->logger->log("No connection.");
            return false;
        }
    }

    public function addUser($userName, $email, $password) {
        if ($this->conn) {
            try {
                // Define regex patterns for password validation
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
                }
    
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
                $stmt = $this->conn->prepare("
                    INSERT INTO users (UserName, Email, Password)
                    VALUES (:userName, :email, :password)
                ");
                $stmt->bindParam(':userName', $userName, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    
                $stmt->execute();
    
                if ($stmt->rowCount() > 0) {
                    $this->logger->log('User ' . $userName . ' added successfully.');                    
                    return true;
                } else {
                    return false;
                }
            } catch (PDOException $e) {
                $this->logger->log("Insert failed: " . $e->getMessage());
                return false;
            } catch (Exception $e) {
                // Log the validation error message
                $this->logger->log("Validation failed for user '$userName': " . $e->getMessage());
                return false;
            }
        } else {
            $this->logger->log("No connection.");
            return false;
        }
    }

    public function getStoryPageById($userId) {
        if ($this->conn) {
            try {
                $stmt = $this->conn->prepare("SELECT StoryPage FROM users WHERE UserID = :userId");
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result) {
                    $this->logger->log('StoryPage for user ' . $userId . ' retrieved: ' . $result['StoryPage']);
                    return $result['StoryPage'];
                } else {
                    return null;
                }
            } catch (PDOException $e) {
                $this->logger->log("Query failed: " . $e->getMessage());
                return null;
            }
        } else {
            $this->logger->log("No connection.");
            return null;
        }
    }

    public function updateStoryPageById($userId, $storyPage) {
        if ($this->conn) {
            try {
                $stmt = $this->conn->prepare("UPDATE users SET StoryPage = :storyPage WHERE UserID = :userId");
                $stmt->bindParam(':storyPage', $storyPage, PDO::PARAM_STR);
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $this->logger->log('StoryPage for user ' . $userId . ' updated to: ' . $storyPage);
                    return true;
                } else {
                    $this->logger->log('No changes made to StoryPage for user ' . $userId);
                    return false;
                }
            } catch (PDOException $e) {
                $this->logger->log("Update failed: " . $e->getMessage());
                return false;
            }
        } else {
            $this->logger->log("No connection.");
            return false;
        }
    }

    public function validateUser($email, $password) {
        $this->logger->log("Attempting to validate password for email: " . $email);
    
        // Check if the database connection is valid
        if (!$this->conn) {
            $this->logger->log("No database connection.");
            return false;
        }
    
        try {
            // Prepare statement to fetch the password hash
            $stmt = $this->conn->prepare("SELECT Password FROM users WHERE Email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Check if user exists
            if (!$result) {
                $this->logger->log("User not found for email: " . $email);
                return false; // User does not exist
            }
            $this->logger->log("Hashed password:" . $result['Password']);
            $this->logger->log("Inputted Password:" . $password);

            // Verify the hashed password
            if (password_verify($password, $result['Password'])) {
                $this->logger->log("Password validation successful for email: " . $email);
                return true; // Password is correct
            } else {
                $this->logger->log("Password validation failed for email: " . $email);
                return false; // Incorrect password
            }
        } catch (PDOException $e) {
            $this->logger->log("Validation query failed: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->logger->log("Validation error: " . $e->getMessage());
            return false;
        }
    }
}
?>