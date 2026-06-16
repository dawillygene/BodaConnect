# Task 4: Process Improvement Report

## Title
**BodaConnect CI/CD Process Improvement Report**

## 1. Objective
The goal of this task was to improve the BodaConnect CI/CD and operational process by implementing two practical changes and showing their impact on delivery quality.

The two implemented changes were:
- adding automated **test coverage reporting**
- adding a **rollback workflow** for production releases

These improvements were selected because they directly address two weaknesses identified in the earlier analysis:
- poor visibility into test quality
- no structured rollback path after a failed or risky deployment

## 2. Baseline Before Improvement
Before the changes:
- the CI pipeline only reported whether tests passed or failed
- no code coverage artifact or coverage summary was published in GitHub Actions
- recovery from a bad production release depended on manual server access and manual image selection
- rollback steps were not standardized in the repository workflow

This meant the process could deploy successfully, but:
- test quality was not measurable from the pipeline
- release recovery was slower and more error-prone

## 3. Implemented Changes

### Change 1: Test Coverage Reporting
The existing CI workflow in `.github/workflows/ci.yml` was updated so the test job now:
- enables `pcov` coverage collection in GitHub Actions
- runs PHPUnit with coverage output
- generates:
  - `junit.xml`
  - `clover.xml`
  - HTML coverage output
  - text summary output
- publishes the coverage summary in the GitHub Actions run summary
- uploads the full `coverage/` directory as an artifact

### Change 2: Production Rollback Workflow
A new workflow was added in `.github/workflows/rollback.yml`.

This workflow:
- uses `workflow_dispatch` for manual trigger
- accepts:
  - `backend_tag`
  - `frontend_tag`
  - optional `reason`
- verifies that the selected Docker image tags exist
- reconnects to the production server using the existing SSH deployment method
- redeploys the selected known-good image tags
- runs production health checks after rollback
- records a rollback summary in GitHub Actions

## 4. Validation Performed
The following checks were completed after implementation:

- both workflow YAML files parsed successfully
- the existing Laravel test suite still passed locally

Local validation result:
- `14 passed (70 assertions)`

Important limitation:
- the actual coverage-producing command could not be executed locally because the current local PHP environment does not have `pcov` or `xdebug`
- that part is expected to run in GitHub Actions, where `pcov` is now explicitly enabled in the workflow

## 5. Measured Impact
This task was measured using **visibility and recovery improvement**, not raw pipeline speed.

### Before-and-After Comparison

| Metric | Before | After | Measured Impact |
|---|---|---|---|
| Test coverage visibility | No coverage information in CI | Coverage summary and artifact generated in CI workflow | Test quality becomes visible in every run |
| Coverage artifact availability | 0 coverage artifacts | 1 coverage artifact set per CI run | Evidence can be downloaded and reviewed |
| Production rollback process | Manual server intervention | Dedicated rollback workflow | Recovery becomes repeatable and faster to execute |
| Rollback input control | No structured inputs | Explicit backend and frontend tag inputs | Lower chance of rollback mistakes |
| Post-rollback validation | Manual confirmation | Automated health checks | Recovery success is verified automatically |

### Operational Impact Summary
- **Visibility improved** because CI is no longer limited to pass/fail output.
- **Recovery improved** because rollback is now standardized inside GitHub Actions.
- **Risk reduced** because rollback uses explicit image tags and health verification.
- **Consistency improved** because the rollback process no longer depends only on memory or ad hoc shell commands.

## 6. Before-and-After Interpretation
The improvements do not primarily target build speed. Instead, they improve delivery quality in two important ways:

- Coverage reporting improves confidence in the codebase by exposing how much of the application is exercised by tests.
- The rollback workflow improves operational resilience by providing a controlled recovery path after a failed or unstable release.

This is important for BodaConnect because the project already showed issues around deployment verification, MQTT verification, and release reliability. Faster recovery and better test insight directly reduce the impact of those failures.

## 7. Evidence to Include in Submission
For the final assignment submission, include:
- a screenshot of the updated `Run Tests` job showing coverage artifact upload
- a screenshot of the GitHub Actions summary showing coverage output
- a screenshot of the new rollback workflow dispatch form
- a screenshot of a successful rollback run after selecting image tags
- a before-and-after comparison chart in Excel or PowerPoint using the table above

## 8. Conclusion
The BodaConnect CI/CD process was improved by adding:
- automated test coverage reporting
- a production rollback workflow

The measured impact is that the pipeline now provides better **quality visibility** and a safer **recovery mechanism**. These changes make the process more mature, more controlled, and easier to monitor during real deployments.
