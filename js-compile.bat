@echo off
set DIRECTORY=%~dp0
php "%DIRECTORY%\src\AnhNhan\ModHub\cli.php" rsrc:compile && r.js.cmd -o build.js optimize=none && uglifyjs dist/main.js --comments -c -m -o dist/main.min.js && uglifyjs cache/libs-pck --comments -c -m -o cache/libs-pck
