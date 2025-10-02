<?php

namespace App\Livewire;

use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layout')]
class BirthdayList extends Component
{
    public function getBirthdaysProperty(): Collection
    {
        $today = Carbon::today();
        $threeMonthsFromNow = Carbon::today()->addMonths(3);
        
        // Get all contacts with birthdays
        $contacts = Contact::visibleTo()
            ->whereNotNull('date_of_birth')
            ->get()
            ->map(function ($contact) use ($today) {
                $birthday = Carbon::parse($contact->date_of_birth);
                $currentYear = $today->year;
                
                // Calculate this year's birthday
                $thisYearBirthday = Carbon::create($currentYear, $birthday->month, $birthday->day);
                
                // If birthday has passed this year, use next year's
                $upcomingBirthday = $thisYearBirthday->isPast() 
                    ? $thisYearBirthday->addYear() 
                    : $thisYearBirthday;
                
                // Calculate age they will turn
                $age = $upcomingBirthday->year - $birthday->year;
                
                // Calculate days until birthday
                $daysUntil = $today->diffInDays($upcomingBirthday, false);
                
                $contact->upcoming_date = $upcomingBirthday;
                $contact->upcoming_age = $age;
                $contact->days_until = $daysUntil;
                $contact->is_today = $upcomingBirthday->isToday();
                $contact->event_type = 'birthday';
                
                return $contact;
            })
            ->filter(function ($contact) use ($threeMonthsFromNow) {
                // Only include birthdays in the next 3 months
                return $contact->upcoming_date->lte($threeMonthsFromNow);
            });
        
        return $contacts;
    }

    public function getAnniversariesProperty(): Collection
    {
        $today = Carbon::today();
        $threeMonthsFromNow = Carbon::today()->addMonths(3);
        
        // Get all contacts with anniversaries
        $anniversaries = Contact::visibleTo()
            ->with(['relationships.relatedContact'])
            ->whereNotNull('anniversary_date')
            ->get()
            ->map(function ($contact) use ($today) {
                $anniversary = Carbon::parse($contact->anniversary_date);
                $currentYear = $today->year;
                
                // Calculate this year's anniversary
                $thisYearAnniversary = Carbon::create($currentYear, $anniversary->month, $anniversary->day);
                
                // If anniversary has passed this year, use next year's
                $upcomingAnniversary = $thisYearAnniversary->isPast() 
                    ? $thisYearAnniversary->addYear() 
                    : $thisYearAnniversary;
                
                // Calculate years they will celebrate
                $years = $upcomingAnniversary->year - $anniversary->year;
                
                // Calculate days until anniversary
                $daysUntil = $today->diffInDays($upcomingAnniversary, false);
                
                // Find spouse
                $spouse = $contact->relationships()
                    ->where('relationship_type', 'spouse')
                    ->with('relatedContact')
                    ->first();
                
                $contact->upcoming_date = $upcomingAnniversary;
                $contact->upcoming_years = $years;
                $contact->days_until = $daysUntil;
                $contact->is_today = $upcomingAnniversary->isToday();
                $contact->event_type = 'anniversary';
                $contact->spouse = $spouse ? $spouse->relatedContact : null;
                
                return $contact;
            })
            ->filter(function ($contact) use ($threeMonthsFromNow) {
                // Only include anniversaries in the next 3 months
                return $contact->upcoming_date->lte($threeMonthsFromNow);
            });
        
        // Remove duplicate anniversaries (if both spouses have the same date)
        $uniqueAnniversaries = collect();
        $processedPairs = [];
        
        foreach ($anniversaries as $contact) {
            $spouse = $contact->spouse;
            
            if ($spouse) {
                // Create a unique key for this couple (sorted IDs to avoid duplicates)
                $pairKey = implode('-', [min($contact->id, $spouse->id), max($contact->id, $spouse->id)]);
                
                if (!in_array($pairKey, $processedPairs)) {
                    $processedPairs[] = $pairKey;
                    $uniqueAnniversaries->push($contact);
                }
            } else {
                // No spouse found, add as is
                $uniqueAnniversaries->push($contact);
            }
        }
        
        return $uniqueAnniversaries;
    }

    public function getEventsProperty(): Collection
    {
        // Combine birthdays and anniversaries, then sort by date
        return $this->birthdays
            ->merge($this->anniversaries)
            ->sortBy('upcoming_date')
            ->values();
    }

    public function render()
    {
        return view('livewire.birthday-list', [
            'events' => $this->events,
        ]);
    }
}
