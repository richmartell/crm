<div class="py-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Import Contacts</flux:heading>
            <flux:subheading>Upload a CSV file to import multiple contacts at once</flux:subheading>
        </div>
        <flux:button href="{{ route('contacts.index') }}" variant="ghost" icon="arrow-left">
            Back to Contacts
        </flux:button>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('success') }}
        </flux:callout>
    @endif

    @if (session('error'))
        <flux:callout variant="danger" icon="exclamation-circle">
            {{ session('error') }}
        </flux:callout>
    @endif

    @if($step === 'upload')
        <!-- Upload Step -->
        <flux:card>
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Step 1: Upload CSV File</flux:heading>
                    <flux:subheading>Select or drag a CSV file containing your contacts</flux:subheading>
                </div>

                <flux:separator variant="subtle" />

                <!-- Download Template -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <flux:icon.information-circle class="size-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" />
                        <div class="flex-1">
                            <flux:heading size="sm" class="text-blue-900 dark:text-blue-100 mb-1">Need a template?</flux:heading>
                            <flux:text class="text-blue-800 dark:text-blue-200 text-sm mb-3">
                                Download our CSV template with example data to see the required format.
                            </flux:text>
                            <flux:button wire:click="downloadTemplate" variant="primary" size="sm" icon="arrow-down-tray">
                                Download Template
                            </flux:button>
                        </div>
                    </div>
                </div>

                <!-- File Upload Area -->
                <div 
                    x-data="{ 
                        isDragging: false,
                        handleDrop(e) {
                            this.isDragging = false;
                            if (e.dataTransfer.files.length) {
                                @this.upload('csvFile', e.dataTransfer.files[0]);
                            }
                        }
                    }"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="handleDrop"
                    class="border-2 border-dashed rounded-lg p-12 text-center transition-colors"
                    :class="isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-zinc-300 dark:border-zinc-700'"
                >
                    <flux:icon.arrow-up-tray class="size-12 mx-auto mb-4 text-zinc-400 dark:text-zinc-600" />
                    
                    <flux:heading size="lg" class="mb-2">
                        Drop your CSV file here
                    </flux:heading>
                    <flux:subheading class="mb-4">
                        or click to browse
                    </flux:subheading>

                    <label for="csv-upload" class="cursor-pointer">
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            Select File
                        </span>
                    </label>
                    <input 
                        type="file" 
                        wire:model="csvFile" 
                        accept=".csv,.txt"
                        class="hidden"
                        id="csv-upload"
                    />

                    @if($csvFile)
                        <div class="mt-4 flex items-center justify-center gap-2 text-sm text-green-600 dark:text-green-400">
                            <flux:icon.check-circle class="size-5" />
                            <span>{{ $csvFile->getClientOriginalName() }}</span>
                        </div>
                    @endif

                    @error('csvFile')
                        <div class="mt-4 text-sm text-red-600 dark:text-red-400">
                            {{ $message }}
                        </div>
                    @enderror

                    <div wire:loading wire:target="csvFile" class="mt-4">
                        <flux:text class="text-blue-600 dark:text-blue-400">Uploading file...</flux:text>
                    </div>
                </div>

                <!-- Required Format Info -->
                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                    <flux:heading size="sm" class="mb-2">Required CSV Format</flux:heading>
                    <flux:text size="sm" class="mb-2">Your CSV file must include these required columns:</flux:text>
                    <ul class="list-disc list-inside text-sm text-zinc-600 dark:text-zinc-400 space-y-1 mb-3">
                        <li><strong>first_name</strong> - Contact's first name</li>
                        <li><strong>last_name</strong> - Contact's last name</li>
                    </ul>
                    <flux:text size="sm" class="mb-2">Optional columns:</flux:text>
                    <ul class="list-disc list-inside text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                        <li>email, phone_number, date_of_birth (YYYY-MM-DD), anniversary_date (YYYY-MM-DD)</li>
                        <li>street, address_line_2, city, postcode, country</li>
                        <li>tags (comma-separated), notes</li>
                    </ul>
                </div>

                @if($csvFile)
                    <div class="flex justify-end">
                        <flux:button wire:click="parseFile" variant="primary" icon="arrow-right">
                            Process File
                        </flux:button>
                    </div>
                @endif
            </div>
        </flux:card>

        <!-- Show Errors if any -->
        @if(!empty($validationErrors))
            <flux:card>
                <flux:heading size="lg" class="text-red-600 dark:text-red-400 mb-4">
                    <flux:icon.exclamation-triangle class="size-6 inline" /> Validation Errors
                </flux:heading>
                <flux:text class="mb-4">Please fix the following errors in your CSV file:</flux:text>
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 max-h-96 overflow-y-auto">
                    <ul class="list-disc list-inside space-y-1 text-sm text-red-800 dark:text-red-200">
                        @foreach($validationErrors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </flux:card>
        @endif

    @elseif($step === 'review')
        <!-- Review Step -->
        <flux:card>
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Step 2: Review Import</flux:heading>
                    <flux:subheading>
                        Found {{ count($parsedData) }} contact(s) ready to import
                        @if(count($duplicates) > 0)
                            <span class="text-yellow-600 dark:text-yellow-400">
                                ({{ count($duplicates) }} potential duplicate(s) detected)
                            </span>
                        @endif
                    </flux:subheading>
                </div>

                <flux:separator variant="subtle" />

                @if(count($duplicates) > 0)
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <flux:heading size="sm" class="text-yellow-900 dark:text-yellow-100 mb-3">
                            <flux:icon.exclamation-triangle class="size-5 inline" /> Potential Duplicates Found
                        </flux:heading>
                        <flux:text class="text-yellow-800 dark:text-yellow-200 text-sm mb-4">
                            The following contacts appear to already exist. Choose how to handle each one:
                        </flux:text>

                        <div class="space-y-4">
                            @foreach($duplicates as $index => $duplicate)
                                <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-yellow-300 dark:border-yellow-700">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <flux:badge color="blue" size="sm" class="mb-2">Existing Contact</flux:badge>
                                            <div class="text-sm space-y-1">
                                                <div><strong>Name:</strong> {{ $duplicate['existing']->first_name }} {{ $duplicate['existing']->last_name }}</div>
                                                @if($duplicate['existing']->email)
                                                    <div><strong>Email:</strong> {{ $duplicate['existing']->email }}</div>
                                                @endif
                                                @if($duplicate['existing']->phone_number)
                                                    <div><strong>Phone:</strong> {{ $duplicate['existing']->phone_number }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <flux:badge color="yellow" size="sm" class="mb-2">New Data (Row {{ $duplicate['row'] }})</flux:badge>
                                            <div class="text-sm space-y-1">
                                                <div><strong>Name:</strong> {{ $duplicate['new']['first_name'] }} {{ $duplicate['new']['last_name'] }}</div>
                                                @if(!empty($duplicate['new']['email']))
                                                    <div><strong>Email:</strong> {{ $duplicate['new']['email'] }}</div>
                                                @endif
                                                @if(!empty($duplicate['new']['phone_number']))
                                                    <div><strong>Phone:</strong> {{ $duplicate['new']['phone_number'] }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input 
                                                type="radio" 
                                                wire:model.live="duplicateActions.{{ $duplicate['row'] }}" 
                                                value="skip"
                                                class="text-blue-600"
                                            />
                                            <flux:text size="sm">Skip (keep existing)</flux:text>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input 
                                                type="radio" 
                                                wire:model.live="duplicateActions.{{ $duplicate['row'] }}" 
                                                value="import"
                                                class="text-blue-600"
                                            />
                                            <flux:text size="sm">Import anyway (create duplicate)</flux:text>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Summary of what will be imported -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <flux:heading size="sm" class="text-blue-900 dark:text-blue-100 mb-2">Import Summary</flux:heading>
                    <div class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                        <div>Total contacts in file: <strong>{{ count($parsedData) }}</strong></div>
                        @if(count($duplicates) > 0)
                            @php
                                $skipping = collect($duplicateActions)->filter(fn($action) => $action === 'skip')->count();
                                $importing = count($parsedData) - $skipping;
                            @endphp
                            <div>Duplicates to skip: <strong>{{ $skipping }}</strong></div>
                            <div>Contacts to import: <strong>{{ $importing }}</strong></div>
                        @else
                            <div>Contacts to import: <strong>{{ count($parsedData) }}</strong></div>
                        @endif
                    </div>
                </div>

                <flux:separator variant="subtle" />

                <div class="flex justify-between">
                    <flux:button wire:click="startOver" variant="ghost" icon="arrow-left">
                        Start Over
                    </flux:button>
                    <flux:button wire:click="importContacts" variant="primary" icon="check">
                        Confirm & Import
                    </flux:button>
                </div>
            </div>
        </flux:card>

    @elseif($step === 'complete')
        <!-- Complete Step -->
        <flux:card class="text-center">
            <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/40 mb-4">
                <flux:icon.check-circle class="size-8 text-green-600 dark:text-green-300" />
            </div>
            <flux:heading size="xl" class="mb-2">Import Complete!</flux:heading>
            <flux:subheading class="mb-6">Your contacts have been successfully imported.</flux:subheading>
            
            <div class="flex gap-3 justify-center">
                <flux:button href="{{ route('contacts.index') }}" variant="primary" icon="users">
                    View Contacts
                </flux:button>
                <flux:button wire:click="startOver" variant="ghost" icon="arrow-path">
                    Import More
                </flux:button>
            </div>
        </flux:card>
    @endif
</div>

