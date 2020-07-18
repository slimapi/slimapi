FROM slimapi/nginx-php:7.4.8

ADD .docker /
ADD . ${PROJECT_ROOT}
