# Security Policy

The **404 to 301** team takes the security of the plugin and its users
seriously. This document explains which versions receive security fixes
and how to report a vulnerability responsibly.

## Supported versions

Security fixes are provided for the latest stable release line.

| Version | Supported          |
| ------- | ------------------ |
| 4.x     | :white_check_mark: |
| < 4.0   | :x:                |

If you are running an older release, please update to the current
version before reporting an issue — the bug may already be fixed.

## Reporting a vulnerability

**Please do not report security vulnerabilities through public GitHub
issues, pull requests, or the WordPress.org support forum.** Public
disclosure before a fix is available puts every site running the plugin
at risk.

Instead, report privately through either of these channels:

- **GitHub** — use
  [Report a vulnerability](https://github.com/Joel-James/404-to-301/security/advisories/new)
  to open a private security advisory (preferred).
- **Email** — write to the maintainer at **support@duckdev.com** with
  the details below.

To help us triage quickly, please include:

- A description of the vulnerability and its impact.
- The plugin version, WordPress version, and PHP version affected.
- Step-by-step instructions to reproduce the issue.
- Any proof-of-concept code, screenshots, or logs you can share.
- Whether the issue is already known to be exploited in the wild.

## What to expect

- **Acknowledgement** within **3 business days** of your report.
- An initial assessment and severity rating within **7 days**.
- Regular updates as we work on a fix; we will let you know the
  expected timeline once the issue is confirmed.
- A released patch and a security advisory crediting you (unless you
  prefer to remain anonymous).

## Disclosure policy

We follow coordinated disclosure. Please give us a reasonable window to
release a fix before any public disclosure. We aim to ship security
patches as quickly as possible and will coordinate the disclosure
timing with you.

## Scope

This policy covers the 404 to 301 plugin code in this repository.
Vulnerabilities in third-party dependencies should be reported to the
respective projects, though we appreciate a heads-up so we can update
our bundled versions.

Thank you for helping keep 404 to 301 and its users safe.
