<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{

    public function run(): void
    {
        $students = [
            [
                'name' => 'João Silva',
                'cpf' => '12345678901',
                'birth_date' => '1995-03-15',
                'email' => 'joao.silva@email.com',
            ],
            [
                'name' => 'Maria Santos',
                'cpf' => '98765432109',
                'birth_date' => '1998-07-22',
                'email' => 'maria.santos@email.com',
            ],
            [
                'name' => 'Pedro Oliveira',
                'cpf' => '11122233344',
                'birth_date' => '1996-11-08',
                'email' => 'pedro.oliveira@email.com',
            ],
            [
                'name' => 'Ana Costa',
                'cpf' => '55566677788',
                'birth_date' => '1997-01-30',
                'email' => 'ana.costa@email.com',
            ],
            [
                'name' => 'Carlos Ferreira',
                'cpf' => '99988877766',
                'birth_date' => '1994-09-12',
                'email' => 'carlos.ferreira@email.com',
            ],
            [
                'name' => 'Lucia Almeida',
                'cpf' => '44433322211',
                'birth_date' => '1999-05-18',
                'email' => 'lucia.almeida@email.com',
            ],
        ];

        foreach ($students as $studentData) {
            Student::create($studentData);
        }

        $courses = Course::all();
        $students = Student::all();

        $joao = $students->where('name', 'João Silva')->first();
        $siCourse = $courses->where('name', 'Sistemas de Informação')->first();
        if ($joao && $siCourse) {
            $joao->courses()->attach($siCourse->id, [
                'status' => 'active',
                'enrolled_at' => now()->subMonths(6)
            ]);
        }

        $maria = $students->where('name', 'Maria Santos')->first();
        $admCourse = $courses->where('name', 'Administração')->first();
        if ($maria && $admCourse) {
            $maria->courses()->attach($admCourse->id, [
                'status' => 'active',
                'enrolled_at' => now()->subMonths(4)
            ]);
        }

        $pedro = $students->where('name', 'Pedro Oliveira')->first();
        $engCourse = $courses->where('name', 'Engenharia de Software')->first();
        if ($pedro && $engCourse) {
            $pedro->courses()->attach($engCourse->id, [
                'status' => 'active',
                'enrolled_at' => now()->subMonths(8)
            ]);
        }

        $ana = $students->where('name', 'Ana Costa')->first();
        $medCourse = $courses->where('name', 'Medicina')->first();
        if ($ana && $medCourse) {
            $ana->courses()->attach($medCourse->id, [
                'status' => 'active',
                'enrolled_at' => now()->subMonths(12)
            ]);
        }

        $carlos = $students->where('name', 'Carlos Ferreira')->first();
        $dirCourse = $courses->where('name', 'Direito')->first();
        if ($carlos && $dirCourse) {
            $carlos->courses()->attach($dirCourse->id, [
                'status' => 'active',
                'enrolled_at' => now()->subMonths(3)
            ]);
        }

        $lucia = $students->where('name', 'Lucia Almeida')->first();
        if ($lucia) {
            if ($siCourse) {
                $lucia->courses()->attach($siCourse->id, [
                    'status' => 'active',
                    'enrolled_at' => now()->subMonths(2)
                ]);
            }
            if ($admCourse) {
                $lucia->courses()->attach($admCourse->id, [
                    'status' => 'active',
                    'enrolled_at' => now()->subMonths(1)
                ]);
            }
        }
    }
}
