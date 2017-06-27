<?php
/**
 * Created by PhpStorm.
 * User: fulmine
 * Date: 21.06.2017
 * Time: 22:32
 */

namespace Fulmine\Geo\Model;


class CIBlock implements IModel
{
    /** @var array $iblockFilter фильтр для выборки из инфоблока */
    private $iblockFilter = [];

    /** @var \Fulmine\Geo\Location\IFabric $fabric фабрика для создания локаций  */
    private $fabric;

    function __construct(\Fulmine\Geo\Location\IFabric $fabric, $iblockCode, $iblockId = null){

        $this->fabric = $fabric;

        if(@$iblockCode)
            $this->iblockFilter = ['IBLOCK_CODE' => $iblockCode];
        elseif(@$iblockId)
            $this->iblockFilter = ['IBLOCK_ID' => $iblockId];
        else
            throw new \Exception('iblock not defined');
    }

    /**
     * @param array $filter
     * @return \Fulmine\Geo\Location\ILocation
     * @throws \Exception
     */
    function getLocationByFilter(array $filter)
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $filter = $this->parseFilterNames($filter);

        $filterKeys = array_keys($filter);

        $iblockFilter = ['ACTIVE' => 'Y'];

        foreach ($filterKeys as $filterKey)
            $iblockFilter[] = array(
                'LOGIC' => 'OR',
                [
                    $filterKey => $filter[$filterKey]
                ],
                [
                    $filterKey => false
                ]
            );

        $iblockFilter = array_merge($iblockFilter, $this->iblockFilter);

        $filterKeyValues = array_flip(array_map(function ($n){return $n.'_VALUE';}, $filterKeys));

        $countFound = 0;
        $needCount = count($filterKeys);

        $found = false;

        for(
            $res = \CIBlockElement::GetList(
                array('SORT' => 'ASC'),
                $iblockFilter,
                false,
                false,
                array_merge(array(
                    'ID',
                    'IBLOCK_ID'
                    ), $filterKeys
                )
            );
            $row = $res->Fetch();
        ){
            $count = 0;
            foreach (array_intersect_key($row, $filterKeyValues) as $value)
                if(!empty($value))
                    $count++;


            if($count > $countFound or $countFound === 0){
                $countFound = $count;
                $found = $row;

                if($count === $needCount)
                    break;
            }
        }

        if($found){
            return $this->createLocation(
                $found['ID'],
                $found['IBLOCK_ID']
            );
        }else
            throw new \Exception('Location not found, iblock ok? iblock filled?');
    }

    function getById($id)
    {
        if($row = \CIBlockElement::GetList(
            false,
            array('ID' => $id)+$this->iblockFilter,
            false,
            false,
            array(
                'IBLOCK_ID'
            )
        )->Fetch()) {
            return $this->createLocation($id, $row['IBLOCK_ID']);
        }else
            throw new \Exception("Location by id $id not found");

    }

    protected function createLocation($id, $iblockId){
        for (
            $res = \CIBlockElement::GetProperty(
                $iblockId,
                $id
            );
            $row = $res->Fetch();
        ) {
            if($row['MULTIPLE'] === 'Y')
                $result[$row['CODE']][] = $row['VALUE'];
            else
                $result[$row['CODE']] = $row['VALUE'];
        };

        if (empty($result))
            throw new \Exception('empty location fields');

        $result['ID'] = $id;

        return $this->fabric->createLocation($result);
    }

    protected function parseFilterNames(array &$filter){
        return array_flip(array_map(function($n){return 'PROPERTY_'.$n; }, array_flip($filter)));
    }

    /**
     * @param int $id
     * @return \Fulmine\Geo\Location\ILocation
     */
}