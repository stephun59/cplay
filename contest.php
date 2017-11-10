<?php
$page = [
    'title' => 'Concours',
    'premium' => true,
    'admin' => false,
];

require_once 'src/app.php';

$stmt = $db->prepare('SELECT contest_time FROM users WHERE id = ? ');
$stmt->bindValue( 1, $_SESSION['user']['id'] );
$stmt->execute();
$contestTime = strtotime( $stmt->fetch()['contest_time'] );

if( time() - $contestTime < $contestConfig['delay'] ){
    $canPlay = false;
}else{
    $canPlay = true;
}


if( isset( $_GET['tryout'] ) && $canPlay === true ){
    if( $_GET['tryout'] == rand( $contestConfig['min'],$contestConfig['max'] ) ){
        $stmt = $db->prepare( "SELECT * FROM games
                             LEFT JOIN users_game ON games.id = users_game.game_id
                             WHERE users_game.user_id != ? OR users_game.user_id IS NULL
                             ORDER BY RAND() LIMIT 1");
        $stmt->bindValue( 1, $_SESSION['user']['id'] );
        $stmt->execute();
        $wonGame = $stmt->fetch();

        $query = 'INSERT INTO users_game (game_id, user_id) VALUES (?,?) ';
        $stmt = $db->prepare( $query );
        $stmt->bindvalue( 1, $wonGame['id'] );
        $stmt->bindvalue( 2, $_SESSION['user']['id'] );
        $stmt->execute();

        $won = true;
    }else{
        $won = false;
    }

    $query = 'UPDATE users SET contest_time = NOW() WHERE id = ?';
    $stmt = $db->prepare( $query );
    $stmt->bindValue( 1, $_SESSION['user']['id'] );
    $stmt->execute();
}

require_once 'view/header.php';
?>

<h2>Concours</h2>

<div class="grid-x contest">
    <?php if( !isset( $won ) ){ ?>
        <?php if( $canPlay !== true ){ ?>
            <div class="small-12 cell">
                <div class="callout warning">
                    <i class="fa fa-warning"></i> Vous avez dejà joué cette semaine
                </div>
            </div>
        <?php } ?>

        <?php for( $i = 0; $i < 12; $i++ ){ ?>
            <div class="small-6 medium-3 cell">
                <a href="?tryout=<?php echo rand( $contestConfig['min'],$contestConfig['max'] ); ?>" class="box <?php echo ( $canPlay !== true ) ? 'disable' : ''; ?>">
                    ?
                </a>
            </div>
        <?php } ?>
    <?php }elseif( $won === true ){ ?>
        <div class="small-12 cell">
            <p>
                Bravo, vous avez gagné <?php echo $wonGame['name']; ?> -
                <a href="game.php?id=<?php echo $wonGame['id']; ?>">Voir la fiche du jeu</a>
            </p>
        </div>
    <?php }else{ ?>
        <div class="small-12 cell">
            <p>Vous avez perdu ! Retentez votre chance la semaine prochaine</p>
        </div>
    <?php } ?>
</div>

<?php
require_once 'view/footer.php';
?>
