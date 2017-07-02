<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arComponentDescription = array(
    "NAME" => 'Включаемая область в зависимости от локации',
    "DESCRIPTION" => 'Включает файл в зависимости от текушего местоположения',
//    "ICON" => "/images/icon.gif",
    "PATH" => array(
        "ID" => "content",
        "CHILD" => array(
            "ID" => "fulmine.geo",
            "NAME" => "Включаемая область"
        )
    ),
   /* "AREA_BUTTONS" => array(
        array(
            'URL' => "javascript:alert('Это кнопка!!!');",
            'SRC' => '/images/button.jpg',
            'TITLE' => "Это кнопка!"
        ),
    ),*/
    "CACHE_PATH" => "Y"
);