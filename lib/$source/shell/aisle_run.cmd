@echo off
::setlocal enabledelayedexpansion
cd "%~d0"
cd "%~dp0"
cd ../../../
set root=%cd%
set proj=%~1
set module=%~2
set ctrl=%~3
set act=%~4
set qs=%~5
set php=%~6
cd "%root%\proj\%proj%"
if not defined php set php=php
if "%module%" == "-" set module=
if not defined module (
	"%php%" -f "%root%\proj\%proj%\AppProgram.php" "AISLE_PROJECT=%proj%&AISLE_CONTROLLER=%ctrl%&AISLE_ACTION=%act%%qs%"
) else (
	"%php%" -f "%root%\proj\%proj%\AppProgram.php" "AISLE_PROJECT=%proj%&AISLE_MODULE=%module%&AISLE_CONTROLLER=%ctrl%&AISLE_ACTION=%act%%qs%"	
)