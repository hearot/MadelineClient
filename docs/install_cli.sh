git clone https://github.com/hearot/MadelineClient
cd MadelineClient
wget "https://github.com/MacFJA/PharBuilder/releases/download/0.2.6/phar-builder.phar"
composer install
composer update
sudo rm -rf vendor/danog/madelineproto/old_docs
php -d phar.readonly=0 phar-builder.phar package --no-compression --entry-point=`pwd`/index.php --include-dev --name=ma.phar --output-dir=`pwd` --no-interaction composer.json
sudo chmod 755 ma.phar
sudo mv ma.phar /usr/local/bin/madeline
madeline -v
