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
    
    
    
    class public_levelPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."level"');
            $field = new StringField('id');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, true);
            $field = new StringField('name');
            $this->dataset->AddField($field, false);
            $field = new IntegerField('rank');
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
            $grid->SearchControl = new SimpleSearch('public_levelssearch', $this->dataset,
                array('id', 'name'),
                array($this->RenderText('Id'), $this->RenderText('Name')),
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
            $this->AdvancedSearchControl = new AdvancedSearchControl('public_levelasearch', $this->dataset, $this->GetLocalizerCaptions(), $this->GetColumnVariableContainer(), $this->CreateLinkBuilder());
            $this->AdvancedSearchControl->setTimerInterval(1000);
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('id', $this->RenderText('Id')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('name', $this->RenderText('Name')));
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
            // View column for id field
            //
            $column = new TextViewColumn('id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for id field
            //
            $editor = new TextEdit('id_edit');
            $editor->SetSize(1);
            $editor->SetMaxLength(1);
            $editColumn = new CustomEditColumn('Id', 'id', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for id field
            //
            $editor = new TextEdit('id_edit');
            $editor->SetSize(1);
            $editor->SetMaxLength(1);
            $editColumn = new CustomEditColumn('Id', 'id', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for name field
            //
            $column = new TextViewColumn('name', 'Name', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for name field
            //
            $editor = new TextEdit('name_edit');
            $editor->SetSize(12);
            $editor->SetMaxLength(12);
            $editColumn = new CustomEditColumn('Name', 'name', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for name field
            //
            $editor = new TextEdit('name_edit');
            $editor->SetSize(12);
            $editor->SetMaxLength(12);
            $editColumn = new CustomEditColumn('Name', 'name', $editor, $this->dataset);
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
            // View column for id field
            //
            $column = new TextViewColumn('id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for name field
            //
            $column = new TextViewColumn('name', 'Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for id field
            //
            $editor = new TextEdit('id_edit');
            $editor->SetSize(1);
            $editor->SetMaxLength(1);
            $editColumn = new CustomEditColumn('Id', 'id', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for name field
            //
            $editor = new TextEdit('name_edit');
            $editor->SetSize(12);
            $editor->SetMaxLength(12);
            $editColumn = new CustomEditColumn('Name', 'name', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for id field
            //
            $editor = new TextEdit('id_edit');
            $editor->SetSize(1);
            $editor->SetMaxLength(1);
            $editColumn = new CustomEditColumn('Id', 'id', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for name field
            //
            $editor = new TextEdit('name_edit');
            $editor->SetSize(12);
            $editor->SetMaxLength(12);
            $editColumn = new CustomEditColumn('Name', 'name', $editor, $this->dataset);
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
            // View column for id field
            //
            $column = new TextViewColumn('id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for name field
            //
            $column = new TextViewColumn('name', 'Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for id field
            //
            $column = new TextViewColumn('id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for name field
            //
            $column = new TextViewColumn('name', 'Name', $this->dataset);
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
        public function GetModalGridEditingHandler() { return 'public_level_inline_edit'; }
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
        
        public function GetModalGridDeleteHandler() { return 'public_level_modal_delete'; }
        protected function GetEnableModalGridDelete() { return true; }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset, 'public_levelGrid');
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
        $Page = new public_levelPage("public.level.php", "public_level", GetCurrentUserGrantForDataSource("public.level"), 'UTF-8');
        $Page->SetShortCaption('Public.Level');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetCaption('Public.Level');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("public.level"));
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
	
