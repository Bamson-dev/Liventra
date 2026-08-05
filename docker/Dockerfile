# Multi-stage Dockerfile for Liventra Cloud Platform (docker/Dockerfile)
# Deployable directly on Contabo/Hetzner VPS using Coolify

FROM node:18-alpine AS base
WORKDIR /app

# Copy Monorepo Dependencies
COPY package.json ./
COPY services/api/package.json ./services/api/
RUN npm install --production

# Copy Application Source Code
COPY . .

EXPOSE 3000

ENV NODE_ENV=production
ENV PORT=3000

CMD ["node", "services/api/server.js"]
