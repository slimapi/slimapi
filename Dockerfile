FROM slimapi/nginx-php:7.4.7

ADD .docker /
ADD . ${PROJECT_ROOT}
