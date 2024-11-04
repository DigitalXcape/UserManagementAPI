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
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function getUsers() {
        $data = $this->getData();
        $userList = [];
        
        foreach ($data as $item) {
            $userList[] = $item['UserName'];
        }

        return $userList;
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
                    $this->logger->log('User of ID: ' . $userId . ' Deleted');  
                    return true;
                } else {
                    return false;
                }
            } catch (PDOException $e) {
                $this->logger->log("Delete failed: " . $e->getMessage());
                return false;
            }
        } else {
            $this->logger->log("No connection.");
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
                $stmt = $this->conn->prepare("
                    INSERT INTO users (UserName, Email, Password)
                    VALUES (:userName, :email, :password)
                ");
                $stmt->bindParam(':userName', $userName, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    
                $stmt->execute();
    
                if ($stmt->rowCount() > 0) {
                    $this->logger->log('User ' . $userName . ' Added Successfully');                    
                    return true;
                } else {
                    return false;
                }
            } catch (PDOException $e) {
                $this->logger->log("Insert failed: " . $e->getMessage());
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
        if ($this->conn) {
            try {
                $stmt = $this->conn->prepare("SELECT * FROM users WHERE Email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
                // Directly compare the provided password with the stored password
                if ($user && $user['Password'] === $password) {
                    return $user; // Return user data on successful validation
                }
    
                return false; // Invalid email or password
            } catch (PDOException $e) {
                $this->logger->log("Validation failed: " . $e->getMessage());
                return false; // Handle any errors
            }
        } else {
            $this->logger->log("No connection.");
            return false;
        }
    }
}
?>