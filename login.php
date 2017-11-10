<?php
$page = [
    'title' => 'login',
    'premium' => false,
    'admin' => false,
];

require_once 'src/app.php';

$errors = "";

if ( isset( $_POST["email"] ) ) {
	$stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
	$stmt->bindValue( 1, htmlspecialchars( $_POST["email"] ) );
	$stmt->execute();

    $user = $stmt->fetch();
	if( $user ){

		if( password_verify( $_POST["password"], $user["password"] ) ){
            login( $user );

			header("location: list.php");
		}else{
			$errors .= "<li>Identifiants non reconnus</li>";
		}
	}else{
		$errors .= "<li>Identifiants non reconnus</li>";
	}
}

require_once 'view/header.php';
?>

 <h2>Connectez vous</h2>

 <form method="post" class="grid-x add">
		<?php echo showError( $errors ); ?>

     <div class="small-12 medium-3 cell">
         <input type="email" name="email" placeholder="Votre email">
         <input type="password" name="password" placeholder="Votre mot de passe" />
     </div>

     <input type="submit" value="Valider" class="button expanded" />
 </form>



<?php
require_once 'view/footer.php';
 ?>
