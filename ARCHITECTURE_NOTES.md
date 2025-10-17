# Architecture Notes — Order Fulfillment System

Date: 2025-10-17

Authoring/Review conventions
- Developer header comment is included at the top of PHP files (name + reviewed date).
- Repository & Service pattern is used consistently: Services contain business logic and orchestrate repositories; Repositories encapsulate data access.

Why Repository + Service?
- Separation of concerns: Services implement business rules and orchestration, Repositories encapsulate persistence. This makes logic easier to test and mock.
- Testability: Services can be unit-tested by mocking repository interfaces.
- Single responsibility: Repositories only build/execute queries; Services perform transactions and fire events.

Trade-offs
- Pros:
  - Clear boundaries and easier unit testing.
  - Encourages smaller, focused classes.
  - Easier to replace persistence (swap Eloquent for another adapter) by changing repository implementations.
- Cons:
  - More classes and boilerplate for small simple apps.
  - Requires discipline to avoid anemic service classes that simply proxy repository calls.

Suggested next steps
- Add interfaces for repositories and bind them in a provider for easier mocking.
- Add unit tests for Services replacing repositories with mocks/stubs.
