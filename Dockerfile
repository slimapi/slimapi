ARG BASE_IMAGE
FROM ${BASE_IMAGE}

ADD composer.json ${PROJECT_ROOT}
RUN composer.sh install --prefer-dist && \
    composer.sh dump-autoload --optimize && \
    composer.sh clear-cache --quiet

ADD .docker /
ADD . ${PROJECT_ROOT}
