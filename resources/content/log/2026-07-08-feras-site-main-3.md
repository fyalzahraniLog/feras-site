---
type: activity
title: "A dotnet track joins the study log"
date: "2026-07-08T13:20:00+03:00"
project: feras-site
branch: main
repo: https://github.com/fyalzahraniLog/feras-site
commit: 745c618
slug: 2026-07-08-feras-site-main-3
tags: [feras-site, study, dotnet]
---

- Added `dotnet` to the study section's track whitelist — .NET/C# joins the study stack as a project-driven track, learned by building a real messaging app (Resala) rather than by taking a course.
- The whitelist lives in one place (`ContentRepository::STUDY_TRACKS`) and the seed-integrity tests enforce it, so this two-line change is all it takes to teach the section a new track — the filter chips pick it up automatically once the first entry lands.
- Updated the writing-content contract doc to match, keeping the `/study-log` agent and the parser in agreement.
