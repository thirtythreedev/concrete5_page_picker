<?php

defined('C5_EXECUTE') or die("Access Denied.");
class PagePickerBlockController extends BlockController
{
    protected $btTable = 'btPagePicker';
    protected $btInterfaceWidth = "800";
    protected $btInterfaceHeight = "500";
    protected $btCacheBlockRecord = false;
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;

    protected $btExportTables = array('btPagePicker', 'btPagePickerCid');
    // state var to let us know whether or not
    // to order by btPagePickerCid.position or not
    protected $sortByDisplayOrder = false;
    // holder for cids for ordering
    protected $cids = array();

    /**
     * Used for localization. If we want to localize the name/description we have to include this
     */
    public function getBlockTypeDescription()
    {
        return t("Select Pages and Do stuff with them.");
    }

    public function getBlockTypeName()
    {
        return t("Page Picker");
    }

    public function getPageList($order = false)
    {
        Loader::model('page_list');

        $db = Loader::db();
        $bID = $this->bID;
        if ($this->bID) {
            $q = "select orderBy, paginate, num from btPagePicker where bID = '$bID'";
            $r = $db->query($q);
            if ($r) {
                $row = $r->fetchRow();
            }
        } else {
            $row['orderBy'] = $this->orderBy;
            $row['num'] = $this->num;
        }

        $pl = new PageList();
        $pl->setNameSpace('b' . $this->bID);

        if($order){
            switch ($row['orderBy']) {
                // display is not the PL's responsibility anymore, as it doesn't take into account the
                // position attr on secondary table
                case 'display_asc':
                    $this->sortByDisplayOrder = 'ASC';
                    break;
                case 'display_desc':
                    $this->sortByDisplayOrder = 'DESC';
                    break;
                case 'chrono_asc':
                    $pl->sortByPublicDate();
                    break;
                case 'alpha_asc':
                    $pl->sortByName();
                    break;
                case 'alpha_desc':
                    $pl->sortByNameDescending();
                    break;
                default:
                    $pl->sortByPublicDateDescending();
                    break;
            }
        }

        $num = (int) $row['num'];

        $pl->setItemsPerPage($num);

        $c = Page::getCurrentPage();

        if (is_object($c)) {
            $this->cID = $c->getCollectionID();
        }

        Loader::model('attribute/categories/collection');

        $pl->filter('cvName', '', '!=');

        $cids = $this->getCIDs();

        if(!empty($cids)){
             $pl->filter(false, 'p1.cID IN ('. implode(', ', $cids ) .')');
        }


        return $pl;

    }

    /**
     * Retrive CIDS from secondary db
     */
    public function getCIDs()
    {
        $db = Loader::db();
        $ret = $db->query("SELECT * FROM btPagePickerCid WHERE bID=" . intval($this->bID) );

        foreach($ret as $r){
            array_push($this->cids, $r["colID"]);
        }

        return $this->cids;
    }

    /**
     * Main getPages call
     * @return array|bool
     */
    public function getPages()
    {
        $pl = $this->getPageList(true);
        if ($pl->getItemsPerPage() > 0) {
            $pages = $pl->getPage();
        } else {
            $pages = $pl->get();
        }
        $this->set('pl', $pl);

        if($this->sortByDisplayOrder){
            $cids = $this->cids;
            usort($pages, function($a, $b) use ($cids) {
                    return intval(array_search($a->cID, $cids)) - intval(array_search($b->cID, $cids));
            });
            if($this->sortByDisplayOrder === 'DESC'){
                $pages = array_reverse($pages);
            }
        }

        return $pages;
    }
    /**
     * Function to get all the pages in a CMS
     */
    public function getAllPages()
    {
        // Get a list of all viewable pages
        $home = Page::getByID(1);
        $homeStr = $home->getCollectionPath();

        $pl = new PageList();
        $pl->filterByPath($homeStr, TRUE);
        $pl->ignoreAliases();

        $allowedPages = (array) $pl->get();
        $perm = new Permissions($home);

        if($perm->canRead()) array_unshift($allowedPages, $home);

        $this->set('allowedPages', $allowedPages);
    }

