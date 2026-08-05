# Production Dockerfile for Liventra Cloud Platform
# Optimized for 1-Click Coolify / VPS Deployment

FROM node:18-alpine AS runner
WORKDIR /app

# Install system utilities (curl and wget for health checks)
RUN apk add --no-cache curl wget

# Install dependencies
COPY package.json ./
RUN npm install --production

# Copy application source
COPY . .

EXPOSE 3000

ENV NODE_ENV=production
ENV PORT=3000

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=5 \
  CMD curl -f http://127.0.0.1:3000/health || exit 1

CMD ["npm", "start"]
