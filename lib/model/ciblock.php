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
            print_r($row);
            $count = 0;
            foreach (array_intersect_key($row, $filterKeyValues) as $value)
                if(!empty($value))
                    $count++;

            var_dump($count, $needCount);

            if($count > $countFound or $countFound === 0){
                $countFound = $count;
                $found = $row;

                if($count === $needCount)
                    break;
            }
        }

        if($found){
            for(
                $res =\CIBlockElement::GetProperty(
                    $found['IBLOCK_ID'],
                    $found['ID']
                );
                $row = $res->Fetch();
            ){
                $result[$row['CODE']] = $row['VALUE'];
            };

            if(empty($result))
                throw new \Exception('empty location fields');

            return $this->fabric->createLocation($result);
        }else
            throw new \Exception('Location not found, iblock ok? iblock filled?');
    }

    protected function parseFilterNames(array &$filter){
        return array_flip(array_map(function($n){return 'PROPERTY_'.$n; }, array_flip($filter)));
    }
}