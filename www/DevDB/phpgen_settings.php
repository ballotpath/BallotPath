<?php

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
  'username' => 'postgres',
  'password' => 'Democracy!',
  'database' => 'DevDB'
);
}

function HasAdminPage()
{
    return false;
}

function GetPageInfos()
{
    $result = array();
    $result[] = array('caption' => 'Public.District', 'short_caption' => 'Public.District', 'filename' => 'public.district.php', 'name' => 'public.district');
    $result[] = array('caption' => 'Public.Election Div', 'short_caption' => 'Public.Election Div', 'filename' => 'public.election_div.php', 'name' => 'public.election_div');
    $result[] = array('caption' => 'Public.Election Div Docs', 'short_caption' => 'Public.Election Div Docs', 'filename' => 'public.election_div_docs.php', 'name' => 'public.election_div_docs');
    $result[] = array('caption' => 'Public.Level', 'short_caption' => 'Public.Level', 'filename' => 'public.level.php', 'name' => 'public.level');
    $result[] = array('caption' => 'Public.Office', 'short_caption' => 'Public.Office', 'filename' => 'public.office.php', 'name' => 'public.office');
    $result[] = array('caption' => 'Public.Office Docs', 'short_caption' => 'Public.Office Docs', 'filename' => 'public.office_docs.php', 'name' => 'public.office_docs');
    $result[] = array('caption' => 'Public.Office Holder', 'short_caption' => 'Public.Office Holder', 'filename' => 'public.office_holder.php', 'name' => 'public.office_holder');
    $result[] = array('caption' => 'Public.Office Position', 'short_caption' => 'Public.Office Position', 'filename' => 'public.office_position.php', 'name' => 'public.office_position');
    $result[] = array('caption' => 'OfficeHolderIDs', 'short_caption' => 'OfficeHolderIDs', 'filename' => 'officeHolderIDs.php', 'name' => 'officeHolderIDs');
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
    $page->SetShowUserAuthBar(true);
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