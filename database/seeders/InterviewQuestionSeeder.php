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
            "¿Consultó con algún médico especialista?",
            "¿El embarazo de su hija fue un embarazo planeado?",
            "¿Cuántos embarazos ha tenido?",
            "¿Tuvo su hijo alguna complicación durante el embarazo?",
            "¿Cómo fue el embarazo y qué edad tenía la madre cuando nació su hijo/a?",
            "¿Qué número de hijo(a) es?",
            "¿Tuvo algún problema durante el embarazo?",
            "¿A las cuántas semanas nació?",
            "¿Usó fórceps o hubo necesidad de utilizar la aspiradora?",
            "Descríbame, ¿cómo fue el nacimiento de su bebé?",
            "¿Su hijo(a) lloró al nacer?",
            "Actualmente, ¿viven juntos los padres, quiénes viven en casa?",
            "En caso de haberse separado, ¿cuál fue el motivo de la separación?",
            "¿Le dio permiso si la respuesta es sí. ¿Cuánto tiempo? Si la respuesta es no. ¿Por qué motivo?",
            "¿El niño se encuentra a su edad, y por cuánto tiempo?",
            "¿Gatea? ¿A qué edad?",
            "¿A qué edad caminó?",
            "¿El niño balbucea y a qué edad?",
            "¿En algún momento notó algún retraso en su desarrollo?",
            "¿El niño(a) acudió a la guardería, kinder o algún otro centro educativo? ¿Cómo fue?",
            "Actualmente, ¿qué alimentos consume (describa el plato de comida de un día del menú del niño(a))?",
            "En el día a día, ¿cómo es la rutina del niño(a) desde que se despierta hasta que se duerme?",
            "¿Qué le llama la atención cuando lo observan jugar?",
            "¿Cómo es el ciclo de sueño de su niño(a) (cuántas siestas, a qué hora se acuesta y se levanta)?",
            "¿El niño rechina los dientes (durante el día, durante la noche o ambos)?",
            "¿Es de mayor de 9 años y ya tuvo su período? ¿Qué edad tenía cuando inició su período? ¿Toma alguna medicación o tratamiento hormonal?",
            "¿Ha tenido algún evento emocional fuerte (pérdida de algún familiar, separación, violencia, etc.)?",
            "¿Hay algo más que desea agregar?"
        ];

        foreach ($preguntas as $pregunta) {
            InterviewQuestion::create([
                'question_text' => $pregunta
            ]);
        }
    }
}
