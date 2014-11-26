<?php
defined('C5_EXECUTE') or die("Access Denied.");
$rssUrl = $showRss ? $controller->getRssUrl($b) : '';
$th = Loader::helper('text');
$nh = Loader::helper('navigation');
?>
<!-- Title -->
<h2 class="block-instruction">List pages based on selection.</h2>
<p>Select pages on the left and have them appear on the right. Drag them into the order you want to display them.</p>
<div id="page-picker__wrapper" class="field field_type-relationship">
    <!-- Tab List -->
    <div class="ccm-ui">
        <ul id="ccm-pagepicker-tabs" class="nav nav-tabs">
            <li class="active"><a href="#page-picker__home">Page Picker</a></li>
            <li><a href="#page-picker__settings">Settings</a></li>
        </ul>
    </div>
    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Tab One -->
        <div class="tab-pane active" id="page-picker__home">
            <!-- Relationship Field -->
            <div class="ccm-block-field-group cf relationship-wrapper">
                <!-- Left Column -->
                <div class="relationship-left cf ccm-ui">
                    <table id="page-table" class="display" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>Page Name</th>
                            <th>Page Type</th>
                        </tr>
                        </thead>
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
                            <tr class="js-site-add" data-cid="<?php echo $page->cID; ?>" data-url="<?php echo $url; ?>" data-title="<?php echo $title; ?>">
                                <td>

                                        <?php echo $title; ?>

                                </td>
                                <td class="page-handle">

                                        <?php echo $page->getCollectionTypeHandle(); ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <!-- /Left Column -->
                <!-- Right Column -->
                <div class="relationship-right ccm-ui cf">
                    <div id="page-count">
                        <span class="number">0</span> pages currently selected.
                    </div>

                    <ul class="bl relationship-list page_sort">
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
                            <li id="page--<?php echo $page->cID; ?>" class="relationship-list__item">
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
                <!-- /Right Column -->
            </div>
            <!-- /Relationship Field -->
        </div>
        <!-- /Tab One -->
        <!-- Tab Two -->
        <div class="tab-pane" id="page-picker__settings">
            <div class="ccm-block-field-group ccm-ui">
                <h2><?php echo t('Block Order') ?></h2>
                <label>
                    <?php echo t('Output Order of block.') ?>
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

                </label>
            </div>
            <div class="ccm-block-field-group ccm-ui">
                <h2><?php echo t('Pagination') ?></h2>
                <label class="checkbox">
                    <input type="checkbox" name="paginate"
                           value="1" <?php if ($paginate == 1) { ?> checked <?php } ?> />
                    <?php echo t('Display pagination interface if more items are available than are displayed.') ?>
                </label>
                <h3><?php echo t('Items per page') ?></h3>
                <label>
                    <input type="text" name="num" value="<?php echo $num ?>" style="width: 30px; display:inline-block">
                    <?php echo t('Number of items to paginate on.') ?>
                </label>
            </div>
        </div>
        <!-- /Tab Two -->
    </div>
    <!-- /Tab Content -->
