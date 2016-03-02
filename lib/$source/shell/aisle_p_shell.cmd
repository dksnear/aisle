echo off
cd "%~d0"
cd "%~dp0"
cd ../../../
set root=%cd%
set ctrl=%~1
set act=%~2
set php=%~3
cd "%root%\project\shell"
if not defined php set php=php
"%php%" -f "%root%\proj\shell\AppProgram.php" "AISLE_PROJECT=shell&AISLE_CONTROLLER=%ctrl%&AISLE_ACTION=%act%"
