FROM yejune/webserver:7.1.4e

ARG BUILD_NUMBER

ENV BUILD_NUMBER ${BUILD_NUMBER:-v0.0.1}

HEALTHCHECK --interval=30s --timeout=3s --retries=3 CMD wget -qO- localhost/healthcheck || exit 1

COPY ./ /var/www/
