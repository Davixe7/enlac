<?php

namespace Database\Seeders;

use App\Models\InterviewQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InterviewQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InterviewQuestion::create(['question_text' => '¿Cuál es tu mayor fortaleza?']);
        InterviewQuestion::create(['question_text' => '¿Cuál es tu mayor debilidad?']);
        InterviewQuestion::create(['question_text' => '¿Por qué quieres trabajar en esta empresa?']);
        InterviewQuestion::create(['question_text' => '¿Qué experiencia tienes en este campo?']);
        InterviewQuestion::create(['question_text' => '¿Cuáles son tus objetivos profesionales?']);
        InterviewQuestion::create(['question_text' => '¿Cómo manejas la presión?']);
        InterviewQuestion::create(['question_text' => '¿Qué te motiva?']);
        InterviewQuestion::create(['question_text' => '¿Cómo trabajas en equipo?']);
        InterviewQuestion::create(['question_text' => '¿Qué esperas de este puesto?']);
        InterviewQuestion::create(['question_text' => '¿Por qué deberíamos contratarte?']);
        InterviewQuestion::create(['question_text' => '¿Cuál es tu disponibilidad?']);
        InterviewQuestion::create(['question_text' => '¿Tienes alguna pregunta para nosotros?']);
        InterviewQuestion::create(['question_text' => 'Describe un momento en el que superaste un desafío.']);
        InterviewQuestion::create(['question_text' => '¿Cómo te mantienes actualizado en tu campo?']);
        InterviewQuestion::create(['question_text' => '¿Qué habilidades te hacen destacar?']);
        InterviewQuestion::create(['question_text' => '¿Qué te gusta hacer en tu tiempo libre?']);
        InterviewQuestion::create(['question_text' => '¿Cómo te describirías en tres palabras?']);
        InterviewQuestion::create(['question_text' => '¿Qué salario esperas?']);
        InterviewQuestion::create(['question_text' => '¿Estás dispuesto a reubicarte?']);
        InterviewQuestion::create(['question_text' => '¿Cómo manejas el conflicto?']);
        InterviewQuestion::create(['question_text' => '¿Qué te inspiró a seguir esta carrera?']);
        InterviewQuestion::create(['question_text' => '¿Qué te atrae de nuestra cultura empresarial?']);
        InterviewQuestion::create(['question_text' => '¿Cómo contribuyes al éxito de un equipo?']);
        InterviewQuestion::create(['question_text' => '¿Qué rol sueles desempeñar en un equipo?']);
        InterviewQuestion::create(['question_text' => '¿Cómo te adaptas a los cambios?']);
        InterviewQuestion::create(['question_text' => '¿Qué te gustaría lograr en los próximos cinco años?']);
        InterviewQuestion::create(['question_text' => '¿Qué te diferencia de otros candidatos?']);
        InterviewQuestion::create(['question_text' => '¿Por qué dejaste tu último trabajo?']);
        InterviewQuestion::create(['question_text' => '¿Qué has aprendido de tus errores?']);
        InterviewQuestion::create(['question_text' => '¿Qué te gustaría saber sobre nosotros?']);
    }
}
