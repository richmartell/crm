# Personal CRM

A beautiful, feature-rich personal CRM system for managing family and personal contacts, built with Laravel, Livewire, Tailwind CSS, and Flux UI.

## Features

✨ **Contact Management**
- Store comprehensive contact information (name, email, phone, dates, address, notes, photo)
- Rich profile pages with all contact details
- Upload and display contact photos
- Soft delete support

🏷️ **Smart Tagging**
- Organize contacts with multiple tags (Family, Friends, Work, etc.)
- Color-coded tags for visual organization
- Filter contacts by tags

🔗 **Relationship Tracking**
- Link contacts to represent relationships (spouse, child, parent, friend, etc.)
- Bidirectional relationship support
- Visualize family structures and connections

🏠 **Shared Addresses**
- Multiple contacts can share the same address (e.g., family members)
- Formatted address display

🔍 **Advanced Search & Filter**
- Real-time search across names, emails, and phone numbers
- Filter by tags
- Sort by various fields
- Pagination support

🎨 **Modern UI**
- Beautiful, responsive design using Tailwind CSS and Flux UI
- Dark mode support
- Card-based layout for easy browsing
- Gradient avatars for contacts without photos

## Tech Stack

- **Backend:** Laravel 11
- **Frontend:** Livewire 3, Tailwind CSS v4, Flux UI
- **Database:** MySQL (via Laravel Sail)
- **Development:** Laravel Sail (Docker)

## Requirements

- Docker Desktop
- Git

## Installation

### 1. Clone the Repository

If you haven't already:

```bash
git clone <your-repo-url>
cd crm
```

### 2. Configure Environment

The `.env` file should already be configured for Sail. If not, copy the example:

```bash
cp .env.example .env
```

### 3. Start Sail

```bash
./vendor/bin/sail up -d
```

This will start all Docker containers (MySQL, Redis, etc.)

### 4. Install Dependencies

If composer dependencies aren't installed:

```bash
./vendor/bin/sail composer install
```

Install NPM dependencies:

```bash
./vendor/bin/sail npm install
```

### 5. Generate Application Key

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Run Migrations and Seeders

```bash
./vendor/bin/sail artisan migrate --seed
```

This will create all database tables and populate them with sample data including:
- 5 addresses in various UK cities
- 8 tags (Family, Friends, Work, Rich Uni, Florence School, etc.)
- Sample family with relationships (John & Sarah Smith with children Emma and Oliver)
- University friends (Michael, Lucy)
- Work colleagues (David, Jessica)

### 7. Create Storage Link

```bash
./vendor/bin/sail artisan storage:link
```

This creates a symbolic link for photo uploads.

### 8. Build Frontend Assets

```bash
./vendor/bin/sail npm run dev
```

For production:

```bash
./vendor/bin/sail npm run build
```

### 9. Access the Application

Open your browser and navigate to:

```
http://localhost
```

## Usage

### Adding a Contact

1. Click "Add Contact" button on the contacts list page
2. Fill in the contact information:
   - First Name & Last Name (required)
   - Email, Phone Number
   - Date of Birth, Anniversary Date
   - Photo (upload an image)
   - Address (select existing or create new)
   - Tags (select multiple)
   - Notes

3. Click "Create Contact"

### Managing Relationships

1. Open a contact's profile page
2. Click "Add Relationship" in the Relationships section
3. Select a contact and specify the relationship type (spouse, child, parent, friend, etc.)
4. The relationship will appear on both contacts' profiles

### Searching and Filtering

- Use the search box to find contacts by name, email, or phone
- Use the tag dropdown to filter by specific tags
- Click "Clear filters" to reset

### Editing Contacts

- Click "Edit" on any contact card or profile page
- Modify the information
- Click "Update Contact"

### Deleting Contacts

- Click "Delete" on a contact card
- Confirm the deletion
- Relationships associated with the contact will also be removed

## Database Schema

### Contacts
- id, first_name, last_name
- date_of_birth, anniversary_date
- email, phone_number
- notes, photo
- address_id (foreign key)
- timestamps, soft_deletes

### Addresses
- id, street, city, postcode, country
- timestamps

### Tags
- id, name, color
- timestamps

### Contact_Tag (Pivot)
- contact_id, tag_id
- timestamps

### Contact_Relationships
- id, contact_id, related_contact_id
- relationship_type
- timestamps

## Artisan Commands

### Database

```bash
# Run migrations
./vendor/bin/sail artisan migrate

# Rollback migrations
./vendor/bin/sail artisan migrate:rollback

# Fresh migration with seeding
./vendor/bin/sail artisan migrate:fresh --seed

# Seed database
./vendor/bin/sail artisan db:seed
```

### Clear Cache

```bash
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan view:clear
```

## Development

### Running Tests

```bash
./vendor/bin/sail artisan test
```

### Code Style

```bash
./vendor/bin/sail composer pint
```

### Logs

View application logs:

```bash
./vendor/bin/sail artisan pail
```

## Customization

### Adding New Tags

You can add new tags through the database seeder or directly in the database:

```php
Tag::create([
    'name' => 'Your Tag Name',
    'color' => '#hex-color',
]);
```

### Adding New Relationship Types

Relationship types are stored as strings. Common types:
- spouse
- child
- parent
- sibling
- friend
- colleague
- neighbor

You can use any custom relationship type when creating relationships.

### Customizing Colors

The application uses Tailwind CSS. To customize colors, edit:

```bash
tailwind.config.js
```

### Flux UI Pro

If you have a Flux UI Pro license, you can upgrade by adding your authentication to `composer.json`:

```json
"repositories": [
    {
        "type": "composer",
        "url": "https://composer.fluxui.dev"
    }
],
"require": {
    "livewire/flux-pro": "^1.0"
}
```

Then run:

```bash
./vendor/bin/sail composer update
```

## Troubleshooting

### Permission Issues

If you encounter permission issues:

```bash
./vendor/bin/sail artisan storage:link
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:$USER storage bootstrap/cache
```

### Database Connection

If you can't connect to the database:

1. Ensure Sail is running: `./vendor/bin/sail ps`
2. Check your `.env` file has correct database credentials
3. Try: `./vendor/bin/sail down` then `./vendor/bin/sail up -d`

### Assets Not Loading

If CSS/JS assets aren't loading:

1. Make sure Vite is running: `./vendor/bin/sail npm run dev`
2. Check the browser console for errors
3. Try rebuilding: `./vendor/bin/sail npm run build`

## Contributing

Feel free to submit issues and enhancement requests!

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Credits

- Built with [Laravel](https://laravel.com)
- UI powered by [Livewire](https://livewire.laravel.com)
- Styled with [Tailwind CSS](https://tailwindcss.com)
- Components from [Flux UI](https://flux.laravel.com)
- Development environment by [Laravel Sail](https://laravel.com/docs/sail)

---

**Enjoy managing your personal contacts!** 🎉