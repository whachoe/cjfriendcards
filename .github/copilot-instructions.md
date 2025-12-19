# AI Coding Instructions for cjFriendCards

## Project Overview
**cjFriendCards** is a project for managing and sharing virtual friendship cards. The project uses Jujutsu (jj) for version control with a Git backend.
A friendship card includes details such as the friend's name, contact information, links to other friends detailing the relationship they have, birthday and personal notes. More details will be added in these instructions later.

## Repository Management
- **Version Control**: Uses Jujutsu (jj) with Git backend
- **Main Branch**: `main`
- **Commit Hooks**: Git hooks are configured for:
  - Preventing commits with merge markers (`pre-commit.sample`)
  - Preventing pushes with "DELME" markers (`pre-push.sample`)
  - Auto-generating commit messages based on branch naming (`prepare-commit-msg.sample`)

## Development Patterns
*To be updated as source code is added to the project.*

### Branch Naming Convention
Based on the configured hooks, follow these branch naming patterns:
- `feature/*` - for new features (auto-generates "feat:" prefix)
- `bugfix/*` - for bug fixes (auto-generates "fix:" prefix)
- Other branches - auto-generate "chore:" prefix

### Pre-Commit Checks
Before committing:
1. Ensure no merge conflict markers (`<<<<<<<`, `=======`, `>>>>>>>`) exist in files
2. Avoid "DELME" strings in commit messages (blocks push)
3. Remove duplicate "Signed-off-by" lines from commits

## Key Files to Reference
- `.jj/` - Jujutsu repository configuration and index
- `.jj/repo/store/git/` - Git backend storage
- `.github/` - GitHub configuration and CI/CD (if added)
- `.github/copilot-instructions.md` - This instructions file

## Next Steps When Adding Code
1. Document the main architecture in this file
2. Include specific examples of:
   - Project structure and module organization
   - Data models and core entities (friendship cards schema)
   - Service boundaries and integration points
   - Build and test commands
3. Add project-specific conventions (naming, patterns, error handling)
4. Reference key source files that exemplify important patterns

## Main architecture
1. Language: This application is written in PHP using the Laravel framework (v12.11.0). It follows the Model-View-Controller (MVC) architectural pattern. The framework is already set up so no need to initialize a new Laravel project.
2. Database: Cards are kept in an SQLite database using Eloquent ORM for database interactions.
3. API: The application exposes a RESTful API for managing friendship cards, allowing CRUD operations. The API endpoints will return HTML by default. API endpoints will also support JSON responses when requested via the `Accept: application/json` header.
4. Frontend: The frontend is built using Blade templates with HTMLX for dynamic content updates without full page reloads. 

## Data Models
1. Card: Represents a friendship/contact card. Fields include:
   - id (integer, primary key, auto-increment int or auto-generated UUID)
   - unique_name (string, unique): Used as a primary key for referencing cards.
   - name (string)
   - contact_info (string)
   - birthday (date)
   - notes (text)
   - created_at (timestamp)
   - updated_at (timestamp)
2. Relationship: Represents a relationship between two friendship cards. Fields include: 
   - id (integer, primary key, auto-increment int or auto-generated UUID)
   - card_id (foreign key to FriendshipCard)
   - related_card_id (foreign key to FriendshipCard)
   - relationship_type (string): Possible values include "best_friend", "colleague", "family", "spouse", "child", "parent", "acquaintance", "ex-partner". These values can be extended as needed. At first they may be defined as an ENUM.
   - notes (text)
   - created_at (timestamp)
   - updated_at (timestamp)

## Screens
1. Card List View: Displays a list of all friendship cards with options to view, edit, or delete each card.
2. Card Detail View: Displays detailed information about a specific friendship card, including its relationships to other cards.
3. Card Creation/Edit View: Form for creating a new friendship card or editing an existing one. You can also add relationships to other cards from this view.
4. Birthday calendar view: Displays upcoming birthdays of friends in a calendar format. Also shows the age they will be turning.

## Relationships

1. Many-to-Many: If needed in the future, a more complex relationship model can be implemented to allow cards to have multiple types of relationships with other cards.
2. Some relationships are the opposite of others. For example, if Card A is marked as "parent" of Card B, then Card B should automatically be marked as "child" of Card A. Here's a list of such opposite relationships:
   - parent <-> child
   - spouse <-> spouse
   - ex-partner <-> ex-partner
   - friend <-> friend
   - colleague <-> colleague
   - acquaintance <-> acquaintance
   - family <-> family

## Things for the AI to keep in mind
- Follow Laravel best practices for structuring controllers, models, views, and routes.
- Always provide tests for new features or bug fixes.
- Run those tests to ensure they pass before finalizing any code changes.
- Use migrations for any database schema changes.




---
*Last updated: 2025-12-14*
