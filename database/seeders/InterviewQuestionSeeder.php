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
        $preguntas = [
	    "Cuénteme, ¿por qué acudió a ENLAC?, y, ¿qué espera del Instituto?",
	    "¿Lo ha checado un médico especialista?",
	    "El embarazo de su hijo(a), ¿fue un embarazado planeado?",
	    "¿Cuántos embarazos ha tenido? (contando los abortos en caso de haber tenido)",
	    "¿Cómo fue el embarazo y qué edad tenía la madre cuando nació su hijo(a)?",
	    "¿Qué número de hijo(a) es? (Contando todos los embarazos)",
	    "¿Tomó medicamentos durante el embarazo?, ¿cuáles y por cuánto tiempo?",
	    "¿A las cuántas semanas nació?",
	    "¿Fue cesárea o parto natural?",
	    "Descríbame, ¿cómo fue el nacimiento de su bebé?",
	    "¿Su hijo(a) lloró al nacer?",
	    "Actualmente, ¿viven juntos los padres?, ¿quiénes viven en casa?",
	    "En caso de haberse separado, ¿cuál fue el motivo de la separación?",
	    "¿Le dio pecho? Si la respuesta es SÍ: ¿cuánto tiempo? Si la respuesta es NO, ¿por qué motivo?",
	    "¿El niño se arrastró?, ¿a qué edad?, y, ¿por cuánto tiempo?",
	    "¿Gateó?, ¿usó andador?, ¿a qué edad?",
	    "¿A qué edad caminó?",
	    "¿El niño balbuceó?, ¿a qué edad?",
	    "¿En algún momento notó algún retroceso en su desarrollo?",
	    "¿El niño(a) acudió a la guardería, kínder o escuela?, ¿a qué edad?, y, ¿cómo fue?",
	    "Actualmente, ¿qué alimentos come? (¿Es selectivo?, por ejemplo, papillas, sólidos…). Descríbame el menú de un día del niño(a).",
	    "En el día a día, ¿cómo es la rutina del niño(a)? (desde que se despierta hasta que se duerme)",
	    "¿Qué le llama la atención cuando lo observan jugar? (Clasifica/coloca juguetes en hilera o desordenados, juega solo o con otros niños, los busca para jugar, muestra movimientos repetitivos o aleteo, etc…)",
	    "¿Cómo es el ciclo de sueño de su niño(a)?, ¿tiene ronquidos?, o, ¿se estremece al dormir?",
	    "¿El niño rechina los dientes? (Durante el día, durante la noche o ambos)",
	    "En caso de ser mujer mayor a 9 años: ¿ya tiene su periodo de menstruación?, ¿a qué edad?, ¿es regular?, ¿toma algún medicamento o tratamiento hormonal?",
	    "¿Hay algo en cuestión emocional que usted y/o el(la) niño(a) hayan vivido? (pérdida de algún familiar, separación, violencia, etc…)",
	    "¿Hay algo más que desee agregar?"
        ];

        foreach ($preguntas as $pregunta) {
            InterviewQuestion::create([
                'question_text' => $pregunta
            ]);
        }
    }
}
