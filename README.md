
# Sistema de votación

Herramienta web para que un administrador someta a votación una lista de puntos predefinidos y contabilice en tiempo real los votos a mano alzada de dos grupos. Cada grupo tiene un peso distinto en el resultado: el Grupo 1 pondera 2/3 y el Grupo 2 pondera 1/3, reflejando una estructura de representación diferenciada. El resultado final se expresa en porcentajes ponderados para las tres categorías posibles: Afirmativo, Abstención y No voto.

## PWA (instalación offline)

La aplicación funciona como PWA: el navegador muestra un botón para instalarla en el dispositivo y funciona sin conexión una vez instalada.

Requiere tres archivos adicionales al `index.html`:

- **`manifest.json`:** define el nombre, ícono y colores de la app.
- **`sw.js`:** service worker que cachea los archivos necesarios al primer acceso.
- Dos líneas en `index.html` para enlazar el manifest y registrar el service worker.



Al imprimir la página desde el navegador, solo debe ser visible la sección de resultados: el punto votado, la tabla de porcentajes y las barras de progreso. Los botones de acción y el formulario deben ocultarse.

## Criterios técnicos

- Una sola página HTML con todo incluido (HTML, CSS y JS en el mismo archivo).
- Bootstrap vía CDN para los estilos, sin CSS custom ni librerías adicionales.
- JavaScript reducido al mínimo indispensable.
- Todo el sistema en español: labels, mensajes de error y resultados.

## Flujo general

La página tiene un encabezado con fondo de color y el título del sistema en texto blanco, visible en todo momento.

La página carga mostrando la **pantalla de inicio**, donde el administrador puede revisar y editar los 5 puntos a votar antes de comenzar. Una vez confirmados, comienza el ciclo de votación y ya no se puede volver a la pantalla de inicio.

El administrador completa los datos de ambos grupos y presiona **Confirmar**. Si todo es válido, el formulario se oculta y se muestra la sección de resultados.

Desde la sección de resultados hay tres acciones posibles:
- **Corregir:** oculta los resultados y vuelve al formulario con los datos intactos para que el administrador los revise.
- **Votar anexo:** disponible solo si el punto actual no es un anexo. Abre una nueva votación numerada como `N.1` (por ejemplo, si se acaba de votar el punto 3, el anexo es el punto 3.1). El campo de texto del anexo es editable, ya que no tiene texto predefinido. Al confirmar el resultado del anexo, la única opción disponible es pasar al siguiente punto (no hay anexo del anexo).
- **Siguiente punto:** limpia los campos de los grupos, carga el siguiente punto predefinido y muestra el formulario. Si ya se votaron todos los puntos, el botón no aparece.

## Pantalla de inicio

Muestra una textarea vacía numerada. El administrador debe ingresar el texto del punto a votar. Puede agregar más puntos con el botón **Agregar punto**, que añade una nueva textarea vacía al final de la lista. No hay límite de puntos. Ningún punto puede estar vacío al presionar **Comenzar**.

## Formulario principal (`index.html`)

### Puntos a votar

Una vez iniciado el ciclo, el campo que muestra el punto actual es de solo lectura; el administrador no puede modificarlo.

Cuando se vota un anexo, el campo de texto es editable y el administrador debe ingresar el texto manualmente.

Se muestra un indicador del progreso actual, por ejemplo: *"Punto 3 de 5"* o *"Punto 3.1 (anexo)"*.

### Grupos

Dos fieldsets, uno por grupo, cada uno con los siguientes campos:

- **Presentes:** cantidad de personas presentes en el grupo (entero, obligatorio, mínimo 0).
- **Afirmativo:** cantidad de personas que votan afirmativo (entero, obligatorio, mínimo 0).
- **Abstención:** cantidad de personas que se abstienen (entero, obligatorio, mínimo 0).

## Validaciones

El botón **Confirmar** siempre está habilitado. Al presionar se verifican todas las reglas en orden; si alguna falla se muestra un `alert` y se detiene:

- El punto a votar no puede estar vacío. → *"Ingresá el punto a votar."*
- En cada grupo, la suma de Afirmativo y Abstención no puede superar los Presentes. → *"Grupo N: los votos superan los presentes."*
- Al menos uno de los dos grupos debe tener Presentes mayor a 0. → *"Al menos un grupo debe tener presentes."*

## Cálculo del resultado

Cada categoría (Afirmativo, Abstención y No voto) se calcula de la misma manera: se obtiene qué fracción de los presentes de cada grupo corresponde a esa categoría, y luego se pondera por el peso del grupo (2/3 para el Grupo 1, 1/3 para el Grupo 2). La suma de los tres porcentajes siempre da 100%.

### Casos especiales

- Si un grupo tiene 0 presentes, no se pondera y todo el peso recae sobre el otro grupo.
- Si ambos grupos tienen 0 presentes, se muestra un error y no se calcula el resultado.

### Formato

Los porcentajes se muestran redondeados a dos decimales. El resultado es puramente visual en pantalla.

Las categorías siempre se muestran en este orden: Afirmativo, Abstención, No voto. Cada una tiene su color distintivo:

- **Afirmativo:** verde
- **Abstención:** rojo
- **No voto:** gris

## Ejemplo práctico

**Punto a votar:**
> Se propone modificar el reglamento interno para que las reuniones ordinarias
> pasen de frecuencia semanal a frecuencia quincenal, liberando el espacio los
> martes alternos para actividades abiertas a la comunidad.

| | Grupo 1 | Grupo 2 |
|---|---|---|
| Presentes | 12 | 9 |
| Afirmativo | 8 | 3 |
| Abstención | 2 | 4 |
| *No voto* | *2* | *2* |

Al presionar **Confirmar**, el sistema valida los datos y calcula el resultado.

El Grupo 1 tiene el doble de presentes que el Grupo 2 y una mayoría afirmativa clara, mientras que el Grupo 2 está más dividido. Al ponderar por el peso de cada grupo (2/3 y 1/3), el resultado final es:

- **Afirmativo: 55.56%** *(8/12 × 2/3) + (3/9 × 1/3)*
- **Abstención: 22.22%** *(2/12 × 2/3) + (4/9 × 1/3)*
- ***No voto: 22.22%*** *(2/12 × 2/3) + (2/9 × 1/3)*

Debajo de la tabla aparece una barra de progreso por categoría y los botones **Corregir** y **Votar de nuevo**.

