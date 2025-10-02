<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layout')]
class ContactImport extends Component
{
    use WithFileUploads;

    public $csvFile;
    public $parsedData = [];
    public $validationErrors = [];
    public $duplicates = [];
    public $step = 'upload'; // upload, review, complete
    public $duplicateActions = []; // Array to track user choices for duplicates

    protected $validationAttributes = [
        'csvFile' => 'CSV file',
    ];

    public function updatedCsvFile()
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);
    }

    public function downloadTemplate()
    {
        $headers = [
            'first_name',
            'last_name',
            'email',
            'phone_number',
            'date_of_birth',
            'anniversary_date',
            'street',
            'address_line_2',
            'city',
            'postcode',
            'country',
            'tags',
            'notes',
        ];

        $exampleData = [
            [
                'John',
                'Doe',
                'john.doe@example.com',
                '+1234567890',
                '1990-01-15',
                '2015-06-20',
                '123 Main Street',
                'Apt 4B',
                'New York',
                '10001',
                'USA',
                'Family,Friends',
                'Met at conference',
            ],
            [
                'Jane',
                'Smith',
                'jane.smith@example.com',
                '+1987654321',
                '1985-03-22',
                '',
                '456 Oak Avenue',
                '',
                'Los Angeles',
                '90001',
                'USA',
                'Work',
                'VP of Engineering',
            ],
        ];

        $content = implode(',', $headers) . "\n";
        foreach ($exampleData as $row) {
            $content .= '"' . implode('","', $row) . '"' . "\n";
        }

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'contacts_import_template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function parseFile()
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $this->reset(['validationErrors', 'parsedData', 'duplicates', 'duplicateActions']);

        try {
            $path = $this->csvFile->getRealPath();
            $file = fopen($path, 'r');
            
            // Read header
            $header = fgetcsv($file);
            
            if (!$header) {
                $this->validationErrors[] = 'The CSV file is empty or invalid.';
                fclose($file);
                return;
            }

            // Validate required columns
            $requiredColumns = ['first_name', 'last_name'];
            $missingColumns = array_diff($requiredColumns, $header);
            
            if (!empty($missingColumns)) {
                $this->validationErrors[] = 'Missing required columns: ' . implode(', ', $missingColumns);
                fclose($file);
                return;
            }

            $rowNumber = 1;
            $validData = [];

            while (($row = fgetcsv($file)) !== false) {
                $rowNumber++;
                
                if (count($row) !== count($header)) {
                    $this->errors[] = "Row {$rowNumber}: Column count mismatch.";
                    continue;
                }

                $data = array_combine($header, $row);
                
                // Validate row
                $rowErrors = $this->validateRow($data, $rowNumber);
                
                if (!empty($rowErrors)) {
                    $this->validationErrors = array_merge($this->validationErrors, $rowErrors);
                    continue;
                }

                // Check for duplicates
                $duplicate = Contact::where('user_id', Auth::id())
                    ->where(function ($query) use ($data) {
                        // Check by email if provided
                        if (!empty($data['email'])) {
                            $query->where('email', $data['email']);
                        }
                        // Also check by name combination
                        $query->orWhere(function ($q) use ($data) {
                            $q->where('first_name', $data['first_name'])
                              ->where('last_name', $data['last_name']);
                        });
                    })
                    ->first();

                if ($duplicate) {
                    $this->duplicates[] = [
                        'row' => $rowNumber,
                        'existing' => $duplicate,
                        'new' => $data,
                    ];
                    $this->duplicateActions[$rowNumber] = 'skip'; // Default action
                }

                $validData[] = $data;
            }

            fclose($file);

            if (empty($validData)) {
                $this->validationErrors[] = 'No valid data found in the CSV file.';
                return;
            }

            $this->parsedData = $validData;

            // Move to review step if there are no errors
            if (empty($this->validationErrors)) {
                $this->step = 'review';
            }

        } catch (\Exception $e) {
            $this->validationErrors[] = 'Error parsing CSV file: ' . $e->getMessage();
        }
    }

    protected function validateRow($data, $rowNumber)
    {
        $errors = [];

        // Required fields
        if (empty(trim($data['first_name'] ?? ''))) {
            $errors[] = "Row {$rowNumber}: First name is required.";
        }

        if (empty(trim($data['last_name'] ?? ''))) {
            $errors[] = "Row {$rowNumber}: Last name is required.";
        }

        // Email is optional but validate format if provided
        $email = trim($data['email'] ?? '');
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Row {$rowNumber}: Invalid email format ({$email}).";
        }

        // Validate dates if provided
        if (!empty($data['date_of_birth'] ?? '')) {
            if (!$this->isValidDate($data['date_of_birth'])) {
                $errors[] = "Row {$rowNumber}: Invalid date_of_birth format. Use YYYY-MM-DD.";
            }
        }

        if (!empty($data['anniversary_date'] ?? '')) {
            if (!$this->isValidDate($data['anniversary_date'])) {
                $errors[] = "Row {$rowNumber}: Invalid anniversary_date format. Use YYYY-MM-DD.";
            }
        }

        return $errors;
    }

    protected function isValidDate($date)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    public function importContacts()
    {
        if (empty($this->parsedData)) {
            session()->flash('error', 'No data to import.');
            return;
        }

        DB::beginTransaction();

        try {
            $imported = 0;
            $skipped = 0;

            foreach ($this->parsedData as $index => $data) {
                $rowNumber = $index + 2; // +2 because index starts at 0 and we have header

                // Check if this row is a duplicate and should be skipped
                if (isset($this->duplicateActions[$rowNumber]) && $this->duplicateActions[$rowNumber] === 'skip') {
                    $skipped++;
                    continue;
                }

                // Create or update address if provided
                $addressId = null;
                if (!empty($data['street'] ?? '') || !empty($data['city'] ?? '')) {
                    $address = Address::create([
                        'street' => $data['street'] ?? '',
                        'address_line_2' => $data['address_line_2'] ?? '',
                        'city' => $data['city'] ?? '',
                        'postcode' => $data['postcode'] ?? '',
                        'country' => $data['country'] ?? '',
                    ]);
                    $addressId = $address->id;
                }

                // Create contact
                $contact = Contact::create([
                    'user_id' => Auth::id(),
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => !empty($data['email']) ? $data['email'] : null,
                    'phone_number' => $data['phone_number'] ?? null,
                    'date_of_birth' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                    'anniversary_date' => !empty($data['anniversary_date']) ? $data['anniversary_date'] : null,
                    'address_id' => $addressId,
                    'notes' => $data['notes'] ?? null,
                    'is_shared' => false,
                ]);

                // Handle tags
                if (!empty($data['tags'] ?? '')) {
                    $tagNames = array_map('trim', explode(',', $data['tags']));
                    $tagIds = [];
                    
                    foreach ($tagNames as $tagName) {
                        if (!empty($tagName)) {
                            $tag = Tag::firstOrCreate(['name' => $tagName], [
                                'color' => '#' . substr(md5($tagName), 0, 6), // Generate color from name
                            ]);
                            $tagIds[] = $tag->id;
                        }
                    }
                    
                    if (!empty($tagIds)) {
                        $contact->tags()->sync($tagIds);
                    }
                }

                $imported++;
            }

            DB::commit();

            session()->flash('success', "Successfully imported {$imported} contact(s). {$skipped} duplicate(s) skipped.");
            $this->step = 'complete';

        } catch (\Exception $e) {
            DB::rollBack();
            $this->validationErrors[] = 'Import failed: ' . $e->getMessage();
        }
    }

    public function reset(...$properties)
    {
        if (empty($properties)) {
            $this->reset(['csvFile', 'parsedData', 'validationErrors', 'duplicates', 'step', 'duplicateActions']);
        } else {
            parent::reset(...$properties);
        }
    }

    public function startOver()
    {
        $this->reset();
    }

    public function render()
    {
        return view('livewire.contact-import');
    }
}

