<?php
defined('C5_EXECUTE') or die("Access Denied.");
$rssUrl = $showRss ? $controller->getRssUrl($b) : '';
$th = Loader::helper('text');
//$ih = Loader::helper('image'); //<--uncomment this line if displaying image attributes (see below)
//Note that $nh (navigation helper) is already loaded for us by the controller (for legacy reasons)
?>

<div class="ccm-page-list">
    <?php

        // Get a list of all viewable pages
//        $home = Page::getByID(1);
//        $homeStr = $home->getCollectionPath();
//
//        $pl = new PageList();
//        $pl->filterByPath($homeStr, TRUE);
//        $pl->ignoreAliases();
//
//        $allowedPages = (array) $pl->get();
//        $perm = new Permissions($home);
//        if($perm->canRead()) array_unshift($allowedPages, $home);

    ?>
</div>


