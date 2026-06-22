build: update-php-lib update-js-lib

update-php-lib:
	composer update --prefer-dist

update-js-lib:
	./node_modules/gulp/bin/gulp.js