</div>
<style>
    /*! heading */
    .block-instruction {
        color: black;
        font-size: 1.5em;
        font-family: sans-serif;
    }

    /*! namespace */
    #page-picker__wrapper {
        margin-top: 30px;
        margin-bottom: 30px;
    }

    /*! clearfix */
    #page-picker__wrapper .cf:before,
    #page-picker__wrapper .cf:after {
        content: " ";
        display: table;
    }

    #page-picker__wrapper .cf:after {
        clear: both;
    }

    /*! tab panel states */
    #page-picker__wrapper .tab-pane {
        display: none;
    }

    #page-picker__wrapper .tab-pane.active {
        display: block;
    }

    /*! picked pages relationship field wrapper */
    #page-picker__wrapper .relationship-wrapper {
        margin-bottom: 30px;
        padding-bottom: 0;
        border:0;
        border: 1px solid #DDD;
        padding: 5px;
        background-color: #F6F6F6;
    }

    /*! picked pages left column */
    #page-picker__wrapper .relationship-left {
        width: 55%;
        float: left;
        padding: 0.5em;
    }

    /*! picked pages left column datatable specific */
    #page-picker__wrapper .dataTables_filter {
        float: none;
        padding: 8px 10px;
        border: 1px solid #DDD;
        border-bottom: none;
        background-color: #F9F9F9;
    }

    #page-picker__wrapper .dataTables_filter label {
        text-transform: uppercase;
        font-weight: bold;
        line-height: 28px;
        margin: 0;
        text-align: left;
    }

    #page-picker__wrapper .dataTables_filter label input {
        float: right;
        width: 320px;
        border-radius: 14px;
        margin-bottom: 0;
    }

    #page-picker__wrapper table.dataTable {
        border-collapse: collapse;
        margin-bottom: 5px;
    }

    #page-picker__wrapper tbody,
    #page-picker__wrapper thead,
    #page-picker__wrapper tfoot {
        border: 1px solid #DDD;
        background-color: #F9F9F9;
    }

    #page-picker__wrapper th {
        border: none;
        padding: 8px 10px;
    }

    #page-picker__wrapper tr:hover {
        cursor: pointer;
    }

    #page-picker__wrapper td {
        background-color: white;
        color: #08C;
        padding: 8px 10px;
    }

    #page-picker__wrapper td.page-handle {
        color: #CCC;
    }

    #page-picker__wrapper tr:hover td {
        background-color: #F5F5F5;
    }

    #page-picker__wrapper tr:hover td.page-handle {
        background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEwAACxMBAJqcGAAAARhJREFUOI2l071KA0EUBeAvm4CxslXwDUSi1pZCwCKkSGlhpW8gPoOWKv5AQLAQrKxs7BVUBLW0E4Jgk1RqodFiNrLG2RDwwBR7zr1zz5mZ5S9msYd3XKTrFbuoROp/MIomDjEV0adxhH2UY823WBw0IUUNV/2bNHOaq1iI8HUhpkTIXMRZpHAC4xH+FGOoJFjF5hDW+7GBFcJp52EZSzlaAZ2ScHg9VAXbPcyji1KGa+EcX3jMCvCJj8x3N4fLuvD2jwjtIibxhJdI0Uw68T6izaGYCPe5NsBFHtZxkOBOiFGLFLXwHOEbqeOHHlEWnmd9iMkNXGKkXygLcY6F11nIaAUh8wm2Y81ZVLCDDq5xgza2hD/yF74Bz8I4AWZ7GL8AAAAASUVORK5CYII=");
        background-position: right 10px center;
        background-repeat: no-repeat;
    }

    #page-picker__wrapper .dataTables_info {
        max-width: 41%;        
    }

    /*! picked pages right column */
    #page-picker__wrapper .relationship-right {
        padding: 1em;
        float: left;
        width: 45%;
        padding-top: 8px;
        padding-left: 0;
        box-sizing: border-box;
        padding-bottom: 0;
        padding: 0.5em;
        margin-left: -1px;
    }

    /*! picked pages count */
    #page-count {
        height: 60px;
        line-height: 40px;
        padding: 10px;
        border: 1px solid #DDD;
        border-bottom: none;
        background-color: #F9F9F9;
    }

    /*! picked pages list */
    #page-picker__wrapper .relationship-list {
        background-color: white;
        min-height: 331px;
        border: 1px solid #DDD;
        list-style-type: none;
        margin-left: 0;
        padding-left: 0;
        position: relative;
    }

    /*! picked pages list items */
    #page-picker__wrapper .relationship-list__item {
        width: 100%;
        padding: 6px 10px 5px 25px;
        background-color: #FFFFFF;
        border-bottom: 1px solid #DDD;
        position: relative;
        cursor: move; /* fallback if grab cursor is unsupported */
        cursor: grab;
        cursor: -moz-grab;
        cursor: -webkit-grab;
    }

    /*! picked pages list items sorting state */
    #page-picker__wrapper .relationship-list__item.ui-sortable-helper {
        background-color: #f1f1f1;
        min-width: 100%;
        min-height: 29px;
        border-top: 1px solid #ddd;
        cursor: -webkit-grabbing;
        cursor: -moz-grabbing;
    }

    /*! picked pages list items icons */
    #page-picker__wrapper .relationship-list__item .ui-icon {
        color: white;
        font-size: 16px;
        margin-top: -8px;
    }

    #page-picker__wrapper .relationship-list__item .ui-icon-arrowthick-2-n-s {
        position: absolute;
        top: 50%;
        left: 10px;
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAPCAYAAAAs9AWDAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNS1jMDIxIDc5LjE1NTc3MiwgMjAxNC8wMS8xMy0xOTo0NDowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QzU4MzA5Nzk2Q0U0MTFFNEE2MTRBNTM5RTExQTU2M0MiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QzU4MzA5N0E2Q0U0MTFFNEE2MTRBNTM5RTExQTU2M0MiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpDNTgzMDk3NzZDRTQxMUU0QTYxNEE1MzlFMTFBNTYzQyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpDNTgzMDk3ODZDRTQxMUU0QTYxNEE1MzlFMTFBNTYzQyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PjG6qZYAAAA2SURBVHjaYmBAgP8wBiO6AEiMEU0AA8AlWf7//w/Vw8gAYzNh00I3QQZc3sTwEVa/Y9UOEGAAOvwODL6RS00AAAAASUVORK5CYII=')!important;
        background-position: 0 0;
    }

    #page-picker__wrapper .relationship-list__item .ui-icon-circle-close {
        position: absolute;
        top: 50%;
        right: 15px;
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEwAACxMBAJqcGAAAAQtJREFUOI2N0rFKA0EYBOAvsTBWvoLmTCUhxEaCb2AhKXyzKCZCQEghpLLyAVQUjPgqplIrtdg92dytkoGDu52b/2dmhzr6GOMTj/F5xwV6mf9/sYUprrCf4buYYYJWTvyK4/82RJzguTpkuqa4xFCwqSl43sBtJI9QZEQdDOL7DbbFTMZWPRd4qAzZi2c7yVkf54S0q0iHFLiviKGBJeGaciiH5MQlFs0/iBLf+IrbcmjAR4ZIPe/iDu2M+I3QsG5CdNQDawtW0iEHGBGuYpYQA3nPBQ6T73m6+FJo2Lo4xVl60BLqOVxT/ITNKtESSnUtlCRNviF4nsfNNXGKntCwJRZ4EdIeWQ0b/ACAHzFql1M32wAAAABJRU5ErkJggg==') !important;
        background-position: 0 0;
    }
</style>
