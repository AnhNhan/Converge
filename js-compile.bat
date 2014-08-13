@echo off
set DIRECTORY=%~dp0

REM Delete old build artifacts, since r.js will have trouble reading old main.js
del %DIRECTORY%\cache\main.js %DIRECTORY%\cache\main-dist.js

php "%DIRECTORY%\src\AnhNhan\Converge\cli.php" rsrc:compile && r.js.cmd -o build.js optimize=none
REM && uglifyjs cache/main-dist.js --comments -c -m -o cache/main.js && uglifyjs cache/libs-pck --comments -c -m -o cache/libs-pck
