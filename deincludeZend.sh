#!/bin/bash
echo "Removing require_once from the Zend Library"
cd vendor/zendframework/zendframework1/library/Zend
find . -name '*.php' -not -path '*/Loader/Autoloader.php' -not -path '*/Application.php' -print0 | xargs -0 sed --regexp-extended --in-place 's/(require_once)/\/\/ \1/g'

echo "Removing require_once from the Zend Extras Library"
cd ../../extras/library/ZendX
find . -name '*.php' -not -path '*/Loader/Autoloader.php' -not -path '*/Application.php' -print0 | xargs -0 sed --regexp-extended --in-place 's/(require_once)/\/\/ \1/g'

echo "Finished removing the require_once statements from Zend"
