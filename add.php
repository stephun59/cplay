<?php
$page = [
    'title' => 'Ajouter un jeu',
    'premium' => false,
    'admin' => true,
];

require_once 'src/app.php';

$stmt = $db->query( 'SELECT * FROM games_type' );
$types = $stmt->fetchAll();

$stmt = $db->query( 'SELECT * FROM editor' );
$editors = $stmt->fetchAll();

$stmt = $db->query( 'SELECT * FROM developer' );
$developers = $stmt->fetchAll();

$errors = "";
$success = "";
if( isset( $_POST['name'] ) ){
    if( empty( $_POST['name'] ) ){
        $errors .= "<li>Vous deveez renseigner le nom de votre jeu</li>";
    }

    if( empty( $_POST['description'] ) ){
        $errors .= "<li>Vous deveez renseigner la description de votre jeu</li>";
    }

    if( empty( $_POST['type'] ) ){
        $errors .= "<li>Vous deveez renseigner le type de votre jeu</li>";
    }

    if( empty( $_POST['editor'] ) ){
        $errors .= "<li>Vous deveez renseigner l'éditeur de votre jeu</li>";
    }

    if( empty( $_POST['developer'] ) ){
        $errors .= "<li>Vous deveez renseigner le développeur de votre jeu</li>";
    }

    if( empty( $_POST['picture_link'] ) && $_FILES['picture_file']['error'] != 0 ){
        $errors .= "<li>Vous deveez télécharger ou ajouter le lien d'un média pour votre jeu</li>";
    }

    if( empty( $errors ) ){
        $query = 'INSERT INTO games ( name, picture, type_id, description,
            release_date, press_review, player_review,
            developer_id, editor_id ) VALUES ( :name, :picture,
                :type, :description, :release_date, :press_review,
                :player_review, :developer, :editor )';

        $stmt = $db->prepare( $query );

        $stmt->bindValue( 'name', htmlspecialchars( $_POST['name'] ) );
        $stmt->bindValue( 'type', htmlspecialchars( $_POST['type'] ) );
        $stmt->bindValue( 'description', htmlspecialchars( $_POST['description'] ) );
        $stmt->bindValue( 'developer', htmlspecialchars( $_POST['developer'] ) );
        $stmt->bindValue( 'editor', htmlspecialchars( $_POST['editor'] ) );

        $release_date = ( !empty( $_POST['release_date'] ) ) ? $_POST['release_date'] : NULL;
        $stmt->bindValue( 'release_date', $release_date );

        $press_review = ( !empty( $_POST['press_review'] ) ) ? $_POST['press_review'] : NULL;
        $stmt->bindValue( 'press_review', $press_review );

        $player_review = ( !empty( $_POST['player_review'] ) ) ? $_POST['player_review'] : NULL;
        $stmt->bindValue( 'player_review', $player_review );

        if( !empty( $_POST['picture_link'] ) ){
            $stmt->bindValue( 'picture', htmlspecialchars( $_POST['picture_link'] ) );
        }else{
            $result = uploadPicture( $_FILES['picture_file'], $uploadConfig );

            if( $result['status'] ){
                $stmt->bindValue( 'picture', $result['path'] );
            }else{
                $errors .= '<li>' . $result['message'] . '</li>';
            }
        }

        if( empty( $errors ) ){
            if( $stmt->execute() ){
                $success = "Votre commentaire à été publié";
                addLog(  "Ajout du jeu " .  htmlspecialchars( $_POST['name']) );
            }else{
                $errors .= 'Une erreur est survenue lors de la publication';
                addLog( "Erreur lors de l'inserstion d'un jeu en DB" );
            }
        }
    }
}


require_once 'view/header.php';
?>

<h2>Ajouter un jeu</h2>

<form method="post" enctype="multipart/form-data" class="grid-x add">
    <?php echo showError( $errors ); ?>
    <?php echo showSuccess( $success ); ?>

    <div class="small-12 medium-6 cell">
        <input type="text" name="name" placeholder="Nom du jeu" />
        <textarea name="description" rows="6" placeholder="Description du jeu"></textarea>

        <input type="number" name="press_review" placeholder="Note de la presse" />
        <input type="number" name="player_review" placeholder="Note des joueurs" />
    </div>

    <div class="small-12 medium-6 cell">
        <select name="type">
            <option selected disabled class="placeholder">Type du jeu</option>
            <?php foreach( $types as $type ){ ?>
                <option value="<?php echo $type['id']; ?>"><?php echo $type['genre']; ?></option>
            <?php } ?>
        </select>

        <select name="editor">
            <option selected disabled class="placeholder">Editeur du jeu</option>
            <?php foreach( $editors as $editor ){ ?>
                <option value="<?php echo $editor['id']; ?>"><?php echo $editor['name']; ?></option>
            <?php } ?>
        </select>

        <select name="developer">
            <option selected disabled class="placeholder">Développeur du jeu</option>
            <?php foreach( $developers as $developer ){ ?>
                <option value="<?php echo $developer['id']; ?>"><?php echo $developer['name']; ?></option>
            <?php } ?>
        </select>

        <input type="text" name="release_date" placeholder="Date de sortie" onfocus="this.type='date'" onblur="this.type='text'" />

        <h3>Média du jeu</h3>
        <input type="file" name="picture_file" />
        <p class="separator"><span>OU</span></p>
        <input type="url" name="picture_link" placeholder="Lien vers le média" />
    </div>

    <input type="submit" value="Ajouter" class="button expanded" />
</form>

<?php
require_once 'view/footer.php';
?>
