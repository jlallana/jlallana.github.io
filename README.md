
# Sistema de votación

Herramienta web para que un administrador someta un punto a votación y contabilice en tiempo real los votos a mano alzada de dos grupos. Cada grupo tiene un peso distinto en el resultado: el Grupo 1 pondera 2/3 y el Grupo 2 pondera 1/3, reflejando una estructura de representación diferenciada. El resultado final se expresa en porcentajes ponderados para las tres categorías posibles: Afirmativo, Abstención y No voto.

## Impresión

Al imprimir la página desde el navegador, solo debe ser visible la sección de resultados: el punto votado, la tabla de porcentajes y las barras de progreso. Los botones de acción y el formulario deben ocultarse.

## Criterios técnicos

- Una sola página HTML con todo incluido (HTML, CSS y JS en el mismo archivo).
- Bootstrap vía CDN para los estilos, sin CSS custom ni librerías adicionales.
- JavaScript reducido al mínimo indispensable.
- Todo el sistema en español: labels, mensajes de error y resultados.

## Flujo general

La página carga mostrando el formulario. La sección de resultados está oculta.

El administrador completa el punto a votar y los datos de ambos grupos. El botón **Confirmar** permanece deshabilitado hasta que todos los campos obligatorios tengan valor. Al presionar **Confirmar**, se ejecutan las validaciones adicionales. Si todo es válido, el formulario se oculta y se muestra la sección de resultados. La tabla de resultados incluye una columna adicional con el cálculo detallado de cada categoría, en gris cursiva, para que el administrador pueda validar los números.

Desde la sección de resultados hay dos acciones posibles:
- **Corregir:** oculta los resultados y vuelve al formulario con los datos intactos para que el administrador los revise.
- **Votar de nuevo:** limpia el formulario por completo y reinicia el ciclo.

## Formulario principal (`index.html`)

### Punto a votar

Campo de texto multilinea, obligatorio.

### Grupos

Dos fieldsets, uno por grupo, cada uno con los siguientes campos:

- **Presentes:** cantidad de personas presentes en el grupo (entero, obligatorio, mínimo 0).
- **Afirmativo:** cantidad de personas que votan afirmativo (entero, obligatorio, mínimo 0).
- **Abstención:** cantidad de personas que se abstienen (entero, obligatorio, mínimo 0).

## Validaciones

El botón **Confirmar** se habilita únicamente cuando todos los campos tienen valor y los datos son consistentes. Las siguientes reglas se validan en tiempo real mientras el administrador escribe:

- En cada grupo, la suma de Afirmativo y Abstención no puede superar los Presentes. → *"Los votos superan los presentes."* (se muestra debajo del fieldset correspondiente)

Al presionar **Confirmar** se verifica:

- El punto a votar no puede estar vacío. → *"Ingresá el punto a votar."*
- Al menos uno de los dos grupos debe tener Presentes mayor a 0. → *"Al menos un grupo debe tener presentes."* (se muestra como alerta del navegador)

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

| Categoría | Cálculo | Resultado |
|---|---|---|
| **Afirmativo** | (8/12 × 2/3) + (3/9 × 1/3) | **55.56%** |
| **Abstención** | (2/12 × 2/3) + (4/9 × 1/3) | **22.22%** |
| **No voto** | (2/12 × 2/3) + (2/9 × 1/3) | **22.22%** |

Debajo de la tabla aparece una barra de progreso por categoría y los botones **Corregir** y **Votar de nuevo**.

