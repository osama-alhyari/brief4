<?php
session_start();
include_once 'db.php';
unset($_SESSION['user_id']);
unset($_SESSION['admin']);

class User
{
    private $conn;
    private $table = 'users';

    public $id;
    public $email;
    public $password;
    public $role_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function login()
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE email = :email LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($this->password, $user['password'])) {
            $this->id = $user['user_id'];
            $this->role_id = $user['role_id'];
            return true;
        }

        return false;
    }
}

$database = new Database();
$db = $database->connect();
$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];

    if ($user->login()) {
        $_SESSION['user_id'] = $user->id;
        if ($user->role_id == 1) {
            $_SESSION['admin'] = true;
        }
        header('Location: http://localhost/Ecommerce-Website/client/home.php');
        exit();
    } else {
        $error_message = 'Invalid email or password.';
    }
}
