<?php

session_start();
date_default_timezone_set('Europe/Berlin');
header("charset=utf-8");
mb_internal_encoding("UTF-8");

Bit::using('Bit.Base.*');
Bit::setPathOfAlias('App', __DIR__.DS.'App');
Bit::setPathOfAlias('Themes', __DIR__.DS.'Themes');
Bit::setPathOfAlias('Wrapper', __DIR__.DS.'Wrapper');
Bit::setPathOfAlias('Pages', __DIR__.DS.'Pages');

Bit::using('App.Lib.*');
Bit::using('App.Helper.*');

Bit::IncludeAll("App.Config.*");
$site = new Site();