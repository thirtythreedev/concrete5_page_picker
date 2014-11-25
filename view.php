<?php
defined('C5_EXECUTE') or die("Access Denied.");
$rssUrl = $showRss ? $controller->getRssUrl($b) : '';
$th = Loader::helper('text');
?>
<div class="ccm-page-list">
    <?php foreach ($pages as $page):
        // Prepare data for each page being listed...
        $title = $th->entities($page->getCollectionName());
        $url = $nh->getLinkToCollection($page);
        $target = ($page->getCollectionPointerExternalLink() != '' && $page->openCollectionPointerExternalLinkInNewWindow()) ? '_blank' : $page->getAttribute('nav_target');
        $target = empty($target) ? '_self' : $target;
        $description = $page->getCollectionDescription();
        $description = $controller->truncateSummaries ? $th->wordSafeShortText($description, $controller->truncateChars) : $description;
        $description = $th->entities($description);
        /* End data preparation. */

        /* The HTML from here through "endforeach" is repeated for every item in the list... */ ?>
        <h3 class="ccm-page-list-title">
            <a href="<?php echo $url ?>" target="<?php echo $target ?>"><?php echo $title ?> <?php echo $page->cID; ?></a>
        </h3>
        <div class="ccm-page-list-description">
            <?php echo $description ?>
        </div>
    <?php endforeach; ?>
    <?php if ($showPagination): ?>
        <div id="pagination">
            <div class="ccm-spacer"></div>
            <div class="ccm-pagination">
                <span class="ccm-page-left"><?php echo $paginator->getPrevious('&laquo; ' . t('Previous')) ?></span>
                <?php echo $paginator->getPages() ?>
                <span class="ccm-page-right"><?php echo $paginator->getNext(t('Next') . ' &raquo;') ?></span>
            </div>
        </div>
    <?php endif; ?>
</div><!-- end .ccm-page-list -->

