<?php

defined('C5_EXECUTE') or die("Access Denied.");
class PagePickerBlockController extends BlockController
{
    protected $btHandle = 'page_picker';
    protected $btTable = 'btPagePicker';
    protected $btInterfaceWidth = "800";
    protected $btInterfaceHeight = "800";
    protected $btCacheBlockRecord = false;
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btCacheBlockOutputLifetime = CACHE_LIFETIME;

    protected $btExportFileColumns = array('fID');
    protected $btExportTables = array('btPagePicker', 'btPagePickerCID');

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

    public function getPageList()
    {
        Loader::model('page_list');

        $db = Loader::db();
        $bID = $this->bID;

        if ($this->bID) {
            $q = "select num, cParentID, cThis, orderBy, ctID, displayAliases, rss from btPagePicker where bID = '$bID'";
            $r = $db->query($q);
            if ($r) {
                $row = $r->fetchRow();
            }
        } else {
            $row['num'] = $this->num;
            $row['cParentID'] = $this->cParentID;
            $row['cThis'] = $this->cThis;
            $row['orderBy'] = $this->orderBy;
            $row['ctID'] = $this->ctID;
            $row['rss'] = $this->rss;
            $row['displayAliases'] = $this->displayAliases;
        }

        $pl = new PageList();
        $pl->setNameSpace('b' . $this->bID);

        $cArray = array();

        switch ($row['orderBy']) {
            case 'display_asc':
                $pl->sortByDisplayOrder();
                break;
            case 'display_desc':
                $pl->sortByDisplayOrderDescending();
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

        $num = (int)$row['num'];

        $pl->setItemsPerPage($num);

        $c = Page::getCurrentPage();

        if (is_object($c)) {
            $this->cID = $c->getCollectionID();
        }

        Loader::model('attribute/categories/collection');

        $pl->filter('cvName', '', '!=');

        $cids = $this->getCIDs();

        $pl->filter(false, 'WHERE bID = (cID IN ('.implode(',', $cids ).')');


        $columns = $db->MetaColumns(CollectionAttributeKey::getIndexedSearchTable());
        if (isset($columns['AK_EXCLUDE_PAGE_LIST'])) {
            $pl->filter(false, '(ak_exclude_page_list = 0 or ak_exclude_page_list is null)');
        }

        if (intval($row['cParentID']) != 0) {
            $cParentID = ($row['cThis']) ? $this->cID : $row['cParentID'];
            if ($this->includeAllDescendents) {
                $pl->filterByPath(Page::getByID($cParentID)->getCollectionPath());
            } else {
                $pl->filterByParentID($cParentID);
            }
        }

        return $pl;
    }

    /**
     * Retrive CIDS from secondary db
     */
    public function getCIDs()
    {

    }

    public function getPages()
    {
        $pl = $this->getPageList();

        if ($pl->getItemsPerPage() > 0) {
            $pages = $pl->getPage();
        } else {
            $pages = $pl->get();
        }
//        $pl->filterByAttribute()
        $this->set('pl', $pl);

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

    public function on_page_view()
    {
        $html = Loader::helper('html');
        $this->addHeaderItem( $html->javascript('//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js') );
        $this->addHeaderItem( $html->css('//cdn.datatables.net/1.10.4/css/jquery.dataTables.min.css') );
    }

    public function view()
    {
        $cArray = $this->getPages();
        $nh = Loader::helper('navigation');
        $this->set('nh', $nh);
        $this->set('cArray', $cArray); //Legacy (pre-5.4.2)
        $this->set('pages', $cArray); //More descriptive variable name (introduced in 5.4.2)

        //Pagination...
        $showPagination = false;
        $paginator = null;

        $pl = $this->get(
            'pl'
        ); //Terrible horrible hacky way to get the $pl object set in $this->getPages() -- we need to do it this way for backwards-compatibility reasons
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
        $this->set('bt', BlockType::getByHandle('page_picker') );
    }

    function save($args)
    {
        $db = Loader::db();

        $bID = $this->bID;

        $c = $this->getCollectionObject();

        if (is_object($c)) {
            $this->cID = $c->getCollectionID();
        }

        $db->query("DELETE FROM btPagePickerCID WHERE bID=" . intval($this->bID));

        // foreach CID store em for use on a query later
        $pos = 0;

        foreach($args['cid'] as $cid){
            $vals = array(intval($this->bID), intval($cid), $pos);
            $db->query("INSERT INTO btPagePickerCID (bID, cID, position) values (?,?,?)", $vals);
            $pos++;
        }

        parent::save($args);
    }


}


