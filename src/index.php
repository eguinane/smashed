<?php

session_start();

if (array_key_exists('reset_session', $_GET)) {
    $_SESSION = [];
}

$mysql = new MySQLi(
    hostname: 'mariadb',
    username: 'smash',
    password: 'spiked',
    database: 'smashed'
);

if(!array_key_exists('players', $_SESSION)) {
    $_SESSION['players'] = [];
    foreach($mysql->query("SELECT id, nickname FROM players ORDER BY nickname")->fetch_all(mode: MYSQLI_ASSOC) as $row) {
        $_SESSION['players_list'][$row['nickname']] = $row['id'];
    }
}

if (array_key_exists('add_player', $_GET)) {
    $player_name = $_GET['add_player'];
    $_SESSION['players'][$player_name] = array();
    ksort($_SESSION['players']);
}

if (array_key_exists('del_player', $_GET)) {
    $player_name = $_GET['del_player'];
    unset($_SESSION['players'][$player_name]);
    foreach($_SESSION['players'] as $aggressor => $stats) {
        if (array_key_exists($player_name, $stats)) {
            unset($_SESSION['players'][$aggressor][$player_name]);
        }
    }
}

if (array_key_exists('aggressor', $_GET)) {
    $aggressor_name = $_GET['aggressor'];
    if (array_key_exists('victime', $_GET)) {
        $victime_name = $_GET['victime'];
    }
    else {
        $victime_name = 'unknown';
    }
    $_SESSION['players'][$aggressor_name][$victime_name] = ( $_SESSION['players'][$aggressor_name][$victime_name] ?? 0) + 1;
}

if (array_key_exists('victorious', $_GET)) {
    $victorious_name = $_GET['victorious'];

    if(!array_key_exists('unknown', $_SESSION['players'][$victorious_name])) {
        $_SESSION['players'][$victorious_name]['unknown'] = 0;
    }

    $sql = "INSERT INTO participants (game_id, aggressor_id, victim_id, spiked, victorious) VALUES ";

    $params = [];
    $placeholders = [];
    foreach ($_SESSION['players'] as $aggressor => $stats) {
        foreach ($stats as $victime => $spikes) {
            $placeholders[] = '(?, ?, ?, ?, ?)';
            $params[] = $_SESSION['next_game'];
            $params[] = $_SESSION['players_list'][$aggressor];
            $params[] = $_SESSION['players_list'][$victime] ?? null;
            $params[] = $spikes;
            if ($aggressor == $victorious_name && $victime == 'unknown' ) {
                $params[] = 1;
            }
            else {
                $params[] = 0;
            }
        }
    }

    $mysql->execute_query($sql.implode(', ', $placeholders),  $params);

    foreach($_SESSION['players'] as $aggressor => $stats) {
        $_SESSION['players'][$aggressor] = array();
    }
    unset($_SESSION['next_game']);
}

if(!array_key_exists('next_game', $_SESSION)) {
    $res = $mysql->query("SELECT max(game_id) + 1 as next_game FROM participants")->fetch_assoc();
    $_SESSION['next_game'] = $res['next_game'] ?? 1;
}


require_once './components/player_list.php';

?>
<!DOCTYPE html>
<head>
    <title>Smash Counter</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="player_list">
        <div class="row">
            <div>
                <h3>Potential players</h3>
                <div class="flex-row">
                    <?php echo generate_potential_player_list($_SESSION['players_list']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div>
                <h3>Active players</h3>
                <div class="flex-row">
                    <?php echo generate_active_player_list() ?>
                </div>
            </div>
        </div>
    <?php if(!empty($_SESSION['players']) ) { ?>
        <h3>Players for game #<?php echo $_SESSION['next_game'] ?></h3>

        <table>
            <?php echo generate_active_player_selector() ?>
        </table>

        <h3>Victorious</h3>

        <div class="flex-row">
            <?php echo generate_victorious_player_list() ?>
        </div>
    <?php } ?>
    </div>
</body>
