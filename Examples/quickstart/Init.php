<?php

session_start();
date_default_timezone_set('Europe/Berlin');
header("charset=utf-8");
mb_internal_encoding("UTF-8");

Bit::setPathOfAlias('App', __DIR__.DS.'App');
Bit::setPathOfAlias('Themes', __DIR__.DS.'Themes');
Bit::setPathOfAlias('Wrapper', __DIR__.DS.'Wrapper');
Bit::setPathOfAlias('Pages', __DIR__.DS.'Pages');

Bit::using('App.*'); //loading first ;)
Bit::using('Bit.Base.*'); // Helper for Components

Bit::IncludeAll("App.Config.*");