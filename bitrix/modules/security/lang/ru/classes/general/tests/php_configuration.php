<?
$MESS["SECURITY_SITE_CHECKER_PhpConfigurationTest_NAME"] = "Проверка настроек PHP";
$MESS["SECURITY_SITE_CHECKER_PHP_ENTROPY"] = "Не установлен дополнительный источник энтропии при создании идентификатора сессии";
$MESS["SECURITY_SITE_CHECKER_PHP_ENTROPY_DETAIL"] = "Отсутствие дополнительной энтропии может использоваться для предугадывания случайных чисел";
$MESS["SECURITY_SITE_CHECKER_PHP_ENTROPY_RECOMMENDATION"] = "Необходимо в настройках php указать:<br>session.entropy_file = /dev/urandom<br>session.entropy_length = 128";
$MESS["SECURITY_SITE_CHECKER_PHP_INCLUDE"] = "Разрешено подключение файлов по URL (URL wrappers)";
$MESS["SECURITY_SITE_CHECKER_PHP_INCLUDE_DETAIL"] = "Эта опция php крайне противопоказана";
$MESS["SECURITY_SITE_CHECKER_PHP_INCLUDE_RECOMMENDATION"] = "Необходимо в настройках php указать:<br>allow_url_include = Off";
$MESS["SECURITY_SITE_CHECKER_PHP_FOPEN"] = "Разрешено чтение файлов по URL (URL wrappers)";
$MESS["SECURITY_SITE_CHECKER_PHP_FOPEN_DETAIL"] = "Если эта, сомнительная, возможность PHP не требуется - рекомендуется отключить, т.к. она может стать отправной точкой для различного типа атак";
$MESS["SECURITY_SITE_CHECKER_PHP_FOPEN_RECOMMENDATION"] = "Необходимо в настройках php указать:<br>allow_url_fopen = Off";
$MESS["SECURITY_SITE_CHECKER_PHP_ASP"] = "Включено использование тегов в стиле ASP";
$MESS["SECURITY_SITE_CHECKER_PHP_ASP_DETAIL"] = "Многие разработчики не догадываются о существовании подобной опции, а как следствие могут не учесть её в различных проверках";
$MESS["SECURITY_SITE_CHECKER_PHP_ASP_RECOMMENDATION"] = "Необходимо в настройках php указать:<br>asp_tags = Off";
$MESS["SECURITY_SITE_CHECKER_LOW_PHP_VERSION_ENTROPY"] = "Версия php устарела";
$MESS["SECURITY_SITE_CHECKER_LOW_PHP_VERSION_ENTROPY_DETAIL"] = "Текущая версия php не поддерживает установку дополнительного источника энтропии при создании идентификатора сессии";
$MESS["SECURITY_SITE_CHECKER_LOW_PHP_VERSION_ENTROPY_RECOMMENDATION"] = "Обновить версию php как минимум до 5.3.3, но лучше до последней стабильной";
?>