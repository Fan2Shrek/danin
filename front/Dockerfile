FROM node:20-alpine AS base

FROM base AS main

WORKDIR /app
COPY package*.json ./

FROM main AS deps

RUN npm install


FROM deps AS dev

CMD ["npm", "run", "dev"]


FROM main AS build

ARG VITE_API_URL
ARG VITE_MERCURE_URL
ARG VITE_MATOMO_URL
ENV VITE_API_URL=$VITE_API_URL
ENV VITE_MERCURE_URL=$VITE_MERCURE_URL
ENV VITE_MATOMO_URL=$VITE_MATOMO_URL

COPY --from=deps /app/node_modules ./node_modules
COPY . .
RUN npm run build-only


FROM nginx:alpine AS prod

COPY ./.devops/default.conf /etc/nginx/conf.d/default.conf
COPY --from=build /app/dist /usr/share/nginx/html
