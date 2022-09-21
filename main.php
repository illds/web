<?php

session_start();

error_reporting(E_ERROR | E_PARSE);

function checkHit($xVal, $yVal, $rVal){
    return ($xVal >= 0 && $yVal <= 0 && $rVal >= $xVal - $yVal)
        || ($xVal >= 0 && $yVal >= 0 && $xVal <= $rVal && $yVal <= $rVal / 2)
        || $xVal <= 0 && $yVal >= 0 && pow($xVal, 2) + pow($yVal, 2) <= pow($rVal / 2, 2);
}

function validate($xVal, $yVal, $rVal){
    return is_numeric($xVal) && is_numeric($yVal) && is_numeric($rVal);
}

function getResultArray($xVal, $yVal, $rVal, $timezone) {
    $results = array();

    foreach ($xVal as $value) {
        $isHit = checkHit($value, $yVal, $rVal);
        $currentTime = date('H:i:s', time() - $timezone * 60);
        $executionTime = round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 7);

        array_push($results, array(
            "x" => $value,
            "y" => $yVal,
            "r" => $rVal,
            "currentTime" => $currentTime,
            "execTime" => $executionTime,
            "isHit" => $isHit
        ));
    }

    return $results;
}

function generateTableWithRows($results) {
//    $html = $wholeTable == 'true'? '<table id="result-table"><tr class="table-header">
//        <th class="coords-col">X</th>
//        <th class="coords-col">Y</th>
//        <th class="coords-col">R</th>
//        <th class="time-col">Request time</th>
//        <th class="time-col">Execution time</th>
//        <th class="hitres-col">Hit</th>
//    </tr>': '';
//    $html = '<table id="result-table"><tr class="table-header">
//        <th class="coords-col">X</th>
//        <th class="coords-col">Y</th>
//        <th class="coords-col">R</th>
//        <th class="time-col">Request time</th>
//        <th class="time-col">Execution time</th>
//        <th class="hitres-col">Hit</th>
//    </tr>';
    $html = '';
    foreach ($results as $elem)
        $html .= generateRow($elem);

// if ($wholeTable == 'true')
//    $html .= '</table>';

    return $html;
}

function generateRow($elem) {
    $isHit = $elem['isHit'] ? 'Yes': 'No';
    $elemHtml = $elem["isHit"]? '<tr class="hit-yes">' : '<tr class="hit-no">';
    $elemHtml .= '<td>' . $elem['x'] . '</td>';
    $elemHtml .= '<td>' . $elem['y'] . '</td>';
    $elemHtml .= '<td>' . $elem['r'] . '</td>';
    $elemHtml .= '<td>' . $elem['currentTime'] . '</td>';
    $elemHtml .= '<td>' . $elem['execTime'] . '</td>';
    $elemHtml .= '<td>' . $isHit . '</td>';
    $elemHtml .= '</tr>';

    return $elemHtml;
}

function clear(){
    $_SESSION['results'] = array();
}

$xVal = explode(",", $_GET['x']);
$yVal = $_GET['y'];
$rVal = $_GET['r'];
$state = $_GET['state'];

//if (!validate($xVal, $yVal, $rVal)){
//    echo "Error";
//}

if ($state == 1) {
    if (isset($_SESSION['results'])) {
        foreach ($_SESSION['results'] as $element) echo generateTableWithRows($element);
    }
} else if ($state == 2){
    clear();
} else {
    $timezone = $_GET['timezone'];

    $results = getResultArray($xVal, $yVal, $rVal, $timezone);

    if (!isset($_SESSION['results'])) {
        $_SESSION['results'] = array($results);
    } else {
        array_push($_SESSION['results'], $results);
    }

    foreach ($_SESSION['results'] as $element) echo generateTableWithRows($element);
}

?>