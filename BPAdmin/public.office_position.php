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
            $field = new IntegerField('office_rank');
            $this->dataset->AddField($field, false);
            $this->dataset->AddLookupField('district_id', 'public.district', new IntegerField('id'), new StringField('name', 'district_id_name', 'district_id_name_public_district'), 'district_id_name_public_district');
            $this->dataset->AddLookupField('office_id', 'public.office', new IntegerField('id'), new StringField('title', 'office_id_title', 'office_id_title_public_office'), 'office_id_title_public_office');
            $this->dataset->AddLookupField('office_holder_id', '(SELECT oh.id, oh.state,
                concat(oh.last_name, \', \', oh.first_name, \' \', oh.middle_name) AS fullName
               FROM office_holder oh
              ORDER BY oh.last_name, oh.first_name)', new IntegerField('id'), new StringField('fullname', 'office_holder_id_fullname', 'office_holder_id_fullname_officeHolderIDs'), 'office_holder_id_fullname_officeHolderIDs');
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
            $grid->SearchControl = new SimpleSearch('public_office_positionssearch', $this->dataset,
                array('district_id_name', 'office_id_title', 'office_holder_id_fullname', 'position_name', 'term_start', 'term_end', 'filing_deadline', 'next_election', 'notes', 'office_rank'),
                array($this->RenderText('Political District Name'), $this->RenderText('Office Title'), $this->RenderText('Office Holder Id'), $this->RenderText('Position Name'), $this->RenderText('Term Start'), $this->RenderText('Term End'), $this->RenderText('Filing Deadline'), $this->RenderText('Next Election'), $this->RenderText('Notes'), $this->RenderText('Office Rank')),
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
            $lookupDataset = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."district"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('state');
            $lookupDataset->AddField($field, false);
            $field = new StringField('name');
            $lookupDataset->AddField($field, false);
            $field = new StringField('level_id');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('election_div_id');
            $lookupDataset->AddField($field, false);
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateLookupSearchInput('district_id', $this->RenderText('Political District Name'), $lookupDataset, 'id', 'name', false));
            
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
            $field = new StringField('age_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('res_requirements');
            $lookupDataset->AddField($field, false);
            $field = new StringField('prof_requirements');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('salary');
            $lookupDataset->AddField($field, false);
            $field = new StringField('notes');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('office_rank');
            $lookupDataset->AddField($field, false);
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateLookupSearchInput('office_id', $this->RenderText('Office Title'), $lookupDataset, 'id', 'title', false));
            
            $selectQuery = 'SELECT oh.id, oh.state,
                concat(oh.last_name, \', \', oh.first_name, \' \', oh.middle_name) AS fullName
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
            $lookupDataset->AddField($field, false);
            $field = new StringField('state');
            $lookupDataset->AddField($field, false);
            $field = new StringField('fullname');
            $lookupDataset->AddField($field, false);
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateLookupSearchInput('office_holder_id', $this->RenderText('Office Holder Id'), $lookupDataset, 'id', 'fullname', false));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('position_name', $this->RenderText('Position Name')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateDateTimeSearchInput('term_start', $this->RenderText('Term Start')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateDateTimeSearchInput('term_end', $this->RenderText('Term End')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateDateTimeSearchInput('filing_deadline', $this->RenderText('Filing Deadline')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateDateTimeSearchInput('next_election', $this->RenderText('Next Election')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('notes', $this->RenderText('Notes')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('office_rank', $this->RenderText('Office Rank')));
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
            $column = new TextViewColumn('district_id_name', 'Political District Name', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for district_id field
            //
            $editor = new MultiLevelComboBoxEditor('district_id_edit', $this->CreateLinkBuilder());
            
            $dataset0 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."state"');
            $field = new StringField('abbr');
            $field->SetIsNotNull(true);
            $dataset0->AddField($field, true);
            $field = new StringField('name');
            $dataset0->AddField($field, false);
            
            $editor->AddLevel($dataset0, 'abbr', 'name', $this->RenderText('State'), null);
            
            $dataset1 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."district"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $dataset1->AddField($field, true);
            $field = new StringField('state');
            $dataset1->AddField($field, false);
            $field = new StringField('name');
            $dataset1->AddField($field, false);
            $field = new StringField('level_id');
            $dataset1->AddField($field, false);
            $field = new IntegerField('election_div_id');
            $dataset1->AddField($field, false);
            
            $editor->AddLevel($dataset1, 'id', 'name', $this->RenderText('Political District Name'), new ForeignKeyInfo('abbr', 'state'));
            $editColumn = new MultiLevelLookupEditColumn('Political District Name', 'district_id', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for district_id field
            //
            $editor = new MultiLevelComboBoxEditor('district_id_edit', $this->CreateLinkBuilder());
            
            $dataset0 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."state"');
            $field = new StringField('abbr');
            $field->SetIsNotNull(true);
            $dataset0->AddField($field, true);
            $field = new StringField('name');
            $dataset0->AddField($field, false);
            
            $editor->AddLevel($dataset0, 'abbr', 'name', $this->RenderText('State'), null);
            
            $dataset1 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."district"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $dataset1->AddField($field, true);
            $field = new StringField('state');
            $dataset1->AddField($field, false);
            $field = new StringField('name');
            $dataset1->AddField($field, false);
            $field = new StringField('level_id');
            $dataset1->AddField($field, false);
            $field = new IntegerField('election_div_id');
            $dataset1->AddField($field, false);
            
            $editor->AddLevel($dataset1, 'id', 'name', $this->RenderText('Political District Name'), new ForeignKeyInfo('abbr', 'state'));
            $editColumn = new MultiLevelLookupEditColumn('Political District Name', 'district_id', $editor, $this->dataset);
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
            $column = new TextViewColumn('office_id_title', 'Office Title', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for office_id field
            //
            $editor = new MultiLevelComboBoxEditor('office_id_edit', $this->CreateLinkBuilder());
            
            $dataset0 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."state"');
            $field = new StringField('abbr');
            $field->SetIsNotNull(true);
            $dataset0->AddField($field, true);
            $field = new StringField('name');
            $dataset0->AddField($field, false);
            
            $editor->AddLevel($dataset0, 'abbr', 'name', $this->RenderText('State'), null);
            
            $selectQuery = 'SELECT op.id office_id, o.title office_title, ed.id election_div_id, ed.name ed_name, ed.phys_addr_state state, concat(d.name, \' - \', o.title) officeTitle
            FROM office_position op
            JOIN district d ON op.district_id = d.id
            JOIN election_div ed ON d.election_div_id = ed.id
            JOIN office o ON op.office_id = o.id';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $dataset1 = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'office_title_by_election_div');
            $field = new IntegerField('office_id');
            $dataset1->AddField($field, false);
            $field = new StringField('office_title');
            $dataset1->AddField($field, false);
            $field = new IntegerField('election_div_id');
            $dataset1->AddField($field, true);
            $field = new StringField('ed_name');
            $dataset1->AddField($field, false);
            $field = new StringField('state');
            $dataset1->AddField($field, false);
            $field = new StringField('officetitle');
            $dataset1->AddField($field, false);
            
            $editor->AddLevel($dataset1, 'election_div_id', 'election_div_name', $this->RenderText('Election Division'), new ForeignKeyInfo('abbr', 'election_div_state'));
            
            $dataset2 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."office"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $dataset2->AddField($field, true);
            $field = new StringField('title');
            $field->SetIsNotNull(true);
            $dataset2->AddField($field, false);
            $field = new IntegerField('num_positions');
            $dataset2->AddField($field, false);
            $field = new StringField('responsibilities');
            $dataset2->AddField($field, false);
            $field = new IntegerField('term_length_months');
            $dataset2->AddField($field, false);
            $field = new StringField('filing_fee');
            $dataset2->AddField($field, false);
            $field = new BooleanField('partisan');
            $dataset2->AddField($field, false);
            $field = new StringField('age_requirements');
            $dataset2->AddField($field, false);
            $field = new StringField('res_requirements');
            $dataset2->AddField($field, false);
            $field = new StringField('prof_requirements');
            $dataset2->AddField($field, false);
            $field = new IntegerField('salary');
            $dataset2->AddField($field, false);
            $field = new StringField('notes');
            $dataset2->AddField($field, false);
            $field = new IntegerField('office_rank');
            $dataset2->AddField($field, false);
            $dataset2->SetOrderBy('title', GetOrderTypeAsSQL(otAscending));
            
            $editor->AddLevel($dataset2, 'id', 'title', $this->RenderText('Office Title'), new ForeignKeyInfo('election_div_id', 'election_div_id'));
            $editColumn = new MultiLevelLookupEditColumn('Office Title', 'office_id', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for office_id field
            //
            $editor = new MultiLevelComboBoxEditor('office_id_edit', $this->CreateLinkBuilder());
            
            $dataset0 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."state"');
            $field = new StringField('abbr');
            $field->SetIsNotNull(true);
            $dataset0->AddField($field, true);
            $field = new StringField('name');
            $dataset0->AddField($field, false);
            
            $editor->AddLevel($dataset0, 'abbr', 'name', $this->RenderText('State'), null);
            
            $selectQuery = 'SELECT op.id office_id, o.title office_title, ed.id election_div_id, ed.name ed_name, ed.phys_addr_state state, concat(d.name, \' - \', o.title) officeTitle
            FROM office_position op
            JOIN district d ON op.district_id = d.id
            JOIN election_div ed ON d.election_div_id = ed.id
            JOIN office o ON op.office_id = o.id';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $dataset1 = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'office_title_by_election_div');
            $field = new IntegerField('office_id');
            $dataset1->AddField($field, false);
            $field = new StringField('office_title');
            $dataset1->AddField($field, false);
            $field = new IntegerField('election_div_id');
            $dataset1->AddField($field, true);
            $field = new StringField('ed_name');
            $dataset1->AddField($field, false);
            $field = new StringField('state');
            $dataset1->AddField($field, false);
            $field = new StringField('officetitle');
            $dataset1->AddField($field, false);
            
            $editor->AddLevel($dataset1, 'election_div_id', 'election_div_name', $this->RenderText('Election Division'), new ForeignKeyInfo('abbr', 'election_div_state'));
            
            $dataset2 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."office"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $dataset2->AddField($field, true);
            $field = new StringField('title');
            $field->SetIsNotNull(true);
            $dataset2->AddField($field, false);
            $field = new IntegerField('num_positions');
            $dataset2->AddField($field, false);
            $field = new StringField('responsibilities');
            $dataset2->AddField($field, false);
            $field = new IntegerField('term_length_months');
            $dataset2->AddField($field, false);
            $field = new StringField('filing_fee');
            $dataset2->AddField($field, false);
            $field = new BooleanField('partisan');
            $dataset2->AddField($field, false);
            $field = new StringField('age_requirements');
            $dataset2->AddField($field, false);
            $field = new StringField('res_requirements');
            $dataset2->AddField($field, false);
            $field = new StringField('prof_requirements');
            $dataset2->AddField($field, false);
            $field = new IntegerField('salary');
            $dataset2->AddField($field, false);
            $field = new StringField('notes');
            $dataset2->AddField($field, false);
            $field = new IntegerField('office_rank');
            $dataset2->AddField($field, false);
            $dataset2->SetOrderBy('title', GetOrderTypeAsSQL(otAscending));
            
            $editor->AddLevel($dataset2, 'id', 'title', $this->RenderText('Office Title'), new ForeignKeyInfo('election_div_id', 'election_div_id'));
            $editColumn = new MultiLevelLookupEditColumn('Office Title', 'office_id', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for fullname field
            //
            $column = new TextViewColumn('office_holder_id_fullname', 'Office Holder Id', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for office_holder_id field
            //
            $editor = new MultiLevelComboBoxEditor('office_holder_id_edit', $this->CreateLinkBuilder());
            
            $dataset0 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."state"');
            $field = new StringField('abbr');
            $field->SetIsNotNull(true);
            $dataset0->AddField($field, true);
            $field = new StringField('name');
            $dataset0->AddField($field, false);
            
            $editor->AddLevel($dataset0, 'abbr', 'name', $this->RenderText('State'), null);
            
            $selectQuery = 'SELECT oh.id, oh.state,
                concat(oh.last_name, \', \', oh.first_name, \' \', oh.middle_name) AS fullName
               FROM office_holder oh
              ORDER BY oh.last_name, oh.first_name';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $dataset1 = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'officeHolderIDs');
            $field = new IntegerField('id');
            $dataset1->AddField($field, false);
            $field = new StringField('state');
            $dataset1->AddField($field, false);
            $field = new StringField('fullname');
            $dataset1->AddField($field, false);
            $dataset1->SetOrderBy('fullname', GetOrderTypeAsSQL(otAscending));
            
            $editor->AddLevel($dataset1, 'id', 'fullname', $this->RenderText('Office Holder Id'), new ForeignKeyInfo('abbr', 'state'));
            $editColumn = new MultiLevelLookupEditColumn('Office Holder Id', 'office_holder_id', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for office_holder_id field
            //
            $editor = new MultiLevelComboBoxEditor('office_holder_id_edit', $this->CreateLinkBuilder());
            
            $dataset0 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."state"');
            $field = new StringField('abbr');
            $field->SetIsNotNull(true);
            $dataset0->AddField($field, true);
            $field = new StringField('name');
            $dataset0->AddField($field, false);
            
            $editor->AddLevel($dataset0, 'abbr', 'name', $this->RenderText('State'), null);
            
            $selectQuery = 'SELECT oh.id, oh.state,
                concat(oh.last_name, \', \', oh.first_name, \' \', oh.middle_name) AS fullName
               FROM office_holder oh
              ORDER BY oh.last_name, oh.first_name';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $dataset1 = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'officeHolderIDs');
            $field = new IntegerField('id');
            $dataset1->AddField($field, false);
            $field = new StringField('state');
            $dataset1->AddField($field, false);
            $field = new StringField('fullname');
            $dataset1->AddField($field, false);
            $dataset1->SetOrderBy('fullname', GetOrderTypeAsSQL(otAscending));
            
            $editor->AddLevel($dataset1, 'id', 'fullname', $this->RenderText('Office Holder Id'), new ForeignKeyInfo('abbr', 'state'));
            $editColumn = new MultiLevelLookupEditColumn('Office Holder Id', 'office_holder_id', $editor, $this->dataset);
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
            $column->SetDescription($this->RenderText('Leave blank if only one position for this office'));
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
            $column->SetFullTextWindowHandlerName('public_office_positionGrid_notes_handler_list');
            
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
            
            //
            // View column for office_rank field
            //
            $column = new TextViewColumn('office_rank', 'Office Rank', $this->dataset);
            $column->SetOrderable(true);
            
            /* <inline edit column> */
            //
            // Edit column for office_rank field
            //
            $editor = new TextEdit('office_rank_edit');
            $editColumn = new CustomEditColumn('Office Rank', 'office_rank', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetEditOperationColumn($editColumn);
            /* </inline edit column> */
            
            /* <inline insert column> */
            //
            // Edit column for office_rank field
            //
            $editor = new TextEdit('office_rank_edit');
            $editColumn = new CustomEditColumn('Office Rank', 'office_rank', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $column->SetInsertOperationColumn($editColumn);
            /* </inline insert column> */
            $column->SetDescription($this->RenderText('For sorting purposes, lower number = higher rank'));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for name field
            //
            $column = new TextViewColumn('district_id_name', 'Political District Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('office_id_title', 'Office Title', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for fullname field
            //
            $column = new TextViewColumn('office_holder_id_fullname', 'Office Holder Id', $this->dataset);
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
            $column->SetFullTextWindowHandlerName('public_office_positionGrid_notes_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for office_rank field
            //
            $column = new TextViewColumn('office_rank', 'Office Rank', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for district_id field
            //
            $editor = new MultiLevelComboBoxEditor('district_id_edit', $this->CreateLinkBuilder());
            
            $dataset0 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."state"');
            $field = new StringField('abbr');
            $field->SetIsNotNull(true);
            $dataset0->AddField($field, true);
            $field = new StringField('name');
            $dataset0->AddField($field, false);
            
            $editor->AddLevel($dataset0, 'abbr', 'name', $this->RenderText('State'), null);
            
            $dataset1 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."district"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $dataset1->AddField($field, true);
            $field = new StringField('state');
            $dataset1->AddField($field, false);
            $field = new StringField('name');
            $dataset1->AddField($field, false);
            $field = new StringField('level_id');
            $dataset1->AddField($field, false);
            $field = new IntegerField('election_div_id');
            $dataset1->AddField($field, false);
            
            $editor->AddLevel($dataset1, 'id', 'name', $this->RenderText('Political District Name'), new ForeignKeyInfo('abbr', 'state'));
            $editColumn = new MultiLevelLookupEditColumn('Political District Name', 'district_id', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for office_id field
            //
            $editor = new MultiLevelComboBoxEditor('office_id_edit', $this->CreateLinkBuilder());
            
            $dataset0 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."state"');
            $field = new StringField('abbr');
            $field->SetIsNotNull(true);
            $dataset0->AddField($field, true);
            $field = new StringField('name');
            $dataset0->AddField($field, false);
            
            $editor->AddLevel($dataset0, 'abbr', 'name', $this->RenderText('State'), null);
            
            $selectQuery = 'SELECT op.id office_id, o.title office_title, ed.id election_div_id, ed.name ed_name, ed.phys_addr_state state, concat(d.name, \' - \', o.title) officeTitle
            FROM office_position op
            JOIN district d ON op.district_id = d.id
            JOIN election_div ed ON d.election_div_id = ed.id
            JOIN office o ON op.office_id = o.id';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $dataset1 = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'office_title_by_election_div');
            $field = new IntegerField('office_id');
            $dataset1->AddField($field, false);
            $field = new StringField('office_title');
            $dataset1->AddField($field, false);
            $field = new IntegerField('election_div_id');
            $dataset1->AddField($field, true);
            $field = new StringField('ed_name');
            $dataset1->AddField($field, false);
            $field = new StringField('state');
            $dataset1->AddField($field, false);
            $field = new StringField('officetitle');
            $dataset1->AddField($field, false);
            
            $editor->AddLevel($dataset1, 'election_div_id', 'election_div_name', $this->RenderText('Election Division'), new ForeignKeyInfo('abbr', 'election_div_state'));
            
            $dataset2 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."office"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $dataset2->AddField($field, true);
            $field = new StringField('title');
            $field->SetIsNotNull(true);
            $dataset2->AddField($field, false);
            $field = new IntegerField('num_positions');
            $dataset2->AddField($field, false);
            $field = new StringField('responsibilities');
            $dataset2->AddField($field, false);
            $field = new IntegerField('term_length_months');
            $dataset2->AddField($field, false);
            $field = new StringField('filing_fee');
            $dataset2->AddField($field, false);
            $field = new BooleanField('partisan');
            $dataset2->AddField($field, false);
            $field = new StringField('age_requirements');
            $dataset2->AddField($field, false);
            $field = new StringField('res_requirements');
            $dataset2->AddField($field, false);
            $field = new StringField('prof_requirements');
            $dataset2->AddField($field, false);
            $field = new IntegerField('salary');
            $dataset2->AddField($field, false);
            $field = new StringField('notes');
            $dataset2->AddField($field, false);
            $field = new IntegerField('office_rank');
            $dataset2->AddField($field, false);
            $dataset2->SetOrderBy('title', GetOrderTypeAsSQL(otAscending));
            
            $editor->AddLevel($dataset2, 'id', 'title', $this->RenderText('Office Title'), new ForeignKeyInfo('election_div_id', 'election_div_id'));
            $editColumn = new MultiLevelLookupEditColumn('Office Title', 'office_id', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for office_holder_id field
            //
            $editor = new MultiLevelComboBoxEditor('office_holder_id_edit', $this->CreateLinkBuilder());
            
            $dataset0 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."state"');
            $field = new StringField('abbr');
            $field->SetIsNotNull(true);
            $dataset0->AddField($field, true);
            $field = new StringField('name');
            $dataset0->AddField($field, false);
            
            $editor->AddLevel($dataset0, 'abbr', 'name', $this->RenderText('State'), null);
            
            $selectQuery = 'SELECT oh.id, oh.state,
                concat(oh.last_name, \', \', oh.first_name, \' \', oh.middle_name) AS fullName
               FROM office_holder oh
              ORDER BY oh.last_name, oh.first_name';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $dataset1 = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'officeHolderIDs');
            $field = new IntegerField('id');
            $dataset1->AddField($field, false);
            $field = new StringField('state');
            $dataset1->AddField($field, false);
            $field = new StringField('fullname');
            $dataset1->AddField($field, false);
            $dataset1->SetOrderBy('fullname', GetOrderTypeAsSQL(otAscending));
            
            $editor->AddLevel($dataset1, 'id', 'fullname', $this->RenderText('Office Holder Id'), new ForeignKeyInfo('abbr', 'state'));
            $editColumn = new MultiLevelLookupEditColumn('Office Holder Id', 'office_holder_id', $editor, $this->dataset);
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
            
            //
            // Edit column for office_rank field
            //
            $editor = new TextEdit('office_rank_edit');
            $editColumn = new CustomEditColumn('Office Rank', 'office_rank', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for district_id field
            //
            $editor = new MultiLevelComboBoxEditor('district_id_edit', $this->CreateLinkBuilder());
            
            $dataset0 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."state"');
            $field = new StringField('abbr');
            $field->SetIsNotNull(true);
            $dataset0->AddField($field, true);
            $field = new StringField('name');
            $dataset0->AddField($field, false);
            
            $editor->AddLevel($dataset0, 'abbr', 'name', $this->RenderText('State'), null);
            
            $dataset1 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."district"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $dataset1->AddField($field, true);
            $field = new StringField('state');
            $dataset1->AddField($field, false);
            $field = new StringField('name');
            $dataset1->AddField($field, false);
            $field = new StringField('level_id');
            $dataset1->AddField($field, false);
            $field = new IntegerField('election_div_id');
            $dataset1->AddField($field, false);
            
            $editor->AddLevel($dataset1, 'id', 'name', $this->RenderText('Political District Name'), new ForeignKeyInfo('abbr', 'state'));
            $editColumn = new MultiLevelLookupEditColumn('Political District Name', 'district_id', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for office_id field
            //
            $editor = new MultiLevelComboBoxEditor('office_id_edit', $this->CreateLinkBuilder());
            
            $dataset0 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."state"');
            $field = new StringField('abbr');
            $field->SetIsNotNull(true);
            $dataset0->AddField($field, true);
            $field = new StringField('name');
            $dataset0->AddField($field, false);
            
            $editor->AddLevel($dataset0, 'abbr', 'name', $this->RenderText('State'), null);
            
            $selectQuery = 'SELECT op.id office_id, o.title office_title, ed.id election_div_id, ed.name ed_name, ed.phys_addr_state state, concat(d.name, \' - \', o.title) officeTitle
            FROM office_position op
            JOIN district d ON op.district_id = d.id
            JOIN election_div ed ON d.election_div_id = ed.id
            JOIN office o ON op.office_id = o.id';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $dataset1 = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'office_title_by_election_div');
            $field = new IntegerField('office_id');
            $dataset1->AddField($field, false);
            $field = new StringField('office_title');
            $dataset1->AddField($field, false);
            $field = new IntegerField('election_div_id');
            $dataset1->AddField($field, true);
            $field = new StringField('ed_name');
            $dataset1->AddField($field, false);
            $field = new StringField('state');
            $dataset1->AddField($field, false);
            $field = new StringField('officetitle');
            $dataset1->AddField($field, false);
            
            $editor->AddLevel($dataset1, 'election_div_id', 'election_div_name', $this->RenderText('Election Division'), new ForeignKeyInfo('abbr', 'election_div_state'));
            
            $dataset2 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."office"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $dataset2->AddField($field, true);
            $field = new StringField('title');
            $field->SetIsNotNull(true);
            $dataset2->AddField($field, false);
            $field = new IntegerField('num_positions');
            $dataset2->AddField($field, false);
            $field = new StringField('responsibilities');
            $dataset2->AddField($field, false);
            $field = new IntegerField('term_length_months');
            $dataset2->AddField($field, false);
            $field = new StringField('filing_fee');
            $dataset2->AddField($field, false);
            $field = new BooleanField('partisan');
            $dataset2->AddField($field, false);
            $field = new StringField('age_requirements');
            $dataset2->AddField($field, false);
            $field = new StringField('res_requirements');
            $dataset2->AddField($field, false);
            $field = new StringField('prof_requirements');
            $dataset2->AddField($field, false);
            $field = new IntegerField('salary');
            $dataset2->AddField($field, false);
            $field = new StringField('notes');
            $dataset2->AddField($field, false);
            $field = new IntegerField('office_rank');
            $dataset2->AddField($field, false);
            $dataset2->SetOrderBy('title', GetOrderTypeAsSQL(otAscending));
            
            $editor->AddLevel($dataset2, 'id', 'title', $this->RenderText('Office Title'), new ForeignKeyInfo('election_div_id', 'election_div_id'));
            $editColumn = new MultiLevelLookupEditColumn('Office Title', 'office_id', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for office_holder_id field
            //
            $editor = new MultiLevelComboBoxEditor('office_holder_id_edit', $this->CreateLinkBuilder());
            
            $dataset0 = new TableDataset(
                new PgConnectionFactory(),
                GetConnectionOptions(),
                '"public"."state"');
            $field = new StringField('abbr');
            $field->SetIsNotNull(true);
            $dataset0->AddField($field, true);
            $field = new StringField('name');
            $dataset0->AddField($field, false);
            
            $editor->AddLevel($dataset0, 'abbr', 'name', $this->RenderText('State'), null);
            
            $selectQuery = 'SELECT oh.id, oh.state,
                concat(oh.last_name, \', \', oh.first_name, \' \', oh.middle_name) AS fullName
               FROM office_holder oh
              ORDER BY oh.last_name, oh.first_name';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $dataset1 = new QueryDataset(
              new PgConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'officeHolderIDs');
            $field = new IntegerField('id');
            $dataset1->AddField($field, false);
            $field = new StringField('state');
            $dataset1->AddField($field, false);
            $field = new StringField('fullname');
            $dataset1->AddField($field, false);
            $dataset1->SetOrderBy('fullname', GetOrderTypeAsSQL(otAscending));
            
            $editor->AddLevel($dataset1, 'id', 'fullname', $this->RenderText('Office Holder Id'), new ForeignKeyInfo('abbr', 'state'));
            $editColumn = new MultiLevelLookupEditColumn('Office Holder Id', 'office_holder_id', $editor, $this->dataset);
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
            
            //
            // Edit column for office_rank field
            //
            $editor = new TextEdit('office_rank_edit');
            $editColumn = new CustomEditColumn('Office Rank', 'office_rank', $editor, $this->dataset);
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
            $column = new TextViewColumn('district_id_name', 'Political District Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('office_id_title', 'Office Title', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for fullname field
            //
            $column = new TextViewColumn('office_holder_id_fullname', 'Office Holder Id', $this->dataset);
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
            
            //
            // View column for office_rank field
            //
            $column = new TextViewColumn('office_rank', 'Office Rank', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for name field
            //
            $column = new TextViewColumn('district_id_name', 'Political District Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('office_id_title', 'Office Title', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for fullname field
            //
            $column = new TextViewColumn('office_holder_id_fullname', 'Office Holder Id', $this->dataset);
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
            
            //
            // View column for office_rank field
            //
            $column = new TextViewColumn('office_rank', 'Office Rank', $this->dataset);
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
        public function GetModalGridEditingHandler() { return 'public_office_position_inline_edit'; }
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
        
        public function GetModalGridDeleteHandler() { return 'public_office_position_modal_delete'; }
        protected function GetEnableModalGridDelete() { return true; }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset, 'public_office_positionGrid');
            if ($this->GetSecurityInfo()->HasDeleteGrant())
               $result->SetAllowDeleteSelected(true);
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
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'public_office_positionGrid_notes_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);//
            // View column for notes field
            //
            $column = new TextViewColumn('notes', 'Notes', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'public_office_positionGrid_notes_handler_view', $column);
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
        $Page = new public_office_positionPage("public.office_position.php", "public_office_position", GetCurrentUserGrantForDataSource("public.office_position"), 'UTF-8');
        $Page->SetShortCaption('Political Office Positions');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetCaption('Political Office Positions');
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
	
