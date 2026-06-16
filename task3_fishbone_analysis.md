# Task 3: Process Analysis - Fishbone Diagram Content

## Slide Title
**Root Cause Analysis of Unreliable Releases in the BodaConnect CI/CD Pipeline**

## Main Effect
Place this at the fish head on the right side:

**Unreliable Releases**

Short explanation under the head:
- failed staging deployments
- blocked production releases
- delayed approvals
- inconsistent release timing

## Fishbone Layout
Use one central spine pointing right into `Unreliable Releases`.

Top branches:
- People / Process
- Environment / Infrastructure
- Tools / Monitoring

Bottom branches:
- Pipeline / Automation
- Application / Configuration

## Branch Content

### 1. People / Process
- Production deployment requires manual approval before release continues.
- Release timing depends on reviewer availability.
- Pipeline fixes were pushed directly on `main`, so troubleshooting happened during live delivery.
- Failures triggered reactive fixes instead of earlier prevention before deployment.

Evidence:
- `Run #20` `Export stable deploy volume prefixes` stayed `Waiting`.
- `Run #17` `Add deployment failure diagnostics to workflow` stayed `Waiting`.
- Approval evidence exists in `images/Screenshot of approval step.png`.

### 2. Pipeline / Automation
- The workflow is sequential, so one failed job blocks every later release step.
- Staging must succeed before production can start.
- Verification steps can fail after successful build, test, and Docker push stages.
- Release throughput is reduced because production is gated after staging.

Evidence:
- `Run #24` failed after build, test, and Docker push succeeded, then staging failed.
- `Run #15` reached deployment and then failed before production.
- `Run #23` is the successful comparison case showing full pipeline completion.

### 3. Environment / Infrastructure
- SSH host validation and remote access can break deployment before files are copied.
- Remote server availability affects deployment speed and success.
- Docker volume selection and shared-instance handling increased deployment complexity.
- Elastic and Metricbeat security changes introduced more environment-dependent failure points.

Evidence:
- `Run #24` `Authenticate Metricbeat against secured Elastic services` failed in staging.
- `Run #21` `Auto-select existing deploy data volumes` failed quickly in `1m 19s`.
- `Run #5` `Fix staging SSH host handling` shows SSH configuration was already a deployment problem area.

### 4. Application / Configuration
- Verification endpoint mismatches caused staging and production verification fixes.
- MQTT verification depends on correct remote command expansion and live container state.
- Deployment depends on many secrets and environment variables being correct.
- Frontend proxy and MQTT path changes show tight configuration coupling between services.

Evidence:
- `Run #8` `Fix production verification endpoints`.
- `Run #7` `Fix staging verification endpoints`.
- `Run #19` `Fix remote MQTT verification command expansion`.
- `Run #23` `Relax frontend MQTT proxy matching`.

### 5. Tools / Monitoring
- Monitoring helped detect problems, but many issues were only discovered during deployment verification.
- Failure diagnosis is present after failure, not before release.
- MQTT-enabled verification happens near the end of the workflow.
- Long waiting and failed runs make trend analysis harder without stronger categorization.

Evidence:
- `Run #17` added deployment failure diagnostics.
- `Run #24` shows monitoring/security changes affecting release reliability.
- Existing screenshots show runtime and deployment evidence under `images/`.

## Evidence Summary Table
Use this as a small table on the second half of the slide or speaker notes.

| Run | Commit / Title | Result | Duration | What it proves |
|---|---|---:|---:|---|
| #24 | Authenticate Metricbeat against secured Elastic services | Failure | 7m 0s | Infrastructure and configuration changes can break staging after earlier CI steps succeed |
| #23 | Relax frontend MQTT proxy matching | Success | 13m 5s | The full pipeline can succeed when deployment and verification inputs are correct |
| #21 | Auto-select existing deploy data volumes | Failure | 1m 19s | Deployment automation and server environment handling are brittle |
| #20 | Export stable deploy volume prefixes | Waiting | n/a | Manual approval or release gating delays delivery |
| #19 | Fix remote MQTT verification command expansion | Success | 9m 22s | MQTT verification reliability depends on correct remote command behavior |
| #18 | Make database seeder work without dev factories | Failure | 15m 4s | A release can still fail late even after earlier stages pass |
| #17 | Add deployment failure diagnostics to workflow | Waiting | n/a | Diagnostic improvements were needed because failures were occurring during deployment |
| #15 | Test admin dashboard metrics indexing command | Failure | 7m 40s | Deployment verification failure can stop production delivery |
| #8 | Fix production verification endpoints | Success | 19m 18s | Verification endpoint correctness directly affects release success |
| #7 | Fix staging verification endpoints | Success | 7m 53s | Staging URL verification was a real root cause area |
| #5 | Fix staging SSH host handling | Success | 58s | SSH configuration was an early source of deployment failure |
| #3 | Fix test after CI failure demonstration | Success | 2m 22s | Build/test failures were also part of pipeline instability |

## Suggested PowerPoint Placement
Use these exact positions:

- Right side fish head:
  - `Unreliable Releases`
- Top branch 1:
  - `People / Process`
- Top branch 2:
  - `Environment / Infrastructure`
- Top branch 3:
  - `Tools / Monitoring`
- Bottom branch 1:
  - `Pipeline / Automation`
- Bottom branch 2:
  - `Application / Configuration`

Use short labels on each branch and put the full evidence table in a side box or speaker notes.

## Short Conclusion for the Slide
**Conclusion:** Most BodaConnect release failures did not happen during coding or build creation. They happened later, during deployment verification, remote environment setup, MQTT-related validation, and manual production approval. This means the biggest bottlenecks are release gating, environment configuration, and late-stage verification rather than raw build speed.
