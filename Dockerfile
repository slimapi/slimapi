FROM slimapi/nginx-php:7.4.9

ADD .docker /
ADD . ${PROJECT_ROOT}
