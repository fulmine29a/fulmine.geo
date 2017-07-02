<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
    "GROUPS" => array(
        "BASE" => array(
            "NAME" => 'Файл'
        ),
    ),
    "PARAMETERS" => array(
        "FILE_NAME" => array(
            "PARENT" => "BASE",
            "NAME" => 'Имя включаемого файла',
            "TYPE" => "STRING",
            'DEFAULT' => 'include.php'
        ),
        'RECURSIVE' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Искать в вышележаших каталогах',
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ),
        'CACHE_TIME' => array(
        )
    )
);