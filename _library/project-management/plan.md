🧭 Apollo Document + ICP Signing Platform
Definitive Engineering Roadmap

Architecture boundary:

apollo-doc → document lifecycle + editor + PDF

apollo-sign → certificate + signing

apollo-core → shared runtime

apollo-registry → lifecycle tracking

🧱 PHASE 0 — Platform Foundation Stabilization
Objective

Create a stable runtime skeleton aligned with registry lifecycle before any feature code.

Why this phase exists

Prevents plugin activation failures, dependency drift, and filesystem errors later.

Deliverables

Plugin bootstrap loaders

Registry detection

Storage managers

Logger integration

System health diagnostics

Detailed Tasks
Registry alignment

Detect apollo-registry.json

Validate plugin presence

Confirm dependency graph

Register plugin version

Add lifecycle hook

Environment validation

Verify PHP ≥ 8.2

Verify OpenSSL enabled

Verify upload directory writable

Verify filesystem permissions

Verify memory limits

Storage initialization

apollo-doc:

storage/documents

storage/pdfs

storage/tmp

apollo-sign:

storage/certs

storage/signed

storage/tmp

Logging bootstrap

Use Monolog

Create channels:

document

signing

system

security

Health endpoint

Create REST:

/apollo/v1/system/health

Returns:

registry detected

plugin versions

dependency status

openssl status

storage status

Exit Criteria

Plugins activate clean
Health endpoint OK
Registry shows modules

🧠 PHASE 1 — Identity Binding Layer
Objective

Bind documents to existing users using CPF as legal identifier.

Deliverables

CPF validator

User identity resolver

Permission mapping

Audit identity mapping

Detailed Tasks
CPF validation engine

Implement:

checksum mod11

format normalization

uniqueness verification

User resolver service

Uses:

get_current_user_id

get_user_meta

Returns:

user_id

cpf

full_name

email

Permission roles

Define logical roles:

signer

reviewer

admin

Identity audit schema

Fields:

user_id

cpf

ip

user_agent

timestamp

Exit Criteria

CPF validated
User identity resolved
Audit log working

📝 PHASE 2 — Document Core (Backend Only)
Objective

Create document lifecycle without UI to stabilize storage model.

Deliverables

Document entity

Versioning

Repository

Status machine

Document schema

id UUID

owner_id

cpf

title

content_html

status

version

created_at

updated_at

Status states

draft

locked

finalized

signed

Services

CreateDocument
UpdateDocument
GetDocument
ListDocuments
DeleteDocument

Storage strategy

Filesystem JSON:

storage/documents/{uuid}/v{version}.json

Exit Criteria

Documents created via API
Versions stored
Audit logs generated

🖊 PHASE 3 — Editor UI Layer
Objective

Provide rich editing UI.

Editor

Use TinyMCE (local only)

Features

Autosave

Draft indicator

Version preview

Word count

Sanitization

Security

HTML whitelist

Size limit 2MB

XSS filtering

Exit Criteria

User edits document
Draft saved
Versions increment

📄 PHASE 4 — PDF Generation Engine
Objective

Deterministic HTML → PDF conversion.

Engine

Use TCPDF

Optional import:

FPDI

Deliverables

PdfGeneratorService

Font loader

Metadata injection

Checksum generator

Storage

storage/pdfs/{document_id}/{version}.pdf

Exit Criteria

PDF generated
Checksum stored
Download endpoint works

🔐 PHASE 5 — Certificate Management
Objective

Handle A1 certificate lifecycle.

Certificate schema

id

owner_id

cpf

subject

issuer

fingerprint

valid_from

valid_to

encrypted_path

Features

Upload PFX

Password validation

Metadata extraction

Expiration check

Encryption AES256

Exit Criteria

Certificate stored
Metadata extracted
Expiration validated

✍️ PHASE 6 — Signing Engine
Objective

Digitally sign PDFs.

Signing method

PKCS7 via OpenSSL

Workflow

Load PDF
Load certificate
Embed signature
Timestamp
Store signed file

Storage

storage/signed/{document_id}/{version}.pdf

Audit event

pdf_signed

Exit Criteria

Signed PDF valid
Signature hash stored

🔗 PHASE 7 — Integration Flow
Objective

End-to-end pipeline.

Workflow

Document finalized
PDF generated
User selects certificate
Document signed
Status updated

Error handling

expired cert

invalid password

tampered PDF

permission denied

Exit Criteria

Full flow works

🛡 PHASE 8 — Security Hardening
Objective

Production safety.

Tasks

CSRF protection

Rate limiting

Temp file cleanup

Tamper detection

Immutable audit logs

Role enforcement

🧩 PHASE 9 — Registry Lifecycle Integration
Objective

Full lifecycle tracking.

Tasks

Register document events

Register signing events

Version tracking

Health monitoring

Dependency validation

🧪 PHASE 10 — QA + Validation
Tests

End-to-end signing

Load 10 docs per user

Certificate expiration test

Tamper detection

Recovery tests

📊 Final Capability Matrix

✅ CPF bound identity
✅ Rich editor
✅ Versioning
✅ Deterministic PDF
✅ ICP compatible signing
✅ Full audit trail
✅ Registry lifecycle tracking
✅ WordPress native integration




