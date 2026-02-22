<?php

namespace App\Services;

use App\Models\User;

class RegisteredUserService
{
    public function createUser(array $data): User
    {
        $user = User::create([
            'username' => $data['username'],
            'password' => $data['password'],
        ]);

        $user->notes()->create([
            'title' => 'Bienvenido a NotesTips',
            'content' => "# Tu primera nota\n\n".
                "Puedes escribir contenido en formato mackdown:\n\n".
                "## Formato basico\n\n".
                "- **Negrita** con `**texto**`\n".
                "- *Cursiva* con `*texto*`\n".
                "- Enlaces: [OpenAI](https://openai.com)\n".
                "- Codigo: `print('hola')`\n\n".
                "## Tareas\n\n".
                "- [ ] Crear mi primera tarea\n".
                "- [x] Marcar una tarea como completada\n\n".
                "## Bloque de codigo\n\n".
                "```js\n".
                "const mensaje = 'Hola desde Markdown';\n".
                "console.log(mensaje);\n".
                "```\n\n".
                'Edita esta nota o crea una nueva cuando quieras.',
        ]);

        return $user;
    }
}
