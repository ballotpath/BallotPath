<?php
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
    include_once dirname(__FILE__) . '/' . 'authorization.php';

    function GetConnectionOptions()
    {
        $result = GetGlobalConnectionOptions();
        $result['client_encoding'] = 'utf8';
        GetApplication()->GetUserAuthorizationStrategy()->ApplyIdentityToConnectionOptions($result);
        return $result;
    }

    
    
    // OnBeforePageExecute event handler
    
    
    
    class public_office_positionPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."office_position"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, true);
            $field = new IntegerField('district_id');
            $this->dataset->AddField($field, false);
            $field = new IntegerField('office_id');
            $this->dataset->AddField($field, false);
            $field = new IntegerField('office_holder_id');
            $this->dataset->AddField($field, false);
            $field = new StringField('position_name');
            $this->dataset->AddField($field, false);
            $field = new DateField('term_start');
            $this->dataset->AddField($field, false);
            $field = new DateField('term_end');
            $this->dataset->AddField($field, false);
            $field = new DateField('filing_deadline');
            $this->dataset->AddField($field, false);
            $field = new DateField('next_election');
            $this->dataset->AddField($field, false);
            $field = new StringField('notes');
            $this->dataset->AddField($field, false);
            $this->dataset->AddLookupField('district_id', '(SELECT d.id, concat(d.state, \' - \', d.name) as districtName
            FROM district d
            ORDER BY districtName)', new IntegerField('id'), new StringField('districtname', 'district_id_districtname', 'district_id_districtname_Query01'), 'district_id_districtname_Query01');
            $this->dataset->AddLookupField('office_id', 'public.office', new IntegerField('id'), new StringField('title', 'office_id_title', 'office_id_title_public_office'), 'office_id_title_public_office');
            $this->dataset->AddLookupField('office_holder_id', '(SELECT id, CONCAT(oh.last_name, \', \', oh.first_name, \' \', oh.middle_name)
            FROM office_holder oh
            ORDER BY oh.last_name, oh.first_name)', new IntegerField('id'), new StringField('concat', 'office_holder_id_concat', 'office_holder_id_concat_officeHolderIDs'), 'office_holder_id_concat_officeHolderIDs');
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
            if (GetCurrentUserGrantForDataSource('public.district')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Public.District'), 'public.district.php', $this->RenderText('Public.District'), $currentPageCaption == $this->RenderText('Public.District')));
            if (GetCurrentUserGrantForDataSource('public.election_div')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Public.Election Div'), 'public.election_div.php', $this->RenderText('Public.Election Div'), $currentPageCaption == $this->RenderText('Public.Election Div')));
            if (GetCurrentUserGrantForDataSource('public.election_div_docs')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Public.Election Div Docs'), 'public.election_div_docs.php', $this->RenderText('Public.Election Div Docs'), $currentPageCaption == $this->RenderText('Public.Election Div Docs')));
            if (GetCurrentUserGrantForDataSource('public.level')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Public.Level'), 'public.level.php', $this->RenderText('Public.Level'), $currentPageCaption == $this->RenderText('Public.Level')));
            if (GetCurrentUserGrantForDataSource('public.office')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Public.Office'), 'public.office.php', $this->RenderText('Public.Office'), $currentPageCaption == $this->RenderText('Public.Office')));
            if (GetCurrentUserGrantForDataSource('public.office_docs')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Public.Office Docs'), 'public.office_docs.php', $this->RenderText('Public.Office Docs'), $currentPageCaption == $this->RenderText('Public.Office Docs')));
            if (GetCurrentUserGrantForDataSource('public.office_holder')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Public.Office Holder'), 'public.office_holder.php', $this->RenderText('Public.Office Holder'), $currentPageCaption == $this->RenderText('Public.Office Holder')));
            if (GetCurrentUserGrantForDataSource('public.office_position')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Public.Office Position'), 'public.office_position.php', $this->RenderText('Public.Office Position'), $currentPageCaption == $this->RenderText('Public.Office Position')));
            if (GetCurrentUserGrantForDataSource('officeHolderIDs')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('OfficeHolderIDs'), 'officeHolderIDs.php', $this->RenderText('OfficeHolderIDs'), $currentPageCaption == $this->RenderText('OfficeHolderIDs')));
            
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
            $grid->SearchControl = new SimpleSearch('public_office_positionssearch', $this->dataset,
                array('district_id_districtname', 'office_id_title', 'office_holder_id_concat', 'position_name', 'term_start', 'term_end', 'filing_deadline', 'next_election', 'notes'),
                array($this->RenderText('District Id'), $this->RenderText('Office Id'), $this->RenderText('Office Holder Id'), $this->RenderText('Position Name'), $this->RenderText('Term Start'), $this->RenderText('Term End'), $this->RenderText('Filing Deadline'), $this->RenderText('Next Election'), $this->RenderText('Notes')),
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
            $this->AdvancedSearchControl = new AdvancedSearchControl('public_office_positionasearch', $this->dataset, $this->GetLocalizerCaptions(), $this->GetColumnVariableContainer(), $this->CreateLinkBuilder());
            $this->AdvancedSearchControl->setTimerInterval(1000);
            $selectQuery = 'SELECT d.id, concat(d.state, \' - \', d.name) as districtName
            FROM district d
            ORDER BY districtName';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $lookupDataset = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'Query01');
            $field = new IntegerField('id');
            $lookupDataset->AddField($field, true);
            $field = new StringField('districtname');
            $lookupDataset->AddField($field, false);
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateLookupSearchInput('district_id', $this->RenderText('District Id'), $lookupDataset, 'id', 'districtname', false));
            
            $lookupDataset = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."office"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('title');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('num_positions');
            $lookupDataset->AddField($field, false);
            $field = new StringField('responsibilities');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('term_length_months');
            $lookupDataset->AddField($field, false);
            $field = new StringField('filing_fee');
            $lookupDataset->AddField($field, false);
            $field = new BooleanField('partisan');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('age_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('res_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('prof_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('salary');
            $lookupDataset->AddField($field, false);
            $field = new StringField('notes');
            $lookupDataset->AddField($field, false);
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateLookupSearchInput('office_id', $this->RenderText('Office Id'), $lookupDataset, 'id', 'title', false));
            
            $selectQuery = 'SELECT id, CONCAT(oh.last_name, \', \', oh.first_name, \' \', oh.middle_name)
            FROM office_holder oh
            ORDER BY oh.last_name, oh.first_name';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $lookupDataset = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'officeHolderIDs');
            $field = new IntegerField('id');
            $lookupDataset->AddField($field, true);
            $field = new StringField('concat');
            $lookupDataset->AddField($field, false);
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateLookupSearchInput('office_holder_id', $this->RenderText('Office Holder Id'), $lookupDataset, 'id', 'concat', false));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('position_name', $this->RenderText('Position Name')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateDateTimeSearchInput('term_start', $this->RenderText('Term Start')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateDateTimeSearchInput('term_end', $this->RenderText('Term End')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateDateTimeSearchInput('filing_deadline', $this->RenderText('Filing Deadline')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateDateTimeSearchInput('next_election', $this->RenderText('Next Election')));
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
                $column = new RowOperationByLinkColumn($this->GetLocalizerCaptions()->GetMessageString('Edit'), OPERATION_EDIT, $this->dataset);
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
            // View column for districtname field
            //
            $column = new TextViewColumn('district_id_districtname', 'District Id', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for district_id field
            //
            $editor = new ComboBox('district_id_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $selectQuery = 'SELECT d.id, concat(d.state, \' - \', d.name) as districtName
            FROM district d
            ORDER BY districtName';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $lookupDataset = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'Query01');
            $field = new IntegerField('id');
            $lookupDataset->AddField($field, true);
            $field = new StringField('districtname');
            $lookupDataset->AddField($field, false);
            $editColumn = new LookUpEditColumn(
                'District Id', 
                'district_id', 
                $editor, 
                $this->dataset, 'id', 'districtname', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for district_id field
            //
            $editor = new ComboBox('district_id_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $selectQuery = 'SELECT d.id, concat(d.state, \' - \', d.name) as districtName
            FROM district d
            ORDER BY districtName';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $lookupDataset = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'Query01');
            $field = new IntegerField('id');
            $lookupDataset->AddField($field, true);
            $field = new StringField('districtname');
            $lookupDataset->AddField($field, false);
            $editColumn = new LookUpEditColumn(
                'District Id', 
                'district_id', 
                $editor, 
                $this->dataset, 'id', 'districtname', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('office_id_title', 'Office Id', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for office_id field
            //
            $editor = new ComboBox('office_id_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."office"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('title');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('num_positions');
            $lookupDataset->AddField($field, false);
            $field = new StringField('responsibilities');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('term_length_months');
            $lookupDataset->AddField($field, false);
            $field = new StringField('filing_fee');
            $lookupDataset->AddField($field, false);
            $field = new BooleanField('partisan');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('age_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('res_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('prof_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('salary');
            $lookupDataset->AddField($field, false);
            $field = new StringField('notes');
            $lookupDataset->AddField($field, false);
            $lookupDataset->SetOrderBy('title', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'Office Id', 
                'office_id', 
                $editor, 
                $this->dataset, 'id', 'title', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for office_id field
            //
            $editor = new ComboBox('office_id_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."office"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('title');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('num_positions');
            $lookupDataset->AddField($field, false);
            $field = new StringField('responsibilities');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('term_length_months');
            $lookupDataset->AddField($field, false);
            $field = new StringField('filing_fee');
            $lookupDataset->AddField($field, false);
            $field = new BooleanField('partisan');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('age_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('res_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('prof_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('salary');
            $lookupDataset->AddField($field, false);
            $field = new StringField('notes');
            $lookupDataset->AddField($field, false);
            $lookupDataset->SetOrderBy('title', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'Office Id', 
                'office_id', 
                $editor, 
                $this->dataset, 'id', 'title', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for concat field
            //
            $column = new TextViewColumn('office_holder_id_concat', 'Office Holder Id', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for office_holder_id field
            //
            $editor = new ComboBox('office_holder_id_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $selectQuery = 'SELECT id, CONCAT(oh.last_name, \', \', oh.first_name, \' \', oh.middle_name)
            FROM office_holder oh
            ORDER BY oh.last_name, oh.first_name';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $lookupDataset = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'officeHolderIDs');
            $field = new IntegerField('id');
            $lookupDataset->AddField($field, true);
            $field = new StringField('concat');
            $lookupDataset->AddField($field, false);
            $editColumn = new LookUpEditColumn(
                'Office Holder Id', 
                'office_holder_id', 
                $editor, 
                $this->dataset, 'id', 'concat', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for office_holder_id field
            //
            $editor = new ComboBox('office_holder_id_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $selectQuery = 'SELECT id, CONCAT(oh.last_name, \', \', oh.first_name, \' \', oh.middle_name)
            FROM office_holder oh
            ORDER BY oh.last_name, oh.first_name';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $lookupDataset = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'officeHolderIDs');
            $field = new IntegerField('id');
            $lookupDataset->AddField($field, true);
            $field = new StringField('concat');
            $lookupDataset->AddField($field, false);
            $editColumn = new LookUpEditColumn(
                'Office Holder Id', 
                'office_holder_id', 
                $editor, 
                $this->dataset, 'id', 'concat', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for position_name field
            //
            $column = new TextViewColumn('position_name', 'Position Name', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for position_name field
            //
            $editor = new TextEdit('position_name_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Position Name', 'position_name', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for position_name field
            //
            $editor = new TextEdit('position_name_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Position Name', 'position_name', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for term_start field
            //
            $column = new DateTimeViewColumn('term_start', 'Term Start', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d');
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for term_start field
            //
            $editor = new DateTimeEdit('term_start_edit', false, 'Y-m-d H:i:s', GetFirstDayOfWeek());
            $editColumn = new CustomEditColumn('Term Start', 'term_start', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for term_start field
            //
            $editor = new DateTimeEdit('term_start_edit', false, 'Y-m-d H:i:s', GetFirstDayOfWeek());
            $editColumn = new CustomEditColumn('Term Start', 'term_start', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for term_end field
            //
            $column = new DateTimeViewColumn('term_end', 'Term End', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d');
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for term_end field
            //
            $editor = new DateTimeEdit('term_end_edit', false, 'Y-m-d H:i:s', GetFirstDayOfWeek());
            $editColumn = new CustomEditColumn('Term End', 'term_end', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for term_end field
            //
            $editor = new DateTimeEdit('term_end_edit', false, 'Y-m-d H:i:s', GetFirstDayOfWeek());
            $editColumn = new CustomEditColumn('Term End', 'term_end', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for filing_deadline field
            //
            $column = new DateTimeViewColumn('filing_deadline', 'Filing Deadline', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d');
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for filing_deadline field
            //
            $editor = new DateTimeEdit('filing_deadline_edit', false, 'Y-m-d H:i:s', GetFirstDayOfWeek());
            $editColumn = new CustomEditColumn('Filing Deadline', 'filing_deadline', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for filing_deadline field
            //
            $editor = new DateTimeEdit('filing_deadline_edit', false, 'Y-m-d H:i:s', GetFirstDayOfWeek());
            $editColumn = new CustomEditColumn('Filing Deadline', 'filing_deadline', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for next_election field
            //
            $column = new DateTimeViewColumn('next_election', 'Next Election', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d');
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for next_election field
            //
            $editor = new DateTimeEdit('next_election_edit', false, 'Y-m-d H:i:s', GetFirstDayOfWeek());
            $editColumn = new CustomEditColumn('Next Election', 'next_election', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for next_election field
            //
            $editor = new DateTimeEdit('next_election_edit', false, 'Y-m-d H:i:s', GetFirstDayOfWeek());
            $editColumn = new CustomEditColumn('Next Election', 'next_election', $editor, $this->dataset);
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
            $column->SetFullTextWindowHandlerName('notes_handler');
            
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
            // View column for districtname field
            //
            $column = new TextViewColumn('district_id_districtname', 'District Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('office_id_title', 'Office Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for concat field
            //
            $column = new TextViewColumn('office_holder_id_concat', 'Office Holder Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for position_name field
            //
            $column = new TextViewColumn('position_name', 'Position Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for term_start field
            //
            $column = new DateTimeViewColumn('term_start', 'Term Start', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d');
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for term_end field
            //
            $column = new DateTimeViewColumn('term_end', 'Term End', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d');
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for filing_deadline field
            //
            $column = new DateTimeViewColumn('filing_deadline', 'Filing Deadline', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d');
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for next_election field
            //
            $column = new DateTimeViewColumn('next_election', 'Next Election', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d');
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for notes field
            //
            $column = new TextViewColumn('notes', 'Notes', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('notes_handler');
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for district_id field
            //
            $editor = new ComboBox('district_id_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $selectQuery = 'SELECT d.id, concat(d.state, \' - \', d.name) as districtName
            FROM district d
            ORDER BY districtName';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $lookupDataset = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'Query01');
            $field = new IntegerField('id');
            $lookupDataset->AddField($field, true);
            $field = new StringField('districtname');
            $lookupDataset->AddField($field, false);
            $editColumn = new LookUpEditColumn(
                'District Id', 
                'district_id', 
                $editor, 
                $this->dataset, 'id', 'districtname', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for office_id field
            //
            $editor = new ComboBox('office_id_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."office"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('title');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('num_positions');
            $lookupDataset->AddField($field, false);
            $field = new StringField('responsibilities');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('term_length_months');
            $lookupDataset->AddField($field, false);
            $field = new StringField('filing_fee');
            $lookupDataset->AddField($field, false);
            $field = new BooleanField('partisan');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('age_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('res_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('prof_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('salary');
            $lookupDataset->AddField($field, false);
            $field = new StringField('notes');
            $lookupDataset->AddField($field, false);
            $lookupDataset->SetOrderBy('title', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'Office Id', 
                'office_id', 
                $editor, 
                $this->dataset, 'id', 'title', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for office_holder_id field
            //
            $editor = new ComboBox('office_holder_id_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $selectQuery = 'SELECT id, CONCAT(oh.last_name, \', \', oh.first_name, \' \', oh.middle_name)
            FROM office_holder oh
            ORDER BY oh.last_name, oh.first_name';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $lookupDataset = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'officeHolderIDs');
            $field = new IntegerField('id');
            $lookupDataset->AddField($field, true);
            $field = new StringField('concat');
            $lookupDataset->AddField($field, false);
            $editColumn = new LookUpEditColumn(
                'Office Holder Id', 
                'office_holder_id', 
                $editor, 
                $this->dataset, 'id', 'concat', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for position_name field
            //
            $editor = new TextEdit('position_name_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Position Name', 'position_name', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for term_start field
            //
            $editor = new DateTimeEdit('term_start_edit', false, 'Y-m-d H:i:s', GetFirstDayOfWeek());
            $editColumn = new CustomEditColumn('Term Start', 'term_start', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for term_end field
            //
            $editor = new DateTimeEdit('term_end_edit', false, 'Y-m-d H:i:s', GetFirstDayOfWeek());
            $editColumn = new CustomEditColumn('Term End', 'term_end', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for filing_deadline field
            //
            $editor = new DateTimeEdit('filing_deadline_edit', false, 'Y-m-d H:i:s', GetFirstDayOfWeek());
            $editColumn = new CustomEditColumn('Filing Deadline', 'filing_deadline', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for next_election field
            //
            $editor = new DateTimeEdit('next_election_edit', false, 'Y-m-d H:i:s', GetFirstDayOfWeek());
            $editColumn = new CustomEditColumn('Next Election', 'next_election', $editor, $this->dataset);
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
            // Edit column for district_id field
            //
            $editor = new ComboBox('district_id_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $selectQuery = 'SELECT d.id, concat(d.state, \' - \', d.name) as districtName
            FROM district d
            ORDER BY districtName';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $lookupDataset = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'Query01');
            $field = new IntegerField('id');
            $lookupDataset->AddField($field, true);
            $field = new StringField('districtname');
            $lookupDataset->AddField($field, false);
            $editColumn = new LookUpEditColumn(
                'District Id', 
                'district_id', 
                $editor, 
                $this->dataset, 'id', 'districtname', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for office_id field
            //
            $editor = new ComboBox('office_id_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."office"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('title');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('num_positions');
            $lookupDataset->AddField($field, false);
            $field = new StringField('responsibilities');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('term_length_months');
            $lookupDataset->AddField($field, false);
            $field = new StringField('filing_fee');
            $lookupDataset->AddField($field, false);
            $field = new BooleanField('partisan');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('age_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('res_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('prof_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('salary');
            $lookupDataset->AddField($field, false);
            $field = new StringField('notes');
            $lookupDataset->AddField($field, false);
            $lookupDataset->SetOrderBy('title', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'Office Id', 
                'office_id', 
                $editor, 
                $this->dataset, 'id', 'title', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for office_holder_id field
            //
            $editor = new ComboBox('office_holder_id_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $selectQuery = 'SELECT id, CONCAT(oh.last_name, \', \', oh.first_name, \' \', oh.middle_name)
            FROM office_holder oh
            ORDER BY oh.last_name, oh.first_name';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $lookupDataset = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'officeHolderIDs');
            $field = new IntegerField('id');
            $lookupDataset->AddField($field, true);
            $field = new StringField('concat');
            $lookupDataset->AddField($field, false);
            $editColumn = new LookUpEditColumn(
                'Office Holder Id', 
                'office_holder_id', 
                $editor, 
                $this->dataset, 'id', 'concat', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for position_name field
            //
            $editor = new TextEdit('position_name_edit');
            $editor->SetSize(25);
            $editor->SetMaxLength(25);
            $editColumn = new CustomEditColumn('Position Name', 'position_name', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for term_start field
            //
            $editor = new DateTimeEdit('term_start_edit', false, 'Y-m-d H:i:s', GetFirstDayOfWeek());
            $editColumn = new CustomEditColumn('Term Start', 'term_start', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for term_end field
            //
            $editor = new DateTimeEdit('term_end_edit', false, 'Y-m-d H:i:s', GetFirstDayOfWeek());
            $editColumn = new CustomEditColumn('Term End', 'term_end', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for filing_deadline field
            //
            $editor = new DateTimeEdit('filing_deadline_edit', false, 'Y-m-d H:i:s', GetFirstDayOfWeek());
            $editColumn = new CustomEditColumn('Filing Deadline', 'filing_deadline', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for next_election field
            //
            $editor = new DateTimeEdit('next_election_edit', false, 'Y-m-d H:i:s', GetFirstDayOfWeek());
            $editColumn = new CustomEditColumn('Next Election', 'next_election', $editor, $this->dataset);
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
            // View column for districtname field
            //
            $column = new TextViewColumn('district_id_districtname', 'District Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('office_id_title', 'Office Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for concat field
            //
            $column = new TextViewColumn('office_holder_id_concat', 'Office Holder Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for position_name field
            //
            $column = new TextViewColumn('position_name', 'Position Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for term_start field
            //
            $column = new DateTimeViewColumn('term_start', 'Term Start', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d');
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for term_end field
            //
            $column = new DateTimeViewColumn('term_end', 'Term End', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d');
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for filing_deadline field
            //
            $column = new DateTimeViewColumn('filing_deadline', 'Filing Deadline', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d');
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for next_election field
            //
            $column = new DateTimeViewColumn('next_election', 'Next Election', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d');
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
            // View column for districtname field
            //
            $column = new TextViewColumn('district_id_districtname', 'District Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('office_id_title', 'Office Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for concat field
            //
            $column = new TextViewColumn('office_holder_id_concat', 'Office Holder Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for position_name field
            //
            $column = new TextViewColumn('position_name', 'Position Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for term_start field
            //
            $column = new DateTimeViewColumn('term_start', 'Term Start', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d');
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for term_end field
            //
            $column = new DateTimeViewColumn('term_end', 'Term End', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d');
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for filing_deadline field
            //
            $column = new DateTimeViewColumn('filing_deadline', 'Filing Deadline', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d');
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for next_election field
            //
            $column = new DateTimeViewColumn('next_election', 'Next Election', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d');
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
        
        public function GetModalGridDeleteHandler() { return 'public_office_position_modal_delete'; }
        protected function GetEnableModalGridDelete() { return true; }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset, 'public_office_positionGrid');
            if ($this->GetSecurityInfo()->HasDeleteGrant())
               $result->SetAllowDeleteSelected(false);
            else
               $result->SetAllowDeleteSelected(false);   
            
            ApplyCommonPageSettings($this, $result);
            
            $result->SetUseImagesForActions(false);
            $result->SetUseFixedHeader(false);
            
            $result->SetShowLineNumbers(false);
            
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
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'notes_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);//
            // View column for notes field
            //
            $column = new TextViewColumn('notes', 'Notes', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'notes_handler', $column);
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

    SetUpUserAuthorization(GetApplication());

    try
    {
        $Page = new public_office_positionPage("public.office_position.php", "public_office_position", GetCurrentUserGrantForDataSource("public.office_position"), 'UTF-8');
        $Page->SetShortCaption('Public.Office Position');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetCaption('Public.Office Position');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("public.office_position"));
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
	