MAIN GOAL TO GET ALL FEATURES AND GOOD CODE AS BELOW DETAILED:

🧾 DIGITAL SIGNATURE + CERTIFICATE STACK
Demoiselle Signer

Folder: signer-master

Core Features (Free / OSS)

PAdES digital signature engine

Multiple signature support

Certificate chain validation

Signature verification

Policy validation (ICP-Brasil)

Detached signature support

Embedded signature support

Timestamp support

Signature visualization data

CRL validation

OCSP validation

“Premium-level” capabilities

Full legal compliance workflow

Trust chain enforcement

Advanced validation policies

Multi-signature orchestration

Utilities / Tools

Signing service wrapper

Signature validator

Certificate trust checker

Policy engine

Signature inspector

Modularity level

Medium — designed as standalone service

Best use

Legal-grade signing workflows

NFePHP

Folder: sped-common-master

Core Features

PFX parsing helpers

Certificate metadata extraction

Encoding normalization

ASN.1 parsing

Key extraction

XML signing helpers

Base64 helpers

Certificate validation utilities

Advanced utilities

Crypto helpers

String normalization

Date handling

File utilities

Modularity level

High — utility library

Best use

Certificate handling backend

OpenSSL

Folder: openssl-master

Core Features

PKCS#12 parsing

PKCS7 signing

SHA hashing

Encryption / decryption

Key generation

Signature verification

Random generators

Premium-level

Hardware token compatibility (via extensions)

Modularity level

Core runtime

Best use

Base crypto layer

📄 PDF STACK
TCPDF

Folder: TCPDF-main

Core Features

HTML → PDF rendering

Page layout engine

Font embedding

UTF-8 support

Metadata support

Header/footer templating

QR codes

Barcodes

Table rendering

SVG rendering

Image embedding

Watermarks

Advanced / premium-grade capabilities

Digital signature placeholder

PDF/A support

Encryption

Attachments

Form fields

Utilities

Font converter

Layout engine

Image processor

Modularity level

High

Best use

Document rendering

FPDI

Folder: FPDI-master

Core Features

Import existing PDFs

Page extraction

PDF merging

Overlay content

Stamp pages

Template reuse

Advanced

Signature placement support

Multi-document merging

Modularity level

High

Best use

Signing workflows and PDF manipulation

📊 LOGGING + OBSERVABILITY
Monolog

Folder: monolog-main

Core Features

Multi-channel logging

Structured logs

Context logging

Error logging

Debug logs

File handlers

Stream handlers

Advanced features

JSON logs

Syslog integration

Slack/email handlers

Log rotation

Performance metrics logging

Utilities

Formatter system

Log processors

Modularity level

Very high

Best use

Audit trail + debugging

🗂 WORKFLOW + PROJECT MANAGEMENT SYSTEMS
UpStream

Folder: upstreamplugin

Core Features

Projects

Tasks

Milestones

Issues

Discussions

File uploads

Client access portals

Notifications

Project timelines

Activity logs

Premium-style features

Custom workflows

Client dashboards

Reporting

Permissions granularity

Utilities

Workflow engine

Task status tracking

Modularity level

Medium

Best use

Approval workflows

WP Project Manager

Folder: wp-project-manager-develop

Core Features

Kanban boards

Task lists

Time tracking

Project templates

Discussions

File attachments

Activity streams

Reports

Notifications

Team roles

Premium-level capabilities

Advanced reporting

Project analytics

Gantt charts

Resource allocation

Utilities

Task engine

Notification system

Modularity level

Medium-high

Best use

Team collaboration workflows

Employee Management System

Folder: employee-management-system-master

Core Features

Employee profiles

Department management

Attendance tracking

Leave requests

Role management

Employee documents

Payroll references

HR reports

Premium-level capabilities

Organizational hierarchy

Employee analytics

Performance tracking

Utilities

HR database schema

Role mapping

Reporting engine

Modularity level

Medium

Best use

HR document workflows

🧠 COMBINED CAPABILITY MAP

If combined you can build:

Document platform

Authoring

Versioning

Rendering

Signing

Storage

Legal workflow system

Multi-party signing

Approval pipelines

Audit trail

Compliance tracking

HR contract system

Employee contracts

Onboarding forms

Leave approvals

Enterprise workflow platform

Task approvals

Document lifecycle

Team collaboration

🧰 UTILITIES YOU CAN REUSE DIRECTLY

From all repos combined:

Certificate parsers

PDF generators

PDF manipulators

Crypto helpers

Logging framework

Task engines

Notification systems

Reporting modules

Role/permission utilities

File handling utilities

🧩 MOST MODULAR REPOS

Highest reuse potential:

1️⃣ Monolog
2️⃣ FPDI
3️⃣ TCPDF
4️⃣ sped-common
5️⃣ OpenSSL

🧨 MOST COMPLEX / HEAVY

Demoiselle Signer

Project Manager plugins

Employee system

🏗 BEST “BUILDABLE CORE STACK”

If goal is Apollo signing platform:

Core:

TCPDF

FPDI

OpenSSL

Monolog

sped-common

Optional advanced:

signer

Optional workflow:

ONE project plugin

🧭 Architectural power level if all integrated

Enterprise document lifecycle platform.