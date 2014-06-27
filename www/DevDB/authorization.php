<?php

require_once 'components/page.php';
require_once 'components/security/datasource_security_info.php';
require_once 'components/security/security_info.php';
require_once 'components/security/hardcoded_auth.php';
require_once 'components/security/user_grants_manager.php';

$users = array('BPAdmin' => 'Democracy!');

$usersIds = array('BPAdmin' => -1);

$dataSourceRecordPermissions = array();

$grants = array('guest' => 
        array()
    ,
    'defaultUser' => 
        array('public.district' => new DataSourceSecurityInfo(false, false, false, false),
        'public.election_div' => new DataSourceSecurityInfo(false, false, false, false),
        'public.election_div_docs' => new DataSourceSecurityInfo(false, false, false, false),
        'public.level' => new DataSourceSecurityInfo(false, false, false, false),
        'public.office' => new DataSourceSecurityInfo(false, false, false, false),
        'public.office_docs' => new DataSourceSecurityInfo(false, false, false, false),
        'public.office_holder' => new DataSourceSecurityInfo(false, false, false, false),
        'public.office_position' => new DataSourceSecurityInfo(false, false, false, false),
        'Query01' => new DataSourceSecurityInfo(false, false, false, false),
        'officeHolderIDs' => new DataSourceSecurityInfo(false, false, false, false))
    ,
    'guest' => 
        array('public.district' => new DataSourceSecurityInfo(false, false, false, false),
        'public.election_div' => new DataSourceSecurityInfo(false, false, false, false),
        'public.election_div_docs' => new DataSourceSecurityInfo(false, false, false, false),
        'public.level' => new DataSourceSecurityInfo(false, false, false, false),
        'public.office' => new DataSourceSecurityInfo(false, false, false, false),
        'public.office_docs' => new DataSourceSecurityInfo(false, false, false, false),
        'public.office_holder' => new DataSourceSecurityInfo(false, false, false, false),
        'public.office_position' => new DataSourceSecurityInfo(false, false, false, false),
        'Query01' => new DataSourceSecurityInfo(false, false, false, false),
        'officeHolderIDs' => new DataSourceSecurityInfo(false, false, false, false))
    ,
    'BPAdmin' => 
        array('public.district' => new DataSourceSecurityInfo(false, false, false, false),
        'public.election_div' => new DataSourceSecurityInfo(false, false, false, false),
        'public.election_div_docs' => new DataSourceSecurityInfo(false, false, false, false),
        'public.level' => new DataSourceSecurityInfo(false, false, false, false),
        'public.office' => new DataSourceSecurityInfo(false, false, false, false),
        'public.office_docs' => new DataSourceSecurityInfo(false, false, false, false),
        'public.office_holder' => new DataSourceSecurityInfo(false, false, false, false),
        'public.office_position' => new DataSourceSecurityInfo(false, false, false, false),
        'Query01' => new DataSourceSecurityInfo(false, false, false, false),
        'officeHolderIDs' => new DataSourceSecurityInfo(false, false, false, false))
    );

$appGrants = array('guest' => new DataSourceSecurityInfo(false, false, false, false),
    'defaultUser' => new DataSourceSecurityInfo(true, false, false, false),
    'guest' => new DataSourceSecurityInfo(false, false, false, false),
    'BPAdmin' => new AdminDataSourceSecurityInfo());

$tableCaptions = array('public.district' => 'Public.District',
'public.election_div' => 'Public.Election Div',
'public.election_div_docs' => 'Public.Election Div Docs',
'public.level' => 'Public.Level',
'public.office' => 'Public.Office',
'public.office_docs' => 'Public.Office Docs',
'public.office_holder' => 'Public.Office Holder',
'public.office_position' => 'Public.Office Position',
'Query01' => 'Query01',
'officeHolderIDs' => 'OfficeHolderIDs');

function SetUpUserAuthorization()
{
    global $usersIds;
    global $grants;
    global $appGrants;
    global $dataSourceRecordPermissions;
    $userAuthorizationStrategy = new HardCodedUserAuthorization(new HardCodedUserGrantsManager($grants, $appGrants), $usersIds);
    GetApplication()->SetUserAuthorizationStrategy($userAuthorizationStrategy);

GetApplication()->SetDataSourceRecordPermissionRetrieveStrategy(
    new HardCodedDataSourceRecordPermissionRetrieveStrategy($dataSourceRecordPermissions));
}

function GetIdentityCheckStrategy()
{
    global $users;
    return new SimpleIdentityCheckStrategy($users, '');
}

?>