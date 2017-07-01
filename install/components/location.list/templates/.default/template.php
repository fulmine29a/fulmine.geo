<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>
<div class="jqmWindow-city-select jqmWindow" id="city-select">
    <a href="#" class="popup-window-close-icon popup-window-titlebar-close-icon jqmClose"><i></i></a>
    <div class="po-content">
        <div class="jqmWindow-city-select_caption">Выберите местоположение</div>
        <noindex>
        <div class="jqmWindow-city-select_body">
            <?foreach ($arResult['ITEMS'] as $item):?>
                <div class="jqmWindow-city-select_city">
                    <a href="<?=$item['URL']?>"><?=$item['CITY_NAME']?></a>
                </div>
            <?endforeach;?>
        </div>
        </noindex>
    </div>
</div>