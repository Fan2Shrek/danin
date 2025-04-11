FROM node:20-alpine AS base

FROM base AS main

COPY . /app
WORKDIR /app


FROM main AS dev

RUN npm install

CMD ["npm", "run", "dev"]

FROM main AS prod
