<?php
include('../html/secr/login.php');
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                   ATTENTION!
 * If you see this message in your browser (Internet Explorer, Mozilla Firefox, Google Chrome, etc.)
 * this means that PHP is not properly installed on your web server. Please refer to the PHP manual
 * for more details: http://php.net/manual/install.php 
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */


    include_once dirname(__FILE__) . '/' . 'components/utils/check_utils.php';
    CheckPHPVersion();
    CheckTemplatesCacheFolderIsExistsAndWritable();


    include_once dirname(__FILE__) . '/' . 'phpgen_settings.php';
    include_once dirname(__FILE__) . '/' . 'database_engine/pgsql_engine.php';
    include_once dirname(__FILE__) . '/' . 'components/page.php';


    function GetConnectionOptions()
    {
        $result = GetGlobalConnectionOptions();
        $result['client_encoding'] = 'utf8';
        GetApplication()->GetUserAuthorizationStrategy()->ApplyIdentityToConnectionOptions($result);
        return $result;
    }

    
    
    // OnBeforePageExecute event handler
    
    
    
    class public_office_holderPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."office_holder"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, true);
            $field = new StringField('first_name');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new StringField('middle_name');
            $this->dataset->AddField($field, false);
            $field = new StringField('last_name');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new StringField('party_affiliation');
            $this->dataset->AddField($field, false);
            $field = new StringField('address1');
            $this->dataset->AddField($field, false);
            $field = new StringField('address2');
            $this->dataset->AddField($field, false);
            $field = new StringField('city');
            $this->dataset->AddField($field, false);
            $field = new StringField('state');
            $this->dataset->AddField($field, false);
            $field = new StringField('zip');
            $this->dataset->AddField($field, false);
            $field = new StringField('phone');
            $this->dataset->AddField($field, false);
            $field = new StringField('fax');
            $this->dataset->AddField($field, false);
            $field = new StringField('email_address');
            $this->dataset->AddField($field, false);
            $field = new StringField('website');
            $this->dataset->AddField($field, false);
            $field = new StringField('photo_link');
            $this->dataset->AddField($field, false);
            $field = new StringField('notes');
            $this->dataset->AddField($field, false);
        }
    
        protected function CreatePageNavigator()
        {
            $result = new CompositePageNavigator($this);
            
            $partitionNavigator = new PageNavigator('pnav', $this, $this->dataset);
            $partitionNavigator->SetRowsPerPage(20);
            $result->AddPageNavigator($partitionNavigator);
            
            return $result;
        }
    
        public function GetPageList()
        {
            $currentPageCaption = $this->GetShortCaption();
            $result = new PageList($this);
            if (GetCurrentUserGrantForDataSource('public.office_holder')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Office Holders'), 'public.office_holder.php', $this->RenderText('Office Holders'), $currentPageCaption == $this->RenderText('Office Holders')));
            if (GetCurrentUserGrantForDataSource('public.office')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Political Offices'), 'public.office.php', $this->RenderText('Political Offices'), $currentPageCaption == $this->RenderText('Political Offices')));
            if (GetCurrentUserGrantForDataSource('public.office_docs')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Political Office Filing Documents'), 'public.office_docs.php', $this->RenderText('Political Office Filing Documents'), $currentPageCaption == $this->RenderText('Political Office Filing Documents')));
            if (GetCurrentUserGrantForDataSource('public.election_div')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Election Divisions'), 'public.election_div.php', $this->RenderText('Election Divisions'), $currentPageCaption == $this->RenderText('Election Divisions')));
            if (GetCurrentUserGrantForDataSource('public.election_div_docs')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Election Division Filing Documents'), 'public.election_div_docs.php', $this->RenderText('Election Division Filing Documents'), $currentPageCaption == $this->RenderText('Election Division Filing Documents')));
            if (GetCurrentUserGrantForDataSource('public.district')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Political Districts'), 'public.district.php', $this->RenderText('Political Districts'), $currentPageCaption == $this->RenderText('Political Districts')));
            if (GetCurrentUserGrantForDataSource('public.office_position')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Political Office Positions'), 'public.office_position.php', $this->RenderText('Political Office Positions'), $currentPageCaption == $this->RenderText('Political Office Positions')));
            
            if ( HasAdminPage() && GetApplication()->HasAdminGrantForCurrentUser() )
              $result->AddPage(new PageLink($this->GetLocalizerCaptions()->GetMessageString('AdminPage'), 'phpgen_admin.php', $this->GetLocalizerCaptions()->GetMessageString('AdminPage'), false, true));
            return $result;
        }
    
        protected function CreateRssGenerator()
        {
            return null;
        }
    
        protected function CreateGridSearchControl(Grid $grid)
        {
            $grid->UseFilter = true;
            $grid->SearchControl = new SimpleSearch('public_office_holderssearch', $this->dataset,
                array('first_name', 'middle_name', 'last_name', 'party_affiliation', 'address1', 'address2', 'city', 'state', 'zip', 'phone', 'fax', 'email_address', 'website', 'photo_link', 'notes'),
                array($this->RenderText('First Name'), $this->RenderText('Middle Name'), $this->RenderText('Last Name'), $this->RenderText('Party Affiliation'), $this->RenderText('Address1'), $this->RenderText('Address2'), $this->RenderText('City'), $this->RenderText('State'), $this->RenderText('Zip'), $this->RenderText('Phone'), $this->RenderText('Fax'), $this->RenderText('Email Address'), $this->RenderText('Website'), $this->RenderText('Photo Link'), $this->RenderText('Notes')),
                array(
                    '=' => $this->GetLocalizerCaptions()->GetMessageString('equals'),
                    '<>' => $this->GetLocalizerCaptions()->GetMessageString('doesNotEquals'),
                    '<' => $this->GetLocalizerCaptions()->GetMessageString('isLessThan'),
                    '<=' => $this->GetLocalizerCaptions()->GetMessageString('isLessThanOrEqualsTo'),
                    '>' => $this->GetLocalizerCaptions()->GetMessageString('isGreaterThan'),
                    '>=' => $this->GetLocalizerCaptions()->GetMessageString('isGreaterThanOrEqualsTo'),
                    'ILIKE' => $this->GetLocalizerCaptions()->GetMessageString('Like'),

                    'STARTS' => $this->GetLocalizerCaptions()->GetMessageString('StartsWith'),
                    'ENDS' => $this->GetLocalizerCaptions()->GetMessageString('EndsWith'),
                    'CONTAINS' => $this->GetLocalizerCaptions()->GetMessageString('Contains')
                    ), $this->GetLocalizerCaptions(), $this, 'CONTAINS'
                );
        }
    
        protected function CreateGridAdvancedSearchControl(Grid $grid)
        {
            $this->AdvancedSearchControl = new AdvancedSearchControl('public_office_holderasearch', $this->dataset, $this->GetLocalizerCaptions(), $this->GetColumnVariableContainer(), $this->CreateLinkBuilder());
            $this->AdvancedSearchControl->setTimerInterval(1000);
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('first_name', $this->RenderText('First Name')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('middle_name', $this->RenderText('Middle Name')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('last_name', $this->RenderText('Last Name')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('party_affiliation', $this->RenderText('Party Affiliation')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('address1', $this->RenderText('Address1')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('address2', $this->RenderText('Address2')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('city', $this->RenderText('City')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('state', $this->RenderText('State')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('zip', $this->RenderText('Zip')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('phone', $this->RenderText('Phone')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('fax', $this->RenderText('Fax')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('email_address', $this->RenderText('Email Address')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('website', $this->RenderText('Website')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('photo_link', $this->RenderText('Photo Link')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('notes', $this->RenderText('Notes')));
        }
    
        protected function AddOperationsColumns(Grid $grid)
        {
            $actionsBandName = 'actions';
            $grid->AddBandToBegin($actionsBandName, $this->GetLocalizerCaptions()->GetMessageString('Actions'), true);
            if ($this->GetSecurityInfo()->HasViewGrant())
            {
                $column = new RowOperationByLinkColumn($this->GetLocalizerCaptions()->GetMessageString('View'), OPERATION_VIEW, $this->dataset);
                $grid->AddViewColumn($column, $actionsBandName);
            }
            if ($this->GetSecurityInfo()->HasEditGrant())
            {
                $column = new ModalDialogEditRowColumn(
                    $this->GetLocalizerCaptions()->GetMessageString('Edit'), $this->dataset,
                    $this->GetLocalizerCaptions()->GetMessageString('Edit'),
                    $this->GetModalGridEditingHandler());
                $grid->AddViewColumn($column, $actionsBandName);
                $column->OnShow->AddListener('ShowEditButtonHandler', $this);
            }
            if ($this->GetSecurityInfo()->HasDeleteGrant())
            {
                $column = new RowOperationByLinkColumn($this->GetLocalizerCaptions()->GetMessageString('Delete'), OPERATION_DELETE, $this->dataset);
                $grid->AddViewColumn($column, $actionsBandName);
                $column->OnShow->AddListener('ShowDeleteButtonHandler', $this);
            $column->SetAdditionalAttribute("data-modal-delete", "true");
            $column->SetAdditionalAttribute("data-delete-handler-name", $this->GetModalGridDeleteHandler());
            }
            if ($this->GetSecurityInfo()->HasAddGrant())
            {
                $column = new RowOperationByLinkColumn($this->GetLocalizerCaptions()->GetMessageString('Copy'), OPERATION_COPY, $this->dataset);
                $grid->AddViewColumn($column, $actionsBandName);
            }
        }
    
        protected function AddFieldColumns(Grid $grid)
        {
            //
            // View column for first_name field
            //
            $column = new TextViewColumn('first_name', 'First Name', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for first_name field
            //
            $editor = new TextEdit('first_name_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('First Name', 'first_name', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for first_name field
            //
            $editor = new TextEdit('first_name_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('First Name', 'first_name', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for middle_name field
            //
            $column = new TextViewColumn('middle_name', 'Middle Name', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for middle_name field
            //
            $editor = new TextEdit('middle_name_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Middle Name', 'middle_name', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for middle_name field
            //
            $editor = new TextEdit('middle_name_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Middle Name', 'middle_name', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for last_name field
            //
            $column = new TextViewColumn('last_name', 'Last Name', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for last_name field
            //
            $editor = new TextEdit('last_name_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Last Name', 'last_name', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for last_name field
            //
            $editor = new TextEdit('last_name_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Last Name', 'last_name', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for party_affiliation field
            //
            $column = new TextViewColumn('party_affiliation', 'Party Affiliation', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for party_affiliation field
            //
            $editor = new TextEdit('party_affiliation_edit');
            $editor->SetSize(1);
            $editor->SetMaxLength(1);
            $editColumn = new CustomEditColumn('Party Affiliation', 'party_affiliation', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for party_affiliation field
            //
            $editor = new TextEdit('party_affiliation_edit');
            $editor->SetSize(1);
            $editor->SetMaxLength(1);
            $editColumn = new CustomEditColumn('Party Affiliation', 'party_affiliation', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            

            //
            // View column for address1 field
            //
            $column = new TextViewColumn('address1', 'Address1', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for address1 field
            //
            $editor = new TextEdit('address1_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Address1', 'address1', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for address1 field
            //
            $editor = new TextEdit('address1_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Address1', 'address1', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for address2 field
            //
            $column = new TextViewColumn('address2', 'Address2', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for address2 field
            //
            $editor = new TextEdit('address2_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Address2', 'address2', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for address2 field
            //
            $editor = new TextEdit('address2_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Address2', 'address2', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for city field
            //
            $column = new TextViewColumn('city', 'City', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for city field
            //
            $editor = new TextEdit('city_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('City', 'city', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for city field
            //
            $editor = new TextEdit('city_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('City', 'city', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for state field
            //
            $column = new TextViewColumn('state', 'State', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for state field
            //
            $editor = new TextEdit('state_edit');
            $editor->SetSize(2);
            $editor->SetMaxLength(2);
            $editColumn = new CustomEditColumn('State', 'state', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for state field
            //
            $editor = new TextEdit('state_edit');
            $editor->SetSize(2);
            $editor->SetMaxLength(2);
            $editColumn = new CustomEditColumn('State', 'state', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for zip field
            //
            $column = new TextViewColumn('zip', 'Zip', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for zip field
            //
            $editor = new TextEdit('zip_edit');
            $editor->SetSize(5);
            $editor->SetMaxLength(5);
            $editColumn = new CustomEditColumn('Zip', 'zip', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for zip field
            //
            $editor = new TextEdit('zip_edit');
            $editor->SetSize(5);
            $editor->SetMaxLength(5);
            $editColumn = new CustomEditColumn('Zip', 'zip', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for phone field
            //
            $column = new TextViewColumn('phone', 'Phone', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for phone field
            //
            $editor = new TextEdit('phone_edit');
            $editor->SetSize(15);
            $editor->SetMaxLength(15);
            $editColumn = new CustomEditColumn('Phone', 'phone', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for phone field
            //
            $editor = new TextEdit('phone_edit');
            $editor->SetSize(15);
            $editor->SetMaxLength(15);
            $editColumn = new CustomEditColumn('Phone', 'phone', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for fax field
            //
            $column = new TextViewColumn('fax', 'Fax', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for fax field
            //
            $editor = new TextEdit('fax_edit');
            $editor->SetSize(15);
            $editor->SetMaxLength(15);
            $editColumn = new CustomEditColumn('Fax', 'fax', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for fax field
            //
            $editor = new TextEdit('fax_edit');
            $editor->SetSize(15);
            $editor->SetMaxLength(15);
            $editColumn = new CustomEditColumn('Fax', 'fax', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for email_address field
            //
            $column = new TextViewColumn('email_address', 'Email Address', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for email_address field
            //
            $editor = new TextEdit('email_address_edit');
            $editor->SetSize(30);
            $editor->SetMaxLength(30);
            $editColumn = new CustomEditColumn('Email Address', 'email_address', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for email_address field
            //
            $editor = new TextEdit('email_address_edit');
            $editor->SetSize(30);
            $editor->SetMaxLength(30);
            $editColumn = new CustomEditColumn('Email Address', 'email_address', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for website field
            //
            $column = new TextViewColumn('website', 'Website', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for website field
            //
            $editor = new TextEdit('website_edit');
            $editor->SetSize(50);
            $editor->SetMaxLength(50);
            $editColumn = new CustomEditColumn('Website', 'website', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for website field
            //
            $editor = new TextEdit('website_edit');
            $editor->SetSize(50);
            $editor->SetMaxLength(50);
            $editColumn = new CustomEditColumn('Website', 'website', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for photo_link field
            //
            $column = new TextViewColumn('photo_link', 'Photo Link', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('public_office_holderGrid_photo_link_handler_list');
            
            /* <inline edit column> */
            //
            // Edit column for photo_link field
            //
            $editor = new TextAreaEdit('photo_link_edit', 50, 8);
            $editColumn = new CustomEditColumn('Photo Link', 'photo_link', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for photo_link field
            //
            $editor = new TextAreaEdit('photo_link_edit', 50, 8);
            $editColumn = new CustomEditColumn('Photo Link', 'photo_link', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for notes field
            //
            $column = new TextViewColumn('notes', 'Notes', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('public_office_holderGrid_notes_handler_list');
            
            /* <inline edit column> */
            //
            // Edit column for notes field
            //
            $editor = new TextAreaEdit('notes_edit', 50, 8);
            $editColumn = new CustomEditColumn('Notes', 'notes', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for notes field
            //
            $editor = new TextAreaEdit('notes_edit', 50, 8);
            $editColumn = new CustomEditColumn('Notes', 'notes', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for first_name field
            //
            $column = new TextViewColumn('first_name', 'First Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for middle_name field
            //
            $column = new TextViewColumn('middle_name', 'Middle Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for last_name field
            //
            $column = new TextViewColumn('last_name', 'Last Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for party_affiliation field
            //
            $column = new TextViewColumn('party_affiliation', 'Party Affiliation', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for address1 field
            //
            $column = new TextViewColumn('address1', 'Address1', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for address2 field
            //
            $column = new TextViewColumn('address2', 'Address2', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for city field
            //
            $column = new TextViewColumn('city', 'City', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for state field
            //
            $column = new TextViewColumn('state', 'State', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for zip field
            //
            $column = new TextViewColumn('zip', 'Zip', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for phone field
            //
            $column = new TextViewColumn('phone', 'Phone', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for fax field
            //
            $column = new TextViewColumn('fax', 'Fax', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for email_address field
            //
            $column = new TextViewColumn('email_address', 'Email Address', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for website field
            //
            $column = new TextViewColumn('website', 'Website', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for photo_link field
            //
            $column = new TextViewColumn('photo_link', 'Photo Link', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('public_office_holderGrid_photo_link_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for notes field
            //
            $column = new TextViewColumn('notes', 'Notes', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('public_office_holderGrid_notes_handler_view');
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for first_name field
            //
            $editor = new TextEdit('first_name_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('First Name', 'first_name', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for middle_name field
            //
            $editor = new TextEdit('middle_name_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Middle Name', 'middle_name', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for last_name field
            //
            $editor = new TextEdit('last_name_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Last Name', 'last_name', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for party_affiliation field
            //
            $editor = new TextEdit('party_affiliation_edit');
            $editor->SetSize(1);
            $editor->SetMaxLength(1);
            $editColumn = new CustomEditColumn('Party Affiliation', 'party_affiliation', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for address1 field
            //
            $editor = new TextEdit('address1_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Address1', 'address1', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for address2 field
            //
            $editor = new TextEdit('address2_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Address2', 'address2', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for city field
            //
            $editor = new TextEdit('city_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('City', 'city', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for state field
            //
            $editor = new TextEdit('state_edit');
            $editor->SetSize(2);
            $editor->SetMaxLength(2);
            $editColumn = new CustomEditColumn('State', 'state', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for zip field
            //
            $editor = new TextEdit('zip_edit');
            $editor->SetSize(5);
            $editor->SetMaxLength(5);
            $editColumn = new CustomEditColumn('Zip', 'zip', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for phone field
            //
            $editor = new TextEdit('phone_edit');
            $editor->SetSize(15);
            $editor->SetMaxLength(15);
            $editColumn = new CustomEditColumn('Phone', 'phone', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for fax field
            //
            $editor = new TextEdit('fax_edit');
            $editor->SetSize(15);
            $editor->SetMaxLength(15);
            $editColumn = new CustomEditColumn('Fax', 'fax', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for email_address field
            //
            $editor = new TextEdit('email_address_edit');
            $editor->SetSize(30);
            $editor->SetMaxLength(30);
            $editColumn = new CustomEditColumn('Email Address', 'email_address', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for website field
            //
            $editor = new TextEdit('website_edit');
            $editor->SetSize(50);
            $editor->SetMaxLength(50);
            $editColumn = new CustomEditColumn('Website', 'website', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for photo_link field
            //
            $editor = new TextAreaEdit('photo_link_edit', 50, 8);
            $editColumn = new CustomEditColumn('Photo Link', 'photo_link', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for notes field
            //
            $editor = new TextAreaEdit('notes_edit', 50, 8);
            $editColumn = new CustomEditColumn('Notes', 'notes', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for first_name field
            //
            $editor = new TextEdit('first_name_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('First Name', 'first_name', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for middle_name field
            //
            $editor = new TextEdit('middle_name_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Middle Name', 'middle_name', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for last_name field
            //
            $editor = new TextEdit('last_name_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Last Name', 'last_name', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for party_affiliation field
            //
            $editor = new TextEdit('party_affiliation_edit');
            $editor->SetSize(1);
            $editor->SetMaxLength(1);
            $editColumn = new CustomEditColumn('Party Affiliation', 'party_affiliation', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for address1 field
            //
            $editor = new TextEdit('address1_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Address1', 'address1', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for address2 field
            //
            $editor = new TextEdit('address2_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Address2', 'address2', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for city field
            //
            $editor = new TextEdit('city_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('City', 'city', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for state field
            //
            $editor = new TextEdit('state_edit');
            $editor->SetSize(2);
            $editor->SetMaxLength(2);
            $editColumn = new CustomEditColumn('State', 'state', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for zip field
            //
            $editor = new TextEdit('zip_edit');
            $editor->SetSize(5);
            $editor->SetMaxLength(5);
            $editColumn = new CustomEditColumn('Zip', 'zip', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for phone field
            //
            $editor = new TextEdit('phone_edit');
            $editor->SetSize(15);
            $editor->SetMaxLength(15);
            $editColumn = new CustomEditColumn('Phone', 'phone', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for fax field
            //
            $editor = new TextEdit('fax_edit');
            $editor->SetSize(15);
            $editor->SetMaxLength(15);
            $editColumn = new CustomEditColumn('Fax', 'fax', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for email_address field
            //
            $editor = new TextEdit('email_address_edit');
            $editor->SetSize(30);
            $editor->SetMaxLength(30);
            $editColumn = new CustomEditColumn('Email Address', 'email_address', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for website field
            //
            $editor = new TextEdit('website_edit');
            $editor->SetSize(50);
            $editor->SetMaxLength(50);
            $editColumn = new CustomEditColumn('Website', 'website', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for photo_link field
            //
            $editor = new TextAreaEdit('photo_link_edit', 50, 8);
            $editColumn = new CustomEditColumn('Photo Link', 'photo_link', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for notes field
            //
            $editor = new TextAreaEdit('notes_edit', 50, 8);
            $editColumn = new CustomEditColumn('Notes', 'notes', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            if ($this->GetSecurityInfo()->HasAddGrant())
            {
                $grid->SetShowAddButton(true);
                $grid->SetShowInlineAddButton(false);
            }
            else
            {
                $grid->SetShowInlineAddButton(false);
                $grid->SetShowAddButton(false);
            }
        }
    
        protected function AddPrintColumns(Grid $grid)
        {
            //
            // View column for first_name field
            //
            $column = new TextViewColumn('first_name', 'First Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for middle_name field
            //
            $column = new TextViewColumn('middle_name', 'Middle Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for last_name field
            //
            $column = new TextViewColumn('last_name', 'Last Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for party_affiliation field
            //
            $column = new TextViewColumn('party_affiliation', 'Party Affiliation', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for address1 field
            //
            $column = new TextViewColumn('address1', 'Address1', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for address2 field
            //
            $column = new TextViewColumn('address2', 'Address2', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for city field
            //
            $column = new TextViewColumn('city', 'City', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for state field
            //
            $column = new TextViewColumn('state', 'State', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for zip field
            //
            $column = new TextViewColumn('zip', 'Zip', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for phone field
            //
            $column = new TextViewColumn('phone', 'Phone', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for fax field
            //
            $column = new TextViewColumn('fax', 'Fax', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for email_address field
            //
            $column = new TextViewColumn('email_address', 'Email Address', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for website field
            //
            $column = new TextViewColumn('website', 'Website', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for photo_link field
            //
            $column = new TextViewColumn('photo_link', 'Photo Link', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for notes field
            //
            $column = new TextViewColumn('notes', 'Notes', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for first_name field
            //
            $column = new TextViewColumn('first_name', 'First Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for middle_name field
            //
            $column = new TextViewColumn('middle_name', 'Middle Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for last_name field
            //
            $column = new TextViewColumn('last_name', 'Last Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for party_affiliation field
            //
            $column = new TextViewColumn('party_affiliation', 'Party Affiliation', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for address1 field
            //
            $column = new TextViewColumn('address1', 'Address1', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for address2 field
            //
            $column = new TextViewColumn('address2', 'Address2', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for city field
            //
            $column = new TextViewColumn('city', 'City', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for state field
            //
            $column = new TextViewColumn('state', 'State', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for zip field
            //
            $column = new TextViewColumn('zip', 'Zip', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for phone field
            //
            $column = new TextViewColumn('phone', 'Phone', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for fax field
            //
            $column = new TextViewColumn('fax', 'Fax', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for email_address field
            //
            $column = new TextViewColumn('email_address', 'Email Address', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for website field
            //
            $column = new TextViewColumn('website', 'Website', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for photo_link field
            //
            $column = new TextViewColumn('photo_link', 'Photo Link', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for notes field
            //
            $column = new TextViewColumn('notes', 'Notes', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
        }
    
        public function GetPageDirection()
        {
            return null;
        }
    
        protected function ApplyCommonColumnEditProperties(CustomEditColumn $column)
        {
            $column->SetShowSetToNullCheckBox(false);
    		$column->SetVariableContainer($this->GetColumnVariableContainer());
        }
    
        function GetCustomClientScript()
        {
            return ;
        }
        
        function GetOnPageLoadedClientScript()
        {
            return ;
        }
        public function GetModalGridEditingHandler() { return 'public_office_holder_inline_edit'; }
        protected function GetEnableModalGridEditing() { return true; }
        public function ShowEditButtonHandler(&$show)
        {
            if ($this->GetRecordPermission() != null)
                $show = $this->GetRecordPermission()->HasEditGrant($this->GetDataset());
        }
        public function ShowDeleteButtonHandler(&$show)
        {
            if ($this->GetRecordPermission() != null)
                $show = $this->GetRecordPermission()->HasDeleteGrant($this->GetDataset());
        }
        
        public function GetModalGridDeleteHandler() { return 'public_office_holder_modal_delete'; }
        protected function GetEnableModalGridDelete() { return true; }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset, 'public_office_holderGrid');
            if ($this->GetSecurityInfo()->HasDeleteGrant())
               $result->SetAllowDeleteSelected(false);
            else
               $result->SetAllowDeleteSelected(false);   
            
            ApplyCommonPageSettings($this, $result);
            
            $result->SetUseImagesForActions(false);
            $result->SetUseFixedHeader(false);
            
            $result->SetShowLineNumbers(false);
            $result->SetUseModalInserting(true);
            
            $result->SetHighlightRowAtHover(false);
            $result->SetWidth('');
            $this->CreateGridSearchControl($result);
            $this->CreateGridAdvancedSearchControl($result);
            $this->AddOperationsColumns($result);
            $this->AddFieldColumns($result);
            $this->AddSingleRecordViewColumns($result);
            $this->AddEditColumns($result);
            $this->AddInsertColumns($result);
            $this->AddPrintColumns($result);
            $this->AddExportColumns($result);
    
            $this->SetShowPageList(true);
            $this->SetHidePageListByDefault(false);
            $this->SetExportToExcelAvailable(false);
            $this->SetExportToWordAvailable(false);
            $this->SetExportToXmlAvailable(false);
            $this->SetExportToCsvAvailable(false);
            $this->SetExportToPdfAvailable(false);
            $this->SetPrinterFriendlyAvailable(false);
            $this->SetSimpleSearchAvailable(true);
            $this->SetAdvancedSearchAvailable(false);
            $this->SetFilterRowAvailable(false);
            $this->SetVisualEffectsEnabled(false);
            $this->SetShowTopPageNavigator(true);
            $this->SetShowBottomPageNavigator(true);
    
            //
            // Http Handlers
            //
            //
            // View column for photo_link field
            //
            $column = new TextViewColumn('photo_link', 'Photo Link', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for photo_link field
            //
            $editor = new TextAreaEdit('photo_link_edit', 50, 8);
            $editColumn = new CustomEditColumn('Photo Link', 'photo_link', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for photo_link field
            //
            $editor = new TextAreaEdit('photo_link_edit', 50, 8);
            $editColumn = new CustomEditColumn('Photo Link', 'photo_link', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'public_office_holderGrid_photo_link_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for notes field
            //
            $column = new TextViewColumn('notes', 'Notes', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for notes field
            //
            $editor = new TextAreaEdit('notes_edit', 50, 8);
            $editColumn = new CustomEditColumn('Notes', 'notes', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for notes field
            //
            $editor = new TextAreaEdit('notes_edit', 50, 8);
            $editColumn = new CustomEditColumn('Notes', 'notes', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'public_office_holderGrid_notes_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);//
            // View column for photo_link field
            //
            $column = new TextViewColumn('photo_link', 'Photo Link', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'public_office_holderGrid_photo_link_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for notes field
            //
            $column = new TextViewColumn('notes', 'Notes', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'public_office_holderGrid_notes_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            return $result;
        }
        
        public function OpenAdvancedSearchByDefault()
        {
            return false;
        }
    
        protected function DoGetGridHeader()
        {
            return '';
        }
    }



    try
    {
        $Page = new public_office_holderPage("public.office_holder.php", "public_office_holder", GetCurrentUserGrantForDataSource("public.office_holder"), 'UTF-8');
        $Page->SetShortCaption('Office Holders');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetCaption('Office Holders');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("public.office_holder"));
        GetApplication()->SetEnableLessRunTimeCompile(GetEnableLessFilesRunTimeCompilation());
        GetApplication()->SetCanUserChangeOwnPassword(
            !function_exists('CanUserChangeOwnPassword') || CanUserChangeOwnPassword());
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e->getMessage());
    }
	
