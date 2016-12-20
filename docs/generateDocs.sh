./vendor/phpdocumentor/phpdocumentor/bin/phpdoc  -d ../src/ -t xml/ --visibility="public" --template="xml"
./vendor/evert/phpdoc-md/bin/phpdocmd  xml/structure.xml . --index README.md
rm -rf xml