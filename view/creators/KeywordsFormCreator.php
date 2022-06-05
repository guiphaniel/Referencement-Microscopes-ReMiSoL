<?php
    include_once(__DIR__ . "/../../config/config.php");
    include_once(__DIR__ . "/FormCreator.php");

    Class KeywordsFormCreator extends FormCreator {
        function __construct() {
            parent::__construct("/processing/keywords_form_processing.php", "POST");
        }

        public function createBody() {            
            $keywordService = KeywordService::getInstance();
            $cats = $keywordService->findAllCategories(); ?>
            <div id="cats-wrapper" data-next-cat-id="<?php $ids = array_keys($cats); echo empty($ids) ? 0 : max($ids) + 1; ?>">
                <?php 
                foreach($cats as $catId => $cat): 
                    $tags = $keywordService->findAllTags($cat)?>
                    <div>
                        <input class="kw-cat" type="text" name="cats[<?=$catId?>]" value="<?=$cat->getName()?>">
                        <div class="rm-bt"></div>
                        <div data-next-tag-id="<?php $ids = array_keys($tags); echo empty($ids) ? 0 : max($ids) + 1; ?>" data-cat-id=<?=$catId?>>
                            <?php
                            foreach($tags as $tagId => $tag): ?>
                                <div>
                                    <input class="kw-tag" type="text" name="keywords[<?=$catId?>][<?=$tagId?>]" value="<?=$tag?>">
                                    <div class="rm-bt"></div>
                                </div>
                            <?php
                            endforeach; 
                            ?>
                            <div class="add-bt add-tag">t</div>
                        </div>
                    </div>
                <?php
                endforeach; ?>
                <div class="add-bt add-cat">c</div>
            </div>
            <input type="submit" class="bt">
            <?php
        }
    }