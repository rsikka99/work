#!/bin/bash
cd vendor/zendframework/zendframework1/library/Zend
find . -name '*.php' -not -path '*/Loader/Autoloader.php' -not -path '*/Application.php' -print0 | xargs -0 sed --regexp-extended --in-place 's/(require_once)/\/\/ \1/g'
cd ../../extras/library/ZendX
find . -name '*.php' -not -path '*/Loader/Autoloader.php' -not -path '*/Application.php' -print0 | xargs -0 sed --regexp-extended --in-place 's/(require_once)/\/\/ \1/g'