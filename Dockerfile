FROM slimapi/docker:7.4.6

ENV PROJECT_ROOT="/var/www"
WORKDIR ${PROJECT_ROOT}

ADD .docker /
ADD . ${PROJECT_ROOT}
