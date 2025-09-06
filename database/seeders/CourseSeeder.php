<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'name' => 'Sistemas de Informação',
                'description' => 'Curso de graduação em Sistemas de Informação com foco em programação, algoritmos, estruturas de dados e engenharia de software.',
                'workload' => 3400,
            ],
            [
                'name' => 'Administração',
                'description' => 'Curso de graduação em Administração com foco em gestão, marketing, finanças e empreendedorismo.',
                'workload' => 3200,
            ],
            [
                'name' => 'Engenharia de Software',
                'description' => 'Curso de graduação em Engenharia de Software cobrindo matemática, física e várias disciplinas de engenharia.',
                'workload' => 3600,
            ],
            [
                'name' => 'Medicina',
                'description' => 'Curso de graduação em Medicina fornecendo treinamento abrangente em anatomia humana, fisiologia e prática clínica.',
                'workload' => 8000,
            ],
            [
                'name' => 'Direito',
                'description' => 'Curso de graduação em Direito cobrindo direito constitucional, civil, penal e prática jurídica.',
                'workload' => 3700,
            ],
        ];

        foreach ($courses as $courseData) {
            Course::create($courseData);
        }
    }
}
