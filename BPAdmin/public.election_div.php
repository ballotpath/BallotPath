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
    
    
    
    class public_election_divPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."election_div"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, true);
            $field = new StringField('name');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new StringField('phys_addr_addr1');
            $this->dataset->AddField($field, false);
            $field = new StringField('phys_addr_addr2');
            $this->dataset->AddField($field, false);
            $field = new StringField('phys_addr_city');
            $this->dataset->AddField($field, false);
            $field = new StringField('phys_addr_state');
            $this->dataset->AddField($field, false);
            $field = new StringField('phys_addr_zip');
            $this->dataset->AddField($field, false);
            $field = new StringField('mail_addr_addr1');
            $this->dataset->AddField($field, false);
            $field = new StringField('mail_addr_addr2');
            $this->dataset->AddField($field, false);
            $field = new StringField('mail_addr_city');
            $this->dataset->AddField($field, false);
            $field = new StringField('mail_addr_state');
            $this->dataset->AddField($field, false);
            $field = new StringField('mail_addr_zip');
            $this->dataset->AddField($field, false);
            $field = new StringField('phone');
            $this->dataset->AddField($field, false);
            $field = new StringField('fax');
            $this->dataset->AddField($field, false);
            $field = new StringField('website');
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
            $grid->SearchControl = new SimpleSearch('public_election_divssearch', $this->dataset,
                array('name', 'phys_addr_addr1', 'phys_addr_addr2', 'phys_addr_city', 'phys_addr_state', 'phys_addr_zip', 'mail_addr_addr1', 'mail_addr_addr2', 'mail_addr_city', 'mail_addr_state', 'mail_addr_zip', 'phone', 'fax', 'website', 'notes'),
                array($this->RenderText('Election Division Name'), $this->RenderText('Physical Address Line 1'), $this->RenderText('Physical Address Line 2'), $this->RenderText('Physical Address City'), $this->RenderText('Physical Address State'), $this->RenderText('Physical Address Zip'), $this->RenderText('Mailing Address Line 1'), $this->RenderText('Mailing Address Line 2'), $this->RenderText('Mailing Address City'), $this->RenderText('Mailing Address State'), $this->RenderText('Mailing Address Zip'), $this->RenderText('Phone'), $this->RenderText('Fax'), $this->RenderText('Website'), $this->RenderText('Notes')),
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
            $this->AdvancedSearchControl = new AdvancedSearchControl('public_election_divasearch', $this->dataset, $this->GetLocalizerCaptions(), $this->GetColumnVariableContainer(), $this->CreateLinkBuilder());
            $this->AdvancedSearchControl->setTimerInterval(1000);
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('name', $this->RenderText('Election Division Name')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('phys_addr_addr1', $this->RenderText('Physical Address Line 1')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('phys_addr_addr2', $this->RenderText('Physical Address Line 2')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('phys_addr_city', $this->RenderText('Physical Address City')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('phys_addr_state', $this->RenderText('Physical Address State')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('phys_addr_zip', $this->RenderText('Physical Address Zip')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('mail_addr_addr1', $this->RenderText('Mailing Address Line 1')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('mail_addr_addr2', $this->RenderText('Mailing Address Line 2')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('mail_addr_city', $this->RenderText('Mailing Address City')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('mail_addr_state', $this->RenderText('Mailing Address State')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('mail_addr_zip', $this->RenderText('Mailing Address Zip')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('phone', $this->RenderText('Phone')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('fax', $this->RenderText('Fax')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('website', $this->RenderText('Website')));
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
            // View column for name field
            //
            $column = new TextViewColumn('name', 'Election Division Name', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for name field
            //
            $editor = new TextEdit('name_edit');
            $editor->SetSize(50);
            $editor->SetMaxLength(50);
            $editColumn = new CustomEditColumn('Election Division Name', 'name', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for name field
            //
            $editor = new TextEdit('name_edit');
            $editor->SetSize(50);
            $editor->SetMaxLength(50);
            $editColumn = new CustomEditColumn('Election Division Name', 'name', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for phys_addr_addr1 field
            //
            $column = new TextViewColumn('phys_addr_addr1', 'Physical Address Line 1', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for phys_addr_addr1 field
            //
            $editor = new TextEdit('phys_addr_addr1_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Physical Address Line 1', 'phys_addr_addr1', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for phys_addr_addr1 field
            //
            $editor = new TextEdit('phys_addr_addr1_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Physical Address Line 1', 'phys_addr_addr1', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for phys_addr_addr2 field
            //
            $column = new TextViewColumn('phys_addr_addr2', 'Physical Address Line 2', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for phys_addr_addr2 field
            //
            $editor = new TextEdit('phys_addr_addr2_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Physical Address Line 2', 'phys_addr_addr2', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for phys_addr_addr2 field
            //
            $editor = new TextEdit('phys_addr_addr2_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Physical Address Line 2', 'phys_addr_addr2', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for phys_addr_city field
            //
            $column = new TextViewColumn('phys_addr_city', 'Physical Address City', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for phys_addr_city field
            //
            $editor = new TextEdit('phys_addr_city_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Physical Address City', 'phys_addr_city', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for phys_addr_city field
            //
            $editor = new TextEdit('phys_addr_city_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Physical Address City', 'phys_addr_city', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for phys_addr_state field
            //
            $column = new TextViewColumn('phys_addr_state', 'Physical Address State', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for phys_addr_state field
            //
            $editor = new TextEdit('phys_addr_state_edit');
            $editor->SetSize(2);
            $editor->SetMaxLength(2);
            $editColumn = new CustomEditColumn('Physical Address State', 'phys_addr_state', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for phys_addr_state field
            //
            $editor = new TextEdit('phys_addr_state_edit');
            $editor->SetSize(2);
            $editor->SetMaxLength(2);
            $editColumn = new CustomEditColumn('Physical Address State', 'phys_addr_state', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for phys_addr_zip field
            //
            $column = new TextViewColumn('phys_addr_zip', 'Physical Address Zip', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for phys_addr_zip field
            //
            $editor = new TextEdit('phys_addr_zip_edit');
            $editor->SetSize(5);
            $editor->SetMaxLength(5);
            $editColumn = new CustomEditColumn('Physical Address Zip', 'phys_addr_zip', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for phys_addr_zip field
            //
            $editor = new TextEdit('phys_addr_zip_edit');
            $editor->SetSize(5);
            $editor->SetMaxLength(5);
            $editColumn = new CustomEditColumn('Physical Address Zip', 'phys_addr_zip', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for mail_addr_addr1 field
            //
            $column = new TextViewColumn('mail_addr_addr1', 'Mailing Address Line 1', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for mail_addr_addr1 field
            //
            $editor = new TextEdit('mail_addr_addr1_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Mailing Address Line 1', 'mail_addr_addr1', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for mail_addr_addr1 field
            //
            $editor = new TextEdit('mail_addr_addr1_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Mailing Address Line 1', 'mail_addr_addr1', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for mail_addr_addr2 field
            //
            $column = new TextViewColumn('mail_addr_addr2', 'Mailing Address Line 2', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for mail_addr_addr2 field
            //
            $editor = new TextEdit('mail_addr_addr2_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Mailing Address Line 2', 'mail_addr_addr2', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for mail_addr_addr2 field
            //
            $editor = new TextEdit('mail_addr_addr2_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Mailing Address Line 2', 'mail_addr_addr2', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for mail_addr_city field
            //
            $column = new TextViewColumn('mail_addr_city', 'Mailing Address City', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for mail_addr_city field
            //
            $editor = new TextEdit('mail_addr_city_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Mailing Address City', 'mail_addr_city', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for mail_addr_city field
            //
            $editor = new TextEdit('mail_addr_city_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Mailing Address City', 'mail_addr_city', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for mail_addr_state field
            //
            $column = new TextViewColumn('mail_addr_state', 'Mailing Address State', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for mail_addr_state field
            //
            $editor = new TextEdit('mail_addr_state_edit');
            $editor->SetSize(2);
            $editor->SetMaxLength(2);
            $editColumn = new CustomEditColumn('Mailing Address State', 'mail_addr_state', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for mail_addr_state field
            //
            $editor = new TextEdit('mail_addr_state_edit');
            $editor->SetSize(2);
            $editor->SetMaxLength(2);
            $editColumn = new CustomEditColumn('Mailing Address State', 'mail_addr_state', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for mail_addr_zip field
            //
            $column = new TextViewColumn('mail_addr_zip', 'Mailing Address Zip', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for mail_addr_zip field
            //
            $editor = new TextEdit('mail_addr_zip_edit');
            $editor->SetSize(5);
            $editor->SetMaxLength(5);
            $editColumn = new CustomEditColumn('Mailing Address Zip', 'mail_addr_zip', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for mail_addr_zip field
            //
            $editor = new TextEdit('mail_addr_zip_edit');
            $editor->SetSize(5);
            $editor->SetMaxLength(5);
            $editColumn = new CustomEditColumn('Mailing Address Zip', 'mail_addr_zip', $editor, $this->dataset);
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
            // View column for website field
            //
            $column = new TextViewColumn('website', 'Website', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('public_election_divGrid_website_handler_list');
            
            /* <inline edit column> */
            //
            // Edit column for website field
            //
            $editor = new TextAreaEdit('website_edit', 50, 8);
            $editColumn = new CustomEditColumn('Website', 'website', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for website field
            //
            $editor = new TextAreaEdit('website_edit', 50, 8);
            $editColumn = new CustomEditColumn('Website', 'website', $editor, $this->dataset);
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
            $column->SetFullTextWindowHandlerName('public_election_divGrid_notes_handler_list');
            
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
            // View column for name field
            //
            $column = new TextViewColumn('name', 'Election Division Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for phys_addr_addr1 field
            //
            $column = new TextViewColumn('phys_addr_addr1', 'Physical Address Line 1', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for phys_addr_addr2 field
            //
            $column = new TextViewColumn('phys_addr_addr2', 'Physical Address Line 2', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for phys_addr_city field
            //
            $column = new TextViewColumn('phys_addr_city', 'Physical Address City', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for phys_addr_state field
            //
            $column = new TextViewColumn('phys_addr_state', 'Physical Address State', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for phys_addr_zip field
            //
            $column = new TextViewColumn('phys_addr_zip', 'Physical Address Zip', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for mail_addr_addr1 field
            //
            $column = new TextViewColumn('mail_addr_addr1', 'Mailing Address Line 1', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for mail_addr_addr2 field
            //
            $column = new TextViewColumn('mail_addr_addr2', 'Mailing Address Line 2', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for mail_addr_city field
            //
            $column = new TextViewColumn('mail_addr_city', 'Mailing Address City', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for mail_addr_state field
            //
            $column = new TextViewColumn('mail_addr_state', 'Mailing Address State', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for mail_addr_zip field
            //
            $column = new TextViewColumn('mail_addr_zip', 'Mailing Address Zip', $this->dataset);
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
            // View column for website field
            //
            $column = new TextViewColumn('website', 'Website', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('public_election_divGrid_website_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for notes field
            //
            $column = new TextViewColumn('notes', 'Notes', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('public_election_divGrid_notes_handler_view');
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for name field
            //
            $editor = new TextEdit('name_edit');
            $editor->SetSize(50);
            $editor->SetMaxLength(50);
            $editColumn = new CustomEditColumn('Election Division Name', 'name', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for phys_addr_addr1 field
            //
            $editor = new TextEdit('phys_addr_addr1_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Physical Address Line 1', 'phys_addr_addr1', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for phys_addr_addr2 field
            //
            $editor = new TextEdit('phys_addr_addr2_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Physical Address Line 2', 'phys_addr_addr2', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for phys_addr_city field
            //
            $editor = new TextEdit('phys_addr_city_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Physical Address City', 'phys_addr_city', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for phys_addr_state field
            //
            $editor = new TextEdit('phys_addr_state_edit');
            $editor->SetSize(2);
            $editor->SetMaxLength(2);
            $editColumn = new CustomEditColumn('Physical Address State', 'phys_addr_state', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for phys_addr_zip field
            //
            $editor = new TextEdit('phys_addr_zip_edit');
            $editor->SetSize(5);
            $editor->SetMaxLength(5);
            $editColumn = new CustomEditColumn('Physical Address Zip', 'phys_addr_zip', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for mail_addr_addr1 field
            //
            $editor = new TextEdit('mail_addr_addr1_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Mailing Address Line 1', 'mail_addr_addr1', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for mail_addr_addr2 field
            //
            $editor = new TextEdit('mail_addr_addr2_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Mailing Address Line 2', 'mail_addr_addr2', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for mail_addr_city field
            //
            $editor = new TextEdit('mail_addr_city_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Mailing Address City', 'mail_addr_city', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for mail_addr_state field
            //
            $editor = new TextEdit('mail_addr_state_edit');
            $editor->SetSize(2);
            $editor->SetMaxLength(2);
            $editColumn = new CustomEditColumn('Mailing Address State', 'mail_addr_state', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for mail_addr_zip field
            //
            $editor = new TextEdit('mail_addr_zip_edit');
            $editor->SetSize(5);
            $editor->SetMaxLength(5);
            $editColumn = new CustomEditColumn('Mailing Address Zip', 'mail_addr_zip', $editor, $this->dataset);
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
            // Edit column for website field
            //
            $editor = new TextAreaEdit('website_edit', 50, 8);
            $editColumn = new CustomEditColumn('Website', 'website', $editor, $this->dataset);
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
            // Edit column for name field
            //
            $editor = new TextEdit('name_edit');
            $editor->SetSize(50);
            $editor->SetMaxLength(50);
            $editColumn = new CustomEditColumn('Election Division Name', 'name', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for phys_addr_addr1 field
            //
            $editor = new TextEdit('phys_addr_addr1_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Physical Address Line 1', 'phys_addr_addr1', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for phys_addr_addr2 field
            //
            $editor = new TextEdit('phys_addr_addr2_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Physical Address Line 2', 'phys_addr_addr2', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for phys_addr_city field
            //
            $editor = new TextEdit('phys_addr_city_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Physical Address City', 'phys_addr_city', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for phys_addr_state field
            //
            $editor = new TextEdit('phys_addr_state_edit');
            $editor->SetSize(2);
            $editor->SetMaxLength(2);
            $editColumn = new CustomEditColumn('Physical Address State', 'phys_addr_state', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for phys_addr_zip field
            //
            $editor = new TextEdit('phys_addr_zip_edit');
            $editor->SetSize(5);
            $editor->SetMaxLength(5);
            $editColumn = new CustomEditColumn('Physical Address Zip', 'phys_addr_zip', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for mail_addr_addr1 field
            //
            $editor = new TextEdit('mail_addr_addr1_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Mailing Address Line 1', 'mail_addr_addr1', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for mail_addr_addr2 field
            //
            $editor = new TextEdit('mail_addr_addr2_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Mailing Address Line 2', 'mail_addr_addr2', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for mail_addr_city field
            //
            $editor = new TextEdit('mail_addr_city_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Mailing Address City', 'mail_addr_city', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for mail_addr_state field
            //
            $editor = new TextEdit('mail_addr_state_edit');
            $editor->SetSize(2);
            $editor->SetMaxLength(2);
            $editColumn = new CustomEditColumn('Mailing Address State', 'mail_addr_state', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for mail_addr_zip field
            //
            $editor = new TextEdit('mail_addr_zip_edit');
            $editor->SetSize(5);
            $editor->SetMaxLength(5);
            $editColumn = new CustomEditColumn('Mailing Address Zip', 'mail_addr_zip', $editor, $this->dataset);
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
            // Edit column for website field
            //
            $editor = new TextAreaEdit('website_edit', 50, 8);
            $editColumn = new CustomEditColumn('Website', 'website', $editor, $this->dataset);
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
            // View column for name field
            //
            $column = new TextViewColumn('name', 'Election Division Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for phys_addr_addr1 field
            //
            $column = new TextViewColumn('phys_addr_addr1', 'Physical Address Line 1', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for phys_addr_addr2 field
            //
            $column = new TextViewColumn('phys_addr_addr2', 'Physical Address Line 2', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for phys_addr_city field
            //
            $column = new TextViewColumn('phys_addr_city', 'Physical Address City', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for phys_addr_state field
            //
            $column = new TextViewColumn('phys_addr_state', 'Physical Address State', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for phys_addr_zip field
            //
            $column = new TextViewColumn('phys_addr_zip', 'Physical Address Zip', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for mail_addr_addr1 field
            //
            $column = new TextViewColumn('mail_addr_addr1', 'Mailing Address Line 1', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for mail_addr_addr2 field
            //
            $column = new TextViewColumn('mail_addr_addr2', 'Mailing Address Line 2', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for mail_addr_city field
            //
            $column = new TextViewColumn('mail_addr_city', 'Mailing Address City', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for mail_addr_state field
            //
            $column = new TextViewColumn('mail_addr_state', 'Mailing Address State', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for mail_addr_zip field
            //
            $column = new TextViewColumn('mail_addr_zip', 'Mailing Address Zip', $this->dataset);
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
            // View column for website field
            //
            $column = new TextViewColumn('website', 'Website', $this->dataset);
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
            // View column for name field
            //
            $column = new TextViewColumn('name', 'Election Division Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for phys_addr_addr1 field
            //
            $column = new TextViewColumn('phys_addr_addr1', 'Physical Address Line 1', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for phys_addr_addr2 field
            //
            $column = new TextViewColumn('phys_addr_addr2', 'Physical Address Line 2', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for phys_addr_city field
            //
            $column = new TextViewColumn('phys_addr_city', 'Physical Address City', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for phys_addr_state field
            //
            $column = new TextViewColumn('phys_addr_state', 'Physical Address State', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for phys_addr_zip field
            //
            $column = new TextViewColumn('phys_addr_zip', 'Physical Address Zip', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for mail_addr_addr1 field
            //
            $column = new TextViewColumn('mail_addr_addr1', 'Mailing Address Line 1', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for mail_addr_addr2 field
            //
            $column = new TextViewColumn('mail_addr_addr2', 'Mailing Address Line 2', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for mail_addr_city field
            //
            $column = new TextViewColumn('mail_addr_city', 'Mailing Address City', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for mail_addr_state field
            //
            $column = new TextViewColumn('mail_addr_state', 'Mailing Address State', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for mail_addr_zip field
            //
            $column = new TextViewColumn('mail_addr_zip', 'Mailing Address Zip', $this->dataset);
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
            // View column for website field
            //
            $column = new TextViewColumn('website', 'Website', $this->dataset);
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
        public function GetModalGridEditingHandler() { return 'public_election_div_inline_edit'; }
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
        
        public function GetModalGridDeleteHandler() { return 'public_election_div_modal_delete'; }
        protected function GetEnableModalGridDelete() { return true; }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset, 'public_election_divGrid');
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
            // View column for website field
            //
            $column = new TextViewColumn('website', 'Website', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for website field
            //
            $editor = new TextAreaEdit('website_edit', 50, 8);
            $editColumn = new CustomEditColumn('Website', 'website', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for website field
            //
            $editor = new TextAreaEdit('website_edit', 50, 8);
            $editColumn = new CustomEditColumn('Website', 'website', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'public_election_divGrid_website_handler_list', $column);
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
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'public_election_divGrid_notes_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);//
            // View column for website field
            //
            $column = new TextViewColumn('website', 'Website', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'public_election_divGrid_website_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for notes field
            //
            $column = new TextViewColumn('notes', 'Notes', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'public_election_divGrid_notes_handler_view', $column);
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
        $Page = new public_election_divPage("public.election_div.php", "public_election_div", GetCurrentUserGrantForDataSource("public.election_div"), 'UTF-8');
        $Page->SetShortCaption('Election Divisions');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetCaption('Election Divisions');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("public.election_div"));
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
	
