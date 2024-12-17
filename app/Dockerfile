FROM columbusinteractive/php:8.4-alpine

COPY --from=ghcr.io/roadrunner-server/roadrunner /usr/bin/rr /usr/local/bin/rr

WORKDIR /app
COPY . ./

RUN --mount=type=cache,target=/app/vendor \
    composer install --no-interaction --no-ansi --optimize-autoloader --prefer-dist

EXPOSE 80
CMD ["rr", "serve", "-c", ".rr.prod.yaml"]
