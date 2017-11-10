

<?php
function averageReview( $reviews ){
    $sum = 0;
    foreach( $reviews as $review ){
        $sum += $review;
    }
    return round( $sum / count( $reviews ), 1 );
}

function getGameType( $identifier ){
    return $GLOBALS['types'][ $identifier ];
}

function getShortDescription( $description, $size = 40 ){
    if( strlen( $description ) > $size ){
        return substr( $description, 0, $size ) . '...';
    }

    return $description;
}

function getShortReview( $review ){
    if( $review < 10 ){
        return 'star-o';
    }

    if( $review < 15 ){
        return 'star-half-o';
    }

    return 'star';
}

function needTriggerWarning( $type ){
    if( $type == "Horreur" || $type == "Adulte" ){
        return true;
    }

    return false;
}

function getFrenchDate( $date, $time = false ){
    $time = strtotime( $date );
    if( $time ){
        return date( 'd/m/Y - H:i', $time );
    }

    return date( 'd / m / Y', $time );
}

function uploadPicture( $file, $config ){
    if( $file['size'] > $config['max'] ){
        return array(
            'status' => false,
            'message' => 'Le fichier est trop volumineux',
        );
    }

    if( !in_array( $file['type'], $config['allows'] ) ){
        return array(
            'status' => false,
            'message' => 'Ce type de ficher n\'est pas autorisé',
        );
    }

    $ext = pathinfo( $file['name'], PATHINFO_EXTENSION );
    $name = uniqid( 'media_' ) . '.' . $ext;
    $path = $config['path'] . $name;
    move_uploaded_file( $file['tmp_name'], $path );
    return array(
        'status' => true,
        'path' => $path,
    );
}

function showError( $errors ){
    if( !empty( $errors ) ){ ?>
        <div class="small-12 cell">
            <div class="callout alert">
                <ul><?php echo $errors; ?></ul>
            </div>
        </div>
     <?php }
}
function showSuccess( $success ){
    if( !empty( $success ) ){ ?>
        <div class="small-12 cell">
            <div class="callout success">
                <ul><?php echo $success; ?></ul>
            </div>
        </div>
     <?php }
}

function addLog( $message ){
    $file = fopen( './logs/' . date("Y-m-d") . '.log','a+');
    fputs($file, date("Y-m-d H:i:s") . ' - ' . $message . "\n" );
    fclose( $file );
}

function login( $user ){
    $_SESSION["auth"] = true;
    $_SESSION["user"] = $user;

    $keychain = generateCookieKeychain( $user );
    setcookie( 'autoauth', $keychain, time() + 15 * 24 * 3600, null, null, false, true  );
}

function generateCookieKeychain( $user ){
    $kc = $user['id'];
    $kc .= '____';
    $kc .= md5( $user['username'] . $user['email'] . $user['password'] );

    return $kc;
}

function generateResetToken(){
    return md5( uniqid( rand(), true ) );
}











// Fin
