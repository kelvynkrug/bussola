<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'Programação Web',
                'description' => 'Conceitos básicos de programação e técnicas de resolução de problemas.',
                'workload' => 80,
            ],
            [
                'name' => 'Estruturas de Dados',
                'description' => 'Estudo de estruturas de dados fundamentais e algoritmos.',
                'workload' => 80,
            ],
            [
                'name' => 'Banco de Dados',
                'description' => 'Introdução ao projeto e gerenciamento de banco de dados.',
                'workload' => 80,
            ],
            [
                'name' => 'Matemática',
                'description' => 'Conceitos matemáticos fundamentais e aplicações.',
                'workload' => 80,
            ],
            [
                'name' => 'Gestão Empresarial',
                'description' => 'Princípios de gestão empresarial e liderança.',
                'workload' => 80,
            ],
            [
                'name' => 'Marketing',
                'description' => 'Estratégias de marketing e análise do comportamento do consumidor.',
                'workload' => 80,
            ],
            [
                'name' => 'Física',
                'description' => 'Conceitos fundamentais de física e trabalho laboratorial.',
                'workload' => 80,
            ],
            [
                'name' => 'Anatomia',
                'description' => 'Anatomia humana e sistemas fisiológicos.',
                'workload' => 80,
            ],
            [
                'name' => 'Direito Constitucional',
                'description' => 'Estudo dos princípios constitucionais e estrutura legal.',
                'workload' => 80,
            ],
            [
                'name' => 'Ética',
                'description' => 'Ética profissional e habilidades de comunicação.',
                'workload' => 80,
            ],
        ];

        foreach ($subjects as $subjectData) {
            Subject::create($subjectData);
        }

        $courses = Course::all();
        $subjects = Subject::all();

        $siCourse = $courses->where('name', 'Sistemas de Informação')->first();
        if ($siCourse) {
            $siSubjects = $subjects->whereIn('name', [
                'Programação Web',
                'Estruturas de Dados',
                'Banco de Dados',
                'Matemática',
                'Ética'
            ]);
            $siCourse->subjects()->attach($siSubjects->pluck('id'));
        }

        $admCourse = $courses->where('name', 'Administração')->first();
        if ($admCourse) {
            $admSubjects = $subjects->whereIn('name', [
                'Gestão Empresarial',
                'Marketing',
                'Matemática',
                'Ética'
            ]);
            $admCourse->subjects()->attach($admSubjects->pluck('id'));
        }

        $engCourse = $courses->where('name', 'Engenharia de Software')->first();
        if ($engCourse) {
            $engSubjects = $subjects->whereIn('name', [
                'Matemática',
                'Física',
                'Programação Web',
                'Ética'
            ]);
            $engCourse->subjects()->attach($engSubjects->pluck('id'));
        }

        $medCourse = $courses->where('name', 'Medicina')->first();
        if ($medCourse) {
            $medSubjects = $subjects->whereIn('name', [
                'Anatomia',
                'Física',
                'Matemática',
                'Ética'
            ]);
            $medCourse->subjects()->attach($medSubjects->pluck('id'));
        }

        $dirCourse = $courses->where('name', 'Direito')->first();
        if ($dirCourse) {
            $dirSubjects = $subjects->whereIn('name', [
                'Direito Constitucional',
                'Ética'
            ]);
            $dirCourse->subjects()->attach($dirSubjects->pluck('id'));
        }
    }
}
