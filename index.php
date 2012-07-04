<?php
header("Content-Type: text/plain");
require_once("toggl/toggl.php");
require_once("codebase/Codebase.class.php");
require_once("sqlite.class.php");
require_once("sqlite.setup.php");

$verbose = true;
$sqlOutput = false;
$dryrun = false;
$dateFrom = '03-07-2012'; // decide from which date on you want to retrieve the time entries

if($sqlOutput) {
    print_r($db->query("SELECT * FROM entries")->fetchArray(SQLITE3_ASSOC));
}

/* Employee list */
$employees = array('Employee 1','Employee 2','Employee 3'); //, etc...

/* Toggl credentials */
$toggl['Employee 1'] = 'TOGGL-API-KEY-HERE';
$toggl['Employee 2'] = 'TOGGL-API-KEY-HERE';
$toggl['Employee 3'] = 'TOGGL-API-KEY-HERE';

/* Codebase credentials */
$codebase['Employee 1'] = array('API-USERNAME-HERE','API-KEY-HERE','HOSTNAME-HERE','s');
$codebase['Employee 2'] = array('API-USERNAME-HERE','API-KEY-HERE','HOSTNAME-HERE','s');
$codebase['Employee 3'] = array('API-USERNAME-HERE','API-KEY-HERE','HOSTNAME-HERE','s');

foreach($employees as $employee) {
    $t = new Toggl($toggl[$employee]);
    $c = new Codebase($codebase[$employee][0],$codebase[$employee][1],$codebase[$employee][2],$codebase[$employee][3]);
    $token = $t->getToken();
    if(empty($token) || $c->projects()===false) { // if we can't login to Codebase AND Toggl, skip this employee
        _log("Can't log in to Codebase and/or Toggl",$employee);
        continue;
    }

    $projects = $c->projects();
    foreach($projects as $project) {
        $p[strtolower($project['name'])] = $project['permalink'];
    }

    $entries = $t->timeEntriesLoadRecent(strtotime($dateFrom),strtotime("+1 year"));
    foreach($entries->data->data as $entry) {
        // duration, billable, workspace (name,id), stop, updated_at, id, start, user_id, tag_names, description
        $minutes = ceil($entry->duration/60);
        $parts = explode(" ",$entry->description);
        $ticket_id = null;
        foreach($parts as $key=>$part) {
            if(substr($part,0,1)=='#' AND is_numeric(substr($part,1))) {
                $ticket_id = substr($part,1);
                unset($parts[$key]);
            }
        }

        if(!isset($entry->project->name)) {
            $projectGuess = array_shift($parts);
        } else {
            $projectGuess = $entry->project->name;
        }
        if(isset($p[strtolower($projectGuess)])) {
            $projectIdentifier = $p[strtolower($projectGuess)];
            $summary = implode(" ",$parts);
            $results = $db->querySingle("SELECT * FROM entries WHERE `t-entry-id` = '".$entry->id."'");
            if($results===null) {
                if(!$dryrun) {
                    if($ticket_id!=null) {
                        $result = $c->note($projectIdentifier,$summary,$ticket_id,array(),$minutes);
                        $db->exec("INSERT INTO entries VALUES (null,".$minutes.",".$ticket_id.",null,".$entry->id.",'".$summary."',datetime());");
                    } else {
                        $params = array(
                            'summary'=>$summary,
                            'minutes'=>$minutes,
                            'session-date'=>date("Y-m-d",strtotime($entry->start))
                        );
                        $result = $c->addTimeEntry($projectIdentifier,$params);
                        $db->exec("INSERT INTO entries VALUES (null,".$minutes.",null,".$result['id'].",".$entry->id.",'".$summary."',datetime());");
                    }
                }
                if(isset($result['error'])) {
                    _log("Error: ".$result['error'],$employee);
                } else {
                    _log("Succesfully added '".$summary."' (".$minutes."m) to ".$projectIdentifier,$employee);
                }
            } elseif($verbose) {
                _log("'".$summary."' to ".$projectIdentifier." is already added",$employee);
            }
        } elseif($verbose) {
            _log("Project '".$projectGuess."' is not found in Codebase",$employee);
        }
    }
}

function _log($text,$employee=null) {
    $string = date("d-m-Y H:i:s")." ".$employee.": ".$text."\n";
    file_put_contents('tracktime.log',$string,FILE_APPEND);
    echo $string;
}