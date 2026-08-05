# Production Dockerfile for Liventra Cloud Platform
# Optimized for 1-Click Coolify / VPS Deployment

FROM node:18-alpine AS runner
WORKDIR /app

# Install dependencies
COPY package.json ./
RUN npm install --production

# Copy application source
COPY . .

EXPOSE 3000

ENV NODE_ENV=production
ENV PORT=3000

HEALTHCHECK --interval=30s --timeout=5s --start-period=5s --retries=3 \
  CMD wget --no-verbose --tries=1 --spider http://localhost:3000/health || exit 1

CMD ["npm", "start"]
