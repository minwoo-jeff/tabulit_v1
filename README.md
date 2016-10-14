# PROD Deployment Checklist #

## Standard Symfony2 Deployment Practices: ##

========================================

1. export SYMFONY_ENV=prod
2. composer install --no-dev --optimize-autoloader
3. php app/console cache:clear --env=prod --no-debug
4. php app/console assetic:dump --env=prod --no-debug

## For Tabulit: ##
========================================


1. Re-upload param.yml, config.yml, security.yml
2. Run php composer.phar require ircmaxell/password-compat for hashes
3. mkdir uploads in web/bundles/app/ directory