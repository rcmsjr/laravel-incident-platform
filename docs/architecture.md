# IncidentPulse Architecture (Weekend MVP)

## Goals

- Build a portfolio-grade internal operations platform in 2 days.
- Keep architecture pragmatic and readable.
- Use async workflows where they provide clear value.

## System Style

- Modular monolith (single Laravel app, clear module boundaries).
- Event-driven workflows using Laravel events/listeners.
- Async processing with Redis queues + Horizon.

## Monorepo Layout

- backend: Laravel 11 API and async workers.
- frontend: Vue 3 + Vite + TypeScript + Tailwind UI.
- infra: Docker and infra helpers.
- docs: architecture, API contracts, runbooks.

## Backend Modules

- User: users and ownership.
- Incident: incident lifecycle and orchestration actions.
- Attachment: file upload + object storage.
- AI: summarization, categorization, tags.
- Notification: async delivery and retries.
- Audit: auditable event trail.
- Shared: cross-cutting conventions and helpers.

## Core Flow

Controller -> Action -> DB transaction -> Domain event -> Queue jobs/listeners

Example:

- CreateIncidentAction persists incident and dispatches IncidentCreated.
- Listeners trigger AI summary generation, notifications, and audit write.

## Data Design

- PostgreSQL as source of truth.
- UUID for main entities (users, incidents, attachments, notifications, audit logs).
- JSONB fields for tags/metadata where schema flexibility is useful.
- Add practical indexes for filtering and dashboard queries.

## Observability

- Health endpoint for app dependencies.
- Structured logs with contextual fields (incident_id, actor_id, correlation_id).
- Horizon queue visibility.
- Sentry error reporting for HTTP and queue workers.
- Failed jobs table and retry workflow.
