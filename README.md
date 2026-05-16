# IncidentPulse Platform

IncidentPulse is a portfolio-grade internal operations platform focused on incident lifecycle management, async workflows, and production-oriented engineering decisions.

## Repository Layout

- backend: Laravel 11 modular monolith API.
- frontend: Vue 3 + Vite + TypeScript + Tailwind application.
- infra: infrastructure helper files.
- docs: architecture, API contracts, and runbooks.
- docker-compose.yml: local infra services (PostgreSQL, Redis, MinIO).

## Weekend Scope

- Backend modules: User, Incident, Attachment, AI, Notification, Audit, Shared.
- Async event flow: Action -> Event -> Queue Jobs/Listeners.
- Operational visibility: Horizon, structured logs, health endpoint, failed jobs, Sentry.
- Frontend MVP: dashboard, incident list/detail, create incident, attachments.

## Quick Start

1. Start infrastructure from repository root:

	docker compose up -d postgres redis minio

2. Follow backend and frontend setup guides:

	- docs/runbook-local.md
	- backend/README.md
	- frontend/README.md

## Documentation

- Architecture: docs/architecture.md
- API MVP contract: docs/api-mvp.md
- Local runbook: docs/runbook-local.md
