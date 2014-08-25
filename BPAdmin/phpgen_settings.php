<?php
include('../html/secr/login.php');

//  define('SHOW_VARIABLES', 1);
//  define('DEBUG_LEVEL', 1);

//  error_reporting(E_ALL ^ E_NOTICE);
//  ini_set('display_errors', 'On');

set_include_path('.' . PATH_SEPARATOR . get_include_path());


include_once dirname(__FILE__) . '/' . 'components/utils/system_utils.php';

//  SystemUtils::DisableMagicQuotesRuntime();

SystemUtils::SetTimeZoneIfNeed('America/Los_Angeles');

function GetGlobalConnectionOptions()
{
    return array(
  'server' => 'localhost',
  'port' => '5432',
  'username' => 'BallotPath',
  'password' => 'Democracy!',
  'database' => 'BallotPath'
);
}

function HasAdminPage()
{
    return false;
}

function GetPageInfos()
{
    $result = array();
    $result[] = array('caption' => 'Office Holders', 'short_caption' => 'Office Holders', 'filename' => 'public.office_holder.php', 'name' => 'public.office_holder');
    $result[] = array('caption' => 'Political Offices', 'short_caption' => 'Political Offices', 'filename' => 'public.office.php', 'name' => 'public.office');
    $result[] = array('caption' => 'Political Office Filing Documents', 'short_caption' => 'Political Office Filing Documents', 'filename' => 'public.office_docs.php', 'name' => 'public.office_docs');
    $result[] = array('caption' => 'Election Divisions', 'short_caption' => 'Election Divisions', 'filename' => 'public.election_div.php', 'name' => 'public.election_div');
    $result[] = array('caption' => 'Election Division Filing Documents', 'short_caption' => 'Election Division Filing Documents', 'filename' => 'public.election_div_docs.php', 'name' => 'public.election_div_docs');
    $result[] = array('caption' => 'Political Districts', 'short_caption' => 'Political Districts', 'filename' => 'public.district.php', 'name' => 'public.district');
    $result[] = array('caption' => 'Political Office Positions', 'short_caption' => 'Political Office Positions', 'filename' => 'public.office_position.php', 'name' => 'public.office_position');
    return $result;
}

function GetPagesHeader()
{
    return
    '';
}

function GetPagesFooter()
{
    return
        ''; 
    }

function ApplyCommonPageSettings(Page $page, Grid $grid)
{
    $page->SetShowUserAuthBar(false);
    $grid->BeforeUpdateRecord->AddListener('Global_BeforeUpdateHandler');
    $grid->BeforeDeleteRecord->AddListener('Global_BeforeDeleteHandler');
    $grid->BeforeInsertRecord->AddListener('Global_BeforeInsertHandler');
}

/*
  Default code page: 1252
*/
function GetAnsiEncoding() { return 'windows-1252'; }

function Global_BeforeUpdateHandler($page, $rowData, &$cancel, &$message, $tableName)
{

}

function Global_BeforeDeleteHandler($page, $rowData, &$cancel, &$message, $tableName)
{

}

function Global_BeforeInsertHandler($page, $rowData, &$cancel, &$message, $tableName)
{

}

function GetDefaultDateFormat()
{
    return 'Y-m-d';
}

function GetFirstDayOfWeek()
{
    return 0;
}

function GetEnableLessFilesRunTimeCompilation()
{
    return false;
}



?>