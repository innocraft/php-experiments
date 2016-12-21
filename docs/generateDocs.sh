./vendor/phpdocumentor/phpdocumentor/bin/phpdoc  -d ../src/ -t xml/ --template="xml"
./vendor/evert/phpdoc-md/bin/phpdocmd xml/structure.xml . --index="README.md"
rm -rf xml