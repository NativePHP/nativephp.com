---
title: Bifrost
order: 15
---

## Overview

[Bifrost](https://bifrost.nativephp.com) is NativePHP's cloud build platform — sign in with GitHub, and a push, tag,
or release kicks off a signed iOS and Android build without you touching a keystore, provisioning profile, or CI
config.

- **Managed signing** — paste one App Store Connect key and Bifrost creates and renews your certificates and
  provisioning profiles for you. No CSR-on-a-Mac, no yearly renewal chores.
- **Build from a push** — a PR, push, tag, or release triggers a build; you get notified in Slack, Discord, or email
  when it's done.
- **AI build diagnosis** — a failed build gets its log read automatically, with the root cause and a suggested fix,
  free on every plan.
- **Monorepo-aware** — point Bifrost at the folder your app lives in; it works alongside a Laravel API or web app in
  the same repository.
- **Workflows** — chain steps (build, notify, distribute) so a single trigger does everything you need.

## Getting started

Sign in to [Bifrost](https://bifrost.nativephp.com) with GitHub, attach a NativePHP license to your team, and pick
the folder your app lives in — Bifrost detects the app for you. From there, pushing to your configured branch (or
tagging a release) starts a build.

## Documentation

Bifrost has its own documentation covering Teams, Projects, Credentials, Builds, and Workflows in depth:

[**Read the Bifrost docs →**](https://bifrost.nativephp.com/docs/mobile)

<aside>

Desktop build support on Bifrost isn't available yet — this page covers mobile (iOS/Android) builds.

</aside>
