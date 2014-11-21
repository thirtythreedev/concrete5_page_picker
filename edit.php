<?php
defined('C5_EXECUTE') or die("Access Denied.");
$rssUrl = $showRss ? $controller->getRssUrl($b) : '';
$th = Loader::helper('text');
//$ih = Loader::helper('image'); //<--uncomment this line if displaying image attributes (see below)
//Note that $nh (navigation helper) is already loaded for us by the controller (for legacy reasons)
$nh = Loader::helper('navigation');
?>
<div id="page-picker__wrapper" class="field field_type-relationship" >
    <div class="acf_relationship has-search">
        <!-- Left List -->
        <div class="relationship_left cf">
        <p>Select pages from this list</p>
        <table id="page-table" class="display" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>Page Name</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($allowedPages as $page):
            // Prepare data for each page being listed...
            $title = $th->entities($page->getCollectionName());
            $url = $nh->getLinkToCollection($page);
            $target = ($page->getCollectionPointerExternalLink() != '' && $page->openCollectionPointerExternalLinkInNewWindow()) ? '_blank' : $page->getAttribute('nav_target');
            $target = empty($target) ? '_self' : $target;
            $description = $page->getCollectionDescription();
            $description = $controller->truncateSummaries ? $th->wordSafeShortText($description, $controller->truncateChars) : $description;
            $description = $th->entities($description);
            ?>
            <tr>
                <td>
                    <a class="js-site-add" href="<?php echo $url; ?>" data-cid="<?php echo $page->cID; ?>">
                        <?php echo $title; ?>
                        <span class="acf-button-add"></span>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        </table>

        </div>
        <!-- /Left List -->
        <!-- Right List -->
        <div class="relationship_right">
            <div id="page_count">
                <span class="number">0</span> pages currently selected.
            </div>
            <p>Drag to reorder. Click the delete icon to remove a page.</p>
            <ul class="bl relationship_list page_sort">
                <?php foreach ($allowedPages as $page):
                    // Prepare data for each page being listed...
                    $title = $th->entities($page->getCollectionName());
                    $url = $nh->getLinkToCollection($page);
                    $target = ($page->getCollectionPointerExternalLink() != '' && $page->openCollectionPointerExternalLinkInNewWindow()) ? '_blank' : $page->getAttribute('nav_target');
                    $target = empty($target) ? '_self' : $target;
                    $description = $page->getCollectionDescription();
                    $description = $controller->truncateSummaries ? $th->wordSafeShortText($description, $controller->truncateChars) : $description;
                    $description = $th->entities($description);
                    // TODO : REMOVE THIS WHEN WE HAVE FEASIBLE DATA
                    continue;
                ?>
                <li id="page--<?php echo $page->cID; ?>">
                    <a href="<?php echo $url; ?>" data-cid="<?php echo $page->cID; ?>"><?php echo $title; ?>
                        <span class="acf-button-remove"></span>
                    </a>
                    <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                    <input type="hidden" name="cids[]" value="<?php echo $page->cID; ?>">
                    <a class="js-delete-button" alt="Delete Page" data-cid="<?php echo $page->cID; ?>"><span class="ui-icon ui-icon-circle-close"></span></a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <!-- / Right List -->
    </div>
</div>

<style>
    #page-picker__wrapper{
        margin-top: 30px;
    }

    .relationship_left{
        width:400px;
        float:left;
        margin-left:1%;
    }

    .relationship_right{
        padding: 1em;
        float: left;
        width: calc(100% - 450px);
        max-width:400px;
        min-width:200px;
        padding-top:0;
    }

    .relationship_right ul{
        list-style-type:none;
        margin-left:0;
        padding-left:0;
    }

    .relationship_right li{
          width:100%;
          padding:0.5em;
          background-color: #f9f9f9;
          position:relative;
          padding-left:20px;
          border-top: 1px solid #ddd;
          cursor: move; /* fallback if grab cursor is unsupported */
          cursor: grab;
          cursor: -moz-grab;
          cursor: -webkit-grab;
      }

    .relationship_right li:first-of-type{
        border-top:1px solid black;
    }

    .relationship_right li:last-of-type{
        border-bottom:1px solid black;
    }

    .relationship_right .ui-icon{
        color: white;
        font-size:16px;
        margin-top:-8px;
    }

    .relationship_right li .ui-icon-arrowthick-2-n-s{
        position:absolute;
        top:50%;
        left:0;
    }

    .relationship_right li .ui-icon-circle-close{
        position:absolute;
        top:50%;
        right:15px;
    }

    .relationship_right li.ui-sortable-helper{
        background-color: #f1f1f1;
        cursor: -webkit-grabbing; cursor: -moz-grabbing;
        border:1px solid #66aa33;
    }

    .cf:before,
    .cf:after {
        content: " "; /* 1 */
        display: table; /* 2 */
    }

    .cf:after {
        clear: both;
    }

    /*datatable specific*/
    table.dataTable tbody tr a{
        display:block;
        width:100%;
    }

</style>

