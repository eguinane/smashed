<?php

function generate_potential_player_list($player_list): string {
    $res = [];
    foreach ($player_list as $player => $id) {
        if (!array_key_exists($player, $_SESSION['players'])) {
            $res[] = '<div class="player"><a href="index.php?add_player='.$player.'">'.$player.'</a></div>';
        }
    }
    return implode("\n", $res);
}

function generate_active_player_list(): string {
    $res = [];
    foreach ($_SESSION['players'] as $player => $stats) {
            $res[] = '<div class="player"><a href="index.php?del_player='.$player.'">'.$player.'</a></div>';
    }
    return implode("\n", $res);
}

function generate_victorious_player_list(): string {
    $res = [];
    foreach ($_SESSION['players'] as $player => $stats) {
            $res[] = '<div class="player"><a href="index.php?victorious='.$player.'">'.$player.'</a></div>';
    }
    return implode("\n", $res);
}

function generate_active_player_selector(): string {
    $res = generate_aggressor_list();
    $res .= generate_unknown_victim_rows_list();
    $res .= generate_victims_rows_list();

    return $res;
}

function generate_aggressor_list(): string {
    $res = '<tr>';
    $res .= '<th></th>';
    foreach (array_keys($_SESSION['players']) as $aggressor) {
        $res .= '<th>'.$aggressor.'</th>';
    }
    $res .= '</tr>';
    return $res;
}

function generate_unknown_victim_rows_list(): string {
    $res = '<tr>';
    $res .= '<th>unknown</th>';
    foreach ($_SESSION['players'] as $aggressor => $stats) {
        if(array_key_exists('unknown', $stats)) {
            $value = $stats['unknown'];
        }
        else {
            $value = 0;
        }

        $res .= '<th><a href="index.php?aggressor='.$aggressor.'">'.$value.'</a></th>';
    }
    $res .= '</tr>';
    return $res;
}

function generate_victims_rows_list(): string {
    $res = '';
    foreach (array_keys($_SESSION['players']) as $victime) {
        $res .= '<tr>';
        $res .= '<th>'.$victime.'</th>';
        foreach ($_SESSION['players'] as $aggressor => $stats) {
            if ( $victime == $aggressor ) {
                $res .= '<td></td>';
            }
            else {
                if(array_key_exists($victime, $stats)) {
                    $value = $stats[$victime];
                }
                else {
                    $value = 0;
                }

                $res .= '<th><a href="index.php?aggressor='.$aggressor.'&victime='.$victime.'">'.$value.'</a></th>';
            }
        }
        $res .= '</tr>';
    }
    return $res;
}


