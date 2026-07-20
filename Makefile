build: update-php-lib update-js-lib

test:
	./tests/session-lifecycle.sh

update-php-lib:
	composer update --prefer-dist

update-js-lib:
	./node_modules/gulp/bin/gulp.js
