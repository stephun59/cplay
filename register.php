<?php
$page = [
    'title' => 'Inscription',
    'premium' => false,
    'admin' => false,
];

require_once 'src/app.php';

$errors = "";
if( isset( $_POST['username'] ) ){

	if( empty( $_POST['username'] ) ){
	    $errors .= "<li>Vous devez saisir un nom d'utilisateur</li>";
	}

	if ( strlen( $_POST['username'] ) < 4 ) {
		$errors .= "<li>Votre nom d'utilisateur doit contenir au moins 4 caractères</li>";
	}

	if( empty( $_POST['email'] ) ){
	    $errors .= "<li>Vous devez saisir une adresse mail</li>";
	}

	if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
		$errors .= '<li>L\'adresse mail saisie n\'est pas valide</li>';
	}

	$stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
	$stmt->bindValue(1, $_POST["email"]);
	$stmt->execute();

	if ( $stmt->fetch() ) {
		$errors .="<li>Cette adresse mail est déjà utilisée</li>";
	}

	if( empty( $_POST['password'] ) ){
	    $errors .= "<li>Vous devez définir un mot de passe</li>";
	}

	if ( $_POST['password'] !== $_POST['checkpassword']) {
		$errors .= "<li>Les mots de passes ne correspondent pas</li>";
	}


	if (is_numeric($_POST['password'])) {
		$errors .= "<li>Le mot de passe ne peut contenir que des chiffres</li>" ;
	}

	if (strlen($_POST['password']) < 8) {
		$errors .= "<li>Le mot de passe doit contenir au moins 8 caractères</li>";
	}

	if (empty( $errors ) ) {
		$query = 'INSERT INTO users (username, email, password) VALUES ( :username, :email, :password)';
		$stmt = $db->prepare( $query );

		$stmt->bindValue('username', htmlspecialchars( $_POST['username'] ) );
		$stmt->bindValue('email', htmlspecialchars( $_POST['email'] ) );

		$stmt->bindValue('password', password_hash( $_POST['password'], PASSWORD_DEFAULT ) );

		if ( $stmt ->execute() ) {
            $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->bindValue(1, $_POST["email"]);
            $stmt->execute();
            $user = $stmt->fetch();
            login( $user );
            header("location: list.php");
		}else{
			$errors .= "<li>Erreur lors de l'inscription</li>";
		}
	}
}

require_once 'view/header.php';
 ?>


<!-- Le HTML de mon formulaire. -->

 <h2>Inscrivez-vous</h2>

 <form method="post" class="grid-x add">
	<?php echo showError( $errors ); ?>

     <div class="small-12 medium-3 cell">
         <input type="text" name="username" placeholder="votre pseudo" />
         <input type="text" name="email" placeholder="votre mail">
         <input type="password" name="password" placeholder="Votre mot de passe" />
         <input type="password" name="checkpassword" placeholder="confirmez votre MDP">
     </div>

     <input type="submit" value="Valider" class="button expanded" />
 </form>



 <?php require_once 'view/footer.php'; ?>
