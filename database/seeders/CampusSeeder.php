<?php

namespace Database\Seeders;

use App\Models\Locations\Building;
use App\Models\Locations\Campus;
use App\Models\People\Phone;
use Illuminate\Database\Seeder;

class CampusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildingA = Building::where('id', '1')->first();
        $buildingB = Building::where('id', '2')->first();
        // make the campus address
        $faxPhone = Phone::factory()->create();
        $admissionPhone = Phone::factory()->create();
        $hs = Campus::create(
            [
                'name' => 'Kalinec High School',
                'abbr' => 'HS',
                'title' => 'The High School',
                'established' => '2024-01-01',
                'img' => "https://www.shutterstock.com/image-photo/exterior-view-typical-american-school-260nw-1965106786.jpg",
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M337.8 5.4C327-1.8 313-1.8 302.2 5.4L166.3 96 48 96C21.5 96 0 117.5 0 144L0 464c0 26.5 21.5 48 48 48l208 0 0-96c0-35.3 28.7-64 64-64s64 28.7 64 64l0 96 208 0c26.5 0 48-21.5 48-48l0-320c0-26.5-21.5-48-48-48L473.7 96 337.8 5.4zM96 192l32 0c8.8 0 16 7.2 16 16l0 64c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-64c0-8.8 7.2-16 16-16zm400 16c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16l0 64c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-64zM96 320l32 0c8.8 0 16 7.2 16 16l0 64c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-64c0-8.8 7.2-16 16-16zm400 16c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16l0 64c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-64zM232 176a88 88 0 1 1 176 0 88 88 0 1 1 -176 0zm88-48c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16s-7.2-16-16-16l-16 0 0-16c0-8.8-7.2-16-16-16z"/></svg>',
                'color_pri' => '#0096FF',// HS is blue
                'color_sec' => '#000000',
                'order' => '1',
            ]);
        $hs->levels()->sync([1, 2, 3, 4]);
        $hs->addresses()->attach($buildingA->address_id, ['primary' => true]);
        $hs->phones()->attach($buildingA->phones()->first()->id, ['primary' => true]);
        $hs->phones()->attach($faxPhone->id, ['primary' => false, 'label' => 'Fax Line']);
        $hs->phones()->attach($admissionPhone->id, ['primary' => false, 'label' => 'Admissions Line']);
        $hs->rooms()->sync($buildingA->rooms->pluck('id')->toArray());

        $faxPhone = Phone::factory()->create();
        $admissionPhone = Phone::factory()->create();
        $ms = Campus::create(
            [
                'name' => 'Kalinec Middle School',
                'abbr' => 'MS',
                'title' => 'The Middle School',
                'established' => '2024-01-01',
                'img' => "https://thumbs.dreamstime.com/b/middle-school-building-20723831.jpg",
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M337.8 5.4C327-1.8 313-1.8 302.2 5.4L166.3 96 48 96C21.5 96 0 117.5 0 144L0 464c0 26.5 21.5 48 48 48l208 0 0-96c0-35.3 28.7-64 64-64s64 28.7 64 64l0 96 208 0c26.5 0 48-21.5 48-48l0-320c0-26.5-21.5-48-48-48L473.7 96 337.8 5.4zM96 192l32 0c8.8 0 16 7.2 16 16l0 64c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-64c0-8.8 7.2-16 16-16zm400 16c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16l0 64c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-64zM96 320l32 0c8.8 0 16 7.2 16 16l0 64c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-64c0-8.8 7.2-16 16-16zm400 16c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16l0 64c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-64zM232 176a88 88 0 1 1 176 0 88 88 0 1 1 -176 0zm88-48c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16s-7.2-16-16-16l-16 0 0-16c0-8.8-7.2-16-16-16z"/></svg>',
                'color_pri' => '#D70040',// MS is red
                'color_sec' => '#FFFFFF',
                'order' => '2',
            ]);
        $ms->levels()->sync([5, 6, 7]);
        $ms->addresses()->attach($buildingB->address_id, ['primary' => true]);
        $ms->phones()->attach($buildingB->phones()->first()->id, ['primary' => true]);
        $ms->phones()->attach($faxPhone->id, ['primary' => false, 'label' => 'Fax Line']);
        $ms->phones()->attach($admissionPhone->id, ['primary' => false, 'label' => 'Admissions Line']);
        $ms->rooms()->sync($buildingB->rooms->pluck('id')->toArray());

        $faxPhone = Phone::factory()->create();
        $admissionPhone = Phone::factory()->create();
        $es = Campus::create(
            [
                'name' => 'Kalinec Elementary School',
                'abbr' => 'ES',
                'title' => 'The Elementary School',
                'established' => '2024-01-01',
                'img' => "https://media.istockphoto.com/id/122269375/photo/elementary-school-in-pennsylvania.jpg?s=612x612&w=0&k=20&c=Ajdag8XkAyO32kbD2NUiFZs3K5HCN2wSCSuWMQOg1xY=",
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M337.8 5.4C327-1.8 313-1.8 302.2 5.4L166.3 96 48 96C21.5 96 0 117.5 0 144L0 464c0 26.5 21.5 48 48 48l208 0 0-96c0-35.3 28.7-64 64-64s64 28.7 64 64l0 96 208 0c26.5 0 48-21.5 48-48l0-320c0-26.5-21.5-48-48-48L473.7 96 337.8 5.4zM96 192l32 0c8.8 0 16 7.2 16 16l0 64c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-64c0-8.8 7.2-16 16-16zm400 16c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16l0 64c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-64zM96 320l32 0c8.8 0 16 7.2 16 16l0 64c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-64c0-8.8 7.2-16 16-16zm400 16c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16l0 64c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-64zM232 176a88 88 0 1 1 176 0 88 88 0 1 1 -176 0zm88-48c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16s-7.2-16-16-16l-16 0 0-16c0-8.8-7.2-16-16-16z"/></svg>',
                'color_pri' => '#50C878',// ES is green
                'color_sec' => '#000000',
                'order' => '3',
            ]);
        $es->levels()->sync([8, 9, 10, 11, 12, 13]);
        $es->addresses()->attach($buildingA->address_id, ['primary' => true]);
        $es->addresses()->attach($buildingB->address_id, ['primary' => false]);
        $es->phones()->attach($buildingA->phones()->first()->id, ['primary' => true]);
        $es->phones()->attach($buildingB->phones()->first()->id, ['primary' => false, 'label' => '2nd Campus']);
        $es->phones()->attach($faxPhone->id, ['primary' => false, 'label' => 'Fax Line']);
        $es->phones()->attach($admissionPhone->id, ['primary' => false, 'label' => 'Admissions Line']);
        $es->rooms()->sync($buildingB->rooms()->inRandomOrder()->limit(5)->get()
            ->merge($buildingB->rooms()->inRandomOrder()->limit(5)->get())
            ->pluck('id')->toArray());
    }
}
