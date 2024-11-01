.PHONY: phpstan, csfix

phpstan:
	php ./vendor/bin/phpstan analyse --level=5 ./src/

csfix:
	php ./vendor/bin/php-cs-fixer fix ./src