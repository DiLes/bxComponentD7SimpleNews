<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

\Bitrix\Main\Loader::includeModule('iblock');

use Bitrix\Main\ORM\Query;
use Bitrix\Main\Entity;
class BxDocuments extends CBitrixComponent
{

    public function onPrepareComponentParams($params)
    {
        if (!$params['IBLOCK_ID']) {
            die('IBLOCK_ID is required');
        }
        return $params;
    }

    protected function sections()
    {
        $sections = \Bitrix\Iblock\SectionTable::getList([
            'filter' => [
                'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                'ACTIVE' => 'Y',
            ],
            'cache' => [
                'ttl' => 3600
            ],
        ])->fetchAll();

        return $sections;
    }


    protected function elements($sectionId)
    {
        $elements = \Bitrix\Iblock\ElementTable::getList([
            'filter' => [
                'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                'ACTIVE' => 'Y',
                'IBLOCK_SECTION_ID' => $sectionId
            ],
            'select' => [
                '*',
                'TYPE_REPORT',
                'TYPE_REPORT_VALUE_ENUM'
            ],
            'cache' => [
                'ttl' => 3600
            ],
            'runtime' => [
                new Entity\ReferenceField(
                    'TYPE_REPORT',
                    \Bitrix\Iblock\ElementPropertyTable::class,
                    Query\Join::on('this.ID', 'ref.IBLOCK_ELEMENT_ID')->where('ref.IBLOCK_PROPERTY_ID', '18')->whereNotNull("ref.IBLOCK_ELEMENT_ID")
                ),
                new Entity\ReferenceField(
                    'TYPE_REPORT_VALUE_ENUM',
                    \Bitrix\Iblock\PropertyEnumerationTable::class,
                    Query\Join::on('this.TYPE_REPORT.VALUE', 'ref.ID')->whereNotNull("ref.ID")
                ),
                /*new \Bitrix\Main\Entity\ReferenceField(
                    'PROPERTY_CENTER',
                    Bitrix\Iblock\ElementPropertyTable::class,
                    \Bitrix\Main\Entity\Query\Join::on('this.ID', 'ref.IBLOCK_ELEMENT_ID')
                        ->where('ref.IBLOCK_PROPERTY_ID', '2')
                        ->whereNotNull("ref.IBLOCK_ELEMENT_ID")
                ),*/
                /*new \Bitrix\Main\Entity\ReferenceField(
                    'CENTER',
                    \Bitrix\Iblock\ElementTable::class,
                    \Bitrix\Main\Entity\Query\Join::on('this.PROPERTY_CENTER_ID', 'ref.ID')->whereNotNull("ref.ID")
                ),*/
            ]

        ])->fetchAll();


        foreach ($elements as $arElement){
            if ($arElement['ACTIVE_FROM']){
                $arElement['ACTIVE_FROM']->format('Y');
            }
            //echo '<pre>'; print_r($arIblockProp); echo '</pre>';
        }
        return $elements;
    }

    public function executeComponent()
    {
        $this->arResult = [
            'SECTIONS' => $this->sections(),
            'TYPE_REPORT' => '',
            'ELEMENTS' => $this->elements($this->request->get('sectionId') ?? $this->arParams['CURRENT_SECTION_ID']),
        ];
        $this->includeComponentTemplate();
    }
}
