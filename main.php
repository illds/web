<?php

session_start();

error_reporting(E_ERROR | E_PARSE);

function checkHit($xVal, $yVal, $rVal)
{
    return ($xVal >= 0 && $yVal <= 0 && $rVal >= $xVal - $yVal)
        || ($xVal >= 0 && $yVal >= 0 && $xVal <= $rVal && $yVal <= $rVal / 2)
        || $xVal <= 0 && $yVal >= 0 && pow($xVal, 2) + pow($yVal, 2) <= pow($rVal / 2, 2);
}

function validate($xVal, $yVal, $rVal, $timezone)
{
    return isset($xVal) && isset($xVal) && isset($xVal) && isset($timezone)
        && is_numeric($xVal) && is_numeric($yVal) && is_numeric($rVal) && is_numeric($timezone)
        && $xVal > -5 && $xVal < 3 && $yVal >= -5 && $yVal <= 3 && $rVal >= 1 && $rVal <= 3;
}

function getResultArray($xVal, $yVal, $rVal, $timezone)
{
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

function generateTableWithRows($results)
{
    $html = '';

    foreach ($results as $elem)
        $html .= generateRow($elem);

    return $html;
}

function generateRow($elem)
{
    $isHit = $elem['isHit'] ? 'Yes' : 'No';
    $elemHtml = $elem["isHit"] ? '<tr class="hit-yes">' : '<tr class="hit-no">';
    $elemHtml .= '<td>' . $elem['x'] . '</td>';
    $elemHtml .= '<td>' . $elem['y'] . '</td>';
    $elemHtml .= '<td>' . $elem['r'] . '</td>';
    $elemHtml .= '<td>' . $elem['currentTime'] . '</td>';
    $elemHtml .= '<td>' . $elem['execTime'] . '</td>';
    $elemHtml .= '<td>' . $isHit . '</td>';
    $elemHtml .= '</tr>';

    return $elemHtml;
}

function clear()
{
    $_SESSION['results'] = array();
}

function print_error()
{
    echo "Error: invalid values given.";
}

$state = $_GET['state'];

if ($state == 1) {
    if (isset($_SESSION['results']))
        foreach (array_reverse($_SESSION['results']) as $element) echo generateTableWithRows($element);
} else if ($state == 2) {
    clear();
} else if ($state == 0) {
    $xVal = $_GET['x'];
    $yVal = $_GET['y'];
    $rVal = $_GET['r'];
    $timezone = $_GET['timezone'];

    if (validate($xVal, $yVal, $rVal, $timezone)) {
        $xVal = explode(",", $_GET['x']);

        $results = getResultArray($xVal, $yVal, $rVal, $timezone);

        if (!isset($_SESSION['results'])) {
            $_SESSION['results'] = array($results);
        } else {
            array_push($_SESSION['results'], $results);
        }

        foreach (array_reverse($_SESSION['results']) as $element) echo generateTableWithRows($element);
    } else {
        print_error();
    }
} else {
    print_error();
}
?>