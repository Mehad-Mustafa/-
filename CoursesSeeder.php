<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class CoursesSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'title' => 'تعلم Laravel من الصفر إلى الاحتراف',
                'description' => 'كورس شامل لتعلم Laravel framework خطوة بخطوة. ستتعرف على Eloquent ORM، Blade، Routing، Migrations، وكل ما تحتاجه لبناء تطبيقات احترافية.',
                'lessons' => [
                    ['title' => 'مقدمة عن Laravel', 'duration' => 600, 'order' => 1],
                    ['title' => 'تثبيت البيئة', 'duration' => 720, 'order' => 2],
                    ['title' => 'الـ Routing الأساسي', 'duration' => 900, 'order' => 3],
                    ['title' => 'العمل مع Blade', 'duration' => 1200, 'order' => 4],
                    ['title' => 'Eloquent ORM', 'duration' => 1500, 'order' => 5],
                ],
            ],
            [
                'title' => 'تطوير الواجهات الأمامية مع Vue.js',
                'description' => 'تعلم Vue.js 3 وبناء تطبيقات Single Page Applications احترافية. سنغطي Composition API، Vue Router، Pinia، والمزيد.',
                'lessons' => [
                    ['title' => 'مقدمة Vue.js', 'duration' => 540, 'order' => 1],
                    ['title' => 'Components و Props', 'duration' => 900, 'order' => 2],
                    ['title' => 'Composition API', 'duration' => 1080, 'order' => 3],
                ],
            ],
            [
                'title' => 'أساسيات تصميم قواعد البيانات',
                'description' => 'تعلم كيف تصمم قواعد بيانات قوية وفعالة. ستتعرف على Normalization، Indexing، Relationships، والاستعلامات المتقدمة في MySQL.',
                'lessons' => [
                    ['title' => 'مقدمة قواعد البيانات', 'duration' => 600, 'order' => 1],
                    ['title' => 'Normalization', 'duration' => 900, 'order' => 2],
                    ['title' => 'العلاقات بين الجداول', 'duration' => 1200, 'order' => 3],
                    ['title' => 'الفهرسة والأداء', 'duration' => 1080, 'order' => 4],
                ],
            ],
            [
                'title' => 'Tailwind CSS - بناء واجهات حديثة',
                'description' => 'أتقن Tailwind CSS لبناء واجهات مستخدم جذابة وسريعة الاستجابة. ستتعلم Utility-First، Custom Components، Dark Mode، والمزيد.',
                'lessons' => [
                    ['title' => 'مقدمة Tailwind', 'duration' => 480, 'order' => 1],
                    ['title' => 'Utility Classes', 'duration' => 720, 'order' => 2],
                    ['title' => 'Responsive Design', 'duration' => 900, 'order' => 3],
                    ['title' => 'Dark Mode', 'duration' => 600, 'order' => 4],
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $lessons = $courseData['lessons'];
            unset($courseData['lessons']);

            $course = Course::create($courseData);

            foreach ($lessons as $lessonData) {
                $course->lessons()->create([
                    'title' => $lessonData['title'],
                    'description' => 'هذا درس تجريبي. أضف رابط فيديو حقيقي من لوحة الإدارة.',
                    'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'duration' => $lessonData['duration'],
                    'order' => $lessonData['order'],
                    'downloadable' => false,
                ]);
            }
        }

        $this->command->info('✅ Created ' . count($courses) . ' sample courses with lessons');
    }
}
