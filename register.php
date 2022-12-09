<?php
require_once "connection.php";

session_start();

if (isset($_SESSION['user'])) {
    header('location: store.php');
}

if(isset($_REQUEST['register_btn'])){ //onclick for register_btn

    $name = strip_tags($_REQUEST['name']);
    $email = filter_var($_REQUEST['email'],FILTER_SANITIZE_EMAIL);
    $password = strip_tags($_REQUEST['password']);

    if(empty($name)){
        $errorMsg[0][] = 'İsim boş bırakılamaz!';
    }
    if(empty($email)){
        $errorMsg[1][] = 'Email boş bırakılamaz!';
    }
    if(empty($password)){
        $errorMsg[2][] = 'Şifre boş bırakılamaz!';
    }
    if(strlen($password) < 6){
        $errorMsg[2][] = 'Şifre en az 6 karakter içermelidir!';
    }

    if(empty($errorMsg)){

        try{
            $select_stmt = $db->prepare("SELECT name, email FROM users WHERE email = :email");
            $select_stmt->execute([':email' => $email]);
            $row = $select_stmt->fetch(PDO::FETCH_ASSOC);

            
            if(isset($row['email']) == $email){
                $errorMsg[1][] = "Bu email adresi kullanılıyor! Lütfen yeni bir adres girin veya giriş yapın.";
            }
            else{
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $insert_stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
                if(
                    $insert_stmt->execute(
                        [
                            ':name' => $name,
                            ':email' => $email,
                            ':password' => $hashed_password,
                        ]
                    )
                ){
                    header("Location: index.php");
                }
                
            }

        }
        catch(PDOException $e){
            $pdoError = $e->getMessage();
            echo "Error: ".$pdoError;
        }

    }

}
?>


<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
	<title>Register</title>
</head>
<body>
	<div class="container">
		
		<form action="register.php" method="post">
			<div class="mb-3">

				<label for="name" class="form-label">Name</label>
				<input type="text" name="name" class="form-control" placeholder="Jane Doe">

                <?php
                    if(isset($errorMsg[0])){
                        foreach($errorMsg[0] as $nameErrors){
                            echo "<p class = 'small text-danger'>".$nameErrors."</p>";
                        }
                    }
                ?>

			</div>
			<div class="mb-3">

				<label for="email" class="form-label">Email address</label>
				<input type="email" name="email" class="form-control" placeholder="jane@doe.com">

                <?php
                    if(isset($errorMsg[1])){
                        foreach($errorMsg[1] as $emailErrors){
                            echo "<p class = 'small text-danger'>".$emailErrors."</p>";
                        }
                    }
                ?>

			</div>
			<div class="mb-3">

				<label for="password" class="form-label">Password</label>
				<input type="password" name="password" class="form-control" placeholder="">

                <?php
                    if(isset($errorMsg[2][0])){
                        echo "<p class = 'small text-danger'>".$errorMsg[2][0]."</p>";
                    } 
                    elseif (isset($errorMsg[2][1])) {
                        echo "<p class = 'small text-danger'>".$errorMsg[2][1]."</p>";
                    }
                ?>
				
			</div>
			<button type="submit" name="register_btn" class="btn btn-primary">Register Account</button>
		</form>
		Already Have an Account? <a class="register" href="index.php">Login Instead</a>
	</div>
</body>
</html>