    /**
     * Function for add and edit that retrieves the currently selected pages
     */
    public function getSelectedPages()
    {
        $cids = $this->getCIDs();
        if( !empty( $cids ) ){
            $pl = $this->getPageList();
            if ($pl->getItemsPerPage() > 0) {
                $pages = $pl->getPage();
            } else {
                $pages = $pl->get();
            }
            $this->set('selectedPages', $pages);
        } else {
            $this->set('selectedPages', array());
        }

    }

    public function view()
    {
        $cArray = $this->getPages();
        $nh = Loader::helper('navigation');
        $this->set('nh', $nh);
        $this->set('cArray', $cArray); // Legacy (pre-5.4.2)
        $this->set('pages', $cArray); // More descriptive variable name (introduced in 5.4.2)

        //Pagination...
        $showPagination = false;
        $paginator = null;

        $pl = $this->get(
            'pl'
        );
        //Terrible horrible hacky way to get the $pl object set in $this->getPages() -- we need to do it this way for backwards-compatibility reasons
        if ($this->paginate && $this->num > 0 && is_object($pl)) {
            $description = $pl->getSummary();
            if ($description->pages > 1) {
                $showPagination = true;
                $paginator = $pl->getPagination();
            }
        }

        $this->set('showPagination', $showPagination);
        $this->set('paginator', $paginator);
    }

    public function add()
    {
        $this->getAllPages();
        $this->getSelectedPages();

        Loader::model("collection_types");
        $c = Page::getCurrentPage();
        $uh = Loader::helper('concrete/urls');
        //	echo $rssUrl;
        $this->set('c', $c);
        $this->set('uh', $uh);
        $this->set('bt', BlockType::getByHandle('page_picker'));
        $this->set('displayAliases', true);
    }

    public function edit()
    {
        $this->getAllPages();
        $this->getSelectedPages();

        $b = $this->getBlockObject();
        $bCID = $b->getBlockCollectionID();
        $bID = $b->getBlockID();

        $this->set('bID', $bID);
        $c = Page::getCurrentPage();
        if ($c->getCollectionID() != $this->cParentID && (!$this->cThis) && ($this->cParentID != 0)) {
            $isOtherPage = true;
            $this->set('isOtherPage', true);
        }
        $uh = Loader::helper('concrete/urls');
        $this->set('uh', $uh);
        $this->set('bt', BlockType::getByHandle('page_picker'));
    }

    function save($args)
    {
        $db = Loader::db();
        $bID = $this->bID;
        $c = $this->getCollectionObject();
        if (is_object($c)) {
            $this->cID = $c->getCollectionID();
        }
        $args['num'] = ($args['num'] > 0) ? $args['num'] : 0;
        $args['paginate'] = intval($args['paginate']);
        $db->query("DELETE FROM btPagePickerCid WHERE bID=" . intval($this->bID));
//        // foreach CID store em for use on a query later
        $pos = 0;
        if(!empty($args['cids'])){
            foreach($args['cids'] as $cid){
                $vals = array(intval($this->bID), intval($cid), $pos);
                $db->query("INSERT INTO btPagePickerCid (bID, colID, position) values (?,?,?)", $vals);
                $pos++;
            }
        }
        // need to unset cids in args because it screws with c5's built in save
        unset( $args['cids'] );
        parent::save($args);
    }

    protected function importAdditionalData($b, $blockNode)
    {
        if (isset($blockNode->data)) {
            foreach ($blockNode->data as $data) {
                if (strtoupper($data['table']) != strtoupper($this->getBlockTypeDatabaseTable())) {
                    $table = (string)$data['table'];
                    if (isset($data->record)) {
                        foreach ($data->record as $record) {
                            $aar = new ADODB_Active_Record($table);
                            $aar->bID = $b->getBlockID();
                            foreach ($record->children() as $node) {
                                $nodeName = $node->getName();
                                if ((strcasecmp($table, 'btPagePickerCid') === 0) && (strcasecmp($nodeName, 'btPagePickerCid') === 0)) {
                                    continue;
                                }
                                $aar->{$nodeName} = ContentImporter::getValue((string)$node);
                            }
                            $aar->Save();
                        }
                    }
                }
            }
        }
    }

    public function delete()
    {
        $db = Loader::db();
        $db->query("DELETE FROM btPagePickerCid WHERE bID=" . intval($this->bID));
        parent::delete();
    }



}
