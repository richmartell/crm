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
                
                $contact->upcoming_birthday = $upcomingBirthday;
                $contact->upcoming_age = $age;
                $contact->days_until = $daysUntil;
                $contact->is_today = $upcomingBirthday->isToday();
                
                return $contact;
            })
            ->filter(function ($contact) use ($threeMonthsFromNow) {
                // Only include birthdays in the next 3 months
                return $contact->upcoming_birthday->lte($threeMonthsFromNow);
            })
            ->sortBy('upcoming_birthday')
            ->values();
        
        return $contacts;
    }

    public function render()
    {
        return view('livewire.birthday-list', [
            'birthdays' => $this->birthdays,
        ]);
    }
}
