<?php

$servername = "localhost";
$username = "gwada";
$password = "&?p)W]ex?-R/57m";
$dbname = "admin_videos";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
 
if(isset($_POST['submit'])){
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
 
    $errors = [];
 
    if(empty($username)){
        $errors[] = "Username field is required";
    }
 
    if(empty($email)){
        $errors[] = "Email field is required";
    }
 
    if(empty($password)){
        $errors[] = "Password field is required";
    }
 
    if($password !== $password_confirm){
        $errors[] = "Password and confirm password fields do not match";
    }
 
    if(empty($errors)){
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email=:email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
 
        if($user){
            $errors[] = "Email address already exists";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
 
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->execute(['username' => $username, 'email' => $email, 'password' => $password_hash]);
 
            header('Location: login.php');
        }
    }
}
 
?>
 
 <?php include 'header.php'; ?>

 
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
 
    <?php if(!empty($errors)): ?>
        <div style="color: red;">
            <?php foreach($errors as $error): ?>
                <p><?php echo $error ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
 
    <form method="POST">
        <div>
            <label>Username</label>
            <input type="text" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : '' ?>">
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>">
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password">
        </div>
        <div>
            <label>Confirm Password</label>
            <input type="password" name="password_confirm">
        </div>
        <button type="submit" name="submit">Register</button>
    </form>
</body>
</html>
