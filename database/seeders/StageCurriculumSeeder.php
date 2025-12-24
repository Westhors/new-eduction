<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StageCurriculumSeeder extends Seeder
{
    // تعريف المراحل لكل منهج
    private $curriculumStages = [
        // المنهج الوزاري (id: 2 حسب بياناتك)
        'UAE وزاري' => [
            'تمهيدي حضانة',
            'روضة أولى',
            'روضة ثانية',
            'صف أول',
            'صف ثاني',
            'صف ثالث',
            'صف رابع',
            'صف خامس',
            'صف سادس',
            'صف سابع',
            'صف ثامن',
            'صف تاسع',
            'صف عاشر',
            'صف حادي عشر',
            'صف ثاني عشر'
        ],

        // المنهج البريطاني (id: 4)
        'UK بريطاني' => [
            'Nursery',
            'FS1',
            'FS2',
            'Year 1',
            'Year 2',
            'Year 3',
            'Year 4',
            'Year 5',
            'Year 6',
            'Year 7',
            'Year 8',
            'Year 9',
            'Year 10',
            'Year 11',
            'Year 12',
            'Year 13'
        ],

        // المنهج الأمريكي (id: 3)
        'US أمريكي' => [
            'Per KG',
            'KG1',
            'KG2',
            'Grade 1',
            'Grade 2',
            'Grade 3',
            'Grade 4',
            'Grade 5',
            'Grade 6',
            'Grade 7',
            'Grade 8',
            'Grade 9',
            'Grade 10',
            'Grade 11',
            'Grade 12'
        ],

        // منهج IB (id: 5)
        'IB البكالوريا' => [
            'PYP - Pre-KG',
            'PYP - KG 1',
            'PYP - KG 2',
            'PYP - Grade 1',
            'PYP - Grade 2',
            'PYP - Grade 3',
            'PYP - Grade 4',
            'PYP - Grade 5',
            'MYP - Grade 6',
            'MYP - Grade 7',
            'MYP - Grade 8',
            'MYP - Grade 9',
            'MYP - Grade 10',
            'CP / DP - Grade 11',
            'CP / DP - Grade 12'
        ],

        // المنهج الهندي (id: 6)
        'Indian الهندي' => [
            'CBSE - Pre-KG',
            'CBSE - LKG',
            'CBSE - UKG',
            'CBSE - Grade 1',
            'CBSE - Grade 2',
            'CBSE - Grade 3',
            'CBSE - Grade 4',
            'CBSE - Grade 5',
            'CBSE - Grade 6',
            'CBSE - Grade 7',
            'CBSE - Grade 8',
            'CBSE - Grade 9',
            'CBSE - Grade 10',
            'CBSE - Grade 11',
            'CBSE - Grade 12'
        ],

        // المناهج الأخرى (id: 7)
        'Other Curriculums' => [
            'Other - Pre-KG',
            'Other - KG 1',
            'Other - KG 2',
            'Other - Grade 1',
            'Other - Grade 2',
            'Other - Grade 3',
            'Other - Grade 4',
            'Other - Grade 5',
            'Other - Grade 6',
            'Other - Grade 7',
            'Other - Grade 8',
            'Other - Grade 9',
            'Other - Grade 10',
            'Other - Grade 11',
            'Other - Grade 12'
        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $countryId = 227;

        // ثانياً: ربط المناهج بالمراحل
        foreach ($this->curriculumStages as $curriculumName => $stages) {
            // الحصول على id المنهج من جدول curricula (وليس curriculums)
            $curriculum = DB::table('curricula')
                ->where('name', $curriculumName)
                ->first();

            if (!$curriculum) {
                $this->command->error("المنهج '{$curriculumName}' غير موجود في قاعدة البيانات!");
                continue;
            }

            $this->command->info("تم العثور على المنهج: '{$curriculumName}' (ID: {$curriculum->id})");

            foreach ($stages as $stageName) {
                // الحصول على id المرحلة
                $stage = DB::table('stages')
                    ->where('name', $stageName)
                    ->first();

                if (!$stage) {
                    $this->command->error("المرحلة '{$stageName}' غير موجودة في قاعدة البيانات!");
                    continue;
                }

                // التحقق من عدم وجود الرابط مسبقاً
                $existingLink = DB::table('stage_curriculum')
                    ->where('stage_id', $stage->id)
                    ->where('curriculum_id', $curriculum->id)
                    ->first();

                if (!$existingLink) {
                    DB::table('stage_curriculum')->insert([
                        'stage_id' => $stage->id,
                        'curriculum_id' => $curriculum->id,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);

                    $this->command->info("تم ربط المنهج '{$curriculumName}' بالمرحلة '{$stageName}'");
                } else {
                    $this->command->info("الرابط بين المنهج '{$curriculumName}' والمرحلة '{$stageName}' موجود بالفعل");
                }
            }
        }

        $this->command->info('تم إكمال عملية ربط المناهج بالمراحل بنجاح!');
    }
}
