FROM node:26-alpine AS base

FROM base AS main

WORKDIR /app
COPY package*.json ./

FROM main AS deps

RUN npm install


FROM deps AS dev

CMD ["npm", "run", "dev"]


FROM main AS build

ARG NEXT_PUBLIC_API_URL
ARG NEXT_PUBLIC_MERCURE_URL
ENV NEXT_PUBLIC_API_URL=$NEXT_PUBLIC_API_URL
ENV NEXT_PUBLIC_MERCURE_URL=$NEXT_PUBLIC_MERCURE_URL

COPY --from=deps /app/node_modules ./node_modules
COPY . .
RUN npm run build


FROM base AS prod

WORKDIR /app
ENV NODE_ENV=production

COPY --from=build /app/public ./public
COPY --from=build /app/.next ./.next
COPY --from=build /app/node_modules ./node_modules
COPY --from=build /app/package.json ./package.json

EXPOSE 3000
CMD ["npm", "run", "start"]
