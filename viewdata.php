<?php
require_once "../config.php";

// The Tsugi PHP API Documentation is available at:
// http://do1.dr-chuck.com/tsugi/phpdoc/

use \Tsugi\Util\Net;
use \Tsugi\Util\U;
use \Tsugi\Core\LTIX;

// Allow this to just be launched as a naked URL w/o LTI
$LTI = LTIX::session_start();

if ( ! $USER->id || ! $LINK->id ) {
    Net::send403();
    die_with_error_log('Must be logged in to track analytics');
}

function zpad($i) {
    return str_pad($i."", 3, "0", STR_PAD_LEFT);
}

$sql = "SELECT * FROM {$CFG->dbprefix}youtube_views 
WHERE link_id = :link_id LIMIT 1";

$rows = $PDOX->allRowsDie($sql, array(
        ':link_id' => $LINK->id
));

$retval = array();
if ( $rows ) {
    $row = $rows[0];
    $retval['seconds'] = $row['seconds'];
    $retval['width'] = $row['width'];
    $retval['title'] = __('Video Views');
    $retval['hAxis'] = __('Time(Seconds)');
    $retval['vAxis'] = __('Views');
    $vector = array();
    $vector[] = array(__('Seconds'), __('Views'));
    for($i=0;$i<120;$i++) {
        $key = 'b'.zpad($i);
        if ( !isset($row[$key]) ) continue;
        $vector[] = array($i*$row['width'],(int) $row[$key]);
    }
    $retval['vector'] = $vector;
}

echo(json_encode($retval, JSON_PRETTY_PRINT));