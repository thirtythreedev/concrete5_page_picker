<?php
defined('C5_EXECUTE') or die("Access Denied.");
$rssUrl = $showRss ? $controller->getRssUrl($b) : '';
$th = Loader::helper('text');
$nh = Loader::helper('navigation');
?>
<p>
    List pages based on selection.
</p>
<p>Select pages on the left and have them appear on the right. Drag them into the order you want to display them.</p>
<div id="page-picker__wrapper" class="field field_type-relationship">
    <div class="ccm-ui">
        <ul id="ccm-pagepicker-tabs" class="nav nav-tabs">
            <li class="active"><a href="#home">Page Picker</a></li>
            <li><a href="#settings">Settings</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="home">
                <div class="ccm-block-field-group cf relationship-wrapper">
                    <!-- Left List -->
                    <div class="relationship_left cf">
                        <table id="page-table" class="display" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>Page Name</th>
                                <th>Page Type</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Page Name</th>
                                <th>Page Type</th>
                            </tr>
                            </tfoot>
                            <tbody>
                            <?php foreach ($allowedPages as $page):
                                // Prepare data for each page being listed...
                                $title = $th->entities($page->getCollectionName());
                                $url = $nh->getLinkToCollection($page);
                                $target = ($page->getCollectionPointerExternalLink(
                                    ) != '' && $page->openCollectionPointerExternalLinkInNewWindow(
                                    )) ? '_blank' : $page->getAttribute('nav_target');
                                $target = empty($target) ? '_self' : $target;
                                $description = $page->getCollectionDescription();
                                $description = $controller->truncateSummaries ? $th->wordSafeShortText(
                                    $description,
                                    $controller->truncateChars
                                ) : $description;
                                $description = $th->entities($description);
                                ?>
                                <tr>
                                    <td>
                                        <a class="js-site-add" href="<?php echo $url; ?>"
                                           data-cid="<?php echo $page->cID; ?>">
                                            <?php echo $title; ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo $page->getCollectionTypeHandle(); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /Left List -->
                    <!-- Right List -->
                    <div class="relationship_right cf">
                        <div id="page_count">
                            <span class="number">0</span> pages currently selected.
                        </div>
                        <p>Drag to reorder. Click the delete icon to remove a page.</p>
                        <ul class="bl relationship_list page_sort">
                            <?php foreach ($selectedPages as $page):
                                // Prepare data for each page being listed...
                                $title = $th->entities($page->getCollectionName());
                                $url = $nh->getLinkToCollection($page);
                                $target = ($page->getCollectionPointerExternalLink(
                                    ) != '' && $page->openCollectionPointerExternalLinkInNewWindow(
                                    )) ? '_blank' : $page->getAttribute('nav_target');
                                $target = empty($target) ? '_self' : $target;
                                $description = $page->getCollectionDescription();
                                $description = $controller->truncateSummaries ? $th->wordSafeShortText(
                                    $description,
                                    $controller->truncateChars
                                ) : $description;
                                $description = $th->entities($description);
                                ?>
                                <li id="page--<?php echo $page->cID; ?>">
                                    <a href="<?php echo $url; ?>"
                                       data-cid="<?php echo $page->cID; ?>"><?php echo $title; ?>
                                        <span class="acf-button-remove"></span>
                                    </a>
                                    <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                    <input type="hidden" name="cids[]" value="<?php echo $page->cID; ?>">
                                    <a class="js-delete-button" alt="Delete Page"
                                       data-cid="<?php echo $page->cID; ?>"><span
                                            class="ui-icon ui-icon-circle-close"></span></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <!-- / Right List -->
                </div>
            </div>
            <div class="tab-pane" id="settings">
                <div class="ccm-block-field-group">
                    <h2><?php echo t('Block Order') ?></h2>
                    <label>
                        <select name="orderBy" id="orderBy">
                            <option value="display_asc" <?php if ($orderBy == "display_asc"): ?>selected<?php endif; ?>>
                                Display Asc
                            </option>
                            <option value="display_desc"
                                    <?php if ($orderBy == "display_desc"): ?>selected<?php endif; ?>>Display Desc
                            </option>
                            <option value="chrono_asc" <?php if ($orderBy == "chrono_asc"): ?>selected<?php endif; ?>>
                                Date Asc
                            </option>
                            <option value="chrono_desc" <?php if ($orderBy == "chrono_desc"): ?>selected<?php endif; ?>>
                                Date Desc
                            </option>
                            <option value="alpha_asc" <?php if ($orderBy == "alpha_asc"): ?>selected<?php endif; ?>>
                                Alpha Asc
                            </option>
                            <option value="alpha_desc" <?php if ($orderBy == "alpha_desc"): ?>selected<?php endif; ?>>
                                Alpha Desc
                            </option>
                        </select>
                        <?php echo t('Output Order of block.') ?>
                    </label>
                </div>
                <div class="ccm-block-field-group">
                    <h2><?php echo t('Pagination') ?></h2>
                    <label class="checkbox">
                        <input type="checkbox" name="paginate"
                               value="1" <?php if ($paginate == 1) { ?> checked <?php } ?> />
                        <?php echo t('Display pagination interface if more items are available than are displayed.') ?>
                    </label>
                </div>
                <div class="ccm-block-field-group">
                    <h2><?php echo t('Number of Items') ?></h2>
                    <label class="checkbox">
                        <input type="text" name="num" value="<?php echo $num ?>" style="width: 30px">
                        <?php echo t('Number of items to paginate on.') ?>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    div.ccm-block-field-group.relationship-wrapper {
        /*border: 1px solid black;*/
        margin-bottom: 0;
        padding-bottom: 0;
        border:0;
    }
    #page-table_wrapper {
        border-right: 1px solid black;
    }

    #page-picker__wrapper {
        margin-top: 30px;
    }

    .relationship_left {
        width: 50%;
        float: left;
    }

    .relationship_right {
        padding: 1em;
        float: left;
        width: 50%;
        padding-top: 8px;
        padding-left: 0;
        box-sizing: border-box;
        padding-bottom: 0;
        border-left: 1px solid black;
        margin-left: -1px;
    }

    .relationship_right ul {
        list-style-type: none;
        margin-left: 0;
        padding-left: 0;
    }

    .relationship_right li {
        width: 100%;
        padding: 0.6em 0.5em;
        background-color: #f9f9f9;
        position: relative;
        padding-left: 20px;
        border-top: 1px solid #ddd;
        cursor: move; /* fallback if grab cursor is unsupported */
        cursor: grab;
        cursor: -moz-grab;
        cursor: -webkit-grab;
    }

    .relationship_right li:first-of-type {
        border-top: 1px solid black;
    }

    .relationship_right li:last-of-type {
        /*border-bottom:1px solid black;*/
    }

    .relationship_right .ui-icon {
        color: white;
        font-size: 16px;
        margin-top: -8px;
    }

    .relationship_right li .ui-icon-arrowthick-2-n-s {
        position: absolute;
        top: 50%;
        left: 0;
    }

    .relationship_right li .ui-icon-circle-close {
        position: absolute;
        top: 50%;
        right: 15px;
    }

    .relationship_right li.ui-sortable-helper {
        background-color: #f1f1f1;
        cursor: -webkit-grabbing;
        cursor: -moz-grabbing;
        border: 1px solid #66aa33;
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
    table.dataTable tbody tr a {
        display: block;
        width: 100%;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }

</style>

