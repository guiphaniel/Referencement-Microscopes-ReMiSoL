<?php
    include_once(__DIR__ . "/../../config/config.php");
    include_once(__DIR__ . "/FormCreator.php");

    Class KeywordsFormCreator extends FormCreator {
        function __construct() {
            parent::__construct("/processing/keywords_form_processing.php", "POST", bigForm:true);
        }

        public function createBody() {            
            $keywordService = KeywordService::getInstance();
            $cats = $keywordService->findAllCategories(); ?>
            <div class="wrapper" data-next-cat-id="<?php $ids = array_keys($cats); echo empty($ids) ? 0 : max($ids) + 1; ?>">
                <?php 
                foreach($cats as $catId => $cat): 
                    $tags = $keywordService->findAllTags($cat)?>
                    <fieldset>
                        <?php $this->createInput("cat-$catId", "cats[$catId]", "Catégorie", $cat->getName(), class:"ucfirst") ?>
                        <fieldset data-next-tag-id="<?php $ids = array_keys($tags); echo empty($ids) ? 0 : max($ids) + 1; ?>" data-cat-id=<?=$catId?>>
                            <?php
                            foreach($tags as $tagId => $tag): ?>
                                <?php $this->createInputRm("tag-$tagId", "keywords[$catId][$tagId]", "Etiquette", $tag, class:"ucfirst") ?>
                            <?php
                            endforeach; 
                            ?>
                            <div class="bt add-bt add-tag">Ajouter une étiquette</div>
                        </fieldset>
                        <div class="bt rm-bt">Supprimer la catégorie</div>
                    </fieldset>
                <?php
                endforeach; ?>
                <div class="bt add-bt add-cat">Ajouter une catégorie</div>
            </div>
            <input type="submit" class="bt">
            <?php
        }
    }