<?php

function umbrella_filter_ab_tests($content)
{
    $ab_tags = umbrella_get_ab_test_tags();
    foreach ($ab_tags as $counter => $ab_tag) {
        $counter = $counter + 1;
        for ($i = 1; $i < 10; $i++) {
            $tag_to_remove = "umbrella_ab_test" . $counter . "_variant" . $i . "";
            if ($tag_to_remove != $ab_tag) {
                $content = umbrella_content_fix($content, $tag_to_remove);
//                if ($tag_to_remove=='umbrella_ab_test3_variant1'){print_r($content);}
                $pattern_to_remove = "/<" . $tag_to_remove . ">[\s\S]+?<\/" . $tag_to_remove . ">/";
                $content = preg_replace($pattern_to_remove, "", $content);
            } else {
                $content = umbrella_content_fix($content, $ab_tag);
//                if ($tag_to_remove=='umbrella_ab_test3_variant2'){print_r($content);}
                $content = umbrella_clear_ab_tags($content,$ab_tag);
            }
        }
    }
    return $content;
}

function umbrella_clear_ab_tags($content, $ab_tag): string
{
    $fix = [
        sprintf("<%s>", $ab_tag) => "",
        sprintf("</%s>", $ab_tag) => "",
    ];
    return strtr($content, $fix);
}

function umbrella_content_fix($content, $ab_tag): string
{
    $fix = [
        sprintf("<p></%s><br />", $ab_tag) => sprintf("</%s>", $ab_tag),
        sprintf("<p><%s><br />", $ab_tag) => sprintf("<%s>", $ab_tag),
        sprintf("<p></%s></p>", $ab_tag) => sprintf("</%s>", $ab_tag),
        sprintf("<%s></p>", $ab_tag) => sprintf("<%s>", $ab_tag),
        sprintf("</%s><br />", $ab_tag) => sprintf("</%s>", $ab_tag),
        sprintf("<%s><br />", $ab_tag) => sprintf("<%s>", $ab_tag),
    ];
    return strtr($content, $fix);
}

// umbrella_ab_test1_variant2 - 12 - новые экраны страниц услуг
// umbrella_ab_test1_variant1 - 12 - старые экраны страниц услуг
// umbrella_ab_test2_variant1 - 8.3, 8.4 - блоки акций
// umbrella_ab_test3_variant2 - 8.7, 10 - Изменен первый, добавлены 2ой и 3ий слайды на баннере на главной.
// umbrella_ab_test3_variant1 - 10 - старый вариант первого слайда
// umbrella_ab_test4_variant2 - 2.1, 2.2 - новый вариант меню
//<umbrella_ab_test5_variant1> - 7 - Подписи гарантий на главной
function umbrella_get_ab_test_tags(): array
{
    if (($_SERVER["REMOTE_ADDR"] == "90.189.216.196") || is_user_logged_in() ) {
        return ['umbrella_ab_test1_variant2', 'umbrella_ab_test2_variant1','umbrella_ab_test3_variant2', 'umbrella_ab_test4_variant2','umbrella_ab_test5_variant2'];
    } else {
        return ['umbrella_ab_test1_variant1', 'umbrella_ab_test2_nothing', 'umbrella_ab_test3_variant1', 'umbrella_ab_test4_variant1','umbrella_ab_test5_variant1'];
    }
}