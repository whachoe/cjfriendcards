# cjFriendCards

A Laravel web application for managing and sharing virtual friendship cards with relationship tracking, birthday reminders, and export capabilities.

## Features

- **Friend Card Management**: Create, read, update, and delete friendship cards with details like name, contact info, birthday, and personal notes
- **Relationship Tracking**: Define relationships between friends (best_friend, colleague, family, spouse, child, parent, acquaintance, ex-partner)
- **Birthday Calendar**: View all friend birthdays in a dedicated calendar view
- **Export Options**:
  - Export all birthdays as iCal format for calendar integration
  - Export individual cards as vCard format for contact management
- **Dual Interface**:
  - RESTful API endpoints (JSON responses)
  - Web interface with Blade templates (HTML responses)
- **HTMX Integration**: Dynamic content updates without full page reloads

## Technology Stack

- **Backend**: Laravel 11
- **Database**: SQLite (default, MySQL supported)
- **Frontend**: Blade templates with HTMX
- **ORM**: Eloquent
- **Version Control**: Jujutsu (jj) with Git backend

## Project Structure

```
cjFriendCards/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── CardController.php           # API controller for cards
│   │       ├── RelationshipController.php   # API controller for relationships
│   │       ├── WebCardController.php        # Web controller for HTML views
│   │       └── ExportController.php         # Export endpoints (iCal, vCard)
│   ├── Models/
│   │   ├── Card.php                        # Card model with relationships
│   │   └── Relationship.php                # Relationship model
│
├── database/
│   └── migrations/
│       ├── *_create_cards_table.php
│       └── *_create_relationships_table.php
│
├── routes/
│   ├── api.php                             # API routes (/api/v1/...)
│   └── web.php                             # Web routes (HTML responses)
│
├── resources/
│   └── views/
│       ├── layout.blade.php                # Main layout
│       └── cards/
│           ├── index.blade.php             # Card list view
│           ├── create.blade.php            # Create card form
│           ├── edit.blade.php              # Edit card form
│           ├── show.blade.php              # Card detail view
│           └── birthday-calendar.blade.php # Birthday calendar view
│
├── config/
│   ├── app.php                             # App configuration
│   └── database.php                        # Database configuration
│
├── composer.json
├── .env.example
├── .github/
│   └── copilot-instructions.md             # AI coding guidelines
└── README.md                               # This file
```

## Data Models

### Card
Represents a friendship/contact card with the following fields:
- `id` (integer, primary key)
- `unique_name` (string, unique): Used as a primary key for API references
- `name` (string): Friend's display name
- `contact_info` (string, nullable): Email, phone, or other contact details
- `birthday` (date, nullable): Friend's birthday
- `notes` (text, nullable): Personal notes or information
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Relationships**:
- `relationships()`: One-to-many with Relationship (cards this card relates to)
- `relatedCards()`: Many-to-many with Card (bidirectional through relationships)

### Relationship
Represents a relationship between two friendship cards:
- `id` (integer, primary key)
- `card_id` (foreign key to Card)
- `related_card_id` (foreign key to Card)
- `relationship_type` (enum): One of: best_friend, colleague, family, spouse, child, parent, acquaintance, ex-partner
- `notes` (text, nullable): Relationship-specific notes
- `created_at` (timestamp)
- `updated_at` (timestamp)

## API Endpoints

All API endpoints return JSON by default and are prefixed with `/api/v1/`.

### Cards
- `GET /api/v1/cards` - List all cards (paginated)
- `POST /api/v1/cards` - Create a new card
- `GET /api/v1/cards/{id}` - Get a specific card
- `PUT /api/v1/cards/{id}` - Update a card
- `DELETE /api/v1/cards/{id}` - Delete a card

### Relationships
- `GET /api/v1/cards/{card_id}/relationships` - List relationships for a card
- `POST /api/v1/cards/{card_id}/relationships` - Add a relationship
- `GET /api/v1/relationships/{id}` - Get a specific relationship
- `PUT /api/v1/relationships/{id}` - Update a relationship
- `DELETE /api/v1/relationships/{id}` - Delete a relationship

### Exports
- `GET /api/v1/export/birthdays/ical` - Download all birthdays as iCal format
- `GET /api/v1/cards/{id}/export/vcard` - Download a card as vCard format

## Web Routes

- `GET /` - Redirect to card list
- `GET /cards` - List all cards (web view)
- `GET /cards/create` - Show card creation form
- `POST /cards` - Store a new card
- `GET /cards/{id}` - Show card details
- `GET /cards/{id}/edit` - Show card edit form
- `PUT /cards/{id}` - Update a card
- `DELETE /cards/{id}` - Delete a card
- `GET /birthdays` - View birthday calendar

## Getting Started

### Prerequisites
- PHP 8.2 or higher
- Composer
- SQLite (included with PHP) or MySQL
- Node.js (for frontend assets, optional)

### Installation

1. Clone the repository using Jujutsu:
   ```bash
   jj clone git@github.com:yourusername/cjFriendCards.git
   cd cjFriendCards
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Set up environment:
   ```bash
   cp .env.example .env
   ```

4. Create database file (for SQLite):
   ```bash
   mkdir -p database
   touch database/cjfriendcards.sqlite
   ```

5. Run migrations:
   ```bash
   php artisan migrate
   ```

6. Start the development server:
   ```bash
   php artisan serve
   ```

   The application will be available at `http://localhost:8000`

## Development

### Running Tests
```bash
php artisan test
```

### Creating New Cards
```bash
php artisan tinker
>>> App\Models\Card::create(['unique_name' => 'john_doe', 'name' => 'John Doe', 'birthday' => '1990-01-15'])
```

### Git Workflow

This project uses Jujutsu for version control with Git as the backend. Follow the branch naming conventions:

- **Feature branches**: `feature/*` (e.g., `feature/relationship-management`)
- **Bugfix branches**: `bugfix/*` (e.g., `bugfix/birthday-calculation`)
- **Other branches**: Use descriptive names (commit prefix will be "chore:")

Branch names automatically generate commit prefixes:
- `feature/*` → "feat:"
- `bugfix/*` → "fix:"
- Others → "chore:"

## API Usage Examples

### Create a card
```bash
curl -X POST http://localhost:8000/api/v1/cards \
  -H "Content-Type: application/json" \
  -d '{
    "unique_name": "jane_smith",
    "name": "Jane Smith",
    "contact_info": "jane@example.com",
    "birthday": "1992-05-20",
    "notes": "College friend"
  }'
```

### Add a relationship
```bash
curl -X POST http://localhost:8000/api/v1/cards/1/relationships \
  -H "Content-Type: application/json" \
  -d '{
    "related_card_id": 2,
    "relationship_type": "best_friend",
    "notes": "Met in college"
  }'
```

### Export birthdays
```bash
curl http://localhost:8000/api/v1/export/birthdays/ical > birthdays.ics
```

### Export a card as vCard
```bash
curl http://localhost:8000/api/v1/cards/1/export/vcard > jane_smith.vcf
```
