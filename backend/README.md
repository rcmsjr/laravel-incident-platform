# Backend (Laravel 11)

This directory hosts the IncidentPulse backend as a modular monolith.

## Planned Module Root

- app/Modules/Shared
- app/Modules/User
- app/Modules/Incident
- app/Modules/Attachment
- app/Modules/AI
- app/Modules/Notification
- app/Modules/Audit

## Immediate next implementation targets

- [ ] Scaffold Laravel 11 in this directory.
- [ ] Add migrations for incidents, attachments, notifications, audit logs.
- [ ] Implement CreateIncidentAction and IncidentCreated event pipeline.
- [ ] Wire Redis queue + Horizon + Sentry.
