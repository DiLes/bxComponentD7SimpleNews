# bxComponentD7SimpleNews
Bitrix Component D7 simplenews.comp

I. файлы нужно разместить /bitrix/components/ либо /local/components/

II. В ИБ нужно создать свойство:
  a. Название: Тип отчета
  b. Тип: Список
  c. Символьный код: TYPE_REPORT

III. Вызов компонента:

<?$APPLICATION->IncludeComponent(
	"diles:documents",
	"",
	Array(
		"CURRENT_SECTION_ID" => "2",
		"IBLOCK_ID" => "2"
	)
);?>
