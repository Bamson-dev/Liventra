#!/bin/bash
# 1-Click Coolify / VPS Deployment Script for Liventra Cloud Platform

echo "⚡ Deploying Liventra Cloud Platform on VPS via Coolify..."

if ! command -v docker &> /dev/null
then
    echo "❌ Docker is not installed. Installing Docker..."
    curl -fsSL https://get.docker.com -o get-docker.sh
    sh get-docker.sh
fi

echo "🚀 Building Docker Containers..."
docker-compose -f docker/docker-compose.yml up -d --build

echo "✅ Liventra Cloud Platform Deployed Successfully!"
echo "🌐 API Endpoint: http://localhost/api/webinars"
echo "🌐 Health Check: http://localhost/health"